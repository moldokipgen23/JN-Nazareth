<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\ImportantLinkController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FolderController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GalleryFolderController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\ICardController;
use App\Http\Controllers\Admin\ResultCardController;
use App\Http\Controllers\Admin\StudentFileController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\HallOfFameController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\SiteCustomizerController;
use App\Http\Controllers\Admin\TeacherAssignmentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\GradeScaleController;
use App\Http\Controllers\Admin\WorkingYearController;
use App\Http\Controllers\Admin\PromotionRuleController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\QuestionsController as AdminQuestionsController;
use App\Http\Controllers\Teacher\QuestionsController as TeacherQuestionsController;
use App\Http\Controllers\Admin\MarksController as AdminMarksController;
use App\Http\Controllers\Admin\MarksAnalyticsController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\MarksController as TeacherMarksController;
use App\Http\Controllers\Teacher\PortalController as TeacherPortalController;
use App\Http\Controllers\Teacher\NotesController as TeacherNotesController;
use App\Http\Controllers\Admin\NotesController as AdminNotesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\InquiryController as PublicInquiryController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Public routes
// ---------------------------------------------------------------------------

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap']);
Route::get('/robots.txt',  [SitemapController::class, 'robots']);

// ── PWA manifest & icons (dynamic — uses the logo uploaded in Site Customizer) ──
Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('manifest.json');
Route::get('/pwa-icon-{size}.png', [PwaController::class, 'icon'])->where('size', '192|512');

Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/academics', [WebsiteController::class, 'academics'])->name('academics');
Route::get('/academics/fee-structure', [WebsiteController::class, 'feeStructure'])->name('academics.fee');
Route::get('/academics/school-timing', [WebsiteController::class, 'schoolTiming'])->name('academics.timing');
Route::get('/academics/calendar', [WebsiteController::class, 'academicCalendar'])->name('academics.calendar');
Route::get('/academics/curriculum', [WebsiteController::class, 'curriculum'])->name('academics.curriculum');
Route::get('/academics/textbooks', [WebsiteController::class, 'textbooks'])->name('academics.textbooks');
Route::get('/academics/results', [WebsiteController::class, 'results'])->name('academics.results');
Route::get('/admission', [WebsiteController::class, 'admission'])->name('admission');
// Gallery merged into Student Life — /gallery redirects there.
Route::redirect('/gallery', '/student-life')->name('gallery');
Route::get('/gallery/folder/{galleryFolder}', [WebsiteController::class, 'galleryFolder'])->name('gallery.folder');
Route::get('/student-life', [WebsiteController::class, 'studentLife'])->name('student-life');
Route::get('/news', [WebsiteController::class, 'news'])->name('news');
Route::get('/news/{slug}', [WebsiteController::class, 'newsShow'])->name('news.show');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
Route::get('/hall-of-fame', [WebsiteController::class, 'hallOfFame'])->name('hall-of-fame');

// Legacy redirects
Route::get('/blogs', fn () => redirect()->route('news'));
Route::get('/blogs/{slug}', fn ($slug) => redirect()->route('news.show', $slug))->name('blogs.show');

// Public admission inquiry submission
Route::post('/inquiry', [PublicInquiryController::class, 'store'])
     ->middleware('throttle:5,1')->name('inquiry.store');

// ---------------------------------------------------------------------------
// Breeze auth routes
// ---------------------------------------------------------------------------

