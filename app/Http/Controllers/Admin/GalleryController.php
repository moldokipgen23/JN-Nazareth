<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $images = GalleryItem::orderByDesc('created_at')->paginate(24);

        return view('admin.gallery.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images'    => ['required', 'array', 'min:1'],
            'images.*'  => ['image', 'max:5120'],
            'title'     => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('gallery', 'public');

            $item = GalleryItem::create([
                'path'        => $path,
                'title'       => $request->input('title'),
                'uploaded_by' => auth()->id(),
            ]);
        }

        ActivityLogger::log('gallery_item_uploaded', null, "Uploaded " . count($request->file('images')) . " gallery image(s).");

        return redirect()->route('admin.gallery.index')
                         ->with('success', count($request->file('images')) . ' image(s) uploaded successfully.');
    }

    public function destroy(GalleryItem $gallery)
    {
        Storage::disk('public')->delete($gallery->path);

        $gallery->delete();

        ActivityLogger::log('gallery_item_deleted', null, "Deleted gallery image.");

        return redirect()->route('admin.gallery.index')
                         ->with('success', 'Image deleted successfully.');
    }
}
