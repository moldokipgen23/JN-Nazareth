@extends('layouts.website')
@section('title', 'Admission')
@section('description', 'Admission open at ' . setting('school_name') . ' for all classes from Preparatory to Class X. Apply now.')

@section('content')
@php
  // Active info items + WhatsApp contacts (managed in Site Customizer → Admission)
  $admItems    = array_values(array_filter(admission_items(),    fn ($i) => $i['active'] ?? true));
  $admContacts = array_values(array_filter(admission_contacts(), fn ($c) => $c['active'] ?? true));
  $primaryWa   = $admContacts[0]['number'] ?? preg_replace('/\D+/', '', setting('whatsapp', '919862880292'));

  $showTiming   = setting('sec_show_adm_timing', '1')   !== '0';
  $showWhatsapp = setting('sec_show_adm_whatsapp', '1') !== '0';
  $showForm     = setting('sec_show_adm_form', '1')     !== '0';

  // Left-column block order from Site Customizer → Admission (drag to reorder).
  $admLeftDefault = ['adm_items', 'adm_timing', 'adm_whatsapp'];
  $admOrder = json_decode(setting('admission_section_order', ''), true);
  $admOrder = (is_array($admOrder) && $admOrder)
      ? array_values(array_intersect($admOrder, $admLeftDefault))
      : [];
  foreach ($admLeftDefault as $k) { if (! in_array($k, $admOrder, true)) { $admOrder[] = $k; } }
@endphp

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-pen-to-square"></i> ' . setting('adm_hero_label', 'Admissions'),
  'heading' => setting('adm_hero_title', 'Admission Open for New Session'),
  'sub'     => setting('adm_hero_sub', 'Join one of the finest English medium schools in Churachandpur. We welcome students who are eager to learn, grow, and excel.'),
  'crumb'   => 'Admission',
])

<section class="page-section">
  <div class="container">
    <div class="adm-grid">
      <div class="reveal">
        <div class="adm-notice">
          <i class="fas fa-bullhorn"></i>
          <div>
            <strong>{{ setting('adm_notice_title', 'Admissions Open — New Academic Session') }}</strong>
            <span>{{ setting('adm_notice_text', 'Enrollment is currently open for all classes from Preparatory through Class 10. Limited seats available — inquire early!') }}</span>
          </div>
        </div>

        @foreach($admOrder as $admBlock)
        @switch($admBlock)

        @case('adm_items')
        @if(count($admItems))
        <div class="adm-list">
          @foreach($admItems as $it)
          <div class="adm-item">
            <div class="adm-item-ic"><i class="{{ $it['icon'] ?: 'fas fa-circle-info' }}"></i></div>
            <div>
              <div class="adm-item-title">{{ $it['title'] }}</div>
              <div class="adm-item-desc">{{ $it['desc'] }}</div>
            </div>
          </div>
          @endforeach
        </div>
        @endif
        @break

        @case('adm_timing')
        @if($showTiming)
        <div class="timing">
          <div class="timing-head"><i class="fas fa-clock"></i> {{ setting('adm_timing_title', 'School Timings') }}</div>
          <div class="timing-row">
            <div class="timing-slot"><div class="timing-day">Monday – Friday</div><div class="timing-hrs">{{ setting('school_timing_weekday', '8:00 AM – 3:00 PM') }}</div></div>
            <div class="timing-slot"><div class="timing-day">Saturday</div><div class="timing-hrs">{{ setting('school_timing_saturday', '8:00 AM – 12:00 PM') }}</div></div>
          </div>
        </div>
        @endif
        @break

        @case('adm_whatsapp')
        @if($showWhatsapp && count($admContacts))
        <div style="display:flex; flex-direction:column; gap:10px; margin-top:18px;">
          @foreach($admContacts as $c)
          <a href="https://wa.me/{{ $c['number'] }}?text={{ rawurlencode('Hello, I would like to inquire about admission at ' . setting('school_name') . '.') }}"
             target="_blank" rel="noopener" class="btn btn-wa" style="width:100%;justify-content:center;border-radius:12px;">
            <i class="fab fa-whatsapp"></i> {{ $c['name'] }}
          </a>
          @endforeach
        </div>
        @endif
        @break

        @endswitch
        @endforeach
      </div>

      @if($showForm)
      <div class="form-card reveal">
        <div class="form-card-title">{{ setting('adm_form_title', 'Admission Inquiry Form') }}</div>
        <div class="form-card-sub">{{ setting('adm_form_sub', "Fill the form below — we'll respond on WhatsApp within 24 hours.") }}</div>
        <form id="admForm" onsubmit="sendToWA(event)">
          <div class="fg-row">
            <div class="fg"><label for="sName">Student Name *</label><input type="text" id="sName" placeholder="Student's full name" required></div>
            <div class="fg"><label for="pName">Parent / Guardian Name *</label><input type="text" id="pName" placeholder="Parent's full name" required></div>
          </div>
          <div class="fg-row">
            <div class="fg"><label for="phone">Phone Number *</label><input type="tel" id="phone" placeholder="+91 98XXX XXXXX" required></div>
            <div class="fg">
              <label for="cls">Class Applying For *</label>
              <select id="cls" required>
                <option value="">Select Class</option>
                @foreach(\App\Models\Student::classes() as $c)
                  <option>{{ $c }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="fg"><label for="msg">Message / Additional Information</label><textarea id="msg" placeholder="Any questions or details you'd like to share…"></textarea></div>
          <button type="submit" class="btn-submit-wa">
            <i class="fab fa-whatsapp" style="font-size:1.15rem;"></i> Send Inquiry via WhatsApp
          </button>
        </form>
      </div>
      @endif
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  const WA_NUMBER = '{{ $primaryWa }}';
  function sendToWA(e) {
    e.preventDefault();
    const sName = document.getElementById('sName').value.trim();
    const pName = document.getElementById('pName').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const cls   = document.getElementById('cls').value;
    const msg   = document.getElementById('msg').value.trim();

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
      `Hello, I would like to inquire about *admission at {{ setting('school_name') }}*.\n\n` +
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
