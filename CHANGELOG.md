# JN Nazareth CMS — Changelog

Running log of session-level changes. Read this first if you're picking up the project.

Newest entries on top.

---

## Session: 2026-06-04 (part 2) — Fix completion gates + backfill migration + delete marks + exam safeguard

### What changed (incremental)
- **New** `deleteSubject()` in `Admin/MarksController.php` — deletes all marks for a subject group (exam, class, section, subject).
- **New** `destroy()` in `Admin/MarksController.php` — deletes an individual mark.
- **New route** `DELETE /admin/marks/{mark}` (`marks.destroy`) and `POST /admin/marks/delete-subject` (`marks.delete-subject`).
- **View** — added Delete button in pending reviews cards, "Delete All" in "Approve All" bar, and per-row Delete button in the marks table. All use `customConfirm()`.
- **Exam delete safeguard** — `ExamController@destroy()` now blocks deletion if marks are linked, with error showing exact count.
- Removed misleading confirm text from exams index view.
- (Everything from part 1 also applies.)

---

## Session: 2026-06-04 (part 2) — Fix completion gates + backfill migration

### What changed
- All completion gates in controllers/views changed from `whereNotNull('submitted_at')` to `whereNotNull('approved_at')`:
  - `Admin/MarksController.php`: `examSummary()`, `index()` school-wide check, summary tab query
  - `resources/views/admin/marks/index.blade.php`: school-wide export check, summary per-subject check, submission status card
  - `Admin/ResultCardController.php`: `requireAllSubjectsSubmitted()`, `download()`, `fullYear()` — all 5 occurrences
- Added migration `2026_06_04_121910_backfill_approved_at_for_existing_submitted_marks.php` — sets `approved_at = submitted_at` for any mark that has `submitted_at` but null `approved_at`
- Ran migration on local, verified OK

### Files most changed
- `app/Http/Controllers/Admin/MarksController.php`
- `resources/views/admin/marks/index.blade.php`
- `app/Http/Controllers/Admin/ResultCardController.php`
- `database/migrations/2026_06_04_121910_backfill_approved_at_for_existing_submitted_marks.php`

### Known limitations
- Live server still needs the new migration run via cPanel.

---

## Session: 2026-06-04 — Marks approval workflow (admin approve/send-back), pending reviews dashboard

### What changed

#### 🆕 New features
- **Marks approval workflow** — teachers submit marks (`submitted_at`), admin then reviews and explicitly approves (`approved_at`/`approved_by`). Three-state lifecycle: draft → submitted/pending → approved.
- **Pending Approvals dashboard** — when admin opens the Marks page, all submitted-but-not-approved subjects auto-populate as review cards (no filters needed). Shows exam name, class, section, subject, student count, and submission time.
- **Per-subject "Approve All" button** — in the detailed review view, an "Approve All" button approves every pending student in the subject at once.
- **Individual approve/send-back** — each student row in the review table has Approve and Edit (send back) buttons.
- **Bulk send-back on pending cards** — pending review cards have "Send Back" to reset the entire subject for teacher re-editing.
- **Approved stat card** — review stats now include an "Approved" count.
- **Approved column** — student review table shows approval status (Pending/Approved with date).

#### 🧹 UX improvements
- **Empty state replaced** — "Pick exam + class + subject above" is now replaced with the pending approvals list and a helpful filter hint.
- **Visual cues** — approved rows get a faint purple background; pending subjects show amber cards; approval badge in purple.

#### Schema changes
- Added `approved_at` (timestamp, nullable) and `approved_by` (FK → users, nullable) to `marks` table.

