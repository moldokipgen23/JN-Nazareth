<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarksAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('admin.marks.index', array_merge($request->query(), ['view' => 'rankings']));
    }
}
