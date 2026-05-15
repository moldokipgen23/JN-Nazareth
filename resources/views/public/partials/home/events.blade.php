{{-- ══════════════════════════════════════════════════════════════
     8. UPCOMING EVENTS — Compact 3-item list
══════════════════════════════════════════════════════════════ --}}
@if(\App\Helpers\Settings::get('sec_show_events', '1'))
@php $secTitleEvents = \App\Helpers\Settings::get('sec_title_events', 'Upcoming Events'); @endphp
<section style="padding:56px 0; background:var(--cream);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; flex-wrap:wrap; gap:10px;">
            <div>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--primary); display:block; margin-bottom:3px;">{{ \App\Helpers\Settings::get('sec_label_events', "What's Happening") }}</span>
                <h2 style="font-size:22px; font-weight:800; color:#1c1917; margin:0;">{{$secTitleEvents}}</h2>
                @php $secSubEvents = \App\Helpers\Settings::get('sec_sub_events', ''); @endphp
                @if($secSubEvents)<p style="font-size:13px; color:#78716c; margin:4px 0 0;">{{ $secSubEvents }}</p>@endif
            </div>
            <a href="{{ route('events') }}" style="font-size:12px; font-weight:600; color:var(--primary); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">All Events <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
        </div>
        @if(isset($events) && $events->count())
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:12px;">
            @foreach($events->take(3) as $event)
            @php $ed = \Carbon\Carbon::parse($event->starts_at); @endphp
            <div style="background:#fff; border-radius:12px; border:1px solid #f1f5f9; box-shadow:0 2px 8px rgba(0,0,0,.05); display:flex; align-items:center; gap:14px; padding:13px 15px; transition:box-shadow .2s,transform .2s;" onmouseover="this.style.boxShadow='0 6px 22px rgba(0,0,0,.11)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,.05)';this.style.transform=''">
                <div style="flex-shrink:0; width:48px; text-align:center; background:var(--primary); color:#fff; border-radius:10px; padding:7px 4px;">
                    <div style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; opacity:.8;">{{$ed->format('M')}}</div>
                    <div style="font-size:20px; font-weight:800; line-height:1.1;">{{$ed->format('d')}}</div>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:700; color:#1c1917; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{$event->title}}</div>
                    @if($event->location)<div style="font-size:11px; color:#94a3b8; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{$event->location}}</div>@endif
                </div>
                <a href="{{ route('events') }}" style="flex-shrink:0; font-size:10px; font-weight:700; color:var(--primary); text-decoration:none; background:#f0fdf4; border:1px solid #bbf7d0; padding:4px 10px; border-radius:20px;">View</a>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center; padding:36px; background:#fff; border-radius:12px; border:1px solid #f1f5f9;"><p style="font-size:13px; color:#94a3b8;">No upcoming events.</p></div>
        @endif
    </div>
</section>
@endif

