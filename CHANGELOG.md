# JN Nazareth CMS — Changelog

Running log of session-level changes. Read this first if you're picking up the project.

Newest entries on top.

---

## Session: 2026-05-29 — Per-exam marks config, paper-level approval, critical bug fixes

### What changed

#### 🆕 New features
- **Per-exam marks config** — `exam_subject_marks` table, admin sets full/pass marks per (exam, class, subject). Teachers see locked badges, never edit. Bulk fill (whole exam + per-class). Located at: `Exams & Terms → Marks Config` button per exam.
- **Questions: paper-level approval** — "Approve Paper" button approves all pending questions in one (exam, class, subject) group at once. "Download PDF" appears once approved. "View ▼" expands for per-question fine control.
- **Questions: Review + Summary tabs** — Review tab is the work queue; Summary tab is a clickable per-class progress grid.
- **Marks Summary tab — school-wide grid** — always shows every class+section card with X/Y submitted + pending subject list. Class filter hidden on this tab.
- **Per-Subject Review auto-submit** — picking a class auto-reloads page so subject dropdown immediately filters to that class's subjects.
- **"Export ALL Class Results"** — school-wide aggregate button on Results tab. Disabled with pending-list tooltip when any (class × subject) is incomplete; server-side gate too.
- **Admin guide PDF** — `JN_Nazareth_Admin_Guide.pdf` generated for staff training (in project root, outside `cms/`).

#### 🐛 Bugs fixed (critical)
- **Pass/Fail unit mismatch** — was comparing percentage (0-100) against raw pass marks (e.g., 8). Wrong whenever full_marks ≠ 100. Now computes pass% = (pass_marks/full_marks)×100 per mark row and compares like-for-like. Fixed in both `MarksController` rankings and `ResultCardController::failedSubjectsFor`.
- **Zero-marks subject counted as "submitted"** — `whereNull('submitted_at')->exists()` returned false when no marks existed at all, so the subject was considered complete and rankings rendered with empty data. Now: subject is complete only if `submitted_count >= enrolled_count && enrolled_count > 0`.
- **ResultCardController bypassed submission gate** — PDFs generated for partially-submitted classes. Added `requireAllSubjectsSubmitted()` check using `class_subjects` as the expected list.
- **Pass/Fail used "missing data" instead of "below pass marks"** — students with marks in all subjects but 20% in Math were wrongly in Pass list. Now checks `obtained_marks < pass_marks` per non-optional subject from `class_subjects`.
- **Rank pool included failing students** — even after correct pass/fail labels, the rank computation still included everyone. Now filters the pool to passing students only.
- **Edit Teacher Subject Assignments showed all global subjects per class** — should filter by `class_subjects` for that class. Fixed.
- **Class Subjects re-save wiped is_optional / grade_only flags** — when admin re-saved a class's subjects, the controller hardcoded both flags to false, losing prior settings. Now reads existing flags before delete+recreate.
- **Teacher session leaked into admin session** — same browser, picking up wrong user on refresh. Middleware now scopes the cookie to teacher routes only; logout/login redirects respect the originating portal.
- **Deleted teacher could still log in** — `TeacherController::destroy` only deleted the Teacher row, not the linked User. Now also deletes the user account if teacher-only, or unlinks if user has other roles.
- **Inactive exams shown in dropdowns** — added `where('is_active', true)` to exam queries.
- **Per-Subject Review subject dropdown showed all subjects until Filter clicked** — class dropdown now auto-submits the form so the subject list refilters immediately.
- **Questions page 500 — inline @if parse error** — the inline `@if...@endif` in the heading was being mis-paired with the outer `@else`, producing "unexpected else" compile errors. Replaced with a pre-computed @php variable.
- **Marks Review 500 — null enrollment** — orphaned Mark rows crashed the sort closure. Added filter to drop null-enrollment rows + nullsafe accessors throughout the view.
- **Bulk download "Export ALL Class Results" allowed partial school-wide export** — added a server-side gate that lists pending (class, subject) combos and refuses the download.

#### 🧹 Cleanup
- Removed FM/PM/Opt/Grade inline inputs from Class Subjects page — marks config is per-exam now.
- Removed duplicate per-class export buttons on Marks → Results tab. Kept "Class Result ZIP" + "Gradesheet CSV".
- Removed the old Questions "Submission Summary" card list that showed alongside the new progress block.
- Removed the now-unused QA test data artisan command (decided to test manually).

### Schema changes (only additive)
- **New table:** `exam_subject_marks(exam_id, class, subject, full_marks, pass_marks, timestamps)` — unique per (exam, class, subject).
- No data migrations. No destructive changes.

### Files most changed
- `app/Http/Controllers/Admin/MarksController.php` — gating, pass/fail, school-wide pending check, exam summary
- `app/Http/Controllers/Admin/ResultCardController.php` — PDF gate, rank pool, pass/fail helper
- `app/Http/Controllers/Admin/QuestionsController.php` — view param, defensive class_progress
- `app/Http/Controllers/Admin/ClassSubjectController.php` — preserved flags on resave
- `app/Http/Controllers/Admin/ExamController.php` — marksConfig + saveMarksConfig
- `app/Http/Controllers/Teacher/MarksController.php` — exam-scoped full/pass, window enforcement
- `app/Models/ExamSubjectMark.php` — new model + resolveMarks helper
- `resources/views/admin/marks/index.blade.php` — tabs, summary grid, school-wide export
- `resources/views/admin/questions/index.blade.php` — Review/Summary tabs, paper-level approve/download
- `resources/views/admin/teachers/edit.blade.php` — class_subjects-filtered checkboxes
- `resources/views/admin/exams/marks-config.blade.php` — new marks config screen
- `resources/views/teacher/marks/sheet.blade.php` — locked badges, missing-config warning
- `database/migrations/2026_05_29_120110_create_class_subjects_table.php` — class_subjects schema
- `database/migrations/2026_05_29_210000_create_exam_subject_marks_table.php` — exam_subject_marks schema

### How current flow works (post-session)
1. Admin sets up: Academic Year → Class Subjects (tick subjects per class) → Teachers + Subject Assignments + Class Teacher
2. Admin creates Exam → clicks **Marks Config** → sets full/pass per (class, subject) [bulk fill available]
3. During submission window: teachers submit Questions, admin reviews and approves (paper-level), downloads PDF
4. After exam: teachers enter Marks (full/pass locked from admin's config) → Submit Final per subject
5. Admin tracks progress on **Marks → Summary** (school-wide grid)
6. When a class is fully submitted: **Rankings** + **Results** tabs unlock for that class. Pass students get ranks, fail students listed separately
7. When school-wide complete: **"Export ALL Class Results"** button enables, ZIP of every class's result PDFs

### Known limitations / backlog
- Create Teacher form subject row still shows global subjects (Edit Teacher works correctly)
- No "Copy Marks Config from another exam" convenience button
- "Subject done" check is "≥1 approved question" — not a configurable required-question-count
- No formal re-exam / supplementary workflow
- No email/notification reminders for pending submissions
- No dedicated audit-log page (activity logged in DB but no UI yet)
- Class Subjects `is_optional` flag has no UI; rows are created with `false` (set in DB directly if needed)

---

## Earlier history
Pre-2026-05-29 work: see `TODO.md` and `FEATURES.md` for the full feature list and the (already-implemented) earlier roadmap items.
