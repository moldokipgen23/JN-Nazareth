@extends('layouts.admin')
@section('page-title', 'Site Customizer')

@section('content')
@php
  $S = fn($k,$d='') => \App\Helpers\Settings::get($k, $d);
  $on = fn($k) => \App\Helpers\Settings::get('sec_show_'.$k, '1') !== '0';

  // text input
  $inp = fn($k,$label,$d='') =>
    '<div><label class="flabel">'.e($label).'</label>'.
    '<input type="text" name="'.$k.'" value="'.e($S($k,$d)).'" class="finput"></div>';

  // textarea
  $ta = fn($k,$label,$d='',$rows=3) =>
    '<div><label class="flabel">'.e($label).'</label>'.
    '<textarea name="'.$k.'" rows="'.$rows.'" class="fta">'.e($S($k,$d)).'</textarea></div>';

  // image upload (with current preview)
  $img = fn($k,$label) =>
    '<div><label class="flabel">'.e($label).'</label>'.
    ($S($k) ? '<div style="margin-bottom:6px;"><img src="'.\App\Helpers\Settings::storageUrl($S($k)).'" style="height:56px;border-radius:8px;border:1px solid #e2e8f0;object-fit:cover;"></div>' : '').
    '<input type="file" name="'.$k.'" accept="image/*" class="finput" style="padding:6px;"></div>';
@endphp

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Site Customizer</h2>
    <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Everything that appears on the public website.</p>
  </div>
  <a href="{{ route('home') }}" target="_blank"
     style="display:inline-flex; align-items:center; gap:7px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; padding:9px 16px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none;">
    View Website ↗
  </a>
</div>

