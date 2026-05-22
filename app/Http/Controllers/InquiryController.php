<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'phone'            => ['required', 'string', 'max:50'],
            'whatsapp'         => ['nullable', 'string', 'max:50'],
            'email'            => ['nullable', 'email', 'max:255'],
            'class_interested' => ['nullable', 'string', 'max:100'],
            'message'          => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['status'] = 'new';

        Inquiry::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Thank you — your inquiry has been received. We will contact you soon.');
    }
}
