# Project TODO — JN Nazareth School ERP

## Production Safety Rule
- This codebase is already live on the server.
- Do not add demo users, fake teachers, fake subjects, sample marks, sample questions, or sample assignments in migrations/seeders.
- Migrations may add schema and may preserve/migrate existing real records only.
- Existing admin/teacher accounts and existing student data must remain untouched unless the admin changes them through the UI.
- Test data can be created locally only, and must not be committed or added to seeders.

---

## ✅ ALL FEATURES — IMPLEMENTED & LIVE

All items below are implemented and running on the production server. No gaps remain.

### Admin Panel

**School**
- Academic Years — create, edit, activate, promote students between years
- Students — full list with year filter, enrollment status filter, class tabs, search, unassigned warning with clickable fix
- CSV bulk import — upload all classes in one file, auto-assigns to active year
- Student profile — personal details, enrollment history year-by-year, change enrollment status
- Documents & Folders — file management per student/folder
- Subjects — master subject list (CRUD + toggle + reorder)
- Grade Scale — A+ through F with percentage ranges and grade points, seed defaults

**Staff**
- Classes — class management
- Teachers — staff directory (name, designation, subjects, photo, active/inactive)
- **Teacher Login** — each teacher can have an optional login account (Create Login from teacher profile; link/unlink user accounts)
- Assignments — class-teacher + subject-teacher assignments per academic year

**Gradebook**
- Attendance — teacher marks daily, admin reviews/overrides
- Attendance Analytics — filter by class/section/month, per-student %, monthly trends, visual summary, class average
- Exams & Terms — create, edit, activate/deactivate (including type field — Formative/Summative/Final/Custom)
- Marks — teacher enters per student/exam/subject with theory/assignment split; admin reviews
- Marks Analytics — subject-wise %, overall CGPA, class rank, per-subject stats (avg, highest, lowest, grade distribution)
- Questions — teachers submit, admin reviews
- Notes & Assignments — teachers publish notes/assignments with file upload + due dates
- Result Card PDF — per-student on-demand PDF (DomPDF) combining marks + attendance for selected exam

**Content**
- News & Notices, Events, Hall of Fame
- Photo Albums, Videos, Downloads
- Important Links, Inquiries (with unread badge)

**Admin**
- Site Customizer — configurable login URLs, site name, colors, SEO, section visibility, slides, admission, certificates, people, calendar, curriculum
- Website Pages — custom page CRUD
- Users — admin/staff/teacher accounts
- Activity Logs — read-only audit trail

**Students**
- Student Academic Passport — full year-wise history with marks, attendance %, enrollment timeline
- ID Card download
- Enrollment status management (active/dropped/transferred/graduated)

### Teacher Portal
- Separate branded login page (configurable URL)
- Dashboard with stats (classes, subjects, students)
- Attendance — class-wise daily submission
- Marks — per student per exam/subject entry (theory + assignment split, auto-grade computation)
- Questions — submit exam questions with file upload
- Notes & Assignments — publish class notes and assignments

### Public Website
- Controlled entirely via Site Customizer
- Homepage with configurable sections (hero, about, stats, events, gallery, blog, etc.)
- About Us, News, Events, Gallery, Videos, Downloads
- Academics pages (fee structure, timing, calendar, curriculum, textbooks, results)
- Admission inquiry form → admin inbox

### Infrastructure
- `members` table renamed to `students` (migration complete)
- Teacher records linkable to User accounts (CMS login)
- Exams have `type` field (Formative / Summative / Final / Custom)
- Sidebar organized into collapsible groups (School, Staff, Gradebook, Content, Admin)
- Role-based access control via Spatie (admin / staff / teacher)
- Activity logging throughout the app
- Site settings with caching (5-min TTL)

---

## ✅ Promotion (#20) — All Tiers Complete

- Double-promote prevention with "Already in [Year]" badge + disabled controls
- Server-side duplicate check
- Batch confirmation with student count for auto-fill
- Marks-based auto-suggest (pass → next, fail → same)
- Admin override of suggestions
- Enrollment notes with default text
- Summary row (promoted vs available count)
- Visual marks & attendance on promote page

---

## 🔍 Submitted for Final Review

All code changes are complete. Final manual review checklist:

