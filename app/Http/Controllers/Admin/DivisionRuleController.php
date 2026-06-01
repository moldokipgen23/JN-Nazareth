<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DivisionRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisionRuleController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:50',
            'min_percent' => 'required|numeric|min:0|max:100',
            'max_percent' => 'required|numeric|min:0|max:100|gte:min_percent',
        ]);

        DivisionRule::create($data + ['is_active' => true]);

        return back()->with('success', "Division {$data['name']} added.");
    }

    public function update(Request $request, DivisionRule $divisionRule)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:50'],
            'min_percent' => 'required|numeric|min:0|max:100',
            'max_percent' => 'required|numeric|min:0|max:100|gte:min_percent',
        ]);

        $divisionRule->update($data);

        return back()->with('success', 'Division updated.');
    }

    public function destroy(DivisionRule $divisionRule)
    {
        $divisionRule->delete();
        return back()->with('success', 'Division deleted.');
    }

    public function toggle(DivisionRule $divisionRule)
    {
        $divisionRule->update(['is_active' => ! $divisionRule->is_active]);
        return back()->with('success', $divisionRule->is_active ? 'Division activated.' : 'Division deactivated.');
    }
}
