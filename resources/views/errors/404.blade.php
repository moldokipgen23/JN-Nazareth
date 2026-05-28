@extends('layouts.website')

@section('title', 'Page Not Found')

@section('content')
<section style="min-height:62vh; display:flex; align-items:center; justify-content:center; padding:90px 20px;">
  <div style="max-width:540px; text-align:center;">
    <div style="font-size:96px; font-weight:800; line-height:1; background:linear-gradient(135deg,#1d4ed8,#0ea5e9); -webkit-background-clip:text; background-clip:text; color:transparent;">
      404
    </div>
    <h1 style="font-family:'Playfair Display',serif; font-size:26px; color:#0f172a; margin:14px 0 8px;">
      This page has moved or no longer exists
    </h1>
    <p style="font-size:15px; color:#64748b; margin:0 0 26px; line-height:1.6;">
      The page you’re looking for may have been removed, renamed, or is temporarily
      unavailable. Let’s get you back on track.
    </p>
    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
      <a href="{{ route('home') }}"
         style="background:linear-gradient(135deg,#1d4ed8,#0ea5e9); color:#fff; text-decoration:none;
                padding:12px 26px; border-radius:10px; font-weight:600; font-size:14px;">
        Back to Home
      </a>
      <a href="{{ route('news') }}"
         style="background:#f1f5f9; color:#334155; text-decoration:none; border:1px solid #e2e8f0;
                padding:12px 26px; border-radius:10px; font-weight:600; font-size:14px;">
        News &amp; Notices
      </a>
      <a href="{{ route('contact') }}"
         style="background:#f1f5f9; color:#334155; text-decoration:none; border:1px solid #e2e8f0;
                padding:12px 26px; border-radius:10px; font-weight:600; font-size:14px;">
        Contact Us
      </a>
    </div>
  </div>
</section>
@endsection