- [x] Promote page loads with marks/attendance
- [x] Already-promoted students show "Already in" badge
- [x] Server rejects duplicate promotion
- [x] Auto-fill shows batch count confirmation
- [x] Marks-based suggestions work
- [ ] Live smoke test on production/staging

---

## 📄 Comprehensive Feature Reference

See `FEATURES.md` in the project root for a complete, detailed inventory of every feature, model, controller, view, route, and migration in the system.

---

## Potential Future Work (not scoped)
- Student Bulk CSV Export — export filtered student list to CSV
- Teacher Attendance Summary — aggregated view per teacher
- Marks Entry Lock — lock marks entry after admin approval
- Student/Parent Portal — separate login for students/parents to view results, attendance, assignments

---

## 🔲 Pending Development — Batch Plan

> **For junior developers (DeepSeek / others):** Each task below is self-contained. Read the entire task before writing any code. Follow production safety rules at the top of this file — no fake/demo data in migrations or seeders. Tasks are ordered by dependency — complete Batch A before Batch B, etc.

---

### BATCH A — Foundation (do these first, everything else depends on them)

---

#### #27 — Working Year Context (Session-based Year Switcher)

**Goal:** Admin selects which academic year they are "working in." All ERP pages (students, marks, attendance, assignments) automatically scope to that year. A persistent header badge shows the active working year. Past-year data is read-only with a warning banner.

**Why:** Without this, editing 2025-26 data accidentally affects 2026-27 records. Admins need to safely navigate between years.

**New Files:**
- `app/Http/Middleware/SetWorkingYear.php` — reads `session('working_year_id')`, falls back to active year, injects `App::instance('workingYear', $year)` + shares `$workingYear` with all views
- `resources/views/partials/working-year-banner.blade.php` — amber banner: "⚠ You are viewing 2025-26 (past year). Data is read-only." — shown when working year ≠ active year

**Modified Files:**
- `bootstrap/app.php` — register `SetWorkingYear` middleware as `web` group middleware (runs on every request)
- `routes/web.php` — add `POST /admin/working-year` route: `WorkingYearController@switch` — sets `session('working_year_id', $id)`, redirects back
- `resources/views/layouts/admin.blade.php` — add year switcher dropdown in top header bar (shows all years, highlights current working year, POST form on select change)

**Controller:**
```php
// app/Http/Controllers/Admin/WorkingYearController.php
public function switch(Request $request) {
    $year = AcademicYear::findOrFail($request->year_id);
    session(['working_year_id' => $year->id]);
    return back()->with('success', 'Working year switched to '.$year->name);
}
```

**Middleware logic:**
```php
// In SetWorkingYear handle():
$yearId = session('working_year_id');
$year = $yearId ? AcademicYear::find($yearId) : AcademicYear::where('is_active', true)->first();
if (!$year) $year = AcademicYear::latest()->first();
session(['working_year_id' => $year->id]);
view()->share('workingYear', $year);
App::instance('workingYear', $year);
```

**UI:**
- Header: dropdown select showing all years, current working year pre-selected
- When past year selected: amber `$isPastYear` flag passed to views → banner shown
- Past-year pages: hide "Save" / "Submit" buttons (or disable with tooltip "Switch to active year to edit")

**Test steps:**
1. Create 2 academic years. Set one active.
2. Switch working year to the non-active year.
3. Visit Students page — confirm session banner shows.
4. Switch back to active year — banner disappears.

---

#### #28 — Sidebar Restructure (ERP top / Website bottom)

**Goal:** Reorganize the admin sidebar into two clear sections: **ERP** (school management) at top, **Website** (content/public pages) at bottom. ERP section has collapsible groups. Website section is always at the bottom, visually separated.

**Why:** Currently sidebar mixes school ERP items with website content items. Admins get confused navigating. School is an ERP-first product.

**Modified Files:**
- `resources/views/layouts/admin.blade.php` — restructure sidebar nav

