<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Settings;
use App\Http\Controllers\Controller;
use App\Models\HallOfFame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HallOfFameController extends Controller
{
    public function index()
    {
        $items = HallOfFame::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.hall-of-fame.index', compact('items'));
    }

    public function create()
    {
        return view('admin.hall-of-fame.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:150',
            'achievement_title' => 'required|string|max:200',
            'description'       => 'nullable|string|max:2000',
            'year'              => 'nullable|string|max:10',
            'video_url'         => 'nullable|string|max:300',
            'external_link'     => 'nullable|string|max:300',
            'sort_order'        => 'nullable|integer|min:0',
        ]);

        $data['featured']   = $request->boolean('featured');
        $data['active']     = $request->boolean('active', true);
        $data['sort_order'] = $data['sort_order'] ?? HallOfFame::max('sort_order') + 1;

        if ($request->hasFile('photo')) {
            $request->validate(['photo' => 'image|max:102400']);
            $data['photo'] = $request->file('photo')->store('hall-of-fame', 'public');
        }

        HallOfFame::create($data);
        return redirect()->route('admin.hall-of-fame.index')->with('success', 'Achievement added successfully.');
    }

    public function edit(HallOfFame $hallOfFame)
    {
        return view('admin.hall-of-fame.edit', ['item' => $hallOfFame]);
    }

    public function update(Request $request, HallOfFame $hallOfFame)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:150',
            'achievement_title' => 'required|string|max:200',
            'description'       => 'nullable|string|max:2000',
            'year'              => 'nullable|string|max:10',
            'video_url'         => 'nullable|string|max:300',
            'external_link'     => 'nullable|string|max:300',
            'sort_order'        => 'nullable|integer|min:0',
        ]);

        $data['featured']   = $request->boolean('featured');
        $data['active']     = $request->boolean('active');
        $data['sort_order'] = $data['sort_order'] ?? $hallOfFame->sort_order;

        if ($request->hasFile('photo')) {
            $request->validate(['photo' => 'image|max:102400']);
            if ($hallOfFame->photo) {
                Storage::disk('public')->delete($hallOfFame->photo);
            }
            $data['photo'] = $request->file('photo')->store('hall-of-fame', 'public');
        }

        $hallOfFame->update($data);
        return redirect()->route('admin.hall-of-fame.index')->with('success', 'Achievement updated.');
    }

    public function destroy(HallOfFame $hallOfFame)
    {
        if ($hallOfFame->photo) {
            Storage::disk('public')->delete($hallOfFame->photo);
        }
        $hallOfFame->delete();
        return back()->with('success', 'Achievement deleted.');
    }

    public function toggle(HallOfFame $hallOfFame)
    {
        $hallOfFame->update(['active' => !$hallOfFame->active]);
        return back()->with('success', 'Status updated.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|string']);
        $order = json_decode($request->order, true);
        if (!is_array($order)) {
            return response()->json(['error' => 'Invalid order'], 422);
        }
        foreach ($order as $position => $id) {
            HallOfFame::where('id', $id)->update(['sort_order' => $position]);
        }
        return response()->json(['success' => true]);
    }
}
