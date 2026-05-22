<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Download;
use App\Models\GalleryFolder;
use App\Models\GalleryItem;
use App\Models\HallOfFame;
use App\Models\ImportantLink;
use App\Models\Video;

class WebsiteController extends Controller
{
    /** Homepage — all sections. */
    public function home()
    {
        $galleryFolderIds = GalleryFolder::programs()->pluck('id');

        $galleryImages = GalleryItem::whereIn('gallery_folder_id', $galleryFolderIds)
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $latestNews = Blog::where('published', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $hallOfFame = HallOfFame::active()->featured()->ordered()->take(6)->get();

        $notices = Download::published()
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $importantLinks = ImportantLink::published()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('website.home', compact(
            'galleryImages', 'latestNews', 'hallOfFame', 'notices', 'importantLinks'
        ));
    }

    /** Hall of Fame — all active achievers. */
    public function hallOfFame()
    {
        $achievers = HallOfFame::active()->ordered()->get();

        return view('website.hall-of-fame', compact('achievers'));
    }

    /** About Us page. */
    public function about()
    {
        return view('website.about');
    }

    /** Academics page. */
    public function academics()
    {
        return view('website.academics');
    }

    /** Academics → Fee Structure sub-page. */
    public function feeStructure()
    {
        return view('website.academics.fee-structure');
    }

    /** Academics → School Timing sub-page. */
    public function schoolTiming()
    {
        return view('website.academics.school-timing');
    }

    /** Academics → Academic Calendar sub-page. */
    public function academicCalendar()
    {
        return view('website.academics.calendar');
    }

    /** Academics → Curriculum sub-page. */
    public function curriculum()
    {
        return view('website.academics.curriculum');
    }

    /** Academics → Prescribed Textbooks sub-page (Downloads module). */
    public function textbooks()
    {
        $downloads = Download::published()
            ->whereIn('category', ['Textbook', 'Syllabus'])
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return view('website.academics.textbooks', compact('downloads'));
    }

    /** Academics → Results sub-page (Downloads module). */
    public function results()
    {
        $downloads = Download::published()
            ->where('category', 'Result')
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return view('website.academics.results', compact('downloads'));
    }

    /** Admission page. */
    public function admission()
    {
        abort_if(setting('sec_show_admission_page', '1') === '0', 404);

        return view('website.admission');
    }

    /** Gallery page — all gallery folders + their images. */
    public function gallery()
    {
        $folders = GalleryFolder::gallery()
            ->withCount('items')
            ->with('previewImages')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $folderIds = $folders->pluck('id');

        $images = GalleryItem::whereIn('gallery_folder_id', $folderIds)
            ->orderByDesc('created_at')
            ->get();

        return view('website.gallery', compact('folders', 'images'));
    }

    /** Single gallery folder. */
    public function galleryFolder(GalleryFolder $galleryFolder)
    {
        $images = $galleryFolder->items()->orderByDesc('created_at')->get();

        return view('website.gallery-folder', compact('galleryFolder', 'images'));
    }

    /** Student Life page — programme/student-life albums + videos. */
    public function studentLife()
    {
        abort_if(setting('sec_show_student_life_page', '1') === '0', 404);

        $folders = GalleryFolder::programs()
            ->withCount('items')
            ->with('previewImages')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $videos = Video::active()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('website.student-life', compact('folders', 'videos'));
    }

    /** News & Notices page — published posts + downloads. */
    public function news()
    {
        $news = Blog::where('published', true)
            ->orderByDesc('published_at')
            ->paginate(9);

        $downloads = Download::published()
            ->orderByDesc('created_at')
            ->get();

        return view('website.news', compact('news', 'downloads'));
    }

    /** Single news post. */
    public function newsShow(string $slug)
    {
        $post = Blog::where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        $related = Blog::where('published', true)
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('website.news-show', compact('post', 'related'));
    }

    /** Contact page. */
    public function contact()
    {
        return view('website.contact');
    }
}