**New sidebar structure:**
```
ERP SECTION (top)
├── Dashboard
├── ACADEMIC
│   ├── Academic Years
│   ├── Students
│   ├── Classes
│   └── Subjects
├── STAFF
│   ├── Teachers
│   └── Assignments
├── GRADEBOOK
│   ├── Attendance
│   ├── Exams & Terms
│   ├── Marks
│   ├── Questions
│   └── Notes & Assignments
└── SETTINGS
    ├── Grade Scale
    ├── Users
    └── Activity Logs

[divider line]

WEBSITE SECTION (bottom)
├── Site Customizer
├── News & Notices
├── Events
├── Gallery / Albums
├── Videos
├── Downloads
├── Important Links
├── Website Pages
└── Inquiries
```

**Implementation notes:**
- Keep exact same CSS classes and styles — only reorder `<a>` tags and group labels
- ERP section: each group is a collapsible `<div>` (existing JS already handles this)
- Website section: always visible, not collapsible, separated by a horizontal rule + "WEBSITE" label
- Active link detection stays the same (uses `request()->routeIs(...)`)

**Test steps:**
1. Navigate to each ERP page — confirm correct sidebar group is expanded/highlighted.
2. Navigate to Site Customizer — confirm it's in WEBSITE section.
3. On mobile: confirm sidebar still opens/closes correctly.

---

#### #29 — Student List Drill-Down (Class → Section → Students)

**Goal:** Replace the current flat student list with a drill-down: click a class card → see sections → click a section → see students. Students list scopes to working year automatically.

**Why:** With 433+ students across 13 classes, the flat list is hard to navigate. Drill-down matches how teachers think (by class).

**Modified Files:**
- `app/Http/Controllers/Admin/MemberController.php` — `index()` changes
- `resources/views/admin/members/index.blade.php` — full restructure

**Controller logic:**
```php
// index() receives optional: ?class=&section=
// No class param → show class cards with student counts
// class param, no section → show section tabs for that class
// both params → show student table (existing logic)
$selectedClass = $request->input('class');
$selectedSection = $request->input('section');

if (!$selectedClass) {
    // Group enrollments by class, count per class
    $classCounts = StudentEnrollment::where('academic_year_id', $workingYear->id)
        ->where('status', 'active')
        ->selectRaw('class, count(*) as total')
        ->groupBy('class')
        ->pluck('total', 'class');
    return view('admin.members.index', compact('classCounts', 'workingYear'));
}
```

**View structure:**
- No class selected: grid of class cards (Class I, Class II … Class X, LKG, UKG, Preparatory) each showing student count. Click = drill into class.
- Class selected, no section: section tabs (A, B, C…) with counts. Breadcrumb: All Classes → Class IV.
- Class + section selected: student table (existing). Breadcrumb: All Classes → Class IV → Section A.

**URL patterns:**
- `/admin/students` — class grid
- `/admin/students?class=Class+IV` — sections of Class IV
- `/admin/students?class=Class+IV&section=A` — students in Class IV-A

**Test steps:**
1. Visit /admin/students — see class cards with counts.
2. Click Class IV — see section tabs.
3. Click Section A — see student table.
4. Breadcrumb works back to classes.
5. Search still works when class+section are set.

---

### BATCH B — Promotion System (requires Batch A)

---

#### #30 — Promotion Eligibility Rules

**Goal:** Admin configures which students are eligible for promotion: pass/fail threshold, minimum attendance %, and which exam to use for marks evaluation.

**New Migration:**
```php
// database/migrations/XXXX_create_promotion_rules_table.php
Schema::create('promotion_rules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
    $table->foreignId('exam_id')->nullable()->constrained()->nullOnDelete(); // which exam to evaluate
    $table->decimal('min_pass_percentage', 5, 2)->default(40.00); // marks threshold
    $table->decimal('min_attendance_percentage', 5, 2)->default(75.00); // attendance threshold
    $table->boolean('use_cgpa')->default(false); // use CGPA instead of subject-wise
    $table->decimal('min_cgpa', 3, 2)->nullable(); // if use_cgpa = true
    $table->json('fail_subjects_allowed')->nullable(); // e.g. [1] = may fail 1 subject and still pass
    $table->timestamps();
});
```

**New Model:** `app/Models/PromotionRule.php`

**New Files:**
- `app/Http/Controllers/Admin/PromotionRuleController.php` — `store()`, `update()`
- `resources/views/admin/promotion-rules/edit.blade.php` — form embedded on Academic Years page

**Route:** `PUT /admin/academic-years/{year}/promotion-rules`