#### Files changed
- `database/migrations/2026_06_04_000001_add_approved_at_to_marks_table.php` — new migration
- `app/Models/Mark.php` — fillable, casts, `approvedBy()` relationship
- `routes/web.php` — 3 new admin routes: `marks.approve-subject`, `marks.send-back-subject`, `marks.approve`, `marks.send-back`
- `app/Http/Controllers/Admin/MarksController.php` — `index()` now builds `$pendingReviews`; new methods: `approve()`, `approveSubject()`, `sendBack()`, `sendBackSubject()`; `resetSubmission()` also clears approval
- `resources/views/admin/marks/index.blade.php` — pending approvals cards, approve/send-back buttons in table, "Approve All" bar, approved stat

#### Known limitations
- Results/rankings/exports still gate on submission (`submitted_at`) not approval. Admins should approve before declaring results official.

---

## Session: 2026-06-03 — Export rewrites, student profile Marks tab rebuild, misc fixes

### What changed

#### 🆕 New features
- **Student profile Marks tab rebuilt** — now shows an exam dropdown to select a specific exam. When an exam is selected, displays:
  - Full marks table (Subject, Full, Pass, Theory, Assignment, Total, %, Grade, Status)
  - Summary box (Average %, CGPA, Division, Rank)
  - Attendance for that academic year (with daily dots)
  - Falls back to grouped "All Exams" view when no exam is selected.
  - Rank is computed against all students in the same exam + class.
- **`exportCsvResults()` rewrite** — completion gate (all subjects submitted) before generating, pass/fail split, continuous ranking across both sections, proper CSV template (school name, exam, class, column headers).
- **`exportAllResultCards()` rewrite** — changed from per-student ZIP (risk of server timeout/memory exhaustion for large classes) to single class result PDF with pass/fail data building and landscape orientation.
- **`class-result-pdf.blade.php`** — new landscape PDF view matching the Results tab exactly: school header, pass table (with continuous ranks), fail table (ranks continue from pass), signature lines at bottom.

#### 🐛 Bugs fixed
- **Grade empty in student profile Marks tab** — was showing `$m->grade` only (manual entry). Added `$m->computedGrade()` as fallback so auto-computed grades appear.
- **Missing `$year` in Summary tab** — 500 error when no active academic year. Added `@if(!$examId || !$year)` guard.
- **Gradesheet CSV duplicate confusion** — removed from Results tab (identical data to Results CSV, confusing teachers).
- **`exportCsv()` 500 on orphaned marks** — a Mark with no related `StudentEnrollment` crashed the sort closure. Added `->filter(fn ($r) => $r->enrollment !== null)` and null-safe operators throughout.

#### 🧹 UX improvements
- **PDF disclaimer moved to hover tooltip** — student-count text changed from visible `<span>` to `title` attribute on the Class Result PDF button, keeping the button layout compact.

### Schema changes
- None (additive only).

### Files changed
- `app/Http/Controllers/Admin/MarksController.php` — rewrote `exportCsvResults()` and `exportAllResultCards()`, null enrollment filter
- `resources/views/admin/marks/index.blade.php` — removed gradesheet CSV button, added `$year` guard, disclaimer tooltip
- `resources/views/admin/marks/class-result-pdf.blade.php` — new landscape PDF template
- `resources/views/admin/students/show.blade.php` — rebuilt Marks tab with exam dropdown, full table, summary, attendance

### Notes
- Rank computation on student profile queries all marks for the same exam+class and finds this student's position. Optimized for a single student view (not class-wide).
- The "All Exams" view remains unchanged for quick scanning across academic years.

---

## Session: 2026-06-02 — Profile redirect guard fix, PWA dynamic icons from school logo

### What changed

#### 🐛 Bugs fixed
- **Profile update redirects teacher to admin dashboard** — `ProfileController@update` always redirected to `route('profile.edit')` (admin profile). Now checks if teacher guard is active and redirects to `teacher.profile.edit` instead.
- **Profile delete uses wrong guard** — `ProfileController@destroy` now uses `auth('teacher')->logout()` when teacher guard is active.

