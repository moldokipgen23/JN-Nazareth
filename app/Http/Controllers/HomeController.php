<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\GalleryFolder;
use App\Models\GalleryItem;

class HomeController extends Controller
{
    public function index()
    {
        $galleryFolderIds = GalleryFolder::gallery()->pluck('id');

        $galleryImages = GalleryItem::whereIn('gallery_folder_id', $galleryFolderIds)
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $latestNews = Blog::where('published', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('website.home', compact('galleryImages', 'latestNews'));
    }
}