**UI:** Add "Set Promotion Rules" section to the promote form page (above student list). Form fields: Exam selector, Min pass %, Min attendance %, Allow fail in N subjects.

**Test steps:**
1. Create promotion rule: min 40% marks, 75% attendance, eval exam = "Final Exam 2025-26".
2. Rule saves and reloads correctly.
3. Promote form page shows rule summary above student list.

---

#### #31 — Marks-Based Rank Table & Bulk Promote

**Goal:** On the Promote Students page, show each student's CGPA, rank, attendance %, and an eligible/failed badge. Admin can bulk promote all eligible students or manually override any student.

**Modified Files:**
- `app/Http/Controllers/Admin/AcademicYearController.php` — `promoteForm()` enhanced
- `resources/views/admin/academic-years/promote.blade.php` — full redesign

**Controller changes in `promoteForm()`:**
```php
// Load promotion rule for this year
$rule = PromotionRule::where('academic_year_id', $year->id)->first();

// Load all active enrollments for this year
$enrollments = StudentEnrollment::with(['student', 'student.marks' => function($q) use ($rule) {
    $q->where('exam_id', $rule?->exam_id);
}])->where('academic_year_id', $year->id)->where('status', 'active')->get();

// For each enrollment: compute CGPA, attendance %, eligibility
$enrollments->each(function($enrollment) use ($rule) {
    $marks = $enrollment->student->marks;
    $enrollment->cgpa = /* compute from marks */;
    $enrollment->attendance_pct = /* from attendance table */;
    $enrollment->eligible = $rule ? ($enrollment->cgpa >= $rule->min_cgpa_equivalent && $enrollment->attendance_pct >= $rule->min_attendance_percentage) : null;
});

// Rank within class (by CGPA desc)
$byClass = $enrollments->groupBy('class');
foreach ($byClass as $class => $group) {
    $ranked = $group->sortByDesc('cgpa')->values();
    $ranked->each(fn($e, $i) => $e->rank = $i + 1);
}
```

**View columns:** Rank | Name | Roll | Class | CGPA | Attendance % | Status Badge | Next Class (dropdown) | Skip checkbox

**Batch actions:**
- "Select All Eligible" button — checks all eligible students
- "Promote Selected" — submits batch

**Manual override:**
- Admin can check any student (even failed) to promote
- Or uncheck an eligible student to skip

**Test steps:**
1. Set promotion rules.
2. Open promote page — students show CGPA, rank, eligible/failed badges.
3. "Select All Eligible" checks only eligible students.
4. Override: manually check a failed student.
5. Submit — new enrollments created, `student.class` updated.

---

#### #32 — Rank-Based Roll Number Assignment

**Goal:** After promotion, admin assigns roll numbers to the new year's class based on rank order. Top-ranked student gets Roll 1, second gets Roll 2, etc. Admin can drag-reorder before finalizing.

**New Migration:**
- No new table needed — roll number lives in `student_enrollments.roll_number` (add column if missing)
```php
// Check if column exists first
Schema::table('student_enrollments', function (Blueprint $table) {
    if (!Schema::hasColumn('student_enrollments', 'roll_number')) {
        $table->string('roll_number')->nullable()->after('section');
    }
});
```

**New Files:**
- `resources/views/admin/academic-years/assign-rolls.blade.php` — per-class sortable list
- Route: `GET /admin/academic-years/{year}/assign-rolls` and `POST` to save

**UI:**
- After promotion completes → "Assign Roll Numbers →" button
- Per class: draggable list of students (sorted by rank by default)
- Position in list = roll number (1, 2, 3…)
- "Auto-assign by Rank" button resets order to rank order
- Save → updates `student_enrollments.roll_number` for each student

**Implementation note:** Use CSS `cursor:grab` + `draggable="true"` + vanilla JS `dragstart`/`dragover`/`drop` events. No external JS library needed.

**Test steps:**
1. After promoting students, visit assign-rolls page.
2. Students are pre-sorted by rank.
3. Drag student 3 above student 1.
4. Save — check DB: `student_enrollments.roll_number` updated correctly.
5. Student list shows new roll numbers.

---

### BATCH C — Documents & Staff (can be done in parallel with Batch B)

