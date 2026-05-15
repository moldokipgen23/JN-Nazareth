{{-- PROGRAMS CAROUSEL --}}
@if(isset($programFolders) && $programFolders->count() && \App\Helpers\Settings::get('sec_show_programs', '1'))
<section style="padding:72px 0; background:linear-gradient(180deg,#f0fdf4 0%,#fff 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
            <div>
                <span class="section-label">Community in Action</span>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.6rem,3vw,2.4rem); font-weight:800; color:#1c1917; line-height:1.2; margin:10px 0 8px;">{{ \App\Helpers\Settings::get('sec_title_programs', 'Our Programmes') }}</h2>
                <p style="color:#78716c; font-size:.9rem; max-width:480px; line-height:1.65;">{{ \App\Helpers\Settings::get('sec_sub_programs', 'A look at the activities, programmes, and initiatives that bring our community to life.') }}</p>
            </div>
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:10px 18px; text-align:center;">
                <div style="font-family:'Playfair Display',serif; font-size:1.4rem; font-weight:800; color:#0f766e;">{{ $programFolders->count() }}+</div>
                <div style="font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#6b7280;">Albums</div>
            </div>
        </div>
        <div id="programs-carousel" style="display:flex; gap:20px; overflow-x:auto; scroll-snap-type:x mandatory; padding-bottom:12px; scrollbar-width:none; -ms-overflow-style:none;">
            @foreach($programFolders as $folder)
            @php
                $bPrev=$folder->previewImages;
                $bCover=$folder->cover_image?\App\Helpers\Settings::storageUrl($folder->cover_image):($bPrev->get(0)?\App\Helpers\Settings::storageUrl($bPrev->get(0)->path):null);
                $bImg1=$bPrev->get(1)?\App\Helpers\Settings::storageUrl($bPrev->get(1)->path):$bCover;
                $bImg2=$bPrev->get(2)?\App\Helpers\Settings::storageUrl($bPrev->get(2)->path):$bImg1;
            @endphp
            <a href="{{ route('gallery.folder', $folder) }}" style="flex:0 0 210px; scroll-snap-align:start; text-decoration:none; display:block; perspective:900px;">
                <div style="transition:transform .4s cubic-bezier(.25,.8,.25,1),box-shadow .4s;transform-style:preserve-3d;border-radius:16px;overflow:hidden;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.1);"
                     onmouseover="this.style.transform='rotateY(-6deg) rotateX(3deg) scale(1.04)';this.style.boxShadow='0 18px 50px rgba(0,0,0,.2)';"
                     onmouseout="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(0,0,0,.1)';">
                    <div style="position:relative;padding-top:72%;background:#e8f5f0;overflow:hidden;">
                        @if($bImg2)<div style="position:absolute;border-radius:10px;overflow:hidden;inset:6% 0 0 8%;opacity:.3;transform:rotate(3deg);z-index:1;"><img src="{{ $bImg2 }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;"></div>@endif
                        @if($bImg1)<div style="position:absolute;border-radius:10px;overflow:hidden;inset:3% 0 0 4%;opacity:.6;transform:rotate(1.2deg);z-index:2;"><img src="{{ $bImg1 }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;"></div>@endif
                        <div style="position:absolute;inset:0;z-index:3;overflow:hidden;border-radius:10px;">
                            @if($bCover)<img src="{{ $bCover }}" alt="{{ $folder->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                            @else<div style="width:100%;height:100%;background:linear-gradient(135deg,#d1fae5,#ecfdf5);display:flex;align-items:center;justify-content:center;"><svg width="40" height="40" fill="none" stroke="#059669" stroke-width="1.5" viewBox="0 0 24 24" opacity=".4"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>@endif
                        </div>
                        <div style="position:absolute;inset:0;z-index:4;background:linear-gradient(to top,rgba(0,0,0,.72) 0%,transparent 55%);pointer-events:none;"></div>
                        <div style="position:absolute;bottom:10px;left:12px;right:12px;z-index:5;color:#fff;font-size:13px;font-weight:800;text-shadow:0 1px 6px rgba(0,0,0,.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $folder->name }}</div>
                        <div style="position:absolute;top:8px;right:8px;z-index:6;background:rgba(0,0,0,.55);color:#fff;font-size:9px;font-weight:700;padding:3px 8px;border-radius:20px;">{{ $folder->items_count }} photos</div>
                        <div style="position:absolute;top:8px;left:8px;z-index:6;background:#7c3aed;color:#fff;font-size:9px;font-weight:700;padding:3px 8px;border-radius:20px;">PROGRAMME</div>
                    </div>
                    @if($folder->description)<div style="padding:10px 12px 12px;font-size:11px;color:#64748b;line-height:1.5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $folder->description }}</div>@endif
                </div>
            </a>
            @endforeach
        </div>
        <style>#programs-carousel::-webkit-scrollbar{display:none;}</style>
        <div style="display:flex;justify-content:center;gap:10px;margin-top:14px;">
            <button onclick="document.getElementById('programs-carousel').scrollBy({left:-230,behavior:'smooth'})" style="width:36px;height:36px;border-radius:50%;background:#f0fdf4;border:1px solid #bbf7d0;color:#0f766e;cursor:pointer;display:flex;align-items:center;justify-content:center;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
            <button onclick="document.getElementById('programs-carousel').scrollBy({left:230,behavior:'smooth'})" style="width:36px;height:36px;border-radius:50%;background:#f0fdf4;border:1px solid #bbf7d0;color:#0f766e;cursor:pointer;display:flex;align-items:center;justify-content:center;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
    </div>
</section>
@endif