Route::get('/dashboard', function () {
    if (Auth::guard('teacher')->check()) {
        return redirect()->route('teacher.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->middleware(['auth:web,teacher'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ---------------------------------------------------------------------------
// Admin routes — requires auth + (admin or staff role)
// ---------------------------------------------------------------------------

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

        // Dashboard — available to every admin-area role
        Route::middleware('role:admin,staff,teacher')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('working-year', [WorkingYearController::class, 'switch'])->name('working-year.switch');
        });

        // ── Classes — teachers see their own, admins see all ──────
        Route::middleware('role:admin,teacher')->group(function () {
            Route::get('classes', [ClassroomController::class, 'index'])
                 ->name('classes.index');
            Route::get('classes/{class}', [ClassroomController::class, 'show'])
                 ->name('classes.show');
        });

        // ── Attendance — admin review/override only ───────────────
        Route::middleware('role:admin')->group(function () {
            Route::get('attendance', [AdminAttendanceController::class, 'index'])
                 ->name('attendance.index');
            Route::put('attendance/{attendance}', [AdminAttendanceController::class, 'update'])
                 ->name('attendance.update');
            Route::get('attendance/analytics', [AdminAttendanceController::class, 'analytics'])
                 ->name('attendance.analytics');
            Route::get('attendance/export', [AdminAttendanceController::class, 'exportCsv'])
                 ->name('attendance.export');
            Route::post('attendance/bulk', [AdminAttendanceController::class, 'bulkStore'])
                 ->name('attendance.bulk');
            Route::post('attendance/approve-day', [AdminAttendanceController::class, 'approveDay'])
                 ->name('attendance.approve-day');
            Route::post('attendance/reject-day', [AdminAttendanceController::class, 'rejectDay'])
                 ->name('attendance.reject-day');
            Route::post('attendance/approve-all', [AdminAttendanceController::class, 'approveAllPending'])
                 ->name('attendance.approve-all');
            Route::post('attendance/backfill-day', [AdminAttendanceController::class, 'backfillDay'])
                 ->name('attendance.backfill-day');

            // Exams & Marks
            Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
            Route::post('exams', [ExamController::class, 'store'])->name('exams.store');
            Route::put('exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
            Route::delete('exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');
            Route::post('exams/{exam}/toggle', [ExamController::class, 'toggle'])->name('exams.toggle');
            Route::get('exams/{exam}/marks-config', [ExamController::class, 'marksConfig'])->name('exams.marks-config');
            Route::post('exams/{exam}/marks-config', [ExamController::class, 'saveMarksConfig'])->name('exams.marks-config.save');

            Route::get('marks', [AdminMarksController::class, 'index'])->name('marks.index');
            Route::put('marks/{mark}', [AdminMarksController::class, 'update'])->name('marks.update');
            Route::get('marks/export', [AdminMarksController::class, 'exportCsv'])->name('marks.export');
            Route::get('marks/export-results', [AdminMarksController::class, 'exportCsvResults'])->name('marks.export-results');
            Route::get('marks/export-result-cards', [AdminMarksController::class, 'exportAllResultCards'])->name('marks.export-result-cards');
            Route::get('marks/bulk-download', [AdminMarksController::class, 'bulkDownload'])->name('marks.bulk-download');
            Route::get('marks/gradesheet', [AdminMarksController::class, 'gradesheet'])->name('marks.gradesheet');
            Route::post('marks/{mark}/reset-submission', [AdminMarksController::class, 'resetSubmission'])->name('marks.reset-submission');
            Route::post('marks/approve-subject', [AdminMarksController::class, 'approveSubject'])->name('marks.approve-subject');
            Route::post('marks/send-back-subject', [AdminMarksController::class, 'sendBackSubject'])->name('marks.send-back-subject');
            Route::post('marks/delete-subject', [AdminMarksController::class, 'deleteSubject'])->name('marks.delete-subject');
            Route::post('marks/delete-class', [AdminMarksController::class, 'deleteClass'])->name('marks.delete-class');
            Route::post('marks/{mark}/approve', [AdminMarksController::class, 'approve'])->name('marks.approve');
            Route::post('marks/{mark}/send-back', [AdminMarksController::class, 'sendBack'])->name('marks.send-back');
            Route::get('marks/analytics', [MarksAnalyticsController::class, 'index'])->name('marks.analytics');
            Route::get('exam-summary', [AdminMarksController::class, 'examSummary'])->name('marks.exam-summary');

            Route::get('questions', [AdminQuestionsController::class, 'index'])->name('questions.index');
            Route::get('questions/export', [AdminQuestionsController::class, 'export'])->name('questions.export');
            // Literal routes BEFORE parameterized {question} routes
            Route::get('questions/bulk/approve', fn () => redirect()->route('admin.questions.index')->with('error', 'Use the "Approve Paper" button on the Questions page.'))
                 ->name('questions.bulk-approve-get');
            Route::post('questions/bulk/approve', [AdminQuestionsController::class, 'approveGroup'])->name('questions.bulk-approve');
            Route::post('questions/bulk/revision', [AdminQuestionsController::class, 'requestRevisionGroup'])->name('questions.bulk-revision');
            Route::get('questions/question-paper', [AdminQuestionsController::class, 'exportQuestionPaper'])->name('questions.question-paper');
            Route::post('questions/{question}/approve', [AdminQuestionsController::class, 'approve'])->name('questions.approve');
            Route::post('questions/{question}/revision', [AdminQuestionsController::class, 'requestRevision'])->name('questions.revision');
            Route::post('questions/{question}/review', [AdminQuestionsController::class, 'markReviewed'])->name('questions.review');
            Route::delete('questions/{question}', [AdminQuestionsController::class, 'destroy'])->name('questions.destroy');

            // Notes & Assignments — admin review
            Route::get('notes', [AdminNotesController::class, 'index'])->name('notes.index');
            Route::delete('notes/{note}', [AdminNotesController::class, 'destroy'])->name('notes.destroy');
        });

        // ── Staff + admin area ────────────────────────────────────
        Route::middleware('role:admin,staff')->group(function () {

        // Students
        Route::get('students/import', [StudentController::class, 'importForm'])
             ->name('students.import.form');
        Route::post('students/import', [StudentController::class, 'import'])
             ->name('students.import');
        Route::get('students/import-template', [StudentController::class, 'importTemplate'])
             ->name('students.import.template');
        Route::get('students/export', [StudentController::class, 'export'])
             ->name('students.export');
        Route::get('students/export-class', [StudentController::class, 'exportClass'])
             ->name('students.export-class');
        Route::get('students/export-class-csv', [StudentController::class, 'exportClassCsv'])
             ->name('students.export-class-csv');
        Route::get('students/download-class-docs', [StudentController::class, 'downloadClassDocs'])
             ->name('students.download-class-docs');
        Route::get('students/suggest-roll', [StudentController::class, 'suggestRoll'])
             ->name('students.suggest-roll');
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/passport', [StudentController::class, 'passport'])
             ->name('students.passport');
        Route::get('students/{student}/result-card/{exam}', [ResultCardController::class, 'download'])
             ->name('students.result-card');
        Route::get('students/{student}/report-card/full-year', [ResultCardController::class, 'fullYear'])
             ->name('students.report-card.full-year');
        Route::get('students/{student}/icard', [ICardController::class, 'download'])
             ->name('students.icard');
        Route::get('students/icards/batch', [ICardController::class, 'batchDownload'])
             ->name('students.icard.batch');

        // Student files
        Route::post('students/{student}/files', [StudentFileController::class, 'store'])
             ->name('student-files.store');
        Route::delete('student-files/{studentFile}', [StudentFileController::class, 'destroy'])
             ->name('student-files.destroy');
        Route::get('student-files/{studentFile}/download', [StudentFileController::class, 'download'])
             ->name('student-files.download');

        // Folders
        Route::resource('folders', FolderController::class);

        // Folder file browser
        Route::get('folders/{folder}/files', [FolderController::class, 'show'])
             ->name('folders.files');

        // Documents (files inside folders)
        Route::post('folders/{folder}/files', [DocumentController::class, 'store'])
             ->name('documents.store');
        Route::delete('files/{document}', [DocumentController::class, 'destroy'])
             ->name('documents.destroy');
        Route::get('files/{document}/download', [DocumentController::class, 'download'])
             ->name('documents.download');

        // Blogs
        Route::resource('blogs', BlogController::class);
        Route::post('blogs/{blog}/toggle-publish', [BlogController::class, 'togglePublish'])
             ->name('blogs.toggle-publish');

        // Events
        Route::resource('events', EventController::class);

        // Gallery
        Route::resource('gallery', GalleryController::class)->only(['index', 'store', 'destroy']);

        // Gallery Folders
        Route::resource('gallery-folders', GalleryFolderController::class);
        Route::post('gallery-folders/{galleryFolder}/images', [GalleryFolderController::class, 'storeImages'])
             ->name('gallery-folders.images.store');
        Route::delete('gallery-images/{galleryItem}', [GalleryFolderController::class, 'destroyImage'])
             ->name('gallery-folders.images.destroy');

        // Downloads (notices, circulars, forms)
        Route::resource('downloads', DownloadController::class)
             ->except(['show'])->names('downloads');

        // Important Links (link CRUD)
        Route::resource('important-links', ImportantLinkController::class)
             ->except(['show'])->names('important-links');

        // Inquiries (admission enquiries from the public site)
        Route::prefix('inquiries')->name('inquiries.')->group(function () {
            Route::get('/', [InquiryController::class, 'index'])->name('index');
            Route::get('{inquiry}', [InquiryController::class, 'show'])->name('show');
            Route::patch('{inquiry}/mark-replied', [InquiryController::class, 'markReplied'])->name('markReplied');
            Route::delete('{inquiry}', [InquiryController::class, 'destroy'])->name('destroy');
        });

        // Videos
        Route::get('videos', [VideoController::class, 'index'])->name('videos.index');
        Route::post('videos', [VideoController::class, 'store'])->name('videos.store');
        Route::post('videos/{video}/toggle', [VideoController::class, 'toggle'])->name('videos.toggle');
        Route::delete('videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
        Route::get('videos/{video}/edit', [VideoController::class, 'edit'])->name('videos.edit');
        Route::put('videos/{video}', [VideoController::class, 'update'])->name('videos.update');

        // Activity Logs (read-only)
        Route::get('activity-logs', [ActivityLogController::class, 'index'])
             ->name('activity-logs.index');

        // Site Customizer — admin only
        Route::middleware('role:admin')->group(function () {
            Route::get('customizer', [SiteCustomizerController::class, 'index'])
                 ->name('customizer.index');
            Route::post('customizer/save', [SiteCustomizerController::class, 'save'])
                 ->name('customizer.save');
            Route::post('customizer/toggle-section', [SiteCustomizerController::class, 'toggleSection'])
                 ->name('customizer.toggle-section');
            Route::post('customizer/reorder-sections', [SiteCustomizerController::class, 'reorderSections'])
                 ->name('customizer.reorder-sections');
            Route::post('customizer/admission', [SiteCustomizerController::class, 'saveAdmission'])
                 ->name('customizer.admission.save');
            Route::post('customizer/certificates', [SiteCustomizerController::class, 'saveCertificates'])
                 ->name('customizer.certificates.save');
            Route::post('customizer/people', [SiteCustomizerController::class, 'savePeople'])
                 ->name('customizer.people.save');
            Route::post('customizer/calendar', [SiteCustomizerController::class, 'saveCalendar'])
                 ->name('customizer.calendar.save');
            Route::post('customizer/curriculum', [SiteCustomizerController::class, 'saveCurriculum'])
                 ->name('customizer.curriculum.save');
            Route::post('customizer/slides', [SiteCustomizerController::class, 'storeSlide'])
                 ->name('customizer.slides.store');
            Route::post('customizer/slides/{slide}/toggle', [SiteCustomizerController::class, 'toggleSlide'])
                 ->name('customizer.slides.toggle');
            Route::delete('customizer/slides/{slide}', [SiteCustomizerController::class, 'destroySlide'])
                 ->name('customizer.slides.destroy');

            // Hall of Fame — admin only
            Route::get('hall-of-fame', [HallOfFameController::class, 'index'])
                 ->name('hall-of-fame.index');
            Route::get('hall-of-fame/create', [HallOfFameController::class, 'create'])
                 ->name('hall-of-fame.create');
            Route::post('hall-of-fame', [HallOfFameController::class, 'store'])
                 ->name('hall-of-fame.store');
            Route::get('hall-of-fame/{hallOfFame}/edit', [HallOfFameController::class, 'edit'])
                 ->name('hall-of-fame.edit');
            Route::put('hall-of-fame/{hallOfFame}', [HallOfFameController::class, 'update'])
                 ->name('hall-of-fame.update');
            Route::delete('hall-of-fame/{hallOfFame}', [HallOfFameController::class, 'destroy'])
                 ->name('hall-of-fame.destroy');
            Route::post('hall-of-fame/{hallOfFame}/toggle', [HallOfFameController::class, 'toggle'])
                 ->name('hall-of-fame.toggle');
            Route::post('hall-of-fame/reorder', [HallOfFameController::class, 'reorder'])
                 ->name('hall-of-fame.reorder');
        });

        // Subjects — admin only
        Route::middleware('role:admin')->group(function () {
            Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
            Route::post('subjects', [SubjectController::class, 'store'])->name('subjects.store');
            Route::put('subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
            Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
            Route::post('subjects/{subject}/toggle', [SubjectController::class, 'toggle'])->name('subjects.toggle');
            Route::post('subjects/reorder', [SubjectController::class, 'reorder'])->name('subjects.reorder');

            // Class Subjects
            Route::get('class-subjects', [\App\Http\Controllers\Admin\ClassSubjectController::class, 'index'])
                 ->name('class-subjects.index');
            Route::post('class-subjects', [\App\Http\Controllers\Admin\ClassSubjectController::class, 'store'])
                 ->name('class-subjects.store');
            Route::post('class-subjects/{classSubject}/config', [\App\Http\Controllers\Admin\ClassSubjectController::class, 'updateConfig'])
                 ->name('class-subjects.config');
            Route::get('class-subjects/copy', [\App\Http\Controllers\Admin\ClassSubjectController::class, 'copy'])
                 ->name('class-subjects.copy');

            // Grade Scale
            Route::get('grade-scales', [GradeScaleController::class, 'index'])->name('grade-scales.index');
            Route::post('grade-scales', [GradeScaleController::class, 'store'])->name('grade-scales.store');
            Route::put('grade-scales/{gradeScale}', [GradeScaleController::class, 'update'])->name('grade-scales.update');
            Route::delete('grade-scales/{gradeScale}', [GradeScaleController::class, 'destroy'])->name('grade-scales.destroy');
            Route::post('grade-scales/{gradeScale}/toggle', [GradeScaleController::class, 'toggle'])->name('grade-scales.toggle');
            Route::post('grade-scales/seed', [GradeScaleController::class, 'seed'])->name('grade-scales.seed');

            // Division Rules
            Route::post('division-rules', [\App\Http\Controllers\Admin\DivisionRuleController::class, 'store'])->name('division-rules.store');
            Route::put('division-rules/{divisionRule}', [\App\Http\Controllers\Admin\DivisionRuleController::class, 'update'])->name('division-rules.update');
            Route::delete('division-rules/{divisionRule}', [\App\Http\Controllers\Admin\DivisionRuleController::class, 'destroy'])->name('division-rules.destroy');
            Route::post('division-rules/{divisionRule}/toggle', [\App\Http\Controllers\Admin\DivisionRuleController::class, 'toggle'])->name('division-rules.toggle');
        });

        // Sections — admin only
        Route::middleware('role:admin')->group(function () {
            Route::get('sections', [SectionController::class, 'index'])->name('sections.index');
            Route::post('sections', [SectionController::class, 'store'])->name('sections.store');
            Route::delete('sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
        });

        // Academic Years — admin only
        Route::middleware('role:admin')->group(function () {
            Route::get('academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
            Route::post('academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
            Route::put('academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
            Route::delete('academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
            Route::post('academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');
            Route::get('academic-years/{academicYear}/promote', [AcademicYearController::class, 'promoteForm'])->name('academic-years.promote.form');
            Route::post('academic-years/{academicYear}/promote', [AcademicYearController::class, 'promote'])->name('academic-years.promote');
            Route::get('academic-years/{academicYear}/students', [AcademicYearController::class, 'students'])->name('academic-years.students');

            // Promotion rules
            Route::put('academic-years/{academicYear}/promotion-rules', [PromotionRuleController::class, 'update'])->name('promotion-rules.update');

            // Assign roll numbers
            Route::get('academic-years/{academicYear}/assign-rolls', [AcademicYearController::class, 'assignRollsForm'])->name('academic-years.assign-rolls');
            Route::post('academic-years/{academicYear}/assign-rolls', [AcademicYearController::class, 'assignRolls'])->name('academic-years.assign-rolls.save');

            // Enrollment status change (from student profile)
            Route::patch('students/{student}/enrollment-status', [StudentController::class, 'updateEnrollmentStatus'])->name('students.enrollment-status');
        });

        // School Holidays — admin only
        Route::get('school-holidays', [\App\Http\Controllers\Admin\SchoolHolidayController::class, 'index'])
             ->name('school-holidays.index');
        Route::post('school-holidays', [\App\Http\Controllers\Admin\SchoolHolidayController::class, 'store'])
             ->name('school-holidays.store');
        Route::delete('school-holidays/{schoolHoliday}', [\App\Http\Controllers\Admin\SchoolHolidayController::class, 'destroy'])
             ->name('school-holidays.destroy');

        // Pages — admin only
        Route::middleware('role:admin')->group(function () {
            Route::get('pages', [PageController::class, 'index'])->name('pages.index');
            Route::get('pages/create', [PageController::class, 'create'])->name('pages.create');
            Route::post('pages', [PageController::class, 'store'])->name('pages.store');
            Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
            Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');
            Route::delete('pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');

            // Users — admin only
            Route::resource('users', UserController::class);

            // Teachers — admin only
            Route::resource('teachers', TeacherController::class);
            Route::post('teachers/{teacher}/create-login', [TeacherController::class, 'createLogin'])
                 ->name('teachers.create-login');
            Route::post('teachers/{teacher}/link-user', [TeacherController::class, 'linkUser'])
                 ->name('teachers.link-user');
            Route::post('teachers/{teacher}/unlink-user/{user}', [TeacherController::class, 'unlinkUser'])
                 ->name('teachers.unlink-user');
            Route::post('teachers/{teacher}/year-status', [TeacherController::class, 'updateYearStatus'])
                 ->name('teachers.year-status');

            Route::redirect('teacher-assignments', '/admin/teachers', 301)
                 ->name('teacher-assignments.index');
            Route::post('teacher-assignments/class', [TeacherAssignmentController::class, 'storeClassTeacher'])
                 ->name('teacher-assignments.class.store');
            Route::delete('teacher-assignments/class/{assignment}', [TeacherAssignmentController::class, 'destroyClassTeacher'])
                 ->name('teacher-assignments.class.destroy');
            Route::post('teacher-assignments/subject', [TeacherAssignmentController::class, 'storeSubjectTeacher'])
                 ->name('teacher-assignments.subject.store');
            Route::delete('teacher-assignments/subject/{assignment}', [TeacherAssignmentController::class, 'destroySubjectTeacher'])
                 ->name('teacher-assignments.subject.destroy');
        });

        }); // end of role:admin,staff group
    });

// ---------------------------------------------------------------------------
// Teacher mobile portal — lightweight, mobile-first
// ---------------------------------------------------------------------------

Route::prefix('teacher')
    ->name('teacher.')
    ->middleware(['auth:teacher', 'role:teacher,admin'])
    ->group(function () {
        Route::get('/',         [TeacherPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/classes',  [TeacherPortalController::class, 'classes'])->name('classes');
        Route::get('/classes/{class}', [TeacherPortalController::class, 'showClass'])->name('classes.show');
        Route::get('/subjects', [TeacherPortalController::class, 'subjects'])->name('subjects');

        // Attendance
        Route::get('/attendance', [TeacherAttendanceController::class, 'index'])
             ->name('attendance.index');
        Route::get('/attendance/{class}/{section}', [TeacherAttendanceController::class, 'mark'])
             ->name('attendance.mark');
        Route::post('/attendance/{class}/{section}', [TeacherAttendanceController::class, 'store'])
             ->name('attendance.store');

        // Marks
        Route::get('/marks', [TeacherMarksController::class, 'index'])->name('marks.index');
        Route::get('/marks/{exam}/{class}/{section}/{subject}', [TeacherMarksController::class, 'sheet'])
             ->name('marks.sheet');
        Route::post('/marks/{exam}/{class}/{section}/{subject}', [TeacherMarksController::class, 'store'])
             ->name('marks.store');

        // Questions
        Route::get('/questions', [TeacherQuestionsController::class, 'index'])->name('questions.index');
        Route::post('/questions', [TeacherQuestionsController::class, 'store'])->name('questions.store');
        Route::post('/questions/{question}', [TeacherQuestionsController::class, 'update'])->name('questions.update');
        Route::delete('/questions/{question}', [TeacherQuestionsController::class, 'destroy'])->name('questions.destroy');

        // Notes & Assignments
        Route::get('/notes', [TeacherNotesController::class, 'index'])->name('notes.index');
        Route::post('/notes', [TeacherNotesController::class, 'store'])->name('notes.store');
        Route::delete('/notes/{note}', [TeacherNotesController::class, 'destroy'])->name('notes.destroy');

        // Teacher-specific profile & logout (uses teacher session)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
