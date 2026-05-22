<script>
  /* Requires a `lbImages` array defined before this include. */
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
</script>
