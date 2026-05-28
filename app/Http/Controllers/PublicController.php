<?php

namespace App\Http\Controllers;

use App\Models\BannerSlide;
use App\Models\Blog;
use App\Models\Video;
use App\Models\Event;
use App\Models\GalleryFolder;
use App\Models\GalleryItem;
use App\Models\Student;
use App\Models\Page;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $slides = BannerSlide::where('active', true)
                             ->orderBy('sort_order')
                             ->get();

        $events = Event::where('starts_at', '>=', now())
                       ->orderBy('starts_at')
                       ->take(3)
                       ->get();

        $blogs = Blog::where('published', true)
                     ->orderByDesc('created_at')
                     ->take(3)
                     ->get();

        $stats = [
            'members' => Student::count(),
            'events'  => Event::count(),
            'blogs'   => Blog::where('published', true)->count(),
        ];

        $galleryFolders = GalleryFolder::where('type', 'gallery')
                                       ->withCount('items')
                                       ->with(['previewImages'])
                                       ->orderBy('sort_order')
                                       ->orderByDesc('created_at')
                                       ->take(6)
                                       ->get();

        $videos = Video::active()->orderBy('sort_order')->orderByDesc('created_at')->take(6)->get();

        $programFolders = GalleryFolder::where('type', 'programs')
                                    ->withCount('items')
                                    ->with(['previewImages'])
                                    ->orderBy('sort_order')
                                    ->orderByDesc('created_at')
                                    ->take(8)
                                    ->get();

        return view('public.home', compact('slides', 'events', 'blogs', 'stats', 'galleryFolders', 'videos', 'programFolders'));
    }

    public function about()
    {
        $page = Page::where('slug', 'about')->firstOrFail();

        return view('public.about', compact('page'));
    }

    public function blogs()
    {
        $blogs = Blog::where('published', true)
                     ->orderByDesc('created_at')
                     ->paginate(9);

        return view('public.blogs', compact('blogs'));
    }

    public function blogShow(string $slug)
    {
        $blog = Blog::where('slug', $slug)
                    ->where('published', true)
                    ->firstOrFail();

        return view('public.blog-show', compact('blog'));
    }

    public function events()
    {
        $events = Event::where('starts_at', '>=', now())
                       ->orderBy('starts_at')
                       ->paginate(12);

        return view('public.events', compact('events'));
    }

    public function gallery()
    {
        $folders = GalleryFolder::where('type', 'gallery')
                                ->withCount('items')
                                ->with(['previewImages'])
                                ->orderBy('sort_order')
                                ->orderByDesc('created_at')
                                ->get();

        // Images not assigned to any folder
        $images = GalleryItem::whereNull('gallery_folder_id')
                             ->orderByDesc('created_at')
                             ->paginate(24);

        return view('public.gallery', compact('folders', 'images'));
    }

    public function galleryFolder(GalleryFolder $galleryFolder)
    {
        $images = $galleryFolder->items()->orderByDesc('created_at')->paginate(24);

        return view('public.gallery-folder', compact('galleryFolder', 'images'));
    }

    public function page(string $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return view('public.page', compact('page'));
    }

    public function videos()
    {
        $videos = Video::active()->orderBy('sort_order')->orderByDesc('created_at')->paginate(12);
        return view('public.videos', compact('videos'));
    }
}
