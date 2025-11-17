<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mentorship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mentorship\StoreMentorshipPaymentRequest;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipPayment;
use App\Services\Mentorship\MentorshipBillingService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * HTTP controller for managing payments associated with a mentorship.
 */
class MentorshipPaymentController extends Controller
{
    public function __construct(
        private readonly MentorshipBillingService $mentorshipBillingService,
    ) {
    }

    /**
     * Display a listing of mentorship payments for the given mentorship.
     */
    public function index(Mentorship $mentorship): View
    {
        $mentorship->load(['student', 'teacher', 'subject']);

        $payments = MentorshipPayment::query()
            ->where('mentorship_id', $mentorship->id)
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->get();

        return view('mentorships.payments.index', [
            'mentorship' => $mentorship,
            'student'    => $mentorship->student,
            'teacher'    => $mentorship->teacher,
            'subject'    => $mentorship->subject,
            'payments'   => $payments,
        ]);
    }

    /**
     * Show a single mentorship payment for the given mentorship.
     */
    public function show(
        Mentorship $mentorship,
        MentorshipPayment $payment
    ): View {
        if ((int) $payment->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        $mentorship->load(['student', 'teacher', 'subject']);

        return view('mentorships.payments.show', [
            'mentorship' => $mentorship,
            'student'    => $mentorship->student,
            'teacher'    => $mentorship->teacher,
            'subject'    => $mentorship->subject,
            'payment'    => $payment,
        ]);
    }

    /**
     * Show the form for creating a new mentorship payment for the given mentorship.
     */
    public function create(Mentorship $mentorship): View
    {
        $mentorship->load(['student', 'teacher', 'subject']);

        return view('mentorships.payments.create', [
            'mentorship' => $mentorship,
            'student'    => $mentorship->student,
            'teacher'    => $mentorship->teacher,
            'subject'    => $mentorship->subject,
        ]);
    }

    /**
     * Store a newly created mentorship payment for the given mentorship.
     */
    public function store(
        StoreMentorshipPaymentRequest $request,
        Mentorship $mentorship
    ): RedirectResponse {
        $data   = $request->validated();
        $amount = (float) $data['amount'];

        try {
            $this->mentorshipBillingService->registerPayment($mentorship, $amount);
        } catch (DomainException|ModelNotFoundException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'payment' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('mentorships.payments.index', $mentorship)
            ->with('status', 'Mentorship payment successfully registered.');
    }

    /**
     * Remove the specified mentorship payment from storage.
     */
    public function destroy(
        Mentorship $mentorship,
        MentorshipPayment $payment
    ): RedirectResponse {
        if ((int) $payment->mentorship_id !== (int) $mentorship->id) {
            abort(404);
        }

        $payment->delete();

        return redirect()
            ->route('mentorships.payments.index', $mentorship)
            ->with('status', 'Mentorship payment successfully removed.');
    }
}
