<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderByDesc('created_at')->paginate(15);

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['required', 'string'],
            'image'     => ['nullable', 'image', 'max:5120'],
            'published' => ['boolean'],
        ]);

        $validated['slug']      = $this->uniqueSlug(Str::slug($validated['title']));
        $validated['published'] = $request->boolean('published');
        $validated['author_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('blog-images', 'public');
        }

        $blog = Blog::create($validated);

        ActivityLogger::log('blog_created', $blog, "Created blog: {$blog->title}");

        return redirect()->route('admin.blogs.index')
                         ->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['required', 'string'],
            'image'     => ['nullable', 'image', 'max:5120'],
            'published' => ['boolean'],
        ]);

        // Regenerate slug only if title changed
        if ($validated['title'] !== $blog->title) {
            $validated['slug'] = $this->uniqueSlug(Str::slug($validated['title']), $blog->id);
        }

        $validated['published'] = $request->boolean('published');

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $validated['image'] = $request->file('image')
                ->store('blog-images', 'public');
        }

        $blog->update($validated);

        ActivityLogger::log('blog_updated', $blog, "Updated blog: {$blog->title}");

        return redirect()->route('admin.blogs.index')
                         ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $title = $blog->title;
        $blog->delete();

        ActivityLogger::log('blog_deleted', null, "Deleted blog: {$title}");

        return redirect()->route('admin.blogs.index')
                         ->with('success', 'Blog post deleted successfully.');
    }

    public function togglePublish(Blog $blog)
    {
        $blog->update(['published' => !$blog->published]);

        $status = $blog->published ? 'published' : 'unpublished';

        ActivityLogger::log('blog_toggle_publish', $blog, "Blog {$status}: {$blog->title}");

        return redirect()->back()
                         ->with('success', "Blog post {$status} successfully.");
    }

    private function uniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $original = $slug;
        $count = 1;

        while (true) {
            $query = Blog::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
            if (!$query->exists()) {
                break;
            }
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