#### 🔧 Improved
- **PWA icons now use the actual school logo** — The old `icon-192.svg` / `icon-512.svg` showed "JN" text on a green background. Now the manifest is dynamic (served via a Laravel route) and reads the `school_logo` setting from Site Customizer. The `/pwa-icon-{size}.png` route center-crops and resizes whatever logo is uploaded.
- **PNG icons added** — 192x192 and 512x512 PNG files generated from `images/logo.png`, with maskable variants for Android adaptive icons.
- **`apple-touch-icon` uses PNG** instead of SVG for better iOS compatibility.
- **Favicon fallback** in teacher layout uses `icon-192.png` instead of `icon-192.svg`.

#### 🔧 New files
- `app/Http/Controllers/PwaController.php` — serves dynamic manifest and resized icons

### Files changed
- `app/Http/Controllers/ProfileController.php` — guard-aware redirect on update, guard-aware logout on destroy
- `public/manifest.json` — **removed** (replaced by dynamic route)
- `routes/web.php` — added `/manifest.json` and `/pwa-icon-{size}.png` routes
- `public/icon-192.png`, `public/icon-512.png`, `public/icon-192-maskable.png`, `public/icon-512-maskable.png` — added
- `resources/views/layouts/teacher.blade.php` — apple-touch-icon uses PNG, favicon fallback uses PNG

### Notes
- The site logo from Site Customizer → General → School Logo is now used for the PWA install icon.
- If no custom logo is uploaded, it falls back to `images/logo.png`.
- The icon is center-cropped to a square and resized to 192/512px on request.
- Users may need to re-add the PWA to their home screen for the new icon to appear.

---

## Session: 2026-06-02 — Remember Me checkbox tick fix, middleware ordering, auth exception redirect

### What changed

#### 🐛 Bugs fixed
- **Remember Me checkbox tick invisible** — `resources/views/layouts/guest.blade.php` used `background: rgba(...)` (CSS shorthand) which reset `background-image: none` and had higher specificity than Tailwind's `:where()` selector, preventing the white SVG checkmark from rendering. Fixed by removing the `background` override and letting Tailwind handle it.
- **500 error on hard refresh (Cmd+Shift+R) for teacher routes** — `SetWorkingYear` middleware ran *before* `StartSession` (both were prepended to web group), causing `session()` to access an empty store. Moved `SetWorkingYear` and `NoHttpCache` to `append` (after default web middleware) so the session is started by the time they run.

#### 🔧 Improved
- **Teacher auth exception now redirects to teacher login** — Previously, hitting a `auth:teacher`-protected page while unauthenticated returned `abort(404)`. Now redirects to `route('teacher.login')` so users see the login form instead of a 404/500 error.
- **Middleware order refactored in `bootstrap/app.php`**: `TeacherSession` stays prepended (needs to run before `StartSession`), `NoHttpCache` and `SetWorkingYear` moved to appended (need the session available).

### Files changed
- `resources/views/layouts/guest.blade.php` — removed `background` shorthand from checkbox CSS
- `bootstrap/app.php` — fixed middleware order, changed auth exception redirect to teacher login

### Notes
- The "auto-login to teacher dashboard when admin is logged in" is likely caused by a lingering teacher remember-me cookie from a previous teacher login session. To force-logout, visit `/teacher/logout` or clear browser cookies.

### What changed

#### 🐛 Bugs fixed
- **Admin & teacher cannot be logged in simultaneously** — both used the same `web` auth guard, causing colliding remember-me cookies (`remember_web_<hash>`). Teacher's remember token would overwrite admin's, and vice versa.

