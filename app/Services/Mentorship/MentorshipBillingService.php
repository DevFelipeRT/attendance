<?php

declare(strict_types=1);

namespace App\Services\Mentorship;

use App\Enums\ClassLessonStatus;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipDebit;
use App\Models\Mentorship\MentorshipPayment;
use App\Models\Mentorship\MentorshipSession;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Mentorship billing service.
 *
 * Coordinates credit and debit operations for mentorships using an
 * hour-based balance derived from payments and sessions.
 */
class MentorshipBillingService
{
    /**
     * Register a mentorship payment and convert the monetary amount into hour credits.
     *
     * The mentorship can be provided as a model or primary key. The amount must be positive.
     * The mentorship must define a positive hourly rate. The conversion
     * (amount / hourly_rate) must result in an exact integer number of hours.
     *
     * Arithmetic uses integer cents to avoid floating-point inaccuracies.
     *
     * @param Mentorship|int   $mentorship
     * @param float|int|string $amount
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function registerPayment(
        Mentorship|int $mentorship,
        float|int|string $amount
    ): MentorshipPayment {
        $mentorshipModel = $this->resolveMentorship($mentorship);
        $this->validateMentorshipConfiguration($mentorshipModel);

        $amountCents = $this->toCents($amount);
        if ($amountCents <= 0) {
            throw new DomainException('Payment amount must be a positive value.');
        }

        $rateCents = $this->toCents((string) $mentorshipModel->hourly_rate);
        if ($rateCents <= 0) {
            throw new DomainException('Hourly rate must be a positive value for mentorship payments.');
        }

        if ($amountCents % $rateCents !== 0) {
            throw new DomainException('Payment does not convert to an exact number of mentorship hours.');
        }

        $hours = (int) ($amountCents / $rateCents);

        return DB::transaction(function () use ($mentorshipModel, $amountCents, $hours): MentorshipPayment {
            $payment                = new MentorshipPayment();
            $payment->mentorship_id = $mentorshipModel->getKey();
            $payment->amount        = $this->formatAmountFromCents($amountCents);
            $payment->hours         = $hours;
            $payment->paid_at       = now();
            $payment->save();

            return $payment;
        });
    }

    /**
     * Register a debit of mentorship hours based on a mentorship session.
     *
     * This method is intended to be called after attendance processing decides
     * that a debit must occur for the given session. It validates configuration,
     * enforces whole-hour duration and persists a debit record. The pair
     * (mentorship_id, mentorship_session_id) must remain idempotent.
     *
     * If a unique index exists on (mentorship_id, mentorship_session_id),
     * duplicate attempts will fail with a QueryException and be converted to a DomainException.
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function registerDebitForAttendance(MentorshipSession $session): MentorshipDebit
    {
        $mentorship = $session->mentorship;

        if ($mentorship === null) {
            throw new ModelNotFoundException('Mentorship not found for session.');
        }

        $this->validateMentorshipConfiguration($mentorship);

        $durationMinutes = (int) $session->duration_minutes;

        if ($durationMinutes <= 0) {
            throw new DomainException('Session duration must be a positive value for mentorship debit.');
        }

        if ($durationMinutes % 60 !== 0) {
            throw new DomainException('Session duration must represent whole hours for mentorship debit.');
        }

        $hours = (int) ($durationMinutes / 60);

        return DB::transaction(function () use ($mentorship, $session, $hours): MentorshipDebit {
            $existing = MentorshipDebit::where('mentorship_id', $mentorship->getKey())
                ->where('mentorship_session_id', $session->getKey())
                ->lockForUpdate()
                ->first();

            if ($existing !== null) {
                throw new DomainException('Debit for this mentorship session has already been registered.');
            }

            try {
                $debit                        = new MentorshipDebit();
                $debit->mentorship_id         = $mentorship->getKey();
                $debit->mentorship_session_id = $session->getKey();
                $debit->hours                 = $hours;
                $debit->debited_at            = now();
                $debit->save();

                return $debit;
            } catch (QueryException $e) {
                throw new DomainException('Debit for this mentorship session has already been registered.', previous: $e);
            }
        });
    }

    /**
     * Synchronize the debit state for a mentorship session based on attendance.
     *
     * When $shouldDebit is true, this method ensures that a debit exists for the
     * given session, creating or updating it as needed. When $shouldDebit is
     * false, any existing debit for the session is removed.
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function syncAttendanceDebit(
        MentorshipSession $session,
        bool $shouldDebit
    ): void {
        $mentorship = $session->mentorship;

        if ($mentorship === null) {
            throw new ModelNotFoundException('Mentorship not found for session.');
        }

        if ($shouldDebit === false) {
            DB::transaction(function () use ($mentorship, $session): void {
                MentorshipDebit::where('mentorship_id', $mentorship->getKey())
                    ->where('mentorship_session_id', $session->getKey())
                    ->lockForUpdate()
                    ->delete();
            });

            return;
        }

        $this->validateMentorshipConfiguration($mentorship);

        $durationMinutes = (int) $session->duration_minutes;

        if ($durationMinutes <= 0) {
            throw new DomainException('Session duration must be a positive value for mentorship debit.');
        }

        if ($durationMinutes % 60 !== 0) {
            throw new DomainException('Session duration must represent whole hours for mentorship debit.');
        }

        $hours = (int) ($durationMinutes / 60);

        DB::transaction(function () use ($mentorship, $session, $hours): void {
            $existing = MentorshipDebit::where('mentorship_id', $mentorship->getKey())
                ->where('mentorship_session_id', $session->getKey())
                ->lockForUpdate()
                ->first();

            if ($existing === null) {
                $debit                        = new MentorshipDebit();
                $debit->mentorship_id         = $mentorship->getKey();
                $debit->mentorship_session_id = $session->getKey();
                $debit->hours                 = $hours;
                $debit->debited_at            = now();
                $debit->save();

                return;
            }

            if ((int) $existing->hours !== $hours) {
                $existing->hours      = $hours;
                $existing->debited_at = now();
                $existing->save();
            }
        });
    }

    /**
     * Handle session cancellation by removing any existing debit linked to it.
     *
     * This method is meant to be called when a MentorshipSession is set to
     * ClassLessonStatus::Cancelled, either on creation or update.
     *
     * It leverages syncAttendanceDebit() with $shouldDebit = false so that
     * any debit for this session is deleted in a consistent, transactional way.
     *
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function handleSessionCancelled(MentorshipSession $session): void
    {
        if ($session->status !== ClassLessonStatus::Cancelled) {
            return;
        }

        $this->syncAttendanceDebit($session, false);
    }

    /**
     * Check if attendance for a mentorship session has already been debited.
     */
    public function isAttendanceDebited(
        Mentorship $mentorship,
        MentorshipSession $session
    ): bool {
        $existing = MentorshipDebit::where('mentorship_id', $mentorship->getKey())
            ->where('mentorship_session_id', $session->getKey())
            ->lockForUpdate()
            ->first();

        return $existing !== null;
    }

