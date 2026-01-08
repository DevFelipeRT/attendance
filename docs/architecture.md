# Architecture

## High-Level Structure

This project follows a standard Laravel MVC organization:

- **Routes** define browser-only endpoints.
- **Controllers** coordinate requests and responses.
- **Services** encapsulate domain workflows.
- **Models** represent persisted entities via Eloquent ORM.

## Modules / Bounded Contexts

### Class Group Management

Responsible for class-group lifecycle and attendance.

- **Entities:** ClassGroup, ClassLesson, ClassEnrollment, Attendance.
- **Primary workflows:** create class groups, enroll students, generate lessons, capture attendance.
- **Operational modes:** a class group with a single enrollment is treated as a mentorship-style group for lesson duration rules, while groups with multiple enrollments are treated as regular cohorts.

### Mentorship Management

Responsible for 1:1 mentorship delivery and credit-based billing.

- **Entities:** Mentorship, MentorshipSession, MentorshipAttendance, MentorshipPayment, MentorshipDebit.
- **Primary workflows:** create mentorships, schedule sessions, record attendance, log payments and debits.

### Master Data

- **Students, Teachers/Mentors, Subjects** as shared reference data used by class groups and mentorships.

## Infrastructure Notes

- **Persistence:** Eloquent ORM with migrations.
- **Sessions / Cache / Queue:** configured to use database-backed drivers by default.
- **Frontend assets:** built with Vite and Tailwind CSS.
