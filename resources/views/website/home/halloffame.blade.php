@if($hallOfFame->count() && setting('sec_show_halloffame','1') !== '0')
<section id="halloffame" style="background:linear-gradient(160deg,#fffbeb 0%,#fef9f0 100%);">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill" style="background:#fef3c7;color:#b45309;border-color:#fde68a;">
        <i class="fas fa-trophy"></i> {{ setting('hof_label', 'Hall of Fame') }}
      </span>
      <h2 class="sec-title">{{ setting('hof_title', 'Our Pride & Achievers') }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('hof_sub', 'Celebrating the students who make our school proud through academics, sports, and excellence.') }}</p>
    </div>
    <div class="hof-grid">
      @foreach($hallOfFame as $hof)
      <div class="hof-card reveal" style="background:#fff;border:1px solid #fde68a;border-radius:18px;overflow:hidden;box-shadow:0 6px 24px rgba(180,83,9,.1);text-align:center;">
        <div style="height:200px;background:#fef3c7;overflow:hidden;">
          @if($hof->photo)
            <img src="{{ \App\Helpers\Settings::storageUrl($hof->photo) }}" alt="{{ $hof->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          @else
            <div style="height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;color:#d97706;"><i class="fas fa-user-graduate"></i></div>
          @endif
        </div>
        <div style="padding:20px 18px;">
          <div style="font-size:17px;font-weight:800;color:#0f172a;">{{ $hof->name }}</div>
          <div style="font-size:13.5px;font-weight:700;color:#b45309;margin-top:4px;">{{ $hof->achievement_title }}</div>
          @if($hof->year)
            <div style="display:inline-block;margin-top:8px;background:#fef3c7;color:#92400e;font-size:11px;font-weight:700;padding:3px 11px;border-radius:99px;">{{ $hof->year }}</div>
          @endif
          @if($hof->description)
            <p style="font-size:13px;color:#64748b;line-height:1.6;margin:12px 0 0;">{{ \Illuminate\Support\Str::limit($hof->description, 110) }}</p>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    <div style="text-align:center;margin-top:32px;" class="reveal">
      <a href="{{ route('hall-of-fame') }}" class="btn btn-blue-solid"><i class="fas fa-trophy"></i> View Full Hall of Fame</a>
    </div>
  </div>
</section>
@endif
