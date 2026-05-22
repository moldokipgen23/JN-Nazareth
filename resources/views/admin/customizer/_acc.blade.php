@php $accDrag = ($draggable ?? false) && ($movable ?? true); @endphp
<div class="acc {{ $hasToggle && !$on($key) ? 'off' : '' }}" id="acc-{{ $key }}"
     @if($accDrag) data-section="{{ $key }}" @endif>
  <div class="acc-head" onclick="toggleAcc('{{ $key }}')">
    <div class="acc-titlewrap">
      @if($accDrag)
        <i class="fas fa-grip-vertical acc-grip" title="Drag to reorder" onclick="event.stopPropagation()"></i>
      @elseif($draggable ?? false)
        <i class="fas fa-thumbtack" title="Pinned — fixed position" style="color:#94a3b8;font-size:13px;padding:8px 9px;"></i>
      @endif
      <span class="acc-num">{{ $num }}</span>
      <div><strong>{{ $title }}</strong><span>{{ $desc }}</span></div>
    </div>
    <div class="acc-right">
      @if($hasToggle)
        <label class="switch" onclick="event.stopPropagation()" title="Show / hide on website">
          <input type="checkbox" data-key="{{ $key }}" {{ $on($key) ? 'checked' : '' }} onchange="toggleSection(this)">
          <span class="slider"></span>
        </label>
      @endif
      <i class="chev fas fa-chevron-down"></i>
    </div>
  </div>
  <div class="acc-body">

    {{-- ========== GENERAL ========== --}}
    @if($key === 'general')
    <form method="POST" action="{{ route('admin.customizer.save') }}" enctype="multipart/form-data">
      @csrf
      <div class="acc-section-h">School Identity</div>
      <div class="g2">
        {!! $inp('school_name','School Name','J.N. Nazareth English School') !!}
        {!! $inp('school_tagline','Tagline','Quality Education for a Better Future') !!}
      </div>
      <div class="g3">
        {!! $inp('school_established','Established Year','1996') !!}
        {!! $inp('school_board','Education Board','Manipur Board') !!}
        {!! $inp('address_short','Short Address','Khengjang · Churachandpur · Manipur') !!}
      </div>
      <div class="g1">{!! $img('school_logo','School Logo') !!}</div>
      <div class="g1">{!! $ta('ticker_text','Top Ticker Text','Admissions Open for the New Session — Enquire Now') !!}</div>
      <div style="display:flex; align-items:center; gap:10px; margin:-4px 0 14px;">
        <input type="hidden" name="sec_show_ticker" value="0">
        <label class="switch">
          <input type="checkbox" name="sec_show_ticker" value="1" {{ $S('sec_show_ticker','1') !== '0' ? 'checked' : '' }}>
          <span class="slider"></span>
        </label>
        <span style="font-size:12px; color:#374151; font-weight:600;">Show the top ticker bar on the website</span>
      </div>
      <div class="acc-section-h">Contact & Social</div>
      <div class="g2">
        {!! $inp('contact_phone','Phone (display)','+91 98628 80292') !!}
        {!! $inp('whatsapp','WhatsApp Number (digits only)','919862880292') !!}
      </div>
      <div class="g2">
        {!! $inp('contact_email','Contact Email') !!}
        {!! $inp('address_short','Short Address','Khengjang · Churachandpur · Manipur') !!}
      </div>
      <div class="g1">{!! $ta('contact_address','Full Address','Khengjang, B.P.O. Koite, Churachandpur – 795128, Manipur, India') !!}</div>
      <div class="g3">
        {!! $inp('social_facebook','Facebook URL') !!}
        {!! $inp('social_instagram','Instagram URL') !!}
        {!! $inp('social_youtube','YouTube URL') !!}
      </div>
      <div class="g1">{!! $ta('footer_desc','Footer Description','One of the finest English medium schools in Churachandpur, Manipur — offering quality education, strong values, and disciplined learning from Preparatory to Class 10.') !!}</div>

      <div class="acc-section-h">SEO &amp; Branding</div>
      <p class="subnote">These control how the site appears in search engines and when shared on social media.</p>
      <div class="g2">
        {!! $img('favicon','Favicon (browser-tab icon — PNG/ICO, square)') !!}
        {!! $img('seo_og_image','Social Share Image (shown when the site is shared — 1200×630)') !!}
      </div>
      <div class="g1">{!! $ta('seo_meta_description','Default Meta Description (≈155 characters, used by Google)','') !!}</div>
      <div class="g1">{!! $inp('seo_meta_keywords','Meta Keywords (comma-separated)','school, english medium, churachandpur, manipur') !!}</div>
      <div class="g1">{!! $inp('seo_google_verification','Google Search Console — verification token','') !!}</div>
      <p class="subnote">Paste only the content value from Google\'s <code>&lt;meta name="google-site-verification"&gt;</code> tag.</p>

      <div class="acc-section-h">Login URLs &amp; Security</div>
      <p class="subnote">Custom login addresses hide the admin panel from bots. After saving, the default <code>/login</code> stops working — use the URLs below. Use letters, numbers and hyphens only.</p>
      <div class="g2">
        {!! $inp('admin_login_path','Admin Login Path','admin-portal') !!}
        {!! $inp('teacher_login_path','Teacher Login Path','teacher-portal') !!}
      </div>
      <p class="subnote">
        Admin login: <code>{{ url('/') }}/{{ $S('admin_login_path','admin-portal') }}</code> &nbsp;·&nbsp;
        Teacher login: <code>{{ url('/') }}/{{ $S('teacher_login_path','teacher-portal') }}</code><br>
        Emergency fallback (keep private): <code>{{ url('/') }}/cms-recovery-7k3</code>
      </p>

      <button class="save-btn">Save General</button>
    </form>
    @endif

    {{-- ========== HERO ========== --}}
    @if($key === 'hero')
    <form method="POST" action="{{ route('admin.customizer.save') }}" enctype="multipart/form-data">
      @csrf
      <div class="g1">{!! $img('hero_image','Hero Background Image') !!}</div>
      <div class="g1">{!! $ta('hero_tagline','Hero Tagline','Quality Education for a Better Future') !!}</div>
      <div class="g2">
        {!! $inp('hero_badge_text','Top Badge Text','Govt. Recognised · Est. 1996 · Manipur Board Approved') !!}
        {!! $inp('hero_btn1_text','Primary Button Text','Admission Open') !!}
      </div>
      <div class="g2">{!! $inp('hero_btn2_text','Secondary Button Text','Contact School') !!}</div>
      <div class="acc-section-h">Hero Stat Bar (5 quick facts)</div>
      @for($i=1;$i<=5;$i++)
        <div class="blk">
          <div class="blk-t">Stat {{ $i }}</div>
          <div class="g2">{!! $inp("hero_stat_{$i}_value","Value") !!}{!! $inp("hero_stat_{$i}_label","Label") !!}</div>
        </div>
      @endfor
      <button class="save-btn">Save Hero</button>
    </form>
    @endif

    {{-- ========== ABOUT ========== --}}
    @if($key === 'about')
    <form method="POST" action="{{ route('admin.customizer.save') }}" enctype="multipart/form-data">
      @csrf
      <div class="g2">
        {!! $inp('about_title','Section Title','A Legacy of Quality English Education in Manipur') !!}
        {!! $inp('about_years_num','Years Badge Number','28+') !!}
      </div>
      <div class="g1">{!! $inp('about_label','Section Label','About Our School') !!}</div>
      <div class="g1">{!! $ta('about_text_1','Paragraph 1','',4) !!}</div>
      <div class="g1">{!! $ta('about_text_2','Paragraph 2','',4) !!}</div>
      <div class="g2">{!! $img('about_emblem','School Emblem Image') !!}{!! $img('about_image','Main About Image') !!}</div>
      <div class="acc-section-h">Feature Points (3)</div>
      @for($i=1;$i<=3;$i++)
        <div class="blk">
          <div class="blk-t">Feature {{ $i }}</div>
          {!! $inp("about_feat_{$i}_title","Title") !!}
          <div style="height:10px;"></div>
          {!! $ta("about_feat_{$i}_desc","Description") !!}
        </div>
      @endfor
      <button class="save-btn">Save About</button>
    </form>
    @endif

    {{-- ========== HIGHLIGHTS ========== --}}
    @if($key === 'highlights')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g2">
        {!! $inp('highlights_label','Section Label','School Highlights') !!}
        {!! $inp('highlights_title','Section Title','What Makes Us Stand Out') !!}
      </div>
      <div class="g1">{!! $ta('highlights_sub','Section Subtitle','Delivering quality education with discipline, English excellence, and a nurturing environment for every child.') !!}</div>
      <div class="acc-section-h">Highlight Cards (5)</div>
      @php
        $hlD = [
          1 => ['fas fa-school','Preparatory-X','Complete School Journey from Early Years to High School'],
          2 => ['fas fa-chalkboard-user','Qualified','Trained & Experienced Teachers Committed to Every Student'],
          3 => ['fas fa-lightbulb','Smart','Modern & Engaging Learning Environment for Better Understanding'],
          4 => ['fas fa-globe','English','100% English Medium Instruction Across All Classes'],
          5 => ['fas fa-medal','Board ✓','Approved High School Under Manipur Board of Secondary Education'],
        ];
      @endphp
      @for($i=1;$i<=5;$i++)
        <div class="blk">
          <div class="blk-t">Card {{ $i }}</div>
          <div class="g3">
            {!! $inp("hl_{$i}_icon","Icon (Font Awesome class)",$hlD[$i][0]) !!}
            {!! $inp("hl_{$i}_value","Value / Heading",$hlD[$i][1]) !!}
            {!! $inp("hl_{$i}_label","Label / Description",$hlD[$i][2]) !!}
          </div>
        </div>
      @endfor
      <button class="save-btn">Save Highlights</button>
    </form>
    @endif

    {{-- ========== CLASSES ========== --}}
    @if($key === 'classes')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g2">
        {!! $inp('classes_label','Section Label','Academic Programme') !!}
        {!! $inp('classes_title','Section Title','Classes We Offer') !!}
      </div>
      <div class="g1">{!! $ta('classes_sub','Section Subtitle','A structured, English medium journey from early childhood through board-level high school education.') !!}</div>
      <div class="acc-section-h">Class Cards (6)</div>
      @php
        $clsD = [
          1 => ['🌱','Preparatory','Early Years','Play-based learning, English familiarisation, and foundational skills in a safe, nurturing, and joyful classroom setting.','Ages 3 – 4'],
          2 => ['🔤','LKG','(KG-1)','Building strong reading, writing, and numeracy foundations through structured English activities, stories, and interactive lessons.','Ages 4 – 5'],
          3 => ['🔤','UKG','(KG-2)','Building strong reading, writing, and numeracy foundations through structured English activities, stories, and interactive lessons.','Ages 5 – 6'],
          4 => ['📘','Primary','Class I – V','A comprehensive curriculum — English, Mathematics, Science, and Social Studies — building a strong academic base with curiosity and confidence.','Ages 6 – 11'],
          5 => ['📗','Middle School','Class VI – VIII','Deepening knowledge across all subjects with structured learning, preparing students for the rigor of high school education.','Ages 11 – 14'],
          6 => ['🏆','High School','Class IX – X','Board-approved Class 9 & 10 — rigorous preparation for board exams and future academic success.','Manipur Board'],
        ];
      @endphp
      @for($i=1;$i<=6;$i++)
        <div class="blk">
          <div class="blk-t">Class {{ $i }}</div>
          <div class="g3">
            {!! $inp("cls_{$i}_emoji","Emoji / Icon",$clsD[$i][0]) !!}
            {!! $inp("cls_{$i}_name","Class Name",$clsD[$i][1]) !!}
            {!! $inp("cls_{$i}_range","Range / Subtitle",$clsD[$i][2]) !!}
          </div>
          {!! $ta("cls_{$i}_desc","Description",$clsD[$i][3]) !!}
          <div style="height:10px;"></div>
          {!! $inp("cls_{$i}_tag","Tag / Age",$clsD[$i][4]) !!}
        </div>
      @endfor
      <button class="save-btn">Save Classes</button>
    </form>
    @endif

    {{-- ========== ADMISSION ========== --}}
    @if($key === 'admission')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g2">
        {!! $inp('admission_label','Section Label','Admissions') !!}
        {!! $inp('admission_title','Section Title','Admission Open for New Session') !!}
      </div>
      <div class="g1">{!! $ta('admission_sub','Section Subtitle','Join one of the finest English medium schools in Churachandpur. We welcome students who are eager to learn, grow, and excel.') !!}</div>
      <div class="acc-section-h">Info Points</div>
      <div class="g1">{!! $ta('adm_eligibility','Eligibility','Children between ages 3–16. Students from any school background are welcome to apply for classes from Preparatory to Class 10.') !!}</div>
      <div class="g1">{!! $ta('adm_documents','Required Documents','Birth certificate · Previous class marksheet / TC (if applicable) · 2 passport-size photos · Parent / guardian ID proof.') !!}</div>
      <div class="g1">{!! $ta('fee_structure_text','Fee Structure Note','We offer quality education at an affordable cost. Contact us on WhatsApp or visit school for detailed fee information.') !!}</div>
      <div class="acc-section-h">Timings</div>
      <div class="g2">
        {!! $inp('school_timing_weekday','Mon–Fri Timing','8:00 AM – 3:00 PM') !!}
        {!! $inp('school_timing_saturday','Saturday Timing','8:00 AM – 12:00 PM') !!}
      </div>
      <button class="save-btn">Save Admission</button>
    </form>
    @endif

    {{-- ========== HALL OF FAME ========== --}}
    @if($key === 'halloffame')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">Achievers are managed in the <a href="{{ route('admin.hall-of-fame.index') }}" style="color:#0f766e;font-weight:600;">Hall of Fame</a> module. The homepage shows up to 6 featured achievers.</p>
      <div class="g2">
        {!! $inp('hof_label','Section Label','Hall of Fame') !!}
        {!! $inp('hof_title','Section Title','Our Pride & Achievers') !!}
      </div>
      <div class="g1">{!! $ta('hof_sub','Section Subtitle','Celebrating the students who make our school proud through academics, sports, and excellence.') !!}</div>
      <button class="save-btn">Save Hall of Fame</button>
    </form>
    @endif

    {{-- ========== GALLERY ========== --}}
    @if($key === 'gallery')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">Photos are managed in the <a href="{{ route('admin.gallery.index') }}" style="color:#0f766e;font-weight:600;">Gallery</a> module.</p>
      <div class="g2">
        {!! $inp('gallery_label','Section Label','School Gallery') !!}
        {!! $inp('gallery_title','Section Title','Life at Our School') !!}
      </div>
      <div class="g1">{!! $ta('gallery_sub','Section Subtitle','A glimpse into our vibrant school life — events, sports, students, and campus moments captured through the years.') !!}</div>
      <button class="save-btn">Save Gallery</button>
    </form>
    @endif

    {{-- ========== NEWS ========== --}}
    @if($key === 'news')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">Articles come from the <a href="{{ route('admin.blogs.index') }}" style="color:#0f766e;font-weight:600;">News &amp; Notices</a> module.</p>
      <div class="g2">
        {!! $inp('news_label','Section Label','News & Notices') !!}
        {!! $inp('news_title','Section Title','Latest School News') !!}
      </div>
      <button class="save-btn">Save News</button>
    </form>
    @endif

    {{-- ========== NOTICES & CIRCULARS ========== --}}
    @if($key === 'notices')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">Shows the latest notices/circulars from the <a href="{{ route('admin.downloads.index') }}" style="color:#0f766e;font-weight:600;">Downloads</a> module. "View All Notices" links to the News page.</p>
      <div class="g2">
        {!! $inp('notices_label','Section Label','Notices & Circulars') !!}
        {!! $inp('notices_title','Section Title','Latest Notices & Circulars') !!}
      </div>
      <div class="g1">{!! $ta('notices_sub','Section Subtitle','Important notices, circulars and downloadable forms for parents and students.') !!}</div>
      <button class="save-btn">Save Notices &amp; Circulars</button>
    </form>
    @endif

    {{-- ========== IMPORTANT LINKS ========== --}}
    @if($key === 'links')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">Links are managed in the <a href="{{ route('admin.important-links.index') }}" style="color:#0f766e;font-weight:600;">Important Links</a> module.</p>
      <div class="g2">
        {!! $inp('links_label','Section Label','Important Links') !!}
        {!! $inp('links_title','Section Title','Quick & Useful Links') !!}
      </div>
      <div class="g1">{!! $ta('links_sub','Section Subtitle','Helpful resources and official links for parents, students and visitors.') !!}</div>
      <button class="save-btn">Save Important Links</button>
    </form>
    @endif

    {{-- ========== WHY ========== --}}
    @if($key === 'why')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g2">
        {!! $inp('why_label','Section Label','Why Choose Us') !!}
        {!! $inp('why_title','Section Title','The School Advantage') !!}
      </div>
      <div class="g1">{!! $ta('why_sub','Section Subtitle','We combine academic rigour with holistic development, preparing every student for a bright and confident future.') !!}</div>
      <div class="acc-section-h">Advantage Cards (6)</div>
      @php
        $whyD = [
          1 => ['fas fa-book-open','Best English Education','Complete English medium instruction from Preparatory to Class 10 builds strong language proficiency, communication confidence, and academic readiness in every student.'],
          2 => ['fas fa-scale-balanced','Discipline & Values','Rooted in strong moral values and structured discipline, we develop not just academically capable students but responsible, ethical individuals prepared for life.'],
          3 => ['fas fa-user-tie','Experienced Teachers','Our dedicated and qualified teaching staff are passionate about the academic and personal growth of every student, offering focused attention and mentorship.'],
          4 => ['fas fa-heart','Student-Friendly Environment','A safe, inclusive, and nurturing campus where every child is respected, encouraged to participate, and celebrated for their unique strengths and talents.'],
          5 => ['fas fa-trophy','Academic Excellence','A strong track record of academic performance with students achieving excellent results and going on to pursue higher education with confidence and skill.'],
          6 => ['fas fa-running','Sports & Co-Curriculars','Beyond academics, we offer sports, cultural activities, and annual events that build teamwork, leadership, creativity, and well-rounded personalities.'],
        ];
      @endphp
      @for($i=1;$i<=6;$i++)
        <div class="blk">
          <div class="blk-t">Card {{ $i }}</div>
          <div class="g2">
            {!! $inp("why_{$i}_icon","Icon (Font Awesome class)",$whyD[$i][0]) !!}
            {!! $inp("why_{$i}_title","Title",$whyD[$i][1]) !!}
          </div>
          {!! $ta("why_{$i}_desc","Description",$whyD[$i][2]) !!}
        </div>
      @endfor
      <button class="save-btn">Save Why Choose Us</button>
    </form>
    @endif

    {{-- ========== PRINCIPAL ========== --}}
    @if($key === 'principal')
    <form method="POST" action="{{ route('admin.customizer.save') }}" enctype="multipart/form-data">
      @csrf
      <div class="g1">{!! $inp('principal_name','Principal Name','Ngamboi Kipgen') !!}</div>
      <div class="g1">{!! $img('principal_photo','Principal Photo') !!}</div>
      <div class="g1">{!! $ta('principal_quote','Highlighted Quote','Education is not the filling of a pail, but the lighting of a fire. At our school, we believe every child carries within them immense potential — and our role is to kindle that spark.',3) !!}</div>
      <div class="g1">{!! $ta('principal_message','Full Message','',6) !!}</div>
      <button class="save-btn">Save Principal</button>
    </form>
    @endif

    {{-- ========== LOCATION ========== --}}
    @if($key === 'location')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g2">
        {!! $inp('location_label','Section Label','Find Us') !!}
        {!! $inp('location_title','Section Title','Location & Contact') !!}
      </div>
      <div class="g1">{!! $ta('location_sub','Section Subtitle','Located in Khengjang, Churachandpur — at the heart of the community and easily accessible.') !!}</div>
      <div class="g1">{!! $ta('map_embed_url','Google Maps Embed URL','https://maps.google.com/maps?q=24.388994,93.700615&output=embed&z=16&hl=en') !!}</div>
      <div class="g1">{!! $inp('map_directions_url','Google Maps Directions Link','https://maps.app.goo.gl/DfLpg7QV7DaXM37r9') !!}</div>
      <button class="save-btn">Save Location</button>
    </form>
    @endif

    {{-- ========== ACADEMIC — HIGHLIGHTS ========== --}}
    @if($key === 'acad_highlights')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g2">
        {!! $inp('acad_hl_label','Section Label','School Highlights') !!}
        {!! $inp('acad_hl_title','Section Title','What Makes Us Stand Out') !!}
      </div>
      <div class="g1">{!! $ta('acad_hl_sub','Section Subtitle','Delivering quality education with discipline, English excellence, and a nurturing environment for every child.') !!}</div>
      <div class="acc-section-h">Highlight Items (5)</div>
      @php
        $acadHlD = [
          1 => ['fas fa-school','Preparatory-X','Complete School Journey from Early Years to High School'],
          2 => ['fas fa-chalkboard-user','Qualified','Trained & Experienced Teachers Committed to Every Student'],
          3 => ['fas fa-lightbulb','Smart','Modern & Engaging Learning Environment for Better Understanding'],
          4 => ['fas fa-globe','English','100% English Medium Instruction Across All Classes'],
          5 => ['fas fa-medal','Board ✓','Approved High School Under Manipur Board of Secondary Education'],
        ];
      @endphp
      @for($i=1;$i<=5;$i++)
        <div class="blk">
          <div class="blk-t">Highlight {{ $i }}</div>
          <div class="g3">
            {!! $inp("acad_hl_{$i}_icon","Icon (Font Awesome class)",$acadHlD[$i][0]) !!}
            {!! $inp("acad_hl_{$i}_value","Value",$acadHlD[$i][1]) !!}
            {!! $inp("acad_hl_{$i}_label","Label",$acadHlD[$i][2]) !!}
          </div>
        </div>
      @endfor
      <button class="save-btn">Save Highlights</button>
    </form>
    @endif

    {{-- ========== ACADEMIC — CLASSES ========== --}}
    @if($key === 'acad_classes')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g2">
        {!! $inp('acad_cls_label','Section Label','Academic Programme') !!}
        {!! $inp('acad_cls_title','Section Title','Classes We Offer') !!}
      </div>
      <div class="g1">{!! $ta('acad_cls_sub','Section Subtitle','A structured, English medium journey from early childhood through board-level high school education.') !!}</div>
      <div class="acc-section-h">Class Cards (6)</div>
      @php
        $acadClsD = [
          1 => ['🌱','Preparatory','Early Years','Play-based learning, English familiarisation, and foundational skills in a safe, nurturing, and joyful classroom setting.','Ages 3 – 4'],
          2 => ['🔤','LKG','(KG-1)','Building strong reading, writing, and numeracy foundations through structured English activities, stories, and interactive lessons.','Ages 4 – 5'],
          3 => ['🔤','UKG','(KG-2)','Building strong reading, writing, and numeracy foundations through structured English activities, stories, and interactive lessons.','Ages 5 – 6'],
          4 => ['📘','Primary','Class I – V','A comprehensive curriculum — English, Mathematics, Science, and Social Studies — building a strong academic base with curiosity and confidence.','Ages 6 – 11'],
          5 => ['📗','Middle School','Class VI – VIII','Deepening knowledge across all subjects with structured learning, preparing students for the rigor of high school education.','Ages 11 – 14'],
          6 => ['🏆','High School','Class IX – X','Board-approved Class 9 & 10 — rigorous preparation for board exams and future academic success.','Manipur Board'],
        ];
      @endphp
      @for($i=1;$i<=6;$i++)
        <div class="blk">
          <div class="blk-t">Class {{ $i }}</div>
          <div class="g3">
            {!! $inp("acad_cls_{$i}_emoji","Emoji / Icon",$acadClsD[$i][0]) !!}
            {!! $inp("acad_cls_{$i}_name","Class Name",$acadClsD[$i][1]) !!}
            {!! $inp("acad_cls_{$i}_range","Range / Subtitle",$acadClsD[$i][2]) !!}
          </div>
          {!! $ta("acad_cls_{$i}_desc","Description",$acadClsD[$i][3]) !!}
          <div style="height:10px;"></div>
          {!! $inp("acad_cls_{$i}_tag","Tag / Age",$acadClsD[$i][4]) !!}
        </div>
      @endfor
      <button class="save-btn">Save Classes</button>
    </form>
    @endif

    {{-- ========== ACADEMIC — FEE STRUCTURE ========== --}}
    @if($key === 'acad_fee')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g1">{!! $inp('acad_fee_title','Page Heading','Fee Information') !!}</div>

      <div class="acc-section-h">Fees Per Class</div>
      <p class="subnote">Each class shows as a flip card on the website. The front shows the class name; clicking it flips to reveal the fees. Leave both fee fields blank to hide that class.</p>
      @php
        $feeClasses = ['Preparatory','LKG','UKG','Class I','Class II','Class III','Class IV','Class V','Class VI','Class VII','Class VIII','Class IX','Class X'];
      @endphp
      @foreach($feeClasses as $idx => $cn)
        @php $n = $idx + 1; @endphp
        <div class="blk">
          <div class="g3">
            {!! $inp("acad_fee_{$n}_class","Class Name",$cn) !!}
            {!! $inp("acad_fee_{$n}_admission","One-Time Admission Fee","e.g. ₹2,000 / year") !!}
            {!! $inp("acad_fee_{$n}_tuition","Monthly Tuition Fee","e.g. ₹1,200 / month") !!}
          </div>
        </div>
      @endforeach

      <div class="acc-section-h">Note Below the Table</div>
      <div class="g1">{!! $ta('acad_fee_text','Fee Note','We offer quality education at an affordable cost. Contact us on WhatsApp or visit the school for detailed fee information.',4) !!}</div>
      <button class="save-btn">Save Fee Structure</button>
    </form>
    @endif

    {{-- ========== ACADEMIC — SCHOOL TIMING ========== --}}
    @if($key === 'acad_timing')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <div class="g1">{!! $inp('acad_timing_title','Page Heading','School Timing') !!}</div>
      <div class="g2">
        {!! $inp('acad_timing_weekday','Monday – Friday Timing','8:00 AM – 3:00 PM') !!}
        {!! $inp('acad_timing_saturday','Saturday Timing','8:00 AM – 12:00 PM') !!}
      </div>
      <button class="save-btn">Save School Timing</button>
    </form>
    @endif

    {{-- ========== ACADEMIC — ACADEMIC CALENDAR ========== --}}
    @if($key === 'acad_calendar')
    <form method="POST" action="{{ route('admin.customizer.calendar.save') }}" enctype="multipart/form-data">
      @csrf
      <div class="g2">
        {!! $inp('acad_calendar_title','Page Heading','Academic Calendar') !!}
        {!! $inp('acad_calendar_sub','Sub-heading','Important dates, holidays and events for the school year.') !!}
      </div>

      <div class="acc-section-h">Calendar Images</div>
      <p class="subnote">Upload the academic-calendar page(s) as images. They appear in a grid on the <code>/academics/calendar</code> page, click-to-zoom. Drag rows to reorder.</p>

      <div id="cal-list">
        @foreach(acad_calendar_images() as $i => $c)
          @include('admin.customizer._calendar-row', ['idx' => $i, 'caption' => $c['caption'] ?? '', 'file' => $c['file'] ?? ''])
        @endforeach
      </div>
      <button type="button" class="adm-add" onclick="calAddRow()">+ Add Calendar Image</button>
      <div><button class="save-btn">Save Academic Calendar</button></div>

      <template id="tpl-cal">
        @include('admin.customizer._calendar-row', ['idx' => '__I__', 'caption' => '', 'file' => ''])
      </template>
    </form>
    @endif

    {{-- ========== ACADEMIC — CURRICULUM ========== --}}
    @if($key === 'acad_curriculum')
    <form method="POST" action="{{ route('admin.customizer.curriculum.save') }}">
      @csrf
      <div class="g2">
        {!! $inp('acad_curriculum_title','Page Heading','Our Curriculum') !!}
        {!! $inp('acad_curriculum_sub','Sub-heading','A well-rounded curriculum designed to build knowledge, skills and character.') !!}
      </div>

      <div class="acc-section-h">Curriculum Cards</div>
      <p class="subnote">Each card shows on the <code>/academics/curriculum</code> page in a grid. Drag rows to reorder.</p>

      <div id="curr-list">
        @foreach(acad_curriculum_items() as $i => $c)
          @include('admin.customizer._curriculum-row', ['idx' => $i, 'icon' => $c['icon'] ?? 'fas fa-book', 'title' => $c['title'] ?? '', 'desc' => $c['desc'] ?? ''])
        @endforeach
      </div>
      <button type="button" class="adm-add" onclick="currAddRow()">+ Add Curriculum Card</button>
      <div><button class="save-btn">Save Curriculum</button></div>

      <template id="tpl-curr">
        @include('admin.customizer._curriculum-row', ['idx' => '__I__', 'icon' => 'fas fa-book', 'title' => '', 'desc' => ''])
      </template>
    </form>
    @endif

    {{-- ========== ACADEMIC — TEXTBOOKS & RESULTS ========== --}}
    @if($key === 'acad_downloads')
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:18px;">
      <p style="font-size:13px;color:#334155;margin:0 0 12px;">
        <strong>Prescribed Textbooks</strong> and <strong>Results</strong> are managed from the
        <strong>Downloads</strong> module — upload a PDF there and pick its category.
      </p>
      <ul style="font-size:12.5px;color:#475569;margin:0 0 14px;padding-left:18px;line-height:1.7;">
        <li>Category <strong>Textbook</strong> (or <strong>Syllabus</strong>) &rarr; shows on <code>/academics/textbooks</code></li>
        <li>Category <strong>Result</strong> &rarr; shows on <code>/academics/results</code></li>
      </ul>
      <a href="{{ route('admin.downloads.index') }}" class="save-btn" style="display:inline-block;text-decoration:none;">Open Downloads Manager</a>
    </div>
    @endif

    {{-- ========== ADMISSION — PAGE HEADER ========== --}}
    @if($key === 'admission_page')
    <form method="POST" action="{{ route('admin.customizer.admission.save') }}">
      @csrf
      <p class="subnote">The switch above turns the entire Admission page on or off — when off it is removed from the menu and the URL returns 404.</p>
      <div class="g2">
        {!! $inp('adm_hero_label','Hero Label','Admissions') !!}
        {!! $inp('adm_hero_title','Hero Heading','Admission Open for New Session') !!}
      </div>
      <div class="g1">{!! $ta('adm_hero_sub','Hero Subtitle','Join one of the finest English medium schools in Churachandpur. We welcome students who are eager to learn, grow, and excel.') !!}</div>
      <div class="acc-section-h">Highlight Notice (orange banner)</div>
      <div class="g1">{!! $inp('adm_notice_title','Notice Title','Admissions Open — New Academic Session') !!}</div>
      <div class="g1">{!! $ta('adm_notice_text','Notice Text','Enrollment is currently open for all classes from Preparatory through Class 10. Limited seats available — inquire early!') !!}</div>
      <button class="save-btn">Save Admission Page</button>
    </form>
    @endif

    {{-- ========== ADMISSION — INFORMATION ITEMS ========== --}}
    @if($key === 'adm_items')
    <form method="POST" action="{{ route('admin.customizer.admission.save') }}">
      @csrf
      <p class="subnote">Each item is a card on the Admission page. Drag the grip handle to reorder, use the switch to show / hide, and click Delete to remove.</p>
      <div id="adm-items-list">
        @foreach(admission_items() as $i => $it)
          @include('admin.customizer._adm-item-row', [
            'idx' => $i, 'icon' => $it['icon'] ?? '', 'title' => $it['title'] ?? '',
            'desc' => $it['desc'] ?? '', 'active' => $it['active'] ?? true,
          ])
        @endforeach
      </div>
      <button type="button" class="adm-add" onclick="admAddItem()">+ Add Information Item</button>
      <div><button class="save-btn">Save Information Items</button></div>
      <template id="tpl-adm-item">
        @include('admin.customizer._adm-item-row', [
          'idx' => '__I__', 'icon' => '', 'title' => '', 'desc' => '', 'active' => true,
        ])
      </template>
    </form>
    @endif

    {{-- ========== ADMISSION — WHATSAPP CONTACTS ========== --}}
    @if($key === 'adm_whatsapp')
    <form method="POST" action="{{ route('admin.customizer.admission.save') }}">
      @csrf
      <p class="subnote">Add one or more named WhatsApp help-desk numbers. The first active contact is used by the inquiry form. Drag to reorder.</p>
      <div id="adm-contacts-list">
        @foreach(admission_contacts() as $i => $c)
          @include('admin.customizer._adm-contact-row', [
            'idx' => $i, 'name' => $c['name'] ?? '', 'number' => $c['number'] ?? '',
            'active' => $c['active'] ?? true,
          ])
        @endforeach
      </div>
      <button type="button" class="adm-add" onclick="admAddContact()">+ Add WhatsApp Contact</button>
      <div><button class="save-btn">Save WhatsApp Contacts</button></div>
      <template id="tpl-adm-contact">
        @include('admin.customizer._adm-contact-row', [
          'idx' => '__I__', 'name' => '', 'number' => '', 'active' => true,
        ])
      </template>
    </form>
    @endif

    {{-- ========== ADMISSION — SCHOOL TIMINGS ========== --}}
    @if($key === 'adm_timing')
    <form method="POST" action="{{ route('admin.customizer.admission.save') }}">
      @csrf
      <div class="g1">{!! $inp('adm_timing_title','Block Heading','School Timings') !!}</div>
      <div class="g2">
        {!! $inp('school_timing_weekday','Monday – Friday Timing','8:00 AM – 3:00 PM') !!}
        {!! $inp('school_timing_saturday','Saturday Timing','8:00 AM – 12:00 PM') !!}
      </div>
      <button class="save-btn">Save School Timings</button>
    </form>
    @endif

    {{-- ========== ADMISSION — INQUIRY FORM ========== --}}
    @if($key === 'adm_form')
    <form method="POST" action="{{ route('admin.customizer.admission.save') }}">
      @csrf
      <p class="subnote">Submitted inquiries are recorded in the <a href="{{ route('admin.inquiries.index') }}" style="color:#0f766e;font-weight:600;">Inquiries</a> module and opened in WhatsApp.</p>
      <div class="g1">{!! $inp('adm_form_title','Form Heading','Admission Inquiry Form') !!}</div>
      <div class="g1">{!! $ta('adm_form_sub','Form Subtitle',"Fill the form below — we'll respond on WhatsApp within 24 hours.") !!}</div>
      <button class="save-btn">Save Inquiry Form</button>
    </form>
    @endif

    {{-- ========== STUDENT LIFE — PAGE HEADER ========== --}}
    @if($key === 'student_life_page')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">The switch above turns the entire Student Life page on or off — when off it is removed from the menu and the URL returns 404.</p>
      <div class="g2">
        {!! $inp('sl_hero_label','Hero Label','Student Life') !!}
        {!! $inp('sl_hero_title','Hero Heading','Life Beyond the Classroom') !!}
      </div>
      <div class="g1">{!! $ta('sl_hero_sub','Hero Subtitle','Programmes, events, activities and memorable moments that shape our students every day.') !!}</div>
      <button class="save-btn">Save Student Life Page</button>
    </form>
    @endif

    {{-- ========== STUDENT LIFE — PHOTO ALBUMS ========== --}}
    @if($key === 'sl_albums')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">Albums are managed in <a href="{{ route('admin.gallery-folders.index', ['type' => 'programs']) }}" style="color:#0f766e;font-weight:600;">Student Life Albums</a>.</p>
      <div class="g2">
        {!! $inp('sl_albums_label','Section Label','Photo Albums') !!}
        {!! $inp('sl_albums_title','Section Title','Programmes & Activities') !!}
      </div>
      <div class="g1">{!! $ta('sl_albums_sub','Section Subtitle','Photo albums from our school programmes, events and student activities.') !!}</div>
      <button class="save-btn">Save Photo Albums</button>
    </form>
    @endif

    {{-- ========== STUDENT LIFE — VIDEOS ========== --}}
    @if($key === 'sl_videos')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">Videos are managed in the <a href="{{ route('admin.videos.index') }}" style="color:#0f766e;font-weight:600;">Videos</a> module.</p>
      <div class="g2">
        {!! $inp('sl_videos_label','Section Label','Videos') !!}
        {!! $inp('sl_videos_title','Section Title','Watch Our School in Action') !!}
      </div>
      <div class="g1">{!! $ta('sl_videos_sub','Section Subtitle','Highlights, performances and moments from school life.') !!}</div>
      <button class="save-btn">Save Videos</button>
    </form>
    @endif

    {{-- ========== ABOUT PAGE — INTRO ========== --}}
    @if($key === 'about_intro')
    <form method="POST" action="{{ route('admin.customizer.save') }}" enctype="multipart/form-data">
      @csrf
      <p class="subnote">This is the "About Our School" block on the About Us page. The text &amp; images are shared with the homepage About section.</p>
      <div class="g2">
        {!! $inp('about_title','Section Title','A Legacy of Quality English Education in Manipur') !!}
        {!! $inp('about_years_num','Years Badge Number','28+') !!}
      </div>
      <div class="g2">
        {!! $inp('about_label','Section Label','About Our School') !!}
        {!! $inp('about_years_label','Years Badge Label','Years of Excellence') !!}
      </div>
      <div class="g1">{!! $ta('about_text_1','Paragraph 1','',4) !!}</div>
      <div class="g1">{!! $ta('about_text_2','Paragraph 2','',4) !!}</div>
      <div class="g2">{!! $img('about_emblem','School Emblem Image') !!}{!! $img('about_image','Main About Image') !!}</div>
      <div class="acc-section-h">Feature Points (3)</div>
      @for($i=1;$i<=3;$i++)
        <div class="blk">
          <div class="blk-t">Feature {{ $i }}</div>
          <div class="g2">
            {!! $inp("about_feat_{$i}_icon","Icon (Font Awesome class)") !!}
            {!! $inp("about_feat_{$i}_title","Title") !!}
          </div>
          {!! $ta("about_feat_{$i}_desc","Description") !!}
        </div>
      @endfor
      <button class="save-btn">Save About Our School</button>
    </form>
    @endif

    {{-- ========== ABOUT PAGE — FACULTY & STAFF ========== --}}
    @if($key === 'about_faculty')
    <form method="POST" action="{{ route('admin.customizer.save') }}">
      @csrf
      <p class="subnote">This section shows every active teacher on the About Us page. Add, edit or remove teachers in the <a href="{{ route('admin.teachers.index') }}" style="color:#0f766e;font-weight:600;">Teachers</a> module.</p>
      <div class="g2">
        {!! $inp('about_faculty_label','Section Label','Our People') !!}
        {!! $inp('about_faculty_title','Section Title','Faculty & Staff') !!}
      </div>
      <div class="g1">{!! $ta('about_faculty_sub','Section Subtitle','Meet the dedicated teachers who guide and inspire our students every day.') !!}</div>
      <button class="save-btn">Save Faculty & Staff</button>
    </form>
    @endif

    {{-- ========== ABOUT PAGE — PEOPLE GROUPS (Administration / SMC / PTA) ========== --}}
    @if(in_array($key, ['about_administration', 'about_smc', 'about_pta'], true))
    @php
      $pplDefaults = [
        'about_administration' => ['Leadership', 'Administration', 'The team that leads, manages and guides the daily running of our school.'],
        'about_smc'            => ['Governance', 'School Managing Committee (SMC)', 'The committee that oversees the governance and development of the school.'],
        'about_pta'            => ['Community', 'Parents & Teachers Association (PTA)', 'Bringing parents and teachers together to support every child.'],
      ];
      $pd = $pplDefaults[$key];
    @endphp
    <form method="POST" action="{{ route('admin.customizer.people.save') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="pkey" value="{{ $key }}">
      <div class="g2">
        {!! $inp("{$key}_label", 'Section Label', $pd[0]) !!}
        {!! $inp("{$key}_title", 'Section Title', $pd[1]) !!}
      </div>
      <div class="g1">{!! $ta("{$key}_sub", 'Section Subtitle', $pd[2]) !!}</div>
      <div class="acc-section-h">Members</div>
      <p class="subnote">Add as many members as you need. A photo is optional — members without one show their initials. Drag the grip handle to reorder, click &times; to remove.</p>
      <div id="ppl-list-{{ $key }}">
        @foreach(people_members($key) as $i => $m)
          @include('admin.customizer._people-row', [
            'idx' => $i, 'name' => $m['name'] ?? '', 'role' => $m['role'] ?? '',
            'photo' => $m['photo'] ?? '',
          ])
        @endforeach
      </div>
      <button type="button" class="adm-add" onclick="pplAddRow('{{ $key }}')">+ Add New Member</button>
      <div><button class="save-btn">Save Members</button></div>
      <template id="tpl-ppl-{{ $key }}">
        @include('admin.customizer._people-row', [
          'idx' => '__I__', 'name' => '', 'role' => '', 'photo' => '',
        ])
      </template>
    </form>
    @endif

    {{-- ========== ABOUT PAGE — PRINCIPAL'S MESSAGE ========== --}}
    @if($key === 'about_principal')
    <form method="POST" action="{{ route('admin.customizer.save') }}" enctype="multipart/form-data">
      @csrf
      <p class="subnote">The Principal's Message block on the About Us page. This content is shared with the homepage Principal section.</p>
      <div class="g2">
        {!! $inp('principal_name','Principal Name','Ngamboi Kipgen') !!}
        {!! $inp('principal_label','Section Label',"Principal's Message") !!}
      </div>
      <div class="g1">{!! $img('principal_photo','Principal Photo') !!}</div>
      <div class="g1">{!! $ta('principal_quote','Highlighted Quote','Education is not the filling of a pail, but the lighting of a fire. At our school, we believe every child carries within them immense potential — and our role is to kindle that spark.',3) !!}</div>
      <div class="g1">{!! $ta('principal_message','Full Message','',6) !!}</div>
      <button class="save-btn">Save Principal</button>
    </form>
    @endif

    {{-- ========== ABOUT — CERTIFICATES & DOCUMENTS ========== --}}
    @if($key === 'about_certs')
    <form method="POST" action="{{ route('admin.customizer.certificates.save') }}" enctype="multipart/form-data">
      @csrf
      <div class="g2">
        {!! $inp('about_certs_label','Section Label','Transparency') !!}
        {!! $inp('about_certs_title','Section Title','School Documents & Certificates') !!}
      </div>
      <div class="g1">{!! $ta('about_certs_sub','Section Subtitle','Official certificates and safety records of '.$S('school_name','our school').' — displayed openly for parents and the community.') !!}</div>

      <div class="acc-section-h">Certificates</div>
      <p class="subnote">Add a certificate, upload a PDF or image, and it appears as a 3D framed document on the About page. Drag rows to reorder. Use the switch to show / hide a certificate.</p>

      <div id="cert-list">
        @foreach(cert_items() as $i => $c)
          @include('admin.customizer._cert-row', [
            'idx' => $i,
            'title' => $c['title'] ?? '',
            'file' => $c['file'] ?? '',
            'active' => ($c['active'] ?? true),
          ])
        @endforeach
      </div>
      <button type="button" class="adm-add" onclick="certAddRow()">+ Add New Certificate</button>
      <div><button class="save-btn">Save Certificates</button></div>

      <template id="tpl-cert">
        @include('admin.customizer._cert-row', ['idx' => '__I__', 'title' => '', 'file' => '', 'active' => true])
      </template>
    </form>
    @endif

  </div>
</div>