#### 🏗️ Architecture
- **Added `teacher` auth guard** in `config/auth.php` — uses the same `users` provider but stores auth state under a different session key (`login_teacher_<hash>` vs `login_web_<hash>`). This gives each portal its own remember-me cookie name, so they never collide.
- **Teacher routes now use `auth:teacher`** middleware instead of the shared `auth` guard.
- **Teacher login routes** are now in their own `guest:teacher` middleware group (separate from the outer `guest` group), so an admin already logged in via the `web` guard can still access the teacher login page in a different tab.
- **`/dashboard` route** uses `auth:web,teacher` middleware (accepts both guards) and redirects to `teacher.dashboard` or `admin.dashboard` accordingly. This prevents a redirect loop when `guest:teacher` sends an already-authenticated teacher to `/dashboard`.
- **`RoleMiddleware`** detects the correct guard from the route prefix (`teacher*` → teacher guard).
- **`AuthenticatedSessionController`** uses `Auth::guard('teacher')` for teacher login/logout.
- **`LoginRequest`** accepts an optional `$guard` parameter.
- **No `/login` route** is registered — only the configured slugs (`login_path('admin')` / `login_path('teacher')`) and the emergency path (`cms-recovery-7k3`) serve login pages. Everything else returns 404.  
- **Logout routes**: admin at `/logout` (web guard), teacher at `/teacher/logout` (teacher guard) — fully independent.

