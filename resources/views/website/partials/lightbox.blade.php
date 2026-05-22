{{-- Lightbox markup. Pair with partials/lightbox-js and a `lbImages` JS array. --}}
<div class="lb" id="lb" onclick="lbBg(event)">
  <div class="lb-wrap">
    <button class="lb-close" onclick="closeLB()"><i class="fas fa-times"></i></button>
    <button class="lb-arrow lb-prev" onclick="shiftLB(-1)"><i class="fas fa-chevron-left"></i></button>
    <img id="lbImg" class="lb-img" src="" alt="">
    <button class="lb-arrow lb-next" onclick="shiftLB(1)"><i class="fas fa-chevron-right"></i></button>
    <div id="lbCap" class="lb-cap"></div>
  </div>
</div>
