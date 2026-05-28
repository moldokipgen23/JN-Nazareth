@extends('layouts.website')

@section('title', 'Access Denied')

@section('content')
<section style="min-height:62vh; display:flex; align-items:center; justify-content:center; padding:90px 20px;">
  <div style="max-width:540px; text-align:center;">
    <div style="font-size:96px; font-weight:800; line-height:1; background:linear-gradient(135deg,#1d4ed8,#0ea5e9); -webkit-background-clip:text; background-clip:text; color:transparent;">
      403
    </div>
    <h1 style="font-family:'Playfair Display',serif; font-size:26px; color:#0f172a; margin:14px 0 8px;">
      You don’t have access to this page
    </h1>
    <p style="font-size:15px; color:#64748b; margin:0 0 26px; line-height:1.6;">
      This area is restricted. If you believe you should have access,
      please contact the school office.
    </p>
    <a href="{{ route('home') }}"
       style="background:linear-gradient(135deg,#1d4ed8,#0ea5e9); color:#fff; text-decoration:none;
              padding:12px 26px; border-radius:10px; font-weight:600; font-size:14px;">
      Back to Home
    </a>
  </div>
</section>
@endsection
