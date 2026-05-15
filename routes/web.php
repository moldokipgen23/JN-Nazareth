<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ProgramItemController;
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
use App\Http\Controllers\Admin\SiteCustomizerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Public routes
// ---------------------------------------------------------------------------

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap']);
Route::get('/robots.txt',  [SitemapController::class, 'robots']);

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/blogs', [PublicController::class, 'blogs'])->name('blogs');
Route::get('/blogs/{slug}', [PublicController::class, 'blogShow'])->name('blogs.show');
Route::get('/events', [PublicController::class, 'events'])->name('events');
Route::get('/gallery', [PublicController::class, 'gallery'])->name('gallery');
Route::get('/gallery/folder/{galleryFolder}', [PublicController::class, 'galleryFolder'])->name('gallery.folder');
Route::get('/videos', [PublicController::class, 'videos'])->name('videos');
Route::get('/page/{slug}', [PublicController::class, 'page'])->name('page');

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
    ->middleware(['auth', 'role:admin,staff'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Members
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

        // Programs (community activity photos)
        Route::get('programs', [ProgramItemController::class, 'index'])->name('programs.index');
        Route::post('programs', [ProgramItemController::class, 'store'])->name('programs.store');
        Route::post('programs/{programItem}/toggle', [ProgramItemController::class, 'toggle'])->name('programs.toggle');
        Route::delete('programs/{programItem}', [ProgramItemController::class, 'destroy'])->name('programs.destroy');

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
            Route::post('customizer/general', [SiteCustomizerController::class, 'updateGeneral'])
                 ->name('customizer.update-general');
            Route::post('customizer/appearance', [SiteCustomizerController::class, 'updateAppearance'])
                 ->name('customizer.update-appearance');
            Route::post('customizer/seo', [SiteCustomizerController::class, 'updateSeo'])
                 ->name('customizer.update-seo');
            Route::post('customizer/hero', [SiteCustomizerController::class, 'updateHero'])
                 ->name('customizer.update-hero');
            Route::post('customizer/story', [SiteCustomizerController::class, 'updateStory'])
                 ->name('customizer.update-story');
            Route::post('customizer/sections', [SiteCustomizerController::class, 'updateSections'])
                 ->name('customizer.update-sections');
            Route::post('customizer/section-order', [SiteCustomizerController::class, 'updateSectionOrder'])
                 ->name('customizer.update-section-order');
            Route::post('customizer/toggle-section', [SiteCustomizerController::class, 'toggleSection'])
                 ->name('customizer.toggle-section');
            Route::post('customizer/stats', [SiteCustomizerController::class, 'updateStats'])
                 ->name('customizer.update-stats');
            Route::post('customizer/location', [SiteCustomizerController::class, 'updateLocation'])
                 ->name('customizer.update-location');
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
        });
    });
