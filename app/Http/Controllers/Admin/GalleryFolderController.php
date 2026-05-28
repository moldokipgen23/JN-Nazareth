<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\GalleryFolder;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryFolderController extends Controller
{
    // ── List folders by type ────────────────────────────────

    public function index(Request $request)
    {
        $type    = 'programs';
        $folders = GalleryFolder::where('type', $type)
                                ->withCount('items')
                                ->with(['previewImages'])
                                ->orderBy('sort_order')
                                ->orderByDesc('created_at')
                                ->get();

        return view('admin.gallery-folders.index', compact('folders', 'type'));
    }

    // ── Create ──────────────────────────────────────────────

    public function create(Request $request)
    {
        $type = 'programs';
        return view('admin.gallery-folders.create', compact('type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:gallery,programs',
            'description' => 'nullable|string|max:1000',
            'sort_order'  => 'nullable|integer|min:0',
            'cover_image' => 'nullable|image|max:102400',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('gallery-covers', 'public');
        }

        $validated['created_by'] = auth()->id();
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $folder = GalleryFolder::create($validated);

        ActivityLogger::log('gallery_folder_created', $folder, "Created {$folder->type} folder: {$folder->name}");

        return redirect()->route('admin.gallery-folders.show', $folder)
                         ->with('success', 'Folder "' . $folder->name . '" created.');
    }

    // ── Show / Upload ────────────────────────────────────────

    public function show(GalleryFolder $galleryFolder)
    {
        $images = $galleryFolder->items()
                                ->orderBy('created_at')
                                ->paginate(36);

        return view('admin.gallery-folders.show', [
            'folder' => $galleryFolder,
            'images' => $images,
        ]);
    }

    // ── Edit / Update ────────────────────────────────────────

    public function edit(GalleryFolder $galleryFolder)
    {
        return view('admin.gallery-folders.edit', ['folder' => $galleryFolder]);
    }

    public function update(Request $request, GalleryFolder $galleryFolder)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order'  => 'nullable|integer|min:0',
            'cover_image' => 'nullable|image|max:102400',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($galleryFolder->cover_image) {
                Storage::disk('public')->delete($galleryFolder->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')
                ->store('gallery-covers', 'public');
        } else {
            unset($validated['cover_image']);
        }

        // Re-generate slug on name change
        $validated['slug'] = null;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $galleryFolder->update($validated);

        ActivityLogger::log('gallery_folder_updated', $galleryFolder, "Updated folder: {$galleryFolder->name}");

        return redirect()->route('admin.gallery-folders.index', ['type' => $galleryFolder->type])
                         ->with('success', 'Folder updated.');
    }

    // ── Delete ───────────────────────────────────────────────

    public function destroy(GalleryFolder $galleryFolder)
    {
        foreach ($galleryFolder->items as $item) {
            Storage::disk('public')->delete($item->path);
            $item->delete();
        }

        if ($galleryFolder->cover_image) {
            Storage::disk('public')->delete($galleryFolder->cover_image);
        }

        $name = $galleryFolder->name;
        $type = $galleryFolder->type;
        $galleryFolder->delete();

        ActivityLogger::log('gallery_folder_deleted', null, "Deleted folder: {$name}");

        return redirect()->route('admin.gallery-folders.index', ['type' => $type])
                         ->with('success', "Folder \"{$name}\" deleted.");
    }

    // ── Upload images into folder ────────────────────────────

    public function storeImages(Request $request, GalleryFolder $galleryFolder)
    {
        $request->validate([
            'images'   => 'required|array|min:1',
            'images.*' => 'required|image|max:102400',
            'caption'  => 'nullable|string|max:255',
        ]);

        $uploaded = 0;
        foreach ($request->file('images') as $file) {
            $path = $file->store('gallery', 'public');
            GalleryItem::create([
                'gallery_folder_id' => $galleryFolder->id,
                'path'              => $path,
                'caption'           => $request->input('caption'),
                'uploaded_by'       => auth()->id(),
            ]);
            $uploaded++;
        }

        // Auto-set cover from first image if not set
        if (!$galleryFolder->cover_image) {
            $first = $galleryFolder->items()->oldest()->first();
            if ($first) {
                $galleryFolder->update(['cover_image' => $first->path]);
            }
        }

        ActivityLogger::log('gallery_item_uploaded', $galleryFolder,
            "Uploaded {$uploaded} image(s) to folder: {$galleryFolder->name}");

        return back()->with('success', "{$uploaded} image(s) uploaded.");
    }

    // ── Delete single image ──────────────────────────────────

    public function destroyImage(GalleryItem $galleryItem)
    {
        Storage::disk('public')->delete($galleryItem->path);
        $folder = $galleryItem->folder;
        $galleryItem->delete();

        // Reset cover if it was this image
        if ($folder && $folder->cover_image === $galleryItem->path) {
            $next = $folder->items()->oldest()->first();
            $folder->update(['cover_image' => $next?->path]);
        }

        return back()->with('success', 'Image deleted.');
    }
}