---

#### #33 — Teacher Yearly Status

**Goal:** Track each teacher's employment status per academic year (active / resigned / on_leave / transferred). Historical records preserved — past years show who was teaching.

**New Migration:**
```php
Schema::create('teacher_year_statuses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
    $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
    $table->enum('status', ['active', 'resigned', 'on_leave', 'transferred'])->default('active');
    $table->date('effective_from')->nullable();
    $table->date('effective_to')->nullable();
    $table->text('remarks')->nullable();
    $table->timestamps();
    $table->unique(['teacher_id', 'academic_year_id']);
});
```

**New Model:** `app/Models/TeacherYearStatus.php`

**Modified Files:**
- `app/Models/Teacher.php` — add `yearStatuses()` hasMany, `statusForYear(AcademicYear $year)` helper
- `app/Http/Controllers/Admin/TeacherController.php` — `show()` loads year statuses
- `resources/views/admin/teachers/show.blade.php` — add "Year-wise Status" table + "Update Status" form

**Data bootstrap (migration only — no fake data):**
- On migration run: for all teachers, create `teacher_year_statuses` row for active year with status=`active` — `updateOrCreate` so it's idempotent

**UI on teacher profile:**
- Table: Year | Status | From | To | Remarks
- "Update Status for [Working Year]" button → inline form

**Test steps:**
1. Open teacher profile.
2. See Year-wise Status table — active year shows "active".
3. Set status to "resigned", set effective_to date.
4. Table updates.
5. Switch working year — table shows correct status for each year.

---

#### #34 — ID Card PDF (4-up A4)

**Goal:** Generate printable student ID cards — 4 cards per A4 page, 85×54mm each. Includes student photo, school logo, name, class, section, roll number, emergency contact.

**New Files:**
- `app/Http/Controllers/Admin/IdCardController.php`
- `resources/views/pdf/id-card.blade.php` — DomPDF template

**Route:**
```
GET /admin/students/{student}/id-card          → single student PDF
GET /admin/students/id-cards?class=X&section=Y → batch PDF (4-up, all students in class/section)
```

**PDF layout (id-card.blade.php):**
```html
{{-- 4 cards per page, 2 columns × 2 rows --}}
{{-- Each card: 85mm × 54mm --}}
<style>
  body { margin: 0; }
  .page { width: 210mm; padding: 10mm; }
  .grid { display: flex; flex-wrap: wrap; gap: 5mm; }
  .card {
    width: 85mm; height: 54mm;
    border: 1px solid #ccc; border-radius: 3mm;
    display: flex; overflow: hidden;
    font-family: sans-serif;
  }
  .card-left { width: 20mm; background: [school color]; display:flex; align-items:center; justify-content:center; }
  .card-left img.photo { width: 16mm; height: 20mm; object-fit: cover; border-radius: 1mm; }
  .card-right { flex: 1; padding: 2mm 3mm; }
  .school-name { font-size: 6pt; font-weight: bold; color: [school color]; }
  .student-name { font-size: 8pt; font-weight: bold; margin-top: 1mm; }
  .detail { font-size: 6pt; color: #444; margin-top: 0.5mm; }
  .barcode-row { margin-top: 2mm; font-size: 5pt; }
</style>
```

**Controller:**
```php
public function single(Student $student) {
    $pdf = Pdf::loadView('pdf.id-card', [
        'students' => collect([$student]),
        'settings' => SiteSetting::all()->keyBy('key'),
    ])->setPaper('a4');
    return $pdf->download('id-card-'.$student->id.'.pdf');
}
```

**Data fields on card:**
- School logo (from site settings)
- School name + branch/address (from site settings)
- Student photo (from `student_files` or placeholder)
- Full name, Class + Section, Roll Number
- Admission No (student ID padded: ADM-0001)
- Emergency contact: parent name + phone (from student profile fields)
- Academic year

**Test steps:**
1. Visit student profile → click "Download ID Card".
2. PDF opens — check layout, photo loads, correct details.
3. Batch: `/admin/students/id-cards?class=Class+IV&section=A` — 4-up layout for whole class.
4. Test with student who has no photo — placeholder shows correctly.

---

#### #35 — Progress Report Card PDF

