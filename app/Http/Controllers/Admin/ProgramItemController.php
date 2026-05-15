<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramItemController extends Controller
{
    public function index()
    {
        $images = ProgramItem::orderBy('sort_order')->orderByDesc('created_at')->get();
        return view('admin.programs.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images'        => 'required|array|min:1',
            'images.*'      => 'image|max:5120',
            'caption'       => 'nullable|string|max:200',
            'title'         => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'category'      => 'nullable|string|max:50',
            'activity_date' => 'nullable|date',
        ]);

        $base = ProgramItem::max('sort_order') ?? 0;
        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('programs', 'public');
            ProgramItem::create([
                'path'          => $path,
                'caption'       => $request->caption,
                'title'         => $request->title,
                'description'   => $request->description,
                'category'      => $request->category ?? 'general',
                'activity_date' => $request->activity_date,
                'sort_order'    => $base + $i + 1,
                'active'        => true,
            ]);
        }

        return back()->with('success', count($request->file('images')) . ' photo(s) uploaded.');
    }

    public function destroy(ProgramItem $programItem)
    {
        Storage::disk('public')->delete($programItem->path);
        $programItem->delete();
        return back()->with('success', 'Photo deleted.');
    }

    public function toggle(ProgramItem $programItem)
    {
        $programItem->update(['active' => !$programItem->active]);
        return back()->with('success', 'Photo updated.');
    }
}
