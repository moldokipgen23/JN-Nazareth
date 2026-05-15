{{-- ══ LEADER + STORY (combined) ══ --}}
@php
    $secShowStory   = \App\Helpers\Settings::get('sec_show_story', '1');
    $secTitleStory  = \App\Helpers\Settings::get('sec_title_story', 'Leadership & Our Story');
    $leaderName     = \App\Helpers\Settings::get('leader_name', 'Your Leader Name');
    $leaderTitle    = \App\Helpers\Settings::get('leader_title', 'Director');
    $leaderDesc     = \App\Helpers\Settings::get('leader_description', 'Our leadership team supports members, sets direction, and ensures the organisation serves its community well.');
    $leaderPhoto    = \App\Helpers\Settings::get('leader_photo');
    $storyTitle     = \App\Helpers\Settings::get('story_title', 'Our Story');
    $storyContent   = \App\Helpers\Settings::get('story_content',
        "Our organisation began with a simple goal: to bring people together and make a difference. Over the years we've grown into a thriving community of members, volunteers, and supporters.\n\nFrom day one, we've been built on the principles of inclusion, service, and shared purpose. Whether through events, programmes, or everyday encounters, our mission has always been to enrich the lives of those around us."
    );
@endphp
@if($secShowStory)
<section style="padding:80px 0; background:var(--cream); position:relative; overflow:hidden;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-family:'Playfair Display',serif; font-size:clamp(5rem,15vw,12rem); font-weight:900; color:rgba(45,106,79,0.04); white-space:nowrap; pointer-events:none; user-select:none; line-height:1;">{{ \App\Helpers\Settings::get('site_name','Community') }}</div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position:relative; z-index:1;">

        <div style="text-align:center; margin-bottom:56px;">
            <span class="section-label">Leadership &amp; Story</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,3rem); font-weight:800; color:#1c1917; line-height:1.2; margin:14px 0 0;">
                {{ $secTitleStory }}
            </h2>
            <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-top:18px;">
                <span style="height:1px; width:60px; background:#d6cdc0;"></span>
                <svg width="16" height="16" fill="#c8873a" viewBox="0 0 24 24"><rect x="11" y="2" width="2" height="20" rx="1"/><rect x="3" y="8" width="18" height="2" rx="1"/></svg>
                <span style="height:1px; width:60px; background:#d6cdc0;"></span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

            <div>
                <span class="section-label">Leadership</span>
                <h3 style="font-family:'Playfair Display',serif; font-size:clamp(1.4rem,2.5vw,2rem); font-weight:800; color:#1c1917; line-height:1.2; margin:10px 0 20px;">
                    {{ $leaderTitle }}
                </h3>

                <div style="position:relative; width:100%; max-width:300px; margin-bottom:44px;">
                    <div style="position:absolute; bottom:-12px; right:-12px; width:100%; height:100%; border-radius:16px; background:linear-gradient(135deg,var(--primary),var(--secondary)); opacity:0.1; z-index:0;"></div>
                    <div style="position:relative; z-index:1; border-radius:16px; overflow:hidden; box-shadow:0 12px 40px rgba(0,0,0,0.14); aspect-ratio:4/5; background:#e7dfd4;">
                        @if($leaderPhoto)
                            <img src="{{ \App\Helpers\Settings::storageUrl($leaderPhoto) }}" alt="{{ $leaderName }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                        @else
                            <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:linear-gradient(160deg,#e7dfd4,#d6cdc0); min-height:240px;">
                                <svg width="56" height="56" fill="none" stroke="#a8a29e" stroke-width="1.2" viewBox="0 0 24 24" style="margin-bottom:10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span style="font-size:0.78rem; color:#a8a29e; font-weight:500;">No photo uploaded</span>
                            </div>
                        @endif
                    </div>
                    <div style="position:absolute; bottom:-18px; left:50%; transform:translateX(-50%); z-index:2; background:var(--primary); color:#fff; padding:9px 18px; border-radius:50px; white-space:nowrap; box-shadow:0 6px 20px rgba(45,106,79,0.3); text-align:center;">
                        <div style="font-family:'Playfair Display',serif; font-size:.88rem; font-weight:700; line-height:1.2;">{{ $leaderName }}</div>
                        <div style="font-size:0.62rem; opacity:0.8; text-transform:uppercase; letter-spacing:0.08em; margin-top:2px;">{{ $leaderTitle }}</div>
                    </div>
                </div>

                <p style="color:#57534e; font-size:1rem; line-height:1.85; margin-bottom:20px;">{{ $leaderDesc }}</p>

                @if(\App\Helpers\Settings::get('leader_governance_note'))
                <div style="background:#fff; border-left:3px solid var(--primary); border-radius:0 12px 12px 0; padding:12px 16px;">
                    <p style="font-size:0.82rem; color:#78716c; line-height:1.7; margin:0;">
                        {!! nl2br(e(\App\Helpers\Settings::get('leader_governance_note'))) !!}
                    </p>
                </div>
                @endif
            </div>

            <div>
                <span class="section-label">Our Heritage</span>
                <h3 style="font-family:'Playfair Display',serif; font-size:clamp(1.4rem,2.5vw,2rem); font-weight:800; color:#1c1917; line-height:1.2; margin:10px 0 20px;">
                    {{ $storyTitle }}
                </h3>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                    <span style="width:36px; height:2px; background:var(--accent); border-radius:2px; display:inline-block;"></span>
                    <svg width="14" height="14" fill="var(--accent)" viewBox="0 0 24 24"><rect x="11" y="2" width="2" height="20" rx="1"/><rect x="3" y="8" width="18" height="2" rx="1"/></svg>
                    <span style="width:36px; height:2px; background:var(--accent); border-radius:2px; display:inline-block;"></span>
                </div>
                @php $paragraphs = array_filter(array_map('trim', explode("\n", $storyContent))); @endphp
                @foreach($paragraphs as $i => $para)
                @if(!empty($para))
                <p style="font-size:{{ $i === 0 ? '1.02rem' : '.92rem' }}; color:#57534e; line-height:1.85; margin-bottom:14px; {{ $i === 0 ? 'font-weight:500;' : '' }}">
                    @if($i === 0)
                    <span style="font-family:'Playfair Display',serif; font-size:2.6rem; font-weight:800; color:var(--primary); float:left; line-height:0.82; margin-right:8px; margin-top:6px;">{{ mb_substr($para,0,1) }}</span>{{ mb_substr($para,1) }}
                    @else
                    {{ $para }}
                    @endif
                </p>
                @endif
                @endforeach
            </div>

        </div>
    </div>
</section>
@endif
