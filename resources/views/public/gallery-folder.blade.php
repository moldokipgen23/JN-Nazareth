@extends('layouts.public')
@section('title', $galleryFolder->name . ' — Gallery')

@section('content')

{{-- ══ Hero ══ --}}
<section class="page-hero" style="min-height:220px;">
    <div style="position:relative; z-index:2; max-width:900px; margin:0 auto; padding:56px 24px 44px; text-align:center;">
        <a href="{{ route('gallery') }}" style="display:inline-flex; align-items:center; gap:6px; color:rgba(255,255,255,.65); font-size:12px; font-weight:600; text-decoration:none; margin-bottom:18px; text-transform:uppercase; letter-spacing:.08em; transition:color .2s;"
           onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.65)'">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            All Albums
        </a>
        @if($galleryFolder->year)
            <div style="display:inline-block; background:rgba(255,255,255,.15); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.2); border-radius:50px; padding:5px 16px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:rgba(255,255,255,.85); margin-bottom:14px;">
                {{ $galleryFolder->year }}
            </div>
        @endif
        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,5vw,3rem); font-weight:800; color:#fff; margin:0 0 12px; line-height:1.2;">
            {{ $galleryFolder->name }}
        </h1>
        @if($galleryFolder->description)
            <p style="color:rgba(255,255,255,.72); font-size:.95rem; max-width:480px; margin:0 auto 14px; line-height:1.7;">
                {{ $galleryFolder->description }}
            </p>
        @endif
        <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.12); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.2); border-radius:50px; padding:6px 16px; font-size:11px; font-weight:700; color:rgba(255,255,255,.85); text-transform:uppercase; letter-spacing:.08em;">
            {{ $images->total() }} Photos
        </div>
    </div>
</section>

{{-- ══ Masonry Grid ══ --}}
@if($images->count())
<section style="background:var(--cream); padding:56px 0 80px;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div id="gallery-grid" style="columns:2; column-gap:14px;" class="sm:columns-3 lg:columns-4">
            @foreach($images as $idx => $image)
            <div class="gallery-item"
                 data-src="{{ \App\Helpers\Settings::storageUrl($image->path) }}"
                 data-caption="{{ $image->title ?? '' }}"
                 data-idx="{{ $idx }}"
                 style="break-inside:avoid; margin-bottom:14px; cursor:pointer; border-radius:12px; overflow:hidden; position:relative; box-shadow:0 4px 16px rgba(0,0,0,.1); transition:transform .3s, box-shadow .3s;"
                 onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 12px 36px rgba(0,0,0,.22)'; this.querySelector('.gi-ov').style.opacity='1';"
                 onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'; this.querySelector('.gi-ov').style.opacity='0';">
                <img src="{{ \App\Helpers\Settings::storageUrl($image->path) }}"
                     alt="{{ $image->title ?? $galleryFolder->name }}"
                     loading="{{ $idx < 8 ? 'eager' : 'lazy' }}"
                     style="width:100%; height:auto; display:block;">
                <div class="gi-ov" style="position:absolute; inset:0; background:linear-gradient(to top,rgba(27,67,50,.8) 0%,transparent 55%); opacity:0; transition:opacity .3s; display:flex; flex-direction:column; justify-content:flex-end; padding:14px;">
                    @if($image->title)
                        <p style="color:#fff; font-family:'Playfair Display',serif; font-size:.88rem; font-weight:600; margin:0 0 4px;">{{ $image->title }}</p>
                    @endif
                    <div style="color:rgba(255,255,255,.65); font-size:.7rem; display:flex; align-items:center; gap:4px;">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        Click to enlarge
                    </div>
                </div>
                <div style="position:absolute; top:8px; right:8px; background:rgba(0,0,0,.45); color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:16px;">{{ $idx + 1 }}</div>
            </div>
            @endforeach
        </div>

        @if($images->hasPages())
        <div style="margin-top:40px; display:flex; justify-content:center;">{{ $images->links() }}</div>
        @endif
    </div>
</section>

{{-- Lightbox --}}
<div id="lightbox" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.95); backdrop-filter:blur(10px); align-items:center; justify-content:center; flex-direction:column;">
    <button id="lb-close" style="position:absolute; top:18px; right:18px; z-index:10; width:42px; height:42px; border-radius:50%; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <button id="lb-prev" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); z-index:10; width:48px; height:48px; border-radius:50%; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button id="lb-next" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); z-index:10; width:48px; height:48px; border-radius:50%; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    <div style="display:flex; flex-direction:column; align-items:center; max-width:92vw; max-height:92vh;">
        <img id="lb-img" src="" alt="" style="max-width:90vw; max-height:78vh; width:auto; height:auto; border-radius:10px; box-shadow:0 30px 90px rgba(0,0,0,.7); object-fit:contain;">
        <div style="margin-top:14px; text-align:center;">
            <p id="lb-caption" style="color:rgba(255,255,255,.88); font-family:'Playfair Display',serif; font-size:1rem; font-weight:600; min-height:1.3em;"></p>
            <p id="lb-count" style="color:rgba(255,255,255,.4); font-size:.78rem; margin-top:3px;"></p>
        </div>
    </div>
</div>

@else
<section style="padding:80px 24px; text-align:center; background:var(--cream);">
    <p style="font-size:.95rem; color:#a8a29e;">No photos in this album yet.</p>
    <a href="{{ route('gallery') }}" style="display:inline-block; margin-top:16px; color:var(--primary); font-size:.85rem; font-weight:600; text-decoration:none;">← Back to Gallery</a>
</section>
@endif

<script>
(function(){
    const items=Array.from(document.querySelectorAll('.gallery-item'));
    const lb=document.getElementById('lightbox');
    const lbImg=document.getElementById('lb-img');
    const lbCap=document.getElementById('lb-caption');
    const lbCount=document.getElementById('lb-count');
    let current=0, total=items.length;
    const data=items.map(el=>({src:el.dataset.src,caption:el.dataset.caption||'',idx:parseInt(el.dataset.idx,10)}));

    function show(i){
        current=((i%total)+total)%total;
        const d=data[current];
        lbImg.style.opacity='0'; lbImg.style.transform='scale(.96)';
        setTimeout(()=>{lbImg.src=d.src;lbImg.alt=d.caption;lbImg.style.transition='opacity .3s,transform .3s';lbImg.style.opacity='1';lbImg.style.transform='scale(1)';},100);
        lbCap.textContent=d.caption; lbCount.textContent=(current+1)+' / '+total;
    }
    function open(i){show(i);lb.style.display='flex';document.body.style.overflow='hidden';}
    function close(){lb.style.display='none';document.body.style.overflow='';lbImg.src='';}

    items.forEach(el=>el.addEventListener('click',()=>open(parseInt(el.dataset.idx,10))));
    document.getElementById('lb-close').addEventListener('click',close);
    document.getElementById('lb-prev').addEventListener('click',()=>show(current-1));
    document.getElementById('lb-next').addEventListener('click',()=>show(current+1));
    lb.addEventListener('click',e=>{if(e.target===lb)close();});
    document.addEventListener('keydown',e=>{if(lb.style.display!=='flex')return;if(e.key==='ArrowLeft')show(current-1);if(e.key==='ArrowRight')show(current+1);if(e.key==='Escape')close();});
    let tx=0;
    lb.addEventListener('touchstart',e=>{tx=e.touches[0].clientX;},{passive:true});
    lb.addEventListener('touchend',e=>{const dx=e.changedTouches[0].clientX-tx;if(Math.abs(dx)>40)dx<0?show(current+1):show(current-1);},{passive:true});
})();
</script>

@endsection
