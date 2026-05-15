
<?php
    $aboutLabel   = \App\Helpers\Settings::get('about_section_label', 'Who We Are');
    $aboutTitle   = \App\Helpers\Settings::get('about_section_title', 'A Church Rooted in Faith & Community');
    $aboutPreview = \App\Helpers\Settings::get('about_preview', 'We are a vibrant community committed to spreading the love of God and serving our neighbours.');
    $aboutBtnText = \App\Helpers\Settings::get('about_btn_text', 'Our Story');
    $aboutBtnLink = \App\Helpers\Settings::get('about_btn_link', route('about'));
?>
<?php if(\App\Helpers\Settings::get('sec_show_about', '1')): ?>
<section style="padding:80px 0; background:var(--cream);" class="scroll-reveal">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            
            <div style="position:relative; height:440px;">
                <?php
                    $aboutImg1 = \App\Helpers\Settings::get('about_image_1', '');
                    $aboutImg2 = \App\Helpers\Settings::get('about_image_2', '');
                    $img1Src = $aboutImg1 ? asset('storage/'.$aboutImg1) : '/images/community-1.jpg';
                    $img2Src = $aboutImg2 ? asset('storage/'.$aboutImg2) : '/images/community-2.jpg';
                ?>
                <img src="<?php echo e($img1Src); ?>" alt="Community"
                     style="position:absolute; top:0; left:0; width:65%; height:72%; object-fit:cover; border-radius:16px; box-shadow:0 12px 40px rgba(0,0,0,0.18);">
                <img src="<?php echo e($img2Src); ?>" alt="Community"
                     style="position:absolute; bottom:0; right:0; width:55%; height:62%; object-fit:cover; border-radius:16px; box-shadow:0 12px 40px rgba(0,0,0,0.18);">
                <div style="position:absolute; bottom:28px; left:12px; z-index:10; background:var(--primary); color:#fff; padding:14px 18px; border-radius:14px; box-shadow:0 8px 24px rgba(0,0,0,0.25); text-align:center;">
                    <div style="font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:800; line-height:1;">Est.</div>
                    <div style="font-size:1.4rem; font-weight:700;"><?php echo e(date('Y')); ?></div>
                    <div style="font-size:0.65rem; text-transform:uppercase; letter-spacing:0.1em; opacity:0.75; margin-top:2px;"><?php echo e(\App\Helpers\Settings::get('site_name','Our Community')); ?></div>
                </div>
            </div>

            
            <div>
                <span class="section-label"><?php echo e($aboutLabel); ?></span>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,3.5vw,2.8rem); font-weight:800; color:#1c1917; line-height:1.2; margin:12px 0 20px;">
                    <?php echo nl2br(e($aboutTitle)); ?>

                </h2>
                <p style="color:#57534e; font-size:1.05rem; line-height:1.8; margin-bottom:28px;"><?php echo e($aboutPreview); ?></p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:32px;">
                    <?php $__currentLoopData = ['Worship', 'Fellowship', 'Service', 'Discipleship']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display:flex; align-items:center; gap:8px; font-size:0.88rem; font-weight:600; color:#44403c;">
                        <span style="width:8px; height:8px; border-radius:50%; background:var(--accent); flex-shrink:0;"></span>
                        <?php echo e($val); ?>

                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <?php if($aboutBtnText && $aboutBtnLink): ?>
                <a href="<?php echo e($aboutBtnLink); ?>"
                   style="display:inline-flex; align-items:center; gap:8px; background:var(--primary); color:#fff; font-weight:600; padding:13px 28px; border-radius:50px; font-size:0.9rem; text-decoration:none; box-shadow:0 4px 16px rgba(45,106,79,0.3); transition:background 0.2s;"
                   onmouseover="this.style.background='var(--secondary)'" onmouseout="this.style.background='var(--primary)'">
                    <?php echo e($aboutBtnText); ?>

                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /**PATH /Users/moldokipgen/Projects/EHLOM DIGITAL/Ehlom CMS/resources/views/public/partials/home/about.blade.php ENDPATH**/ ?>