@extends('layouts.website')
@section('title', 'Hall of Fame')
@section('description', 'Celebrating the achievers and pride of ' . setting('school_name') . '.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-trophy"></i> ' . setting('hof_label', 'Hall of Fame'),
  'heading' => setting('hof_title', 'Our Pride & Achievers'),
  'sub'     => setting('hof_sub', 'Celebrating the students who make our school proud through academics, sports, and excellence.'),
  'crumb'   => 'Hall of Fame',
])

<section class="page-section">
  <div class="container">
    @if($achievers->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:24px;">
      @foreach($achievers as $hof)
      <div class="reveal" style="background:#fff;border:1px solid #fde68a;border-radius:18px;overflow:hidden;box-shadow:0 6px 24px rgba(180,83,9,.1);text-align:center;">
        <div style="height:230px;background:#fef3c7;overflow:hidden;">
          @if($hof->photo)
            <img src="{{ \App\Helpers\Settings::storageUrl($hof->photo) }}" alt="{{ $hof->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          @else
            <div style="height:100%;display:flex;align-items:center;justify-content:center;font-size:54px;color:#d97706;"><i class="fas fa-user-graduate"></i></div>
          @endif
        </div>
        <div style="padding:22px 20px;">
          <div style="font-size:18px;font-weight:800;color:#0f172a;">{{ $hof->name }}</div>
          <div style="font-size:14px;font-weight:700;color:#b45309;margin-top:4px;">{{ $hof->achievement_title }}</div>
          @if($hof->year)
            <div style="display:inline-block;margin-top:9px;background:#fef3c7;color:#92400e;font-size:11.5px;font-weight:700;padding:3px 12px;border-radius:99px;">{{ $hof->year }}</div>
          @endif
          @if($hof->description)
            <p style="font-size:13.5px;color:#64748b;line-height:1.65;margin:13px 0 0;">{{ $hof->description }}</p>
          @endif
          @if($hof->external_link)
            <a href="{{ $hof->external_link }}" target="_blank" rel="noopener" style="display:inline-block;margin-top:14px;color:#0f766e;font-weight:700;font-size:13px;text-decoration:none;">Learn more →</a>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div class="reveal" style="text-align:center;padding:60px 20px;color:#64748b;">
      <i class="fas fa-trophy" style="font-size:42px;color:#fbbf24;"></i>
      <p style="font-size:16px;font-weight:600;margin-top:14px;">Achievers will be featured here soon.</p>
    </div>
    @endif
  </div>
</section>
@endsection
