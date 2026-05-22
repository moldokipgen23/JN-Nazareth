@if(setting('sec_show_classes','1') !== '0')
<section id="classes">
  <div class="container">
    <div class="cls-header reveal">
      <span class="pill"><i class="fas fa-book-open"></i> {{ setting('classes_label', 'Academic Programme') }}</span>
      <h2 class="sec-title">{{ setting('classes_title', 'Classes We Offer') }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('classes_sub', 'A structured, English medium journey from early childhood through board-level high school education.') }}</p>
    </div>
    <div class="cls-grid">
      @for($i = 1; $i <= 6; $i++)
        <div class="cls-card reveal">
          <div class="cls-card-top">
            <div class="cls-emoji" @if($i === 6) style="background:linear-gradient(135deg,#f59e0b,#b45309);box-shadow:0 4px 18px rgba(245,158,11,.42);" @endif>{{ setting("cls_{$i}_emoji", $clsD[$i][0]) }}</div>
            <div class="cls-name">{{ setting("cls_{$i}_name", $clsD[$i][1]) }}</div>
            <div class="cls-range" @if($i === 6) style="color:#b45309;" @endif>{{ setting("cls_{$i}_range", $clsD[$i][2]) }}</div>
          </div>
          <div class="cls-card-body">
            <p class="cls-desc">{{ setting("cls_{$i}_desc", $clsD[$i][3]) }}</p>
            <span class="cls-tag" @if($i === 6) style="background:#fffbeb;color:#92400e;border-color:#fde68a;" @endif>{{ setting("cls_{$i}_tag", $clsD[$i][4]) }}</span>
          </div>
        </div>
      @endfor
    </div>
    <div style="text-align:center;margin-top:32px;" class="reveal">
      <a href="{{ route('academics') }}" class="btn btn-blue-solid"><i class="fas fa-book-open"></i> View All Classes &amp; Academics</a>
    </div>
  </div>
</section>
@endif