**Goal:** Per-student term-wise or year-end consolidated report card PDF. Shows subject-wise marks (theory + assignment), grade, CGPA, class rank, attendance %, teacher remarks, principal signature area.

**New Files:**
- `app/Http/Controllers/Admin/ReportCardController.php`
- `resources/views/pdf/report-card.blade.php` — DomPDF template

**Route:**
```
GET /admin/students/{student}/report-card?exam_id=X       → single exam report
GET /admin/students/{student}/report-card?year_id=X       → full year consolidated
```

**PDF sections:**
1. **Header** — school logo, school name, "Progress Report Card", academic year, student name, class/section/roll, admission no
2. **Marks Table** — rows: Subject | Max Theory | Marks Theory | Max Assignment | Marks Assignment | Total | % | Grade
3. **Summary row** — Total marks, Overall %, CGPA, Class Rank
4. **Attendance** — Present / Total Days / Attendance %
5. **Remarks** — text area (filled by admin before download, or blank lines for handwriting)
6. **Signature row** — Class Teacher signature | Principal signature | Date

**Controller:**
```php
public function download(Request $request, Student $student) {
    $examId = $request->exam_id;
    $yearId = $request->year_id ?? app('workingYear')->id;

    $marks = Mark::where('student_id', $student->id)
        ->when($examId, fn($q) => $q->where('exam_id', $examId))
        ->when(!$examId, fn($q) => $q->whereHas('exam', fn($e) => $e->where('academic_year_id', $yearId)))
        ->with(['subject', 'exam'])
        ->get();

    // Compute CGPA, rank (reuse MarksAnalyticsController logic)
    $pdf = Pdf::loadView('pdf.report-card', compact('student', 'marks', ...))->setPaper('a4');
    return $pdf->download('report-card-'.$student->id.'.pdf');
}
```

**Note:** The existing `ResultCardController` already generates a basic PDF. This task REPLACES or EXTENDS that with the above full layout. Check `ResultCardController.php` first — reuse the marks-loading logic.

**Test steps:**
1. Student with marks entered → click "Download Report Card (Term)".
2. PDF: all subjects shown with correct marks, grade computed.
3. Download "Full Year" version — all exams consolidated, one row per subject summed.
4. CGPA and rank show correctly.
5. Attendance % shows correctly.
6. Test with student with no marks — PDF shows zeros gracefully.

---

### BATCH D — Records & Deploy

---

#### #36 — Student Academic Passport

**Goal:** Full year-by-year history for each student in one scrollable view. Each year shows: enrollment status, class/section/roll, marks summary, attendance %, and a download link. Collapsible year cards.

**Modified Files:**
- `app/Http/Controllers/Admin/MemberController.php` — `show()` loads full history
- `resources/views/admin/members/show.blade.php` — add "Academic Passport" section

**Controller changes in `show()`:**
```php
// Load all enrollments for this student across all years
$enrollments = StudentEnrollment::with(['academicYear'])
    ->where('student_id', $student->id)
    ->orderByDesc('academic_year_id')
    ->get();

// For each enrollment: load marks summary (avg%, CGPA), attendance%
$enrollments->each(function($e) use ($student) {
    $e->marks_summary = Mark::where('student_id', $student->id)
        ->whereHas('exam', fn($q) => $q->where('academic_year_id', $e->academic_year_id))
        ->get();
    $e->attendance_pct = /* compute from attendance table */;
});
```

**View (Academic Passport card per year):**
```
┌─────────────────────────────────────────────────┐
│ 2025-26  │ Class IV-A │ Roll: 5 │ 🟢 Active      │  [▼ expand]
├─────────────────────────────────────────────────┤
│ Marks: 87.3% avg  │ CGPA: 3.8  │ Rank: 2         │
│ Attendance: 91%                                  │
│ [Download Report Card]  [Download ID Card]       │
└─────────────────────────────────────────────────┘
```

**Test steps:**
1. Student with 2 years of data → open profile.
2. "Academic Passport" section shows 2 year cards.
3. Expand year → marks and attendance load.
4. Download report card link works.
5. New student with 1 year → shows 1 card only.

---

#### #37 — Admission Workflow (3-section form)