### Files changed
- `config/auth.php` — added `teacher` guard
- `routes/web.php` — teacher routes use `auth:teacher`
- `routes/auth.php` — teacher login routes use `guest:teacher`
- `app/Http/Middleware/RoleMiddleware.php` — detect guard from route prefix
- `app/Http/Requests/Auth/LoginRequest.php` — authenticate() accepts guard param
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` — use teacher guard

---

## Session: 2026-06-01 — Per-subject review fixes, Summary tab per-subject ticks

### What changed

#### 🐛 Bugs fixed
- **Stats showing 0 Pass / 0 Fail / 0 Submitted** — `$stats` variable was overwritten by the submission-status loop (`$stats = $subjectsWithMarks->get($s)`), turning it into a DB row object. Renamed to `$subjStats`.
- **Grade column showing "—"** — display used `$r->grade` (null when teacher submits without a grade). Added `$r->computedGrade()` as fallback.
- **Override form overlapping** — Save/Reset buttons and grade input cramped. Added `flex-wrap:nowrap`, `flex-shrink:0`, wider spacing.

#### 🎨 Improved
- **Summary tab: per-subject status** — each class card now lists every subject individually with ✅ green tick (submitted) or ⏳ (pending), sorted alphabetically, replacing the old count-only display.
- **Grade input pre-fills computed grade** — admin override grade input defaults to `computedGrade()` so admin sees the suggested grade immediately.

### Files changed
- `app/Http/Controllers/Admin/MarksController.php` — renamed `$stats` to `$subjStats` in submission-status loop
- `resources/views/admin/marks/index.blade.php` — grade fallback, override form layout, Summary tab per-subject ticks

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

## Session: 2026-06-01 — Fixed Results tab 500 & marks filter UX

### What changed

#### 🐛 Bugs fixed
- **Results tab 500 (array_count_values)** — `array_count_values($grades)` at `MarksController.php:213` crashed when a `grade` was null (no grade scale or ungraded student). Wrapped with `array_filter($grades)` to skip nulls.
- **Results tab 500 (school-wide export block)** — `$_cls` undefined in compiled view due to en dash `–` in string interpolation and missing null guard for `$year`. Fixed: replaced en dash with regular hyphen, concatenated explicitly, added `$year` check to outer `@if`.
- **Missing AuthenticatedSessionController import** — `routes/web.php:468` used `AuthenticatedSessionController::class` without `use` statement. Added the import.

#### 🧹 UX improvements
- **Class dropdown no longer auto-submits** — removed `this.form.submit()` from `onchange` on the class `<select>`. The user now clicks "Filter" manually after picking class + subject, avoiding a confusing page reload before they can select a subject.

#### 🔧 Files changed
- `app/Http/Controllers/Admin/MarksController.php` — `array_filter` guard on grade distribution.
- `resources/views/admin/marks/index.blade.php` — class filter no longer auto-submits; school-wide block uses safe string concat + null guard.
- `routes/web.php` — added missing `use` for `AuthenticatedSessionController`.

#### 🚀 Deployment
After `git push` + pull on server:
```bash
php8.4 artisan view:clear && php8.4 artisan config:clear && php8.4 artisan view:cache && php8.4 artisan config:cache
```

---

## Session: 2026-06-01 (session 2) — CBSE grade scale, continuous ranking, raw marks, pass/fail bug, division rules

### What changed

#### 🆕 New features
- **CBSE 10-point grade scale** — migration replaces A+/A/B+/B/C+/C/D/F (4.00 scale) with A1/A2/B1/B2/C1/C2/D/E (10.00 scale). `GradeScale::defaultScale()` updated accordingly.
- **Continuous ranking across ALL students** — pass students get ranks 1–N (by avg% descending); fail students continue at N+1 onward. No separate ranking.
- **Raw marks per subject + Total column** — subject cells show only the raw mark (e.g. "85" instead of "85/100 A1"). New "Total" column shows sum of raw marks per student.
- **Division Rules feature** — `division_rules` table, `DivisionRule` model + controller (CRUD + toggle), routes under `/admin/division-rules/*`. Tabbed view under Academics → Grade Scale: "Grade Scale (CGPA)" + "Division Rules". Division column shown on all three Result section tables (pass, fail).

#### 🐛 Bug fixed
- **Pass/fail split: failedSubjects not propagated** — `$failedSubjects` was set on a local `$r` variable inside the loop, but the actual `$rows` collection was never updated. All students appeared as pass regardless of actual marks. Now builds `$failRows` with `failedSubjects` directly and ranks from there.

#### 🔧 Files changed
- `database/migrations/2026_06_01_230000_switch_to_cbse_grade_scale.php` — CBSE data migration
- `database/migrations/2026_06_01_231000_create_division_rules_table.php` — new table
- `app/Models/DivisionRule.php` — `divisionFor()`, scopes
- `app/Models/GradeScale.php` — defaultScale returns CBSE scale
- `app/Http/Controllers/Admin/MarksController.php` — pass/fail key propagation fix, division computation
- `app/Http/Controllers/Admin/GradeScaleController.php` — tab param, passes divisions
- `app/Http/Controllers/Admin/DivisionRuleController.php` — CRUD + toggle
- `resources/views/admin/marks/index.blade.php` — raw marks, Total, Division columns, continuous ranks
- `resources/views/admin/grade-scales/index.blade.php` — tabbed view
- `routes/web.php` — division rule routes

#### 🚀 Deployment
```bash
git pull origin main && php artisan migrate && php artisan view:clear && php artisan config:clear && php artisan view:cache && php artisan config:cache
```

---

## Session: 2026-06-01 (session 3) — Mark fix, Rankings removed, admin override fix, per-subject breakdown

### What changed

#### 🐛 Critical fix
- **Admin override no longer resets `submitted_at`** — Previously, when an admin edited a mark in Per-Subject Review, `submitted_at` was set to `null`. This made the subject appear "incomplete" and hid the entire class from Rankings/Results until a teacher re-submitted. Now admin edits preserve `submitted_at`.

#### 🔧 Fixes
- **Subject stats now include ALL students** — The per-subject avg/high/low stats (shown in Results tab) previously only included pass students. Now they include both pass and fail students for accurate class-wide numbers.

#### 🗑️ Removed
- **Rankings tab removed** — It was identical to the Results tab (same tables, same columns, same logic). The Results tab now shows the subject stats cards that were previously only on the Rankings tab. The Rankings tab link and its entire `@elseif($view === 'rankings')` block have been deleted.

#### 🔧 Files changed
- `app/Http/Controllers/Admin/MarksController.php` — removed `submitted_at => null` from admin update; merged pass+fail students into subject stats
- `resources/views/admin/marks/index.blade.php` — removed Rankings tab, added subject stats to Results tab

---

## Earlier history
Pre-2026-05-29 work: see `TODO.md` and `FEATURES.md` for the full feature list and the (already-implemented) earlier roadmap items.
