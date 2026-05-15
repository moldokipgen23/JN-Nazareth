<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderByRaw("CASE WHEN slug IN ('home','about') THEN 0 ELSE 1 END")
                     ->orderBy('title')
                     ->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'content'          => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'published'        => ['nullable', 'boolean'],
            'show_in_nav'      => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($validated['title']);
        $base = $slug;
        $i    = 2;
        while (Page::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $page = Page::create([
            'title'            => $validated['title'],
            'slug'             => $slug,
            'content'          => $validated['content'] ?? '',
            'meta_description' => $validated['meta_description'] ?? null,
            'published'        => $request->boolean('published', true),
            'show_in_nav'      => $request->boolean('show_in_nav', false),
            'updated_by'       => auth()->id(),
        ]);

        ActivityLogger::log('page_created', $page, "Created page: {$page->title}");

        return redirect()->route('admin.pages.edit', $page)
                         ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'content'          => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'published'        => ['nullable', 'boolean'],
            'show_in_nav'      => ['nullable', 'boolean'],
        ]);

        $page->update([
            'title'            => $validated['title'],
            'content'          => $validated['content'] ?? '',
            'meta_description' => $validated['meta_description'] ?? null,
            'published'        => $request->boolean('published', true),
            'show_in_nav'      => $request->boolean('show_in_nav', false),
            'updated_by'       => auth()->id(),
        ]);

        ActivityLogger::log('page_updated', $page, "Updated page: {$page->title}");

        return redirect()->route('admin.pages.edit', $page)
                         ->with('success', 'Page saved successfully.');
    }

    public function destroy(Page $page)
    {
        if (in_array($page->slug, ['home', 'about'])) {
            return back()->with('error', 'Built-in pages cannot be deleted.');
        }

        $title = $page->title;
        $page->delete();

        ActivityLogger::log('page_deleted', null, "Deleted page: {$title}");

        return redirect()->route('admin.pages.index')
                         ->with('success', "Page \"{$title}\" deleted.");
    }
}
