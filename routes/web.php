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
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberFileController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\HallOfFameController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\SiteCustomizerController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\InquiryController as PublicInquiryController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Public routes
// ---------------------------------------------------------------------------

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap']);
Route::get('/robots.txt',  [SitemapController::class, 'robots']);

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
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
        });

        // ── Classes — teachers see their own, admins see all ──────
        Route::middleware('role:admin,teacher')->group(function () {
            Route::get('classes', [ClassroomController::class, 'index'])
                 ->name('classes.index');
            Route::get('classes/{class}', [ClassroomController::class, 'show'])
                 ->name('classes.show');
        });

        // ── Staff + admin area ────────────────────────────────────
        Route::middleware('role:admin,staff')->group(function () {

        // Members
        Route::get('members/import', [MemberController::class, 'importForm'])
             ->name('members.import.form');
        Route::post('members/import', [MemberController::class, 'import'])
             ->name('members.import');
        Route::get('members/import-template', [MemberController::class, 'importTemplate'])
             ->name('members.import.template');
        Route::resource('members', MemberController::class);

        // Member files
        Route::post('members/{member}/files', [MemberFileController::class, 'store'])
             ->name('member-files.store');
        Route::delete('member-files/{memberFile}', [MemberFileController::class, 'destroy'])
             ->name('member-files.destroy');
        Route::get('member-files/{memberFile}/download', [MemberFileController::class, 'download'])
             ->name('member-files.download');

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
            Route::resource('teachers', TeacherController::class)
                 ->except(['show']);
        });

        }); // end of role:admin,staff group
    });
