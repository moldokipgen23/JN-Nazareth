# JN Nazareth School ERP — Complete Feature Reference

> Generated: 28 May 2026  
> Stack: Laravel 13, Tailwind CSS, Alpine.js, MySQL, DomPDF  
> Roles: `admin`, `staff`, `teacher` (via Spatie laravel-permission)

---

## Table of Contents

1. [Authentication & User Management](#1-authentication--user-management)
2. [Student Management](#2-student-management)
3. [Academic Year & Promotion](#3-academic-year--promotion)
4. [Class & Section Management](#4-class--section-management)
5. [Teacher Management](#5-teacher-management)
6. [Subject Management](#6-subject-management)
7. [Examination & Grading](#7-examination--grading)
8. [Attendance Management](#8-attendance-management)
9. [Notes & Assignments](#9-notes--assignments)
10. [Website / Frontend Pages](#10-website--frontend-pages)
11. [News & Blog](#11-news--blog)
12. [Events](#12-events)
13. [Photo Gallery](#13-photo-gallery)
14. [Video Gallery](#14-video-gallery)
15. [Document Manager](#15-document-manager)
16. [Downloads](#16-downloads)
17. [Important Links](#17-important-links)
18. [Inquiries](#18-inquiries)
19. [Hall of Fame](#19-hall-of-fame)
20. [Site Customizer](#20-site-customizer)
21. [Activity Logging](#21-activity-logging)
22. [Dashboard](#22-dashboard)
23. [Frontend Build & Theming](#23-frontend-build--theming)
24. [Packages & Infrastructure](#24-packages--infrastructure)

---

## 1. Authentication & User Management

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `User` | `users` | Extends `Authenticatable`; Spatie `HasRoles`; relationships: `teacher()`, `students()`, `activityLogs()`, `uploadedFiles()`; helpers: `isAdmin()`, `isTeacher()`, `teachesClass()`, `isClassTeacherFor()`, `teachesSubject()` |

### Features

**Configurable Login URLs** — Admin and teacher login paths are stored in `site_settings` and can be changed via the Site Customizer. Defaults: `/admin-portal` (admin), `/teacher-portal` (teacher). An emergency hardcoded fallback (`/cms-recovery-7k3`) exists if the DB is inaccessible.

**Password Reset Flow** — Standard Laravel forgot/reset password with email notification, token-based reset.

**Email Verification** — Optional email verification for new accounts (verify, resend notification).

**Profile Management** — Authenticated users can view/edit their profile, update password, delete account.

**Admin User Management** — Full CRUD for user accounts (admin/staff/teacher roles).

### Controllers

| Controller | Key Methods |
|---|---|
| `Auth/AuthenticatedSessionController` | `create()` — show login form; `store()` — authenticate; `destroy()` — logout |
| `Auth/RegisteredUserController` | `create()`, `store()` |
| `Auth/PasswordController` | `update()` |
| `Auth/PasswordResetLinkController` | `create()`, `store()` |
| `Auth/NewPasswordController` | `create()`, `store()` |
| `Auth/VerifyEmailController` | `__invoke()` |
| `Auth/EmailVerificationPromptController` | `__invoke()` |
| `Auth/EmailVerificationNotificationController` | `__invoke()` |
| `Auth/ConfirmablePasswordController` | `show()`, `store()` |
| `Admin/UserController` | Full resource controller for user CRUD |
| `ProfileController` | `edit()`, `update()`, `destroy()` |

### Key Routes

```
GET|POST  /{admin_login_path}          Admin login (configurable slug)
GET|POST  /{teacher_login_path}        Teacher login (configurable slug)
GET|POST  /cms-recovery-7k3           Emergency login fallback
GET|POST  /forgot-password
GET|POST  /reset-password/{token}
GET       /verify-email
GET|POST  /verify-email/{id}/{hash}
POST      /email/verification-notification
GET|POST  /confirm-password
PUT       /password
POST      /logout
GET|PATCH|DELETE  /profile
```

### Views

`auth/login.blade.php`, `auth/teacher-login.blade.php`, `auth/register.blade.php`, `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`, `auth/confirm-password.blade.php`, `auth/verify-email.blade.php`

---

## 2. Student Management

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Student` | `students` | name, class, section, roll_number, phone, email, address, photo, academic_year, father_name, mother_name, guardian_phone, date_of_birth, blood_group, admission_number, aadhar_number, admission_date, is_active, notes; scopes: `active()`; relationships: `files()`, `enrollments()`, `currentEnrollment()`; static `classes()` returns ordered class list |
| `StudentEnrollment` | `student_enrollments` | Links `student_id` to `academic_year_id` with class, section, roll_number, status, enrolled_on |
| `StudentFile` | `student_files` | Files attached to a student profile (path, type, uploaded_by) |

### Features

**Student List** — Filterable by academic year, enrollment status (active/dropped/transferred/graduated), class tabs, search by name/admission number/adhaar. Unassigned (no enrollment) students shown with warning badge and clickable link to fix.

**CSV Bulk Import** — Upload all classes in a single CSV file. Downloads import template. Auto-assigns to active academic year. Validates rows and reports errors.

**Student Profile** — Personal details, photo, documents/files, enrollment history year-by-year. Each enrollment row shows class, section, roll number, status, and dates.

**Student Passport** — Full academic history page showing enrollment timeline, marks per term/exam, attendance percentage per year. Designed as a printable/sharable document.

**Enrollment Status Management** — Change status: active, dropped, transferred, graduated. Each change recorded.

**ID Card (PDF)** — On-demand ID card download with student photo, name, class, admission number, blood group, guardian contact. Generated via DomPDF.

**Result Card (PDF)** — Per-student on-demand PDF for a selected exam combining marks (subject-wise with theory/assignment split, grade, pass/fail) and attendance summary.

**Document Upload** — Upload and manage files per student (photos, documents, reports).

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/StudentController` | Full resource CRUD + `import()` (CSV), `export()`, `importTemplate()` (download), `passport()`, `enrollmentStatus()`, `icard()`, `resultCard()` |
| `Admin/StudentFileController` | `store()`, `destroy()`, `download()` |

### Key Routes

```
GET|POST  /admin/students/import           CSV import
GET       /admin/students/import-template  Download CSV template
GET       /admin/students/export           Export CSV
GET|POST|PUT|DELETE  /admin/students/{student}  CRUD
GET       /admin/students/{student}/passport         Passport
GET       /admin/students/{student}/result-card/{exam}  Result PDF
GET       /admin/students/{student}/icard  ID Card PDF
PATCH     /admin/students/{student}/enrollment-status
POST      /admin/students/{student}/files
DELETE    /admin/student-files/{studentFile}
GET       /admin/student-files/{studentFile}/download
```

### Views

`admin/students/index.blade.php`, `admin/students/create.blade.php`, `admin/students/edit.blade.php`, `admin/students/show.blade.php`, `admin/students/_form.blade.php`, `admin/students/import.blade.php`, `admin/students/passport.blade.php`, `admin/students/icard-pdf.blade.php`, `admin/students/result-card-pdf.blade.php`

### Migrations

- `2026_04_06_200001_create_members_table.php` (originally `members`, renamed to `students`)
- `2026_05_24_000002_create_student_enrollments_table.php`
- `2026_05_24_000003_create_student_files_table.php`
- `2026_05_28_000002_rename_members_to_students.php`
- `2026_05_24_000004_add_enrollment_status_to_student_enrollments.php`

---

## 3. Academic Year & Promotion

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `AcademicYear` | `academic_years` | name, is_active, starts_on, ends_on; static `current()` returns active year; `activeOrCreate($name)` ensures a year exists |

### Features

**Year Management** — Create, edit, delete academic years. Set one year as active at a time. List all years.

**Promotion Workflow** — Promote students from one academic year to the next:

- **Per-student dropdown**: choose next class or same class (no downgrade)
- **Double-promote prevention**: already-promoted students show "Already in [Year]" badge with disabled controls; server-side check rejects duplicates
- **Batch confirmation**: auto-fill shows count of students to be affected; admin confirms before submission
- **Marks-based auto-suggest**: pass → next class, fail → same class; admin can override
- **Bulk-apply**: "Auto-suggest from marks" applies suggestions to all students at once
- **Enrollment notes**: auto-generated "Promoted from [Class] – [Year]", editable
- **Summary row**: shows promoted count vs available count
- **Visual marks & attendance**: displayed inline on the promote page

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/AcademicYearController` | `index()`, `store()`, `update()`, `destroy()`, `activate()`, `promote()` (GET form, POST execute), `students()` |

### Key Routes

```
GET       /admin/academic-years               List
POST      /admin/academic-years               Create
PUT       /admin/academic-years/{academicYear} Update
DELETE    /admin/academic-years/{academicYear} Delete
POST      /admin/academic-years/{academicYear}/activate  Set active
GET|POST  /admin/academic-years/{academicYear}/promote   Promote
GET       /admin/academic-years/{academicYear}/students  List students
```

### Migrations

- `2026_05_24_000001_create_academic_years_table.php`
- `2026_05_25_000004_seed_active_academic_year.php` (idempotent seed, handles `members` or `students` table)

---

## 4. Class & Section Management

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Section` | `sections` | name, class (string), academic_year_id |

### Features

**Class Listing** — All classes displayed with student rosters. Class details show sections, teacher assignments, student count.

**Section Management** — Create and destroy sections within a class for a given academic year.

**Teacher Portal Integration** — Teachers see their assigned classes with student lists.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/ClassroomController` | `index()`, `show()` (student roster) |
| `Admin/SectionController` | `store()`, `destroy()` |
| `Teacher/PortalController` | `classes()` |

### Key Routes

```
GET     /admin/classes         List all classes
GET     /admin/classes/{class} Show class with student roster
POST    /admin/sections        Create section
DELETE  /admin/sections/{section}  Delete section
```

---

## 5. Teacher Management

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Teacher` | `teachers` | name, designation, subjects (JSON array), phone, email, photo, classes (JSON array), sort_order, is_active; relationships: `classTeacherAssignments()`, `subjectTeacherAssignments()`, `users()` |
| `ClassTeacherAssignment` | `class_teacher_assignments` | teacher_id, class, section, academic_year_id |
| `SubjectTeacherAssignment` | `subject_teacher_assignments` | teacher_id, subject_id, class, section, academic_year_id |

### Features

**Staff Directory** — Teachers listed with name, designation, subjects, photo, active/inactive status. Sortable.

**Teacher Login** — Create login account directly from teacher profile (generates User account). Link existing User accounts to teacher records. Unlink accounts. Teachers can log in to the teacher portal.

**Class Teacher Assignment** — Assign a teacher as class teacher for a specific class/section in an academic year.

**Subject Teacher Assignment** — Assign a teacher to teach a specific subject in a specific class/section in an academic year. Both teachers and subjects are selectable from existing records.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/TeacherController` | CRUD (no show); `createLogin()`, `linkUser()`, `unlinkUser()` |
| `Admin/TeacherAssignmentController` | `index()`, `assignClassTeacher()`, `removeClassTeacher()`, `assignSubjectTeacher()`, `removeSubjectTeacher()` |

### Key Routes

```
GET|POST|PUT|DELETE  /admin/teachers                    CRUD
POST  /admin/teachers/{teacher}/create-login            Create user login
POST  /admin/teachers/{teacher}/link-user               Link existing user
POST  /admin/teachers/{teacher}/unlink-user/{user}      Unlink user
GET   /admin/teacher-assignments                         List all assignments
POST  /admin/teacher-assignments/class                   Assign class teacher
DELETE /admin/teacher-assignments/class/{assignment}      Remove class teacher
POST  /admin/teacher-assignments/subject                 Assign subject teacher
DELETE /admin/teacher-assignments/subject/{assignment}   Remove subject teacher
```

### Migrations

- `2026_05_24_000005_create_teachers_table.php`
- `2026_05_24_000006_create_teacher_assignments_tables.php`

---

## 6. Subject Management

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Subject` | `subjects` | name, code, type, sort_order, is_active |

### Features

**Master Subject List** — Full CRUD for subjects with name, code, and type. Toggle active/inactive with a single click. Drag-to-reorder.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/SubjectController` | `index()`, `store()`, `update()`, `destroy()`, `toggle()`, `reorder()` |

### Key Routes

```
GET|POST  /admin/subjects          List & create
PUT       /admin/subjects/{subject} Update
DELETE    /admin/subjects/{subject} Delete
POST      /admin/subjects/{subject}/toggle  Toggle active
POST      /admin/subjects/reorder           Reorder
```

### Migration

- `2026_05_25_000005_create_subjects_table.php`

---

## 7. Examination & Grading

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Exam` | `exams` | name, type (Formative/Summative/Final/Custom), code, starts_on, ends_on, sort_order, is_active, academic_year_id |
| `ExamQuestion` | `exam_questions` | exam_id, subject_id, teacher_id, question_text, file, reviewed_at, reviewed_by |
| `Mark` | `marks` | student_enrollment_id, exam_id, subject_id, full_marks, pass_marks, theory_marks, assignment_marks, total_marks, obtained_marks, grade, remarks; computed: percentage(), isPass(), grade() |
| `GradeScale` | `grade_scales` | grade (A+, A, B+, ...), min_percent, max_percent, grade_point, is_active; static `gradeFor(percentage)` |

### Features

**Exam Management** — Create, edit, delete exams. Each exam has a name, type (Formative, Summative, Final, or Custom), code, date range. Toggle active/inactive. Belongs to an academic year.

**Marks Entry (Teacher)** — Teachers select exam, class, section, subject → see student list → enter theory marks, assignment marks → system auto-computes total, percentage, grade, pass/fail. Bulk entry with tab navigation.

**Marks Review (Admin)** — Admin views marks across all exams/classes/subjects. Can override/update marks. Overview of all submissions with filtering.

**Marks Analytics** — Subject-wise percentage display, overall CGPA calculation, class rank (1st, 2nd, 3rd), per-subject stats (average, highest, lowest, pass count), grade distribution chart. Filterable by exam, class, section, subject.

**Grade Scale Management** — Define letter grades with percentage ranges and grade points (e.g., A+ = 90-100%, 4.0). Toggle active. Seed default grades with one click. Auto-grade computation uses these scales.

**Result Card PDF** — Per-student PDF generated via DomPDF. Contains student info, subject-wise marks (theory/assignment/total), grades, percentage, pass/fail, attendance summary. On-demand download from student profile.

**Exam Questions** — Teachers submit questions for upcoming exams (with optional file upload). Admin reviews and marks as reviewed/deletes. Filter by exam, subject.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/ExamController` | CRUD + `toggle()` |
| `Admin/MarksController` | `index()` (list), `update()` (bulk update) |
| `Admin/MarksAnalyticsController` | `index()` — analytics dashboard with filters |
| `Admin/GradeScaleController` | CRUD + `toggle()`, `seed()` defaults |
| `Admin/QuestionsController` | `index()`, `review()`, `destroy()` |
| `Admin/ResultCardController` | `download()` — PDF generation |
| `Teacher/MarksController` | `index()` (select params), `sheet()` (entry form), `store()` (save marks) |
| `Teacher/QuestionsController` | `index()`, `store()`, `destroy()` |

### Key Routes — Admin

```
GET|POST|PUT|DELETE  /admin/exams                        CRUD
POST  /admin/exams/{exam}/toggle                         Toggle active
GET|PUT  /admin/marks                                    List & update marks
GET   /admin/marks/analytics                             Marks analytics
GET|POST|PUT|DELETE  /admin/grade-scales                 CRUD
POST  /admin/grade-scales/{gradeScale}/toggle            Toggle active
POST  /admin/grade-scales/seed                           Seed defaults
GET|DELETE  /admin/questions                             Review questions
POST  /admin/questions/{question}/review                Mark reviewed
```

### Key Routes — Teacher

```
GET|POST  /teacher/marks/{exam}/{class}/{section}/{subject}  Marks sheet & store
GET|POST|DELETE  /teacher/questions                           CRUD exam questions
```

### Views

`admin/exams/index.blade.php`, `admin/marks/index.blade.php`, `admin/grade-scales/index.blade.php`, `admin/questions/index.blade.php`, `teacher/marks/index.blade.php`, `teacher/marks/sheet.blade.php`, `teacher/questions/index.blade.php`

### Migrations

- `2026_05_25_000002_create_exams_and_marks_tables.php`
- `2026_05_25_000003_create_exam_questions_table.php`
- `2026_05_28_225716_add_type_to_exams_table.php`
- `2026_05_28_230725_create_grade_scales_table.php`
- `2026_05_28_230735_add_theory_assignment_marks_to_marks_table.php`

---

## 8. Attendance Management

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `AttendanceRecord` | `attendance_records` | student_enrollment_id, date, status (present/absent/late/holiday), marked_by |

### Features

**Teacher Daily Attendance** — Teacher selects class and section → sees student list → marks each student as Present/Absent/Late/Holiday. Date-picker for back-dated entry. One-submission-per-day enforced.

**Admin Attendance View** — View attendance across all classes/sections. Filter by date, class, section. Override individual records.

**Attendance Analytics** — Filter by class, section, month. Per-student attendance percentage. Monthly trend view. Class average. Visual summary with color coding.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/AttendanceController` | `index()`, `update()`, `analytics()` |
| `Teacher/AttendanceController` | `index()`, `mark()` (form), `store()` |

### Key Routes

```
GET   /admin/attendance           Admin view
PUT   /admin/attendance/{record}  Update single record
GET   /admin/attendance/analytics Analytics dashboard
GET   /teacher/attendance         Teacher attendance index
GET|POST /teacher/attendance/{class}/{section}  Mark attendance
```

### Views

`admin/attendance/index.blade.php`, `teacher/attendance/index.blade.php`, `teacher/attendance/mark.blade.php`

### Migration

- `2026_05_25_000001_create_attendance_records_table.php`

---

## 9. Notes & Assignments

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `NoteAssignment` | `note_assignments` | teacher_id, class, section, subject_id, title, description, file_path, due_date, type (note/assignment), is_active |

### Features

**Teacher Publishes** — Teachers create notes or assignments for their classes. Supports title, description, file upload, due date (for assignments). Type: Note or Assignment.

**Admin Review** — Admin views all notes/assignments across all teachers. Can delete inappropriate content.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/NotesController` | `index()`, `destroy()` |
| `Teacher/NotesController` | `index()`, `store()`, `destroy()` |

### Key Routes

```
GET     /admin/notes          List notes
DELETE  /admin/notes/{note}   Delete note
GET|POST|DELETE  /teacher/notes  Teacher CRUD
```

### Views

`admin/notes/index.blade.php`, `teacher/notes/index.blade.php`

### Migration

- `2026_05_28_000001_create_note_assignments_table.php`

---

## 10. Website / Frontend Pages

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Page` | `pages` | slug, title, content (WYSIWYG), meta_title, meta_description, published, show_in_nav; scopes: `published()`, `inNav()` |

### Features

**Homepage** — Configurable sections controlled via Site Customizer: hero banner (slides), about, stats/counters, events, gallery, news/blog. Each section can be shown/hidden and reordered.

**About Us** — Multiple sub-pages: Introduction, Principal's Message, Administration, Faculty, SMC, PTA, Certificates. Content managed via Site Customizer. People groups (administration, SMC, PTA) with photos and designations.

**Academics** — Pages: Fee Structure, School Timing, Academic Calendar (image-based, configured via Customizer), Curriculum (cards, configured via Customizer), Textbooks (downloadable list), Results (exam result viewer). All content managed through admin.

**Admission** — Inquiry form for prospective parents. Admission information (items, contacts, certificates) configured via Site Customizer.

**Student Life** — Photo gallery display. Shows gallery folders with cover images. Lightbox for full-size viewing.

**News** — Blog/news listing with categories (news, notice, announcement). Pagination. Single article view.

**Contact** — Contact information from site settings. Optional form integration.

**Hall of Fame** — Achievement showcase (students/staff). Filtered by category. Photo, name, achievement, year.

**Custom Pages** — Admin can create custom CMS pages with WYSIWYG content. Published/unpublished. Show in navigation toggle.

**SEO** — Sitemap.xml and robots.txt auto-generated. Per-page meta titles and descriptions.

### Controllers

| Controller | Key Methods |
|---|---|
| `WebsiteController` | `home()`, `about()`, `academics()`, `feeStructure()`, `schoolTiming()`, `calendar()`, `curriculum()`, `textbooks()`, `results()`, `admission()`, `studentLife()`, `news()`, `newsShow()`, `contact()`, `hallOfFame()`, `galleryFolder()`, `page()` (custom pages) |
| `PublicController` | Legacy public routes |
| `SitemapController` | `sitemapXml()`, `robotsTxt()` |
| `Admin/PageController` | CRUD for custom pages |

### Key Routes

```
GET  /                     Home
GET  /about                About Us
GET  /academics            Academics overview
GET  /academics/fee-structure
GET  /academics/school-timing
GET  /academics/calendar
GET  /academics/curriculum
GET  /academics/textbooks
GET  /academics/results
GET  /admission            Admission page + inquiry form
GET  /student-life         Student life gallery
GET  /news                 News listing
GET  /news/{slug}          Single article
GET  /contact              Contact
GET  /hall-of-fame         Hall of fame
GET  /gallery/folder/{galleryFolder}  Gallery folder view
GET  /sitemap.xml
GET  /robots.txt
POST /inquiry              Submit admission inquiry (throttled 5/min)
GET  /{slug}               Custom page (wildcard)
```

### Views

`website/home.blade.php`, `website/about.blade.php`, `website/academics.blade.php`, `website/admission.blade.php`, `website/contact.blade.php`, `website/news.blade.php`, `website/news-show.blade.php`, `website/student-life.blade.php`, `website/gallery.blade.php`, `website/gallery-folder.blade.php`, `website/hall-of-fame.blade.php`, `website/partials/lightbox.blade.php`

### Layouts

`layouts/website.blade.php`, `layouts/public.blade.php`

### Migration

- `2026_04_06_200008_create_pages_table.php`

---

## 11. News & Blog

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Blog` | `blogs` | type (news/notice/announcement), title, slug, content, image, published, is_featured, published_at; belongs to author (User) |

### Features

**CRUD** — Create, edit, delete posts. Rich text content with featured image. Type: News, Notice, or Announcement.

**Publish Toggle** — One-click publish/unpublish. Featured posts highlighted.

**Public Display** — News listing on website with pagination. Single article view. Featured posts shown prominently. Legacy `/blogs` URLs redirect to `/news`.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/BlogController` | CRUD + `togglePublish()` |
| `WebsiteController` | `news()`, `newsShow()` |

### Key Routes

```
GET|POST|PUT|DELETE  /admin/blogs              CRUD
POST  /admin/blogs/{blog}/toggle-publish       Toggle publish
GET   /news                                    Public listing
GET   /news/{slug}                             Single article
```

### Views

`admin/blogs/index.blade.php`, `admin/blogs/form.blade.php`

### Migrations

- `2026_04_06_200006_create_blogs_table.php`
- `2026_05_21_000004_add_type_to_blogs_table.php`

---

## 12. Events

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Event` | `events` | title, slug, description, venue, starts_at, ends_at, image, is_active |

### Features

**CRUD** — Full resource controller for school events. Title, description, venue, date/time, image.

**Public Display** — Events shown on the website (homepage section and dedicated page).

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/EventController` | Full resource CRUD |

### Key Routes

```
GET|POST|PUT|DELETE  /admin/events  Resource CRUD
```

### Views

`admin/events/index.blade.php`, `admin/events/form.blade.php`

### Migration

- `2026_04_06_200007_create_events_table.php`

---

## 13. Photo Gallery

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `GalleryFolder` | `gallery_folders` | name, slug, type (gallery/programs), cover_image, description, sort_order; relationships: `items()`, `previewImages()` |
| `GalleryItem` | `gallery` | title, caption, path; belongs to `gallery_folder_id` |

### Features

**Folder Organization** — Group images into folders with name, slug, cover image, description. Type: gallery (student life) or programs. Sortable.

**Batch Upload** — Upload multiple images at once into a folder. Drag-and-drop or multi-select.

**Image Management** — Delete individual images. Preview thumbnails. Edit captions.

**Public Display** — Student Life page shows gallery folders with cover images. Click into folder to view all images with lightbox.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/GalleryController` | `index()`, `store()`, `destroy()` |
| `Admin/GalleryFolderController` | CRUD + `uploadImages()`, `deleteImage()` |
| `WebsiteController` | `studentLife()`, `galleryFolder()` |

### Key Routes

```
GET|POST|DELETE  /admin/gallery                           Manage images
GET|POST|PUT|DELETE  /admin/gallery-folders               Folder CRUD
POST  /admin/gallery-folders/{folder}/images               Batch upload
DELETE  /admin/gallery-images/{item}                       Delete image
```

### Views

`admin/gallery/index.blade.php`, `admin/gallery-folders/index.blade.php`, `admin/gallery-folders/form.blade.php`, `website/gallery.blade.php`, `website/gallery-folder.blade.php`

### Migrations

- `2026_04_06_200009_create_gallery_table.php`
- `2026_04_17_000001_create_gallery_folders_table.php`
- `2026_04_17_000002_add_folder_id_to_gallery_table.php`
- `2026_04_17_000004_add_type_slug_to_gallery_folders_table.php`
- `2026_04_16_132305_create_program_items_table.php`
- `2026_04_17_000003_add_fields_to_program_items_table.php`

---

## 14. Video Gallery

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Video` | `videos` | title, url (YouTube embed), description, is_active |

### Features

**YouTube Embed Management** — Add videos by YouTube URL. Title and description. Toggle active/inactive for public display.

**Public Display** — Videos shown on the website gallery/student-life page.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/VideoController` | CRUD + `toggle()` |

### Key Routes

```
GET|POST|PUT|DELETE  /admin/videos             CRUD
POST  /admin/videos/{video}/toggle             Toggle active
```

### Views

`admin/videos/index.blade.php`, `admin/videos/form.blade.php`

### Migration

- `2026_04_16_192615_create_videos_table.php`

---

## 15. Document Manager

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Folder` | `folders` | name, parent_id (hierarchical), description; relationships: `children()`, `documents()` |
| `Document` | `files` | name, path, size, mime_type, folder_id, uploaded_by |
| `FolderPermission` | `folder_permissions` | folder_id, role (admin/staff/teacher) |

### Features

**Hierarchical Folders** — Nested folder structure with parent/child relationships. Create, rename, delete folders.

**File Upload** — Upload documents into folders. Name, size, mime type tracked. Download and delete.

**Role-based Permissions** — Control which roles (admin/staff/teacher) can access each folder.

**Browser Interface** — Navigate folders, view files with size/type, upload/download.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/FolderController` | CRUD + `files()` browse |
| `Admin/DocumentController` | `store()`, `destroy()`, `download()` |

### Key Routes

```
GET|POST|PUT|DELETE  /admin/folders                 Folder CRUD
GET   /admin/folders/{folder}/files                  Browse folder
POST  /admin/folders/{folder}/files                  Upload document
DELETE  /admin/files/{document}                      Delete
GET   /admin/files/{document}/download               Download
```

### Views

`admin/folders/index.blade.php`, `admin/folders/show.blade.php`

### Migrations

- `2026_04_06_200003_create_folders_table.php`
- `2026_04_06_200004_create_files_table.php`
- `2026_04_06_200005_create_folder_permissions_table.php`

---

## 16. Downloads

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Download` | `downloads` | title, file, category (notice/circular/form), is_active |

### Features

**Public File Distribution** — Upload notices, circulars, forms as downloadable files. Categorised. Toggle active/inactive.

**Admin Management** — Full CRUD for download items.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/DownloadController` | CRUD (no show) |

### Key Routes

```
GET|POST|PUT|DELETE  /admin/downloads  CRUD
```

### Views

`admin/downloads/index.blade.php`, `admin/downloads/form.blade.php`

### Migration

- `2026_05_21_000002_create_downloads_table.php`

---

## 17. Important Links

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `ImportantLink` | `important_links` | title, url, icon, sort_order, is_active |

### Features

**Curated Link List** — Manage external links (e.g., CBSE, NCERT, government portals). Icon support. Sortable. Toggle active.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/ImportantLinkController` | CRUD (no show) |

### Key Routes

```
GET|POST|PUT|DELETE  /admin/important-links  CRUD
```

### Views

`admin/important-links/index.blade.php`

### Migration

- `2026_05_22_120001_create_important_links_table.php`

---

## 18. Inquiries

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `Inquiry` | `inquiries` | name, phone, email, message, replied_at |

### Features

**Public Submission** — Admission inquiry form on the website. Name, phone, email, message. Throttled (5 per minute per IP).

**Admin Inbox** — List all inquiries with unread/new badge. View details. Mark as replied. Delete.

### Controllers

| Controller | Key Methods |
|---|---|
| `InquiryController` | `store()` — public submission |
| `Admin/InquiryController` | `index()`, `show()`, `markReplied()`, `destroy()` |

### Key Routes

```
POST  /inquiry                           Public submission (throttled)
GET   /admin/inquiries                   List
GET   /admin/inquiries/{inquiry}         Show
PATCH /admin/inquiries/{inquiry}/mark-replied
DELETE /admin/inquiries/{inquiry}
```

### Views

`admin/inquiries/index.blade.php`, `admin/inquiries/show.blade.php`

### Migration

- `2026_05_21_000003_create_inquiries_table.php`

---

## 19. Hall of Fame

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `HallOfFame` | `hall_of_fames` | name, achievement, category, year, photo, sort_order, is_active |

### Features

**Achievement Showcase** — Add student/staff achievements with name, achievement description, category, year, photo. Sortable. Toggle active.

**Public Display** — Filtered by category. Photo grid with names and achievements.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/HallOfFameController` | CRUD + `toggle()`, `reorder()` |
| `WebsiteController` | `hallOfFame()` |

### Key Routes

```
GET|POST|PUT|DELETE  /admin/hall-of-fame             CRUD
POST  /admin/hall-of-fame/{entry}/toggle             Toggle active
POST  /admin/hall-of-fame/reorder                    Reorder
```

### Views

`admin/hall-of-fame/index.blade.php`, `admin/hall-of-fame/form.blade.php`, `website/hall-of-fame.blade.php`

### Migrations

- `2026_05_15_000001_create_bongbal_firsts_table.php`
- `2026_05_15_000002_rename_bongbal_firsts_to_hall_of_fames.php`

---

## 20. Site Customizer

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `SiteSetting` | `site_settings` | key (unique), value (text); cached with 5-min TTL |
| `BannerSlide` | `banner_slides` | image, title, subtitle, link, sort_order, is_active |

### Features

**All-in-One Configuration UI** — A single admin page to control the entire public website. Sections:

| Section | What It Controls |
|---|---|
| **General** | School name, address, phone, email, social links (Facebook, YouTube, Instagram), login URL slugs, CDN URL, SEO description, about content, stats counter values |
| **Section Visibility** | Show/hide each homepage section (hero, about, stats, events, gallery, blog, etc.) |
| **Section Reorder** | Drag-to-reorder homepage sections |
| **Admission** | Admission items (title, description), contact persons (name, phone), certificate/documents list |
| **People** | People groups: Administration, SMC, PTA — each with name, designation, photo |
| **Academic Calendar** | Calendar image uploads (one per month) |
| **Curriculum** | Cards with title, description, link |
| **Banner Slides** | Hero slides with image, title, subtitle, link. Toggle, reorder, delete |

### Global Helpers

The following helper functions (defined in `app/Helpers/helpers.php`) provide access to site settings throughout the codebase:

| Function | Returns |
|---|---|
| `setting($key, $default)` | Value of a site setting |
| `login_path($type)` | Login URL for admin or teacher |
| `admission_items()` | Admission page items (from JSON) |
| `admission_contacts()` | Admission contact persons |
| `people_members($group)` | People group members |
| `acad_calendar_images()` | Calendar month images |
| `acad_curriculum_items()` | Curriculum cards |
| `cert_items()` | Certificate/documents list |
| `wa_link()` | WhatsApp link from setting |

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/SiteCustomizerController` | `index()`, `save()`, `toggleSection()`, `reorderSections()`, `admission()`, `certificates()`, `people()`, `calendar()`, `curriculum()`, `slides()` (store, toggle, destroy) |

### Key Routes

```
GET   /admin/customizer                           Main customizer page
POST  /admin/customizer/save                      Save general settings
POST  /admin/customizer/toggle-section            Toggle section visibility
POST  /admin/customizer/reorder-sections          Reorder sections
POST  /admin/customizer/admission                 Save admission config
POST  /admin/customizer/certificates              Save certificates
POST  /admin/customizer/people                    Save people groups
POST  /admin/customizer/calendar                  Save calendar images
POST  /admin/customizer/curriculum                Save curriculum items
POST  /admin/customizer/slides                    Store banner slide
POST  /admin/customizer/slides/{slide}/toggle     Toggle slide active
DELETE /admin/customizer/slides/{slide}           Delete slide
```

### Views

`admin/customizer/index.blade.php`, `admin/customizer/partials/*.blade.php`

### Migrations

- `2026_04_06_300001_create_site_settings_table.php`
- `2026_04_06_300002_create_banner_slides_table.php`

---

## 21. Activity Logging

### Models

| Model | Table | Key Fields / Notes |
|---|---|---|
| `ActivityLog` | `activity_logs` | user_id, action, subject_type, subject_id, description, ip_address |

### Features

**Automatic Audit Trail** — Records user actions throughout the system: who did what, on which record, when, and from which IP.

**Read-only Viewer** — Admin views the log sorted by most recent. Can view all activity with details. No editing/deleting logs.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/ActivityLogController` | `index()`, `show()` |

### Key Routes

```
GET  /admin/activity-logs        List logs
```

### Helpers

`app/Helpers/ActivityLogger.php` — Static `log($action, $subjectType, $subjectId, $description)` method called throughout controllers and models.

### Views

`admin/activity-logs/index.blade.php`

### Migration

- `2026_04_06_200010_create_activity_logs_table.php`

---

## 22. Dashboard

### Features

**Admin Dashboard** — Stats overview: student count, teacher count, class count, recent inquiries, recent activity. Quick links to common tasks. Middleware-gated (admin/staff/teacher).

**Teacher Dashboard** — Shows teacher's classes, subjects, student counts. Quick access to mark attendance, enter marks, and manage notes.

### Controllers

| Controller | Key Methods |
|---|---|
| `Admin/DashboardController` | `index()` |
| `Teacher/PortalController` | `dashboard()` |

### Views

`admin/dashboard.blade.php`, `teacher/dashboard.blade.php`

### Routes

```
GET  /admin        Admin dashboard (admin/staff/teacher)
GET  /teacher      Teacher portal dashboard (teacher)
```

---

## 23. Frontend Build & Theming

### Build Tools

| Tool | Usage |
|---|---|
| **Vite** | Asset bundler with `laravel-vite-plugin` |
| **Tailwind CSS** | Utility-first CSS framework with `@tailwindcss/forms` plugin |
| **Alpine.js** | Reactive UI components (tabs, modals, dropdowns, confirmations) |
| **Chart.js** | Analytics charts (marks analytics, attendance trends) |
| **Axios** | HTTP client for AJAX requests |

### Layouts

| Layout | Used For |
|---|---|
| `layouts/admin.blade.php` | Admin panel — sidebar + top nav |
| `layouts/teacher.blade.php` | Teacher portal — mobile-first |
| `layouts/website.blade.php` | Public website — full header/footer |
| `layouts/public.blade.php` | Alternative public layout |
| `layouts/app.blade.php` | Default app layout |
| `layouts/guest.blade.php` | Login/register pages |
| `layouts/navigation.blade.php` | Top navigation partial |

### View Components

| Component | Purpose |
|---|---|
| `application-logo` | School logo |
| `auth-session-status` | Session status messages |
| `danger-button` | Red danger button |
| `dropdown` / `dropdown-link` | Dropdown menus |
| `input-error` / `input-label` | Form field error/label |
| `modal` | Confirmation/info modal |
| `nav-link` / `responsive-nav-link` | Navigation links |
| `primary-button` / `secondary-button` | Styled buttons |
| `text-input` | Form input |
| `AppLayout` / `GuestLayout` | PHP view components |

### Asset Files

```
resources/css/app.css        Tailwind entry point
resources/js/app.js          Main JS entry (Alpine, Bootstrap)
resources/js/bootstrap.js    Axios/Echo bootstrap
```

---

## 24. Packages & Infrastructure

### Composer (PHP)

| Package | Version | Purpose |
|---|---|---|
| `laravel/framework` | ^13.0 | Core Laravel framework |
| `laravel/breeze` | ^2.4 | Authentication scaffolding |
| `spatie/laravel-permission` | ^7.2 | Role-based access control |
| `barryvdh/laravel-dompdf` | ^3.1 | PDF generation (result cards, ID cards) |
| `laravel/tinker` | ^3.0 | Artisan REPL |

### NPM (JavaScript)

| Package | Purpose |
|---|---|
| `vite` + `laravel-vite-plugin` | Build tooling |
| `tailwindcss` + `@tailwindcss/forms` + `@tailwindcss/vite` | CSS framework |
| `alpinejs` | Reactive UI |
| `axios` | HTTP client |
| `chart.js` | Charts |
| `concurrently` | Run dev servers |

### Console Commands

- `LinkTeachersToUsers` — CLI utility to batch-link teacher records to existing user accounts

### Seeders

| Seeder | Purpose |
|---|---|
| `DatabaseSeeder` | Main seeder |
| `SchoolDemoSeeder` | Demo data (local only) |
| `DemoDataSeeder` | Additional demo data |
| `SiteSettingsSeeder` | Default site settings |
| `SchoolSettingsSeeder` | School-specific defaults |

### Testing

```
tests/Feature/Auth/       Auth tests
tests/Feature/ProfileTest.php  Profile tests
tests/Feature/ExampleTest.php  Smoke test
tests/Unit/               Unit tests
```

### Migration Inventory

The database has evolved through 49 migrations covering every feature above. Key structural decisions:

- **Members → Students** — Table renamed via migration, all models/controllers updated, old `Member` model removed
- **Bongbal Firsts → Hall of Fame** — Legacy table renamed
- **Enrollments** — `student_enrollments` table separates academic year tracking from core student data
- **Exams type field** — Added `type` column (Formative/Summative/Final/Custom) to differentiate exam categories
- **Grade scales** — Self-contained table with point system for CGPA calculation
- **Theory/Assignment split** — Marks table supports separate theory and assignment marks with auto-computation

---

## Feature Count Summary

| Domain | Models | Controllers | View Dirs | Route Groups |
|---|---|---|---|---|
| Auth & Users | 1 | 11 | 7 | 12+ |
| Student Management | 3 | 2 | 6 | 11 |
| Academic Year & Promotion | 1 | 1 | 1 | 6 |
| Class & Section | 1 | 3 | 2 | 4 |
| Teacher Management | 3 | 2 | 2 | 9 |
| Subjects | 1 | 1 | 1 | 5 |
| Exams & Grading | 4 | 7 | 7 | 15 |
| Attendance | 1 | 2 | 2 | 5 |
| Notes & Assignments | 1 | 2 | 2 | 3 |
| Website / Pages | 1 | 3 | 15 | 20+ |
| News & Blog | 1 | 2 | 1 | 4 |
| Events | 1 | 1 | 1 | 1 |
| Photo Gallery | 2 | 3 | 4 | 6 |
| Video Gallery | 1 | 1 | 1 | 2 |
| Document Manager | 3 | 2 | 1 | 5 |
| Downloads | 1 | 1 | 1 | 1 |
| Important Links | 1 | 1 | 1 | 1 |
| Inquiries | 1 | 2 | 1 | 4 |
| Hall of Fame | 1 | 2 | 2 | 4 |
| Site Customizer | 2 | 1 | 2 | 11 |
| Activity Logs | 1 | 1 | 1 | 1 |
| Dashboard | — | 2 | 2 | 2 |

**Total: ~31 models, ~53 controllers, 20 feature domains**
