<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::orderBy('sort_order')->orderByDesc('created_at')->get();
        return view('admin.videos.index', compact('videos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'youtube_url' => ['required', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
            'category'    => ['required', 'in:general,talk,event,music,other'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $data['created_by'] = auth()->id();
        $data['active']     = true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $video = Video::create($data);

        ActivityLogger::log('video_added', $video, "Added video: {$video->title}");

        return redirect()->route('admin.videos.index')->with('success', 'Video added successfully.');
    }

    public function toggle(Video $video)
    {
        $video->update(['active' => !$video->active]);
        return back()->with('success', 'Video visibility updated.');
    }

    public function destroy(Video $video)
    {
        $title = $video->title;
        $video->delete();

        ActivityLogger::log('video_deleted', null, "Deleted video: {$title}");

        return redirect()->route('admin.videos.index')->with('success', 'Video deleted.');
    }

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'youtube_url' => ['required', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
            'category'    => ['required', 'in:general,talk,event,music,other'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $video->update($data);

        ActivityLogger::log('video_updated', $video, "Updated video: {$video->title}");

        return redirect()->route('admin.videos.index')->with('success', 'Video updated successfully.');
    }
}
