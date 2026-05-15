<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    public function index(Request $request)
    {
        $sortBy  = $request->input('sort', 'name');
        $sortDir = $request->input('dir', 'asc');

        $folders = Folder::whereNull('parent_id')
                         ->with('children')
                         ->when($sortBy === 'name', fn($q) => $q->orderBy('name', $sortDir))
                         ->when($sortBy === 'date', fn($q) => $q->orderBy('created_at', $sortDir))
                         ->get();

        return view('admin.folders.index', compact('folders', 'sortBy', 'sortDir'));
    }

    public function show(Request $request, Folder $folder)
    {
        $folder->load(['children', 'files']);
        $subfolders = $folder->children()->orderBy('name')->get();

        $sortBy  = $request->input('sort', 'date');
        $sortDir = $request->input('dir', 'desc');

        $documents = $folder->files()
            ->when($sortBy === 'name', fn($q) => $q->orderBy('original_name', $sortDir))
            ->when($sortBy === 'size', fn($q) => $q->orderBy('size', $sortDir))
            ->when($sortBy === 'date', fn($q) => $q->orderBy('created_at', $sortDir))
            ->get();

        return view('admin.folders.show', compact('folder', 'subfolders', 'documents', 'sortBy', 'sortDir'));
    }

    public function create(Request $request)
    {
        $parentId = $request->input('parent_id');
        $parent   = $parentId ? Folder::findOrFail($parentId) : null;

        return view('admin.folders.create', compact('parent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:folders,id'],
        ]);

        $validated['created_by'] = auth()->id();
        $folder = Folder::create($validated);

        ActivityLogger::log('folder_created', $folder, "Created folder: {$folder->name}");

        if ($folder->parent_id) {
            return redirect()->route('admin.folders.show', $folder->parent_id)
                             ->with('success', 'Folder created successfully.');
        }

        return redirect()->route('admin.folders.index')
                         ->with('success', 'Folder created successfully.');
    }

    public function edit(Folder $folder)
    {
        return view('admin.folders.edit', compact('folder'));
    }

    public function update(Request $request, Folder $folder)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $folder->update($validated);

        ActivityLogger::log('folder_updated', $folder, "Updated folder: {$folder->name}");

        return redirect()->route('admin.folders.index')
                         ->with('success', 'Folder updated successfully.');
    }

    public function destroy(Folder $folder)
    {
        $name = $folder->name;

        $this->deleteFolderRecursively($folder);

        ActivityLogger::log('folder_deleted', null, "Deleted folder: {$name}");

        return redirect()->route('admin.folders.index')
                         ->with('success', 'Folder deleted successfully.');
    }

    private function deleteFolderRecursively(Folder $folder): void
    {
        // Delete all documents in this folder from storage
        foreach ($folder->files as $document) {
            Storage::disk('local')->delete($document->path);
            $document->delete();
        }

        // Recurse into children
        foreach ($folder->children as $child) {
            $this->deleteFolderRecursively($child);
        }

        // Delete the storage directory for this folder
        Storage::disk('local')->deleteDirectory("folders/{$folder->id}");

        $folder->delete();
    }
}
