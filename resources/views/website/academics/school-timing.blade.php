@extends('layouts.website')
@section('title', 'School Timing')
@section('description', 'School hours and timing for ' . setting('school_name') . '.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-clock"></i> Academics',
  'heading' => setting('acad_timing_title', 'School Timing'),
  'sub'     => 'Daily school hours.',
  'crumb'   => 'School Timing',
])

<section class="page-section">
  <div class="container" style="max-width:760px;">
    <div class="reveal" style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.05);">
        <div style="width:52px;height:52px;border-radius:14px;background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:22px;margin:0 auto 14px;">
          <i class="fas fa-calendar-day"></i>
        </div>
        <div style="font-size:13px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Monday – Friday</div>
        <div style="font-size:18px;font-weight:700;color:#0f172a;margin-top:6px;">{{ setting('acad_timing_weekday', '8:00 AM – 3:00 PM') }}</div>
      </div>
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.05);">
        <div style="width:52px;height:52px;border-radius:14px;background:#fef3c7;color:#b45309;display:flex;align-items:center;justify-content:center;font-size:22px;margin:0 auto 14px;">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div style="font-size:13px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Saturday</div>
        <div style="font-size:18px;font-weight:700;color:#0f172a;margin-top:6px;">{{ setting('acad_timing_saturday', '8:00 AM – 12:00 PM') }}</div>
      </div>
    </div>

    <div class="reveal" style="margin-top:18px;text-align:center;">
      <a href="{{ route('academics') }}" style="color:#0f766e;font-weight:600;text-decoration:none;font-size:14px;">
        <i class="fas fa-arrow-left"></i> Back to Academics
      </a>
    </div>
  </div>
</section>
@endsection