    /**
     * Retrieve the mentorship hour balance for a given mentorship.
     *
     * The result contains total credited hours, total debited hours and the final balance.
     *
     * @param Mentorship|int $mentorship
     *
     * @return array{
     *     credits_hours: int,
     *     debits_hours: int,
     *     balance_hours: int
     * }
     *
     * @throws ModelNotFoundException
     */
    public function getBalanceForMentorship(Mentorship|int $mentorship): array
    {
        $mentorshipModel = $this->resolveMentorship($mentorship);

        $credits = (int) MentorshipPayment::where('mentorship_id', $mentorshipModel->getKey())
            ->sum('hours');

        $debits = (int) MentorshipDebit::where('mentorship_id', $mentorshipModel->getKey())
            ->sum('hours');

        return [
            'credits_hours' => $credits,
            'debits_hours'  => $debits,
            'balance_hours' => $credits - $debits,
        ];
    }

    /**
     * Resolve a mentorship instance from a model or primary key.
     *
     * @param Mentorship|int $mentorship
     *
     * @throws ModelNotFoundException
     */
    private function resolveMentorship(Mentorship|int $mentorship): Mentorship
    {
        if ($mentorship instanceof Mentorship) {
            return $mentorship;
        }

        $resolved = Mentorship::find($mentorship);

        if ($resolved === null) {
            throw new ModelNotFoundException('Mentorship not found.');
        }

        return $resolved;
    }

    /**
     * Validate mentorship configuration for billing operations.
     *
     * @throws DomainException
     */
    private function validateMentorshipConfiguration(Mentorship $mentorship): void
    {
        $rateCents = $this->toCents((string) $mentorship->hourly_rate);

        if ($rateCents <= 0) {
            throw new DomainException('Mentorship must define a positive hourly rate for billing operations.');
        }
    }

    /**
     * Convert a monetary amount into integer cents.
     *
     * Accepts numbers or localized strings using comma or dot as decimal separator.
     *
     * @param float|int|string $amount
     *
     * @throws DomainException
     */
    private function toCents(float|int|string $amount): int
    {
        if (is_int($amount)) {
            return $amount * 100;
        }

        if (is_float($amount)) {
            return (int) round($amount * 100);
        }

        $raw = trim((string) $amount);

        $normalized = str_replace([' ', ','], ['', '.'], $raw);

        if (! preg_match('/^-?\d+(\.\d{1,})?$/', $normalized)) {
            throw new DomainException('Invalid monetary amount representation.');
        }

        $parts    = explode('.', $normalized, 2);
        $units    = $parts[0] ?? '0';
        $decimals = $parts[1] ?? '0';

        $decimals = substr(str_pad($decimals, 2, '0'), 0, 2);

        $sign     = ($units[0] ?? '') === '-' ? -1 : 1;
        $unitsAbs = ltrim($units, '-');

        return $sign * ((int) $unitsAbs * 100 + (int) $decimals);
    }

    /**
     * Format integer cents as a decimal string with two fraction digits.
     */
    private function formatAmountFromCents(int $cents): string
    {
        $sign   = $cents < 0 ? '-' : '';
        $value  = abs($cents);
        $units  = intdiv($value, 100);
        $centsR = $value % 100;

        return sprintf('%s%d.%02d', $sign, $units, $centsR);
    }
}
