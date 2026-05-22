@if(setting('sec_show_admission','1') !== '0')
<section id="admission">
  <div class="container">
    <div style="text-align:center;margin-bottom:52px;" class="reveal">
      <span class="pill"><i class="fas fa-pen-to-square"></i> {{ setting('admission_label', 'Admissions') }}</span>
      <h2 class="sec-title">{{ setting('admission_title', 'Admission Open for New Session') }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('admission_sub', 'Join one of the finest English medium schools in Churachandpur. We welcome students who are eager to learn, grow, and excel.') }}</p>
    </div>

    <div class="adm-grid">
      <div class="reveal">
        <div class="adm-notice">
          <i class="fas fa-bullhorn"></i>
          <div>
            <strong>{{ setting('adm_notice_title', 'Admissions Open — New Academic Session') }}</strong>
            <span>{{ setting('adm_notice_text', 'Enrollment is currently open for all classes from Preparatory through Class 10. Limited seats available — inquire early!') }}</span>
          </div>
        </div>
        <div class="adm-list">
          @for($i = 1; $i <= 4; $i++)
            <div class="adm-item">
              <div class="adm-item-ic"><i class="{{ setting("adm_item_{$i}_icon", $admD[$i][0]) }}"></i></div>
              <div>
                <div class="adm-item-title">{{ setting("adm_item_{$i}_title", $admD[$i][1]) }}</div>
                <div class="adm-item-desc">{{ setting("adm_item_{$i}_desc", $admD[$i][2]) }}</div>
              </div>
            </div>
          @endfor
        </div>
        <div class="timing">
          <div class="timing-head"><i class="fas fa-clock"></i> School Timings</div>
          <div class="timing-row">
            <div class="timing-slot"><div class="timing-day">Monday – Friday</div><div class="timing-hrs">{{ setting('acad_timing_weekday', '8:00 AM – 3:00 PM') }}</div></div>
            <div class="timing-slot"><div class="timing-day">Saturday</div><div class="timing-hrs">{{ setting('acad_timing_saturday', '8:00 AM – 12:00 PM') }}</div></div>
          </div>
        </div>
        <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hello, I would like to inquire about admission at ' . $sName . '.') }}" target="_blank" class="btn btn-wa" style="width:100%;justify-content:center;border-radius:12px;">
          <i class="fab fa-whatsapp"></i> Quick Inquiry on WhatsApp
        </a>
      </div>

      <div class="form-card reveal">
        <div class="form-card-title">{{ setting('adm_form_title', 'Admission Inquiry Form') }}</div>
        <div class="form-card-sub">{{ setting('adm_form_sub', "Fill the form below — we'll respond on WhatsApp within 24 hours.") }}</div>
        @if(session('success'))
          <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:10px;padding:11px 14px;font-size:.83rem;margin-bottom:16px;">{{ session('success') }}</div>
        @endif
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
                @foreach(\App\Models\Member::classes() as $c)
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
    </div>
  </div>
</section>
@endif
