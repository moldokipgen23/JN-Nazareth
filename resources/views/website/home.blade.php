@extends('layouts.website')

@section('content')
@php
  use App\Helpers\Settings;
  $wa       = preg_replace('/\D+/', '', setting('whatsapp', '919862880292'));
  $sName    = setting('school_name', 'J.N. Nazareth English School');
  $estd     = setting('school_established', '1996');
  $board    = setting('school_board', 'Manipur Board');
  $heroLogo = setting('school_logo') ? Settings::storageUrl(setting('school_logo')) : asset('images/logo.png');

  // Gallery — DB images, fall back to demo images
  $galItems = [];
  foreach ($galleryImages as $img) {
      $galItems[] = ['src' => Settings::storageUrl($img->path), 'cap' => $img->caption ?: $img->title ?: 'School Gallery'];
  }
  if (empty($galItems)) {
      $galItems = [
          ['src' => asset('images/img-students.png'),  'cap' => 'Happy Students'],
          ['src' => asset('images/img-assembly.png'),  'cap' => 'Morning Assembly'],
          ['src' => asset('images/img-sportsday.png'), 'cap' => 'Annual Sports Meet'],
          ['src' => asset('images/img-awards.png'),    'cap' => 'Farewell & Felicitation Ceremony'],
          ['src' => asset('images/img-sports.png'),    'cap' => 'Sports Activities'],
          ['src' => asset('images/img-staff.png'),     'cap' => 'School Programme'],
          ['src' => asset('images/img-gathering.png'), 'cap' => 'School Gathering'],
          ['src' => asset('images/img-campus.png'),    'cap' => 'School Campus'],
      ];
  }

  // --- Section defaults (used until edited in the Site Customizer) ---
  $heroStatD = [
    1 => [$estd, 'Established'],
    2 => ['Prep – X', 'Classes Offered'],
    3 => ['English', 'Medium of Instruction'],
    4 => ['Board', $board . ' Approved'],
    5 => ['Govt.', 'Recognised School'],
  ];
  $aboutFeatD = [
    1 => ['fas fa-graduation-cap', $board . ' Approved High School', 'Recently approved for Class 10 under the Board of Secondary Education, Manipur — a landmark achievement recognising our commitment to quality education.'],
    2 => ['fas fa-language', '100% English Medium — Preparatory to Class X', 'All subjects are taught in English, building strong language skills, communication confidence, and academic readiness from the very first year.'],
    3 => ['fas fa-shield-halved', 'Discipline, Values & Character Formation', 'We cultivate not just academic excellence but responsible, ethical, and well-rounded individuals prepared for life and for a better Manipur.'],
  ];
  $hlD = [
    1 => ['fas fa-school', 'Preparatory-X', 'Complete School Journey from Early Years to High School'],
    2 => ['fas fa-chalkboard-user', 'Qualified', 'Trained & Experienced Teachers Committed to Every Student'],
    3 => ['fas fa-lightbulb', 'Smart', 'Modern & Engaging Learning Environment for Better Understanding'],
    4 => ['fas fa-globe', 'English', '100% English Medium Instruction Across All Classes'],
    5 => ['fas fa-medal', 'Board ✓', 'Approved High School Under ' . $board . ' of Secondary Education'],
  ];
  $clsD = [
    1 => ['🌱', 'Preparatory', 'Early Years', 'Play-based learning, English familiarisation, and foundational skills in a safe, nurturing, and joyful classroom setting.', 'Ages 3 – 4'],
    2 => ['🔤', 'LKG', '(KG-1)', 'Building strong reading, writing, and numeracy foundations through structured English activities, stories, and interactive lessons.', 'Ages 4 – 5'],
    3 => ['🔤', 'UKG', '(KG-2)', 'Building strong reading, writing, and numeracy foundations through structured English activities, stories, and interactive lessons.', 'Ages 5 – 6'],
    4 => ['📘', 'Primary', 'Class I – V', 'A comprehensive curriculum — English, Mathematics, Science, and Social Studies — building a strong academic base with curiosity and confidence.', 'Ages 6 – 11'],
    5 => ['📗', 'Middle School', 'Class VI – VIII', 'Deepening knowledge across all subjects with structured learning, preparing students for the rigor of high school education.', 'Ages 11 – 14'],
    6 => ['🏆', 'High School', 'Class IX – X', 'Board-approved Class 9 & 10 under the ' . $board . ' — rigorous preparation for board exams and future academic success.', $board],
  ];
  $admD = [
    1 => ['fas fa-user-check', 'Eligibility', 'Children between ages 3–16. Students from any school background are welcome to apply for classes from Preparatory to Class 10.'],
    2 => ['fas fa-file-lines', 'Required Documents', 'Birth certificate · Previous class marksheet / TC (if applicable) · 2 passport-size photos · Parent / guardian ID proof.'],
    3 => ['fas fa-map-pin', 'Visit Us In Person', 'Come to the school during school hours to complete admission directly.'],
    4 => ['fas fa-indian-rupee-sign', 'Affordable Fee Structure', setting('acad_fee_text', 'We offer quality education at an affordable cost. Contact us on WhatsApp or visit school for detailed fee information.')],
  ];
  $whyD = [
    1 => ['fas fa-book-open', 'Best English Education', 'Complete English medium instruction from Preparatory to Class 10 builds strong language proficiency, communication confidence, and academic readiness in every student.'],
    2 => ['fas fa-scale-balanced', 'Discipline & Values', 'Rooted in strong moral values and structured discipline, we develop not just academically capable students but responsible, ethical individuals prepared for life.'],
    3 => ['fas fa-user-tie', 'Experienced Teachers', 'Our dedicated and qualified teaching staff are passionate about the academic and personal growth of every student, offering focused attention and mentorship.'],
    4 => ['fas fa-heart', 'Student-Friendly Environment', 'A safe, inclusive, and nurturing campus where every child is respected, encouraged to participate, and celebrated for their unique strengths and talents.'],
    5 => ['fas fa-trophy', 'Academic Excellence', 'A strong track record of academic performance with students achieving excellent results and going on to pursue higher education with confidence and skill.'],
    6 => ['fas fa-running', 'Sports & Co-Curriculars', 'Beyond academics, we offer sports, cultural activities, and annual events that build teamwork, leadership, creativity, and well-rounded personalities.'],
  ];

  $aboutText1D = $sName . ', established in ' . $estd . ' at Khengjang, Churachandpur, stands as one of the most trusted and reputed English medium schools in the region. Rooted in the motto "Train up a child in the way he should go…" (Proverbs 22:6), we have shaped hundreds of young minds over more than two decades of dedicated service.';
  $aboutText2D = 'Our school offers a complete educational journey from Preparatory through Class 10, guided by qualified teachers, a disciplined environment, and a strong focus on English communication, academic integrity, and character development.';

  // --- Section order (set in Site Customizer → Homepage, drag to reorder) ---
  $defaultOrder = ['hero','highlights','about','classes','why','halloffame','gallery','news','notices','links','admission','principal','location'];
  $order = json_decode(setting('home_section_order', ''), true);
  if (! is_array($order) || ! $order) {
      $order = $defaultOrder;
  }
  // keep only known sections, and append any new ones missing from a saved order
  $order = array_values(array_intersect($order, $defaultOrder));
  foreach ($defaultOrder as $s) {
      if (! in_array($s, $order, true)) { $order[] = $s; }
  }
