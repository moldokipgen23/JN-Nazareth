<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');

        $query = Download::query();
        if ($category && in_array($category, Download::CATEGORIES, true)) {
            $query->where('category', $category);
        }

        $downloads = $query->orderBy('sort_order')
                           ->orderByDesc('created_at')
                           ->paginate(15)
                           ->withQueryString();

        $categories = Download::CATEGORIES;

        return view('admin.downloads.index', compact('downloads', 'categories', 'category'));
    }

    public function create()
    {
        $categories = Download::CATEGORIES;

        return view('admin.downloads.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $file = $request->file('file');
        $validated['file_path'] = $file->store('downloads', 'public');
        $validated['file_type'] = strtolower($file->getClientOriginalExtension());
        $validated['file_size'] = $this->humanSize($file->getSize());
        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['uploaded_by'] = auth()->id();
        unset($validated['file']);

        $download = Download::create($validated);

        ActivityLogger::log('download_created', $download, "Uploaded download: {$download->title}");

        return redirect()->route('admin.downloads.index')
                         ->with('success', 'Download uploaded successfully.');
    }

    public function edit(Download $download)
    {
        $categories = Download::CATEGORIES;

        return view('admin.downloads.edit', compact('download', 'categories'));
    }

    public function update(Request $request, Download $download)
    {
        $validated = $request->validate($this->rules(false));

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($download->file_path);
            $file = $request->file('file');
            $validated['file_path'] = $file->store('downloads', 'public');
            $validated['file_type'] = strtolower($file->getClientOriginalExtension());
            $validated['file_size'] = $this->humanSize($file->getSize());
        }
        $validated['is_published'] = $request->boolean('is_published', true);
        unset($validated['file']);

        $download->update($validated);

        ActivityLogger::log('download_updated', $download, "Updated download: {$download->title}");

        return redirect()->route('admin.downloads.index')
                         ->with('success', 'Download updated successfully.');
    }

    public function destroy(Download $download)
    {
        Storage::disk('public')->delete($download->file_path);

        $title = $download->title;
        $download->delete();

        ActivityLogger::log('download_deleted', null, "Deleted download: {$title}");

        return redirect()->route('admin.downloads.index')
                         ->with('success', 'Download deleted successfully.');
    }

    private function rules(bool $fileRequired = true): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['required', Rule::in(Download::CATEGORIES)],
            'sort_order'   => ['nullable', 'integer'],
            'is_published' => ['nullable', 'boolean'],
            'file'         => [$fileRequired ? 'required' : 'nullable', 'file', 'max:10240'],
        ];
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}
