<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\ImportantLink;
use Illuminate\Http\Request;

class ImportantLinkController extends Controller
{
    public function index()
    {
        $links = ImportantLink::orderBy('sort_order')
                              ->orderByDesc('created_at')
                              ->paginate(20);

        return view('admin.important-links.index', compact('links'));
    }

    public function create()
    {
        return view('admin.important-links.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['is_published'] = $request->boolean('is_published', true);

        $link = ImportantLink::create($validated);

        ActivityLogger::log('important_link_created', $link, "Added important link: {$link->title}");

        return redirect()->route('admin.important-links.index')
                         ->with('success', 'Link added successfully.');
    }

    public function edit(ImportantLink $importantLink)
    {
        return view('admin.important-links.edit', ['link' => $importantLink]);
    }

    public function update(Request $request, ImportantLink $importantLink)
    {
        $validated = $request->validate($this->rules());
        $validated['is_published'] = $request->boolean('is_published', true);

        $importantLink->update($validated);

        ActivityLogger::log('important_link_updated', $importantLink, "Updated important link: {$importantLink->title}");

        return redirect()->route('admin.important-links.index')
                         ->with('success', 'Link updated successfully.');
    }

    public function destroy(ImportantLink $importantLink)
    {
        $title = $importantLink->title;
        $importantLink->delete();

        ActivityLogger::log('important_link_deleted', null, "Deleted important link: {$title}");

        return redirect()->route('admin.important-links.index')
                         ->with('success', 'Link deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'url'          => ['required', 'url', 'max:500'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'sort_order'   => ['nullable', 'integer'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
