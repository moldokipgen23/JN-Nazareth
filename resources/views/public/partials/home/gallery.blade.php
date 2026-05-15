{{-- ══════════════════════════════════════════════════════════════
     7. GALLERY ALBUMS — Folder Carousel
══════════════════════════════════════════════════════════════ --}}
@if(isset($galleryFolders) && $galleryFolders->count() && \App\Helpers\Settings::get('sec_show_gallery', '1'))
@php $secTitleGallery = \App\Helpers\Settings::get('sec_title_gallery', 'Church Gallery'); @endphp
<section style="padding:72px 0; background:#fff;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:32px; flex-wrap:wrap; gap:10px;">
            <div>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--primary); display:block; margin-bottom:6px;">{{ \App\Helpers\Settings::get('sec_label_gallery', 'Our Moments') }}</span>
                <h2 style="font-size:26px; font-weight:800; color:#1c1917; margin:0; line-height:1.2;">{{$secTitleGallery}}</h2>
                @php $secSubGallery = \App\Helpers\Settings::get('sec_sub_gallery', ''); @endphp
                @if($secSubGallery)<p style="font-size:13px; color:#78716c; margin:6px 0 0;">{{ $secSubGallery }}</p>@endif
            </div>
            <a href="{{ route('gallery') }}" style="font-size:13px; font-weight:600; color:var(--primary); text-decoration:none; display:inline-flex; align-items:center; gap:5px; border:1px solid #bbf7d0; padding:7px 16px; border-radius:50px; background:#f0fdf4;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                View All Albums
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div style="position:relative;">
            <div id="gal-carousel" style="display:flex; gap:20px; overflow-x:auto; scroll-snap-type:x mandatory; padding-bottom:12px; scrollbar-width:none; -ms-overflow-style:none;">
                @foreach($galleryFolders as $folder)
                @php
                    $gPrev = $folder->previewImages;
                    $gCover = $folder->cover_image ? \App\Helpers\Settings::storageUrl($folder->cover_image) : ($gPrev->get(0) ? \App\Helpers\Settings::storageUrl($gPrev->get(0)->path) : null);
                    $gImg1  = $gPrev->get(1) ? \App\Helpers\Settings::storageUrl($gPrev->get(1)->path) : $gCover;
                    $gImg2  = $gPrev->get(2) ? \App\Helpers\Settings::storageUrl($gPrev->get(2)->path) : $gImg1;
                @endphp
                <a href="{{ route('gallery.folder', $folder) }}" style="flex:0 0 210px; scroll-snap-align:start; text-decoration:none; display:block; perspective:900px;">
                    <div style="transition:transform .4s cubic-bezier(.25,.8,.25,1), box-shadow .4s; transform-style:preserve-3d; border-radius:16px; overflow:hidden; background:#fff; box-shadow:0 4px 20px rgba(0,0,0,.1);"
                         onmouseover="this.style.transform='rotateY(-6deg) rotateX(3deg) scale(1.04)'; this.style.boxShadow='0 18px 50px rgba(0,0,0,.2)';"
                         onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 20px rgba(0,0,0,.1)';">
                        <div style="position:relative; padding-top:72%; background:#e8f0ee; overflow:hidden;">
                            @if($gImg2)
                            <div style="position:absolute; border-radius:10px; overflow:hidden; inset:6% 0 0 8%; opacity:.3; transform:rotate(3deg); z-index:1;"><img src="{{$gImg2}}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;"></div>
                            @endif
                            @if($gImg1)
                            <div style="position:absolute; border-radius:10px; overflow:hidden; inset:3% 0 0 4%; opacity:.6; transform:rotate(1.2deg); z-index:2;"><img src="{{$gImg1}}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;"></div>
                            @endif
                            <div style="position:absolute; inset:0; z-index:3; overflow:hidden; border-radius:10px;">
                                @if($gCover)
                                    <img src="{{$gCover}}" alt="{{$folder->name}}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div style="width:100%;height:100%;background:linear-gradient(135deg,#d1fae5,#ecfdf5);display:flex;align-items:center;justify-content:center;"><svg width="40" height="40" fill="none" stroke="#059669" stroke-width="1.5" viewBox="0 0 24 24" opacity=".4"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                @endif
                            </div>
                            <div style="position:absolute; inset:0; z-index:4; background:linear-gradient(to top,rgba(0,0,0,.7) 0%,transparent 50%); pointer-events:none;"></div>
                            <div style="position:absolute; bottom:10px; left:12px; right:12px; z-index:5; color:#fff;">
                                <div style="font-size:13px; font-weight:800; text-shadow:0 1px 6px rgba(0,0,0,.5); line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{$folder->name}}</div>
                            </div>
                            <div style="position:absolute; top:8px; right:8px; z-index:6; background:rgba(0,0,0,.55); color:#fff; font-size:9px; font-weight:700; padding:3px 8px; border-radius:20px;">{{$folder->items_count}} photos</div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <style>#gal-carousel::-webkit-scrollbar{display:none;}</style>
        </div>
        <div style="display:flex; justify-content:center; gap:10px; margin-top:16px;">
            <button onclick="document.getElementById('gal-carousel').scrollBy({left:-230,behavior:'smooth'})" style="width:36px;height:36px;border-radius:50%;background:#f0fdf4;border:1px solid #bbf7d0;color:var(--primary);cursor:pointer;display:flex;align-items:center;justify-content:center;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
            <button onclick="document.getElementById('gal-carousel').scrollBy({left:230,behavior:'smooth'})" style="width:36px;height:36px;border-radius:50%;background:#f0fdf4;border:1px solid #bbf7d0;color:var(--primary);cursor:pointer;display:flex;align-items:center;justify-content:center;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
    </div>
</section>
@endif