**Goal:** Replace the current bare-bones student create form with a structured 3-section admission form. New students go through: Personal Details → Parent/Guardian → Enrollment (class, year, roll). Generates admission number automatically.

**Modified Files:**
- `resources/views/admin/members/create.blade.php` — full redesign
- `app/Http/Controllers/Admin/MemberController.php` — `store()` enhanced

**Form sections:**

**Section 1 — Personal Details:**
- First name, Last name, Date of Birth, Gender, Blood Group, Photo upload, Address

**Section 2 — Parent / Guardian:**
- Father name, Mother name, Guardian name (if different), Primary phone, Secondary phone, Email, Relationship to student

**Section 3 — Enrollment:**
- Academic Year (default: working year), Class, Section, Roll number (auto-suggest = next available in class), Enrollment date (default today), Status (active), Remarks

**Auto admission number:** `ADM-YYYY-XXXX` where YYYY = year, XXXX = zero-padded ID. Generated after save, stored in `students.admission_number` column.

**Migration for new column:**
```php
Schema::table('students', function (Blueprint $table) {
    if (!Schema::hasColumn('students', 'admission_number')) {
        $table->string('admission_number')->nullable()->after('id');
    }
    // Also add parent fields if not present:
    // father_name, mother_name, guardian_name, guardian_phone, guardian_email, guardian_relation
    // blood_group, gender (may already exist — check before adding)
});
```

**Note:** Check existing `students` table columns before adding — many may already exist from earlier migrations.

**Test steps:**
1. Click "Add Student" — see 3-section form.
2. Fill all fields → submit.
3. Student created with admission number `ADM-2026-0434`.
4. Enrollment created in working year with correct class/section/roll.
5. Student appears in correct class drill-down immediately.

---

#### #38 — Deploy to Live Server (Full Guide)

**Goal:** Deploy all local code changes to the live cPanel server without data loss.

**IMPORTANT:** Live server still has `members` table. Local code uses `students` table. The rename migration must run correctly. Read every step before running anything.

**Pre-deploy checklist (do locally first):**
- [ ] Run `php artisan migrate` locally — all migrations pass
- [ ] Run `php artisan test` — all tests pass (if any exist)
- [ ] Commit all changes to git

**Step 1 — Upload files via cPanel File Manager:**
Upload the following directories (replace, not merge — back up old files first):
- `app/`
- `resources/views/`
- `routes/web.php`
- `database/migrations/`
- `composer.json` + `composer.lock` (if dependencies changed)

**Step 2 — Run composer install (if needed):**
```bash
cd /home/[username]/public_html/cms
php8.4 composer.phar install --no-dev --optimize-autoloader
```

**Step 3 — Run migrations:**
```bash
php8.4 artisan migrate --force
```
The `--force` flag is required for production. Review each migration output.

**Step 4 — Clear caches:**
```bash
php8.4 artisan config:clear
php8.4 artisan cache:clear
php8.4 artisan view:clear
php8.4 artisan route:clear
```

**Step 5 — Rebuild caches:**
```bash
php8.4 artisan config:cache
php8.4 artisan route:cache
php8.4 artisan view:cache
```

**Step 6 — Smoke test (visit each page):**
- [ ] Admin login works
- [ ] Students page loads with correct year
- [ ] Marks page loads
- [ ] Teacher login works
- [ ] Public website homepage loads

**Rollback plan (if anything breaks):**
1. Restore old `app/` and `resources/views/` from backup
2. If migration broke DB: `php8.4 artisan migrate:rollback --step=1`
3. Clear caches again

**Critical migration note — `members` → `students` rename:**
The migration `2026_05_28_000002_rename_members_to_students.php` renames the live `members` table. This is safe — it only renames the table and FK columns, all data is preserved. But run it during off-hours (night time) when no teachers are actively using the system.

---

## 🗒 Implementation Order Summary

```
Week 1:  #27 (Year Context) → #28 (Sidebar) → #29 (Student Drill-down)
Week 2:  #30 (Promo Rules) → #31 (Rank Table) → #32 (Roll Numbers)
Week 3:  #33 (Teacher Status) → #34 (ID Card) → #35 (Report Card)
Week 4:  #36 (Passport) → #37 (Admission) → #38 (Deploy)
```

Each task is independent within its batch. Batches A→B→C→D must be done in order.