@endphp

@foreach($order as $section)
  @includeIf('website.home.' . $section)
@endforeach

{{-- Gallery Lightbox --}}
<div class="lb" id="lb" onclick="lbBg(event)">
  <div class="lb-wrap">
    <button class="lb-close" onclick="closeLB()"><i class="fas fa-times"></i></button>
    <button class="lb-arrow lb-prev" onclick="shiftLB(-1)"><i class="fas fa-chevron-left"></i></button>
    <img id="lbImg" class="lb-img" src="" alt="">
    <button class="lb-arrow lb-next" onclick="shiftLB(1)"><i class="fas fa-chevron-right"></i></button>
    <div id="lbCap" class="lb-cap"></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  /* --- Gallery Lightbox --- */
  const lbImages = @json($galItems);
  let lbIdx = 0;
  function openLB(idx) {
    lbIdx = idx;
    document.getElementById('lbImg').src = lbImages[idx].src;
    document.getElementById('lbCap').textContent = lbImages[idx].cap;
    document.getElementById('lb').classList.add('on');
    document.body.style.overflow = 'hidden';
  }
  function closeLB() {
    document.getElementById('lb').classList.remove('on');
    document.body.style.overflow = '';
  }
  function lbBg(e) { if (e.target === document.getElementById('lb')) closeLB(); }
  function shiftLB(dir) {
    lbIdx = (lbIdx + dir + lbImages.length) % lbImages.length;
    const img = document.getElementById('lbImg');
    img.style.opacity = '0';
    setTimeout(() => {
      img.src = lbImages[lbIdx].src;
      document.getElementById('lbCap').textContent = lbImages[lbIdx].cap;
      img.style.opacity = '1';
    }, 180);
  }
  document.getElementById('lbImg').style.transition = 'opacity .18s ease';
  document.addEventListener('keydown', e => {
    if (!document.getElementById('lb').classList.contains('on')) return;
    if (e.key === 'Escape')     closeLB();
    if (e.key === 'ArrowLeft')  shiftLB(-1);
    if (e.key === 'ArrowRight') shiftLB(1);
  });

  /* --- Admission form: record to CMS + open WhatsApp --- */
  const WA_NUMBER = '{{ $wa }}';
  function sendToWA(e) {
    e.preventDefault();
    const sName = document.getElementById('sName').value.trim();
    const pName = document.getElementById('pName').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const cls   = document.getElementById('cls').value;
    const msg   = document.getElementById('msg').value.trim();

    /* Silently record the inquiry in the CMS */
    fetch('{{ route('inquiry.store') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        name: sName,
        phone: phone,
        class_interested: cls,
        message: 'Parent/Guardian: ' + pName + (msg ? ('\n' + msg) : '')
      })
    }).catch(() => {});

    const text =
      `Hello, I would like to inquire about *admission at {{ $sName }}*.\n\n` +
      `*Student Name:* ${sName}\n` +
      `*Parent / Guardian:* ${pName}\n` +
      `*Phone:* ${phone}\n` +
      `*Class Applying For:* ${cls}\n` +
      `*Message:* ${msg || 'No additional message'}\n\n` +
      `Kindly guide me through the admission process. Thank you!`;
    window.open('https://wa.me/' + WA_NUMBER + '?text=' + encodeURIComponent(text), '_blank');
  }
</script>
@endpush