@if(session('success'))
<div style="background:#ecfdf5; border:1px solid #6ee7b7; color:#065f46; border-radius:10px; padding:11px 16px; margin-bottom:18px; font-size:13px; font-weight:600;">
  {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:12px 16px; margin-bottom:18px;">
  <ul style="margin:0; padding-left:18px;">
    @foreach($errors->all() as $error)
      <li style="font-size:12px; color:#b91c1c;">{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<style>
.cust-tabs { display:flex; gap:6px; margin-bottom:18px; background:#fff; padding:6px; border-radius:12px; border:1px solid #f1f5f9; box-shadow:0 1px 6px rgba(0,0,0,.05); }
.cust-tab { flex:1; padding:10px 16px; border-radius:8px; font-size:13px; font-weight:700; border:none; cursor:pointer; background:transparent; color:#64748b; transition:all .15s; }
.cust-tab.active { background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; }
.acc { border:1px solid #e2e8f0; border-radius:12px; background:#fff; margin-bottom:12px; overflow:hidden; }
.acc-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:15px 18px; cursor:pointer; user-select:none; }
.acc-head:hover { background:#f8fafc; }
.acc-titlewrap { display:flex; align-items:center; gap:12px; }
.acc-num { width:32px; height:32px; flex-shrink:0; border-radius:9px; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#ffffff; font-size:15px; font-weight:800; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 7px rgba(15,118,110,.45); }
.acc-titlewrap strong { font-size:14px; color:#0f172a; display:block; }
.acc-titlewrap span { font-size:11.5px; color:#64748b; }
.acc-right { display:flex; align-items:center; gap:14px; }
.acc-body { display:none; padding:4px 18px 20px; border-top:1px solid #f1f5f9; }
.acc.open .acc-body { display:block; }
.acc.open .chev { transform:rotate(180deg); }
.acc.off { opacity:.6; }
.chev { color:#94a3b8; transition:transform .2s; font-size:13px; }
.switch { position:relative; display:inline-block; width:40px; height:22px; }
.switch input { opacity:0; width:0; height:0; }
.slider { position:absolute; cursor:pointer; inset:0; background:#cbd5e1; border-radius:22px; transition:.2s; }
.slider:before { content:""; position:absolute; height:16px; width:16px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.2s; }
.switch input:checked + .slider { background:#0f766e; }
.switch input:checked + .slider:before { transform:translateX(18px); }
.flabel { font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px; }
.finput { border:1px solid #e2e8f0; border-radius:8px; padding:8px 11px; font-size:13px; width:100%; box-sizing:border-box; outline:none; }
.fta { border:1px solid #e2e8f0; border-radius:8px; padding:8px 11px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box; font-family:inherit; }
.g2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
.g3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:14px; }
.g1 { display:grid; gap:14px; margin-bottom:14px; }
@media(max-width:680px){ .g2,.g3 { grid-template-columns:1fr; } }
.subnote { font-size:11.5px; color:#64748b; margin:6px 0 14px; }
.blk { border:1px dashed #cbd5e1; border-radius:10px; padding:14px; background:#f8fafc; margin-bottom:12px; }
.blk-t { font-size:11px; font-weight:700; color:#0f766e; text-transform:uppercase; letter-spacing:.4px; margin-bottom:10px; }
.save-btn { background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; border-radius:9px; padding:10px 26px; font-size:13px; font-weight:700; cursor:pointer; margin-top:4px; }
.acc-section-h { font-size:13px; font-weight:700; color:#0f172a; margin:16px 0 8px; }
.acc.dragging { opacity:.45; border-style:dashed; }
.acc.drag-over { box-shadow:0 0 0 2px #14b8a6; }
.acc-grip { color:#0f766e; font-size:15px; cursor:grab; padding:8px 9px; border-radius:8px; background:#f0fdfa; border:1px solid #99f6e4; flex-shrink:0; transition:background .15s; }
.acc-grip:hover { background:#ccfbf1; }
.acc-grip:active { cursor:grabbing; background:#5eead4; }
.acc.dragging .acc-grip { background:#5eead4; }
.adm-row { display:flex; align-items:flex-start; gap:10px; border:1px solid #e2e8f0; border-radius:10px; padding:12px; margin-bottom:10px; background:#f8fafc; }
.adm-row.dragging { opacity:.45; border-style:dashed; }
.adm-row-grip { color:#0f766e; font-size:14px; cursor:grab; padding:8px 7px; border-radius:7px; background:#f0fdfa; border:1px solid #99f6e4; flex-shrink:0; }
.adm-row-grip:hover { background:#ccfbf1; }
.adm-row-grip:active { cursor:grabbing; background:#5eead4; }
.adm-del { background:#fef2f2; color:#dc2626; border:1px solid #fca5a5; border-radius:8px; width:30px; height:30px; flex-shrink:0; cursor:pointer; font-size:13px; }
.adm-add { background:#ecfdf5; color:#065f46; border:1px dashed #6ee7b7; border-radius:9px; padding:9px 16px; font-size:12.5px; font-weight:700; cursor:pointer; margin-bottom:12px; }
</style>

{{-- Tabs --}}
<div class="cust-tabs">
  <button type="button" class="cust-tab active" id="tab-btn-general" onclick="switchTab('general')">General &amp; Settings</button>
  <button type="button" class="cust-tab" id="tab-btn-homepage" onclick="switchTab('homepage')">Homepage</button>
  <button type="button" class="cust-tab" id="tab-btn-admission" onclick="switchTab('admission')">Admission</button>
  <button type="button" class="cust-tab" id="tab-btn-studentlife" onclick="switchTab('studentlife')">Student Life</button>
  <button type="button" class="cust-tab" id="tab-btn-about" onclick="switchTab('about')">About Us</button>
  <button type="button" class="cust-tab" id="tab-btn-academic" onclick="switchTab('academic')">Academic</button>
</div>

@php
  // [key, number, title, description, hasToggle, movable]
  $generalSections = [
    ['general', '·', 'General & Site Identity', 'Name, logo, contact, footer, social — used site-wide', false, false],
  ];
  $homeSections = [
    ['hero',      1, 'Hero Section',          'The large banner at the top of the homepage', true, true],
    ['highlights',2, 'Highlights Section',    'The "What Makes Us Stand Out" cards', true, true],
    ['about',     3, 'About Section',         'The "About Our School" block', true, true],
    ['classes',   4, 'Classes Section',       'The "Classes We Offer" cards', true, true],
    ['why',       5, 'Why Choose Us Section', 'The advantage cards', true, true],
    ['halloffame',6, 'Hall of Fame Section',  'Featured achievers shown on the homepage', true, true],
    ['gallery',   7, 'Gallery Section',       'School gallery heading (photos from Gallery module)', true, true],
    ['news',      8, 'Latest News Section',   'Latest news heading (articles from News module)', true, true],
    ['notices',   9, 'Notices & Circulars',   'Latest notices/circulars from the Downloads module', true, true],
    ['links',     10,'Important Links',       'Useful links from the Important Links module', true, true],
    ['admission', 11,'Admission Section',     'Admission info + inquiry form', true, true],
    ['principal', 12,"Principal's Message",   "Principal's photo, quote and message", true, true],
    ['location',  13,'Location & Contact',    'Map and contact section', true, true],
  ];
  $admissionSections = [
    ['admission_page', 1, 'Admission Page',   'Turn the whole Admission page on/off + page header', true, false],
    ['adm_items',      2, 'Information Items', 'Eligibility, documents, fees — add, edit & reorder', true, true],
    ['adm_whatsapp',   3, 'WhatsApp Contacts', 'Named WhatsApp help-desk numbers', true, true],
    ['adm_timing',     4, 'School Timings',    'The timings block on the admission page', true, true],
    ['adm_form',       5, 'Inquiry Form',      'The admission inquiry form (stays in the right column)', true, false],
  ];
  $studentLifeSections = [
    ['student_life_page', 1, 'Student Life Page', 'Turn the whole Student Life page on/off + page header', true, false],
    ['sl_albums',         2, 'Photo Albums',      'Heading for the Student Life photo-album section', true, true],
    ['sl_videos',         3, 'Videos',            'Heading for the videos section', true, true],
  ];
  $aboutSections = [
    ['about_intro',          1, 'About Our School',                  'Intro text, emblem, photo, years badge & feature points', true, true],
    ['about_principal',      2, "Principal's Message",               "Principal's photo, quote & message", true, true],
    ['about_administration', 3, 'Administration',                    'School leadership & administrative team members', true, true],
    ['about_faculty',        4, 'Faculty & Staff',                   'Teachers shown live from the Teachers module', true, true],
    ['about_smc',            5, 'School Managing Committee (SMC)',    'SMC committee members', true, true],
    ['about_pta',            6, 'Parents & Teachers Association',     'PTA committee members', true, true],
    ['about_certs',          7, 'Certificates & Documents',          'Land, fire-safety, water-testing & building certificates', true, true],
  ];
  $academicSections = [
    ['acad_highlights', 1, 'Highlights',       'The "What Makes Us Stand Out" strip on the Academics page', true, true],
    ['acad_classes',    2, 'Classes We Offer', 'The class cards shown on the Academics page', true, true],
    ['acad_fee',        3, 'Fee Structure',    'Fee Structure card + sub-page content', true, true],
    ['acad_timing',     4, 'School Timing',    'School Timing card + sub-page content', true, true],
    ['acad_calendar',   5, 'Academic Calendar', 'Calendar images shown on the /academics/calendar sub-page', false, false],
    ['acad_curriculum', 6, 'Curriculum',       'Curriculum cards shown on the /academics/curriculum sub-page', false, false],
    ['acad_downloads',  7, 'Textbooks & Results', 'Links the Textbooks & Results pages to the Downloads module', false, false],
  ];

  // Apply each tab's saved drag-to-reorder order.
  $applyOrder = function (array $sections, string $key): array {
      $saved = json_decode(\App\Helpers\Settings::get($key, ''), true);
      if (is_array($saved) && $saved) {
          usort($sections, function ($a, $b) use ($saved) {
              $ia = array_search($a[0], $saved, true);
              $ib = array_search($b[0], $saved, true);
              return ($ia === false ? 999 : $ia) <=> ($ib === false ? 999 : $ib);
          });
      }
      return $sections;
  };
  $homeSections        = $applyOrder($homeSections, 'home_section_order');
  $admissionSections   = $applyOrder($admissionSections, 'admission_section_order');
  $studentLifeSections = $applyOrder($studentLifeSections, 'studentlife_section_order');
  $aboutSections       = $applyOrder($aboutSections, 'about_section_order');
  $academicSections    = $applyOrder($academicSections, 'academic_section_order');
@endphp

{{-- TAB 1 — General & Settings --}}
<div id="tab-general">
  @foreach($generalSections as [$key, $num, $title, $desc, $hasToggle, $movable])
    @include('admin.customizer._acc')
  @endforeach
</div>

{{-- TAB 2 — Homepage --}}
<div id="tab-homepage" style="display:none;">
  <p style="font-size:12px; color:#64748b; margin:0 0 14px;">Each card is a block on the homepage. <strong>Drag</strong> a card by its grip handle to reorder how sections appear, use the switch to show / hide it, and click a card to edit its content.</p>
  <div id="home-sections" data-area="home">
    @foreach($homeSections as [$key, $num, $title, $desc, $hasToggle, $movable])
      @include('admin.customizer._acc', ['draggable' => true])
    @endforeach
  </div>
</div>

{{-- TAB 3 — Admission --}}
<div id="tab-admission" style="display:none;">
  <p style="font-size:12px; color:#64748b; margin:0 0 14px;">Manage the Admission page. The first card turns the whole page on/off. <strong>Drag</strong> the Information Items, WhatsApp and Timings cards by their grip handle to reorder how they appear on the page — the page header and inquiry form stay pinned.</p>
  <div data-area="admission">
    @foreach($admissionSections as [$key, $num, $title, $desc, $hasToggle, $movable])
      @include('admin.customizer._acc', ['draggable' => true])
    @endforeach
  </div>
</div>

{{-- TAB 4 — Student Life --}}
<div id="tab-studentlife" style="display:none;">
  <p style="font-size:12px; color:#64748b; margin:0 0 14px;">Manage the Student Life page (Programme albums + Videos). The first card turns the whole page on/off. <strong>Drag</strong> the Photo Albums and Videos cards by their grip handle to reorder how they appear on the page.</p>
  <div data-area="studentlife">
    @foreach($studentLifeSections as [$key, $num, $title, $desc, $hasToggle, $movable])
      @include('admin.customizer._acc', ['draggable' => true])
    @endforeach
  </div>
</div>

{{-- TAB — About Us --}}
<div id="tab-about" style="display:none;">
  <p style="font-size:12px; color:#64748b; margin:0 0 14px;">Upload official school certificates and documents. They appear as a showcase on the public About Us page where visitors can view each one full-screen.</p>
  <div data-area="about">
    @foreach($aboutSections as [$key, $num, $title, $desc, $hasToggle, $movable])
      @include('admin.customizer._acc', ['draggable' => true])
    @endforeach
  </div>
</div>

{{-- TAB 5 — Academic --}}
<div id="tab-academic" style="display:none;">
  <p style="font-size:12px; color:#64748b; margin:0 0 14px;">Everything on the Academics page. <strong>Drag</strong> a card by its grip handle to reorder sections, use the switch to show / hide a block, and click a card to edit its content.</p>
  <div data-area="academic">
    @foreach($academicSections as [$key, $num, $title, $desc, $hasToggle, $movable])
      @include('admin.customizer._acc', ['draggable' => true])
    @endforeach
  </div>
</div>

<script>
  function switchTab(name){
    ['general','homepage','admission','studentlife','about','academic'].forEach(function(t){
      document.getElementById('tab-' + t).style.display = (t === name) ? 'block' : 'none';
      document.getElementById('tab-btn-' + t).classList.toggle('active', t === name);
    });
    try { localStorage.setItem('customizerTab', name); } catch(e){}
  }

  /* Restore the last-open tab after a save redirect. */
  (function(){
    var saved;
    try { saved = localStorage.getItem('customizerTab'); } catch(e){}
    var valid = ['general','homepage','admission','studentlife','about','academic'];
    if(saved && valid.indexOf(saved) !== -1 && saved !== 'general'){
      switchTab(saved);
    }
  })();
  function toggleAcc(key){
    document.getElementById('acc-' + key).classList.toggle('open');
  }
  function toggleSection(input){
    const key = input.dataset.key;
    const val = input.checked ? 1 : 0;
    document.getElementById('acc-' + key).classList.toggle('off', !input.checked);
    fetch('{{ route('admin.customizer.toggle-section') }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ key: key, value: val })
    }).catch(() => {});
  }

  /* --- Customizer: drag to reorder sections (every tab) --- */
  document.querySelectorAll('[data-area]').forEach(function(list){
    const area = list.dataset.area;
    let dragEl = null;

    function renumber(){
      list.querySelectorAll(':scope > .acc .acc-num').forEach(function(n, i){ n.textContent = i + 1; });
    }
    function saveOrder(){
      const order = [...list.querySelectorAll(':scope > .acc')]
        .map(function(a){ return a.dataset.section; })
        .filter(Boolean);
      fetch('{{ route('admin.customizer.reorder-sections') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ area: area, order: order })
      }).catch(function(){});
    }

    list.querySelectorAll(':scope > .acc').forEach(function(acc){
      const grip = acc.querySelector('.acc-grip');
      if(!grip) return; // pinned section — not draggable
      grip.addEventListener('mousedown', function(){ acc.draggable = true; });
      grip.addEventListener('mouseup',   function(){ acc.draggable = false; });
      acc.addEventListener('dragstart', function(e){
        dragEl = acc;
        acc.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
      });
      acc.addEventListener('dragend', function(){
        acc.classList.remove('dragging');
        acc.draggable = false;
        dragEl = null;
        renumber();
        saveOrder();
      });
      acc.addEventListener('dragover', function(e){
        e.preventDefault();
        if(!dragEl || dragEl === acc) return;
        const box = acc.getBoundingClientRect();
        const before = (e.clientY - box.top) < box.height / 2;
        list.insertBefore(dragEl, before ? acc : acc.nextSibling);
      });
    });

    renumber();
  });

  /* --- Admission: dynamic info-item / contact rows + row drag --- */
  (function(){
    let uid = 9000;

    function bindRow(row, list){
      const grip = row.querySelector('.adm-row-grip');
      if(grip){
        grip.addEventListener('mousedown', function(){ row.draggable = true; });
        grip.addEventListener('mouseup',   function(){ row.draggable = false; });
      }
      row.addEventListener('dragstart', function(e){
        list._drag = row;
        row.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
      });
      row.addEventListener('dragend', function(){
        row.classList.remove('dragging');
        row.draggable = false;
        list._drag = null;
      });
      row.addEventListener('dragover', function(e){
        e.preventDefault();
        const d = list._drag;
        if(!d || d === row) return;
        const box = row.getBoundingClientRect();
        const before = (e.clientY - box.top) < box.height / 2;
        list.insertBefore(d, before ? row : row.nextSibling);
      });
    }

    function addRow(listId, tplId){
      const list = document.getElementById(listId);
      const tpl  = document.getElementById(tplId);
      if(!list || !tpl) return;
      const html = tpl.innerHTML.replace(/__I__/g, 'n' + (++uid));
      list.insertAdjacentHTML('beforeend', html);
      const row = list.lastElementChild;
      bindRow(row, list);
    }

    window.admAddItem    = function(){ addRow('adm-items-list',    'tpl-adm-item'); };
    window.admAddContact = function(){ addRow('adm-contacts-list', 'tpl-adm-contact'); };
    window.pplAddRow     = function(key){ addRow('ppl-list-' + key, 'tpl-ppl-' + key); };
    window.certAddRow    = function(){ addRow('cert-list', 'tpl-cert'); };
    window.calAddRow     = function(){ addRow('cal-list', 'tpl-cal'); };
    window.currAddRow    = function(){ addRow('curr-list', 'tpl-curr'); };
    window.admDelRow     = function(btn){ const r = btn.closest('.adm-row'); if(r) r.remove(); };

    ['adm-items-list', 'adm-contacts-list', 'cert-list', 'cal-list', 'curr-list',
     'ppl-list-about_administration', 'ppl-list-about_smc', 'ppl-list-about_pta'].forEach(function(id){
      const list = document.getElementById(id);
      if(list) list.querySelectorAll('.adm-row').forEach(function(r){ bindRow(r, list); });
    });
  })();
</script>
@endsection
