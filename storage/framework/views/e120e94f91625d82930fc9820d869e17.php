
<?php
    $showSection    = \App\Helpers\Settings::get('sec_show_hall_of_fame', '1');
    $sectionTitle   = \App\Helpers\Settings::get('sec_title_hall_of_fame', '🏆 Hall of Fame');
    $sectionSubtitle= \App\Helpers\Settings::get('sec_sub_hall_of_fame', 'Celebrating the trailblazers and pioneers who shaped our community.');
    $entries        = \App\Models\HallOfFame::active()->ordered()->get();
?>

<?php if($showSection && $entries->isNotEmpty()): ?>
<section style="padding:88px 0; background:#fff; position:relative; overflow:hidden;">

    
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-family:'Playfair Display',serif; font-size:clamp(5rem,14vw,10rem); font-weight:900; color:rgba(200,135,58,0.04); white-space:nowrap; pointer-events:none; user-select:none;">LEGACY</div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position:relative; z-index:1;">

        
        <div style="text-align:center; margin-bottom:56px;">
            <span class="section-label">Community Heritage</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,3.5vw,2.8rem); font-weight:800; color:#1c1917; line-height:1.2; margin:14px 0 12px;">
                <?php echo e($sectionTitle); ?>

            </h2>
            <?php if($sectionSubtitle): ?>
            <p style="color:#78716c; font-size:.95rem; max-width:520px; margin:0 auto; line-height:1.7;"><?php echo e($sectionSubtitle); ?></p>
            <?php endif; ?>
            <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-top:18px;">
                <span style="height:1px; width:60px; background:#d6cdc0;"></span>
                <svg width="14" height="14" fill="#c8873a" viewBox="0 0 24 24"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>
                <span style="height:1px; width:60px; background:#d6cdc0;"></span>
            </div>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style="gap:24px;">
            <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:#fff; border-radius:18px; border:1px solid #e7dfd4; box-shadow:0 4px 20px rgba(0,0,0,.06); overflow:hidden; transition:transform .2s, box-shadow .2s;"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 32px rgba(0,0,0,.1)'"
                 onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 20px rgba(0,0,0,.06)'">

                
                <div style="position:relative; height:200px; overflow:hidden; background:linear-gradient(160deg,#e7dfd4,#d6cdc0);">
                    <?php if($entry->photo): ?>
                    <img src="<?php echo e(\App\Helpers\Settings::storageUrl($entry->photo)); ?>" alt="<?php echo e($entry->name); ?>"
                         style="width:100%; height:100%; object-fit:cover; display:block;">
                    <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:60px;">🏆</div>
                    <?php endif; ?>

                    
                    <?php if($entry->year): ?>
                    <div style="position:absolute; top:12px; right:12px; background:rgba(15,23,42,.75); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; backdrop-filter:blur(6px);">
                        <?php echo e($entry->year); ?>

                    </div>
                    <?php endif; ?>

                    
                    <?php if($entry->featured): ?>
                    <div style="position:absolute; top:12px; left:12px; background:linear-gradient(135deg,#c8873a,#a06030); color:#fff; font-size:10px; font-weight:700; padding:4px 10px; border-radius:20px;">
                        ⭐ Featured
                    </div>
                    <?php endif; ?>
                </div>

                
                <div style="padding:20px 22px 22px;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--accent); margin-bottom:6px;">
                        <?php echo e($entry->achievement_title); ?>

                    </div>
                    <h3 style="font-family:'Playfair Display',serif; font-size:1.15rem; font-weight:800; color:#1c1917; margin:0 0 10px; line-height:1.3;">
                        <?php echo e($entry->name); ?>

                    </h3>
                    <?php if($entry->description): ?>
                    <p style="font-size:.85rem; color:#57534e; line-height:1.7; margin:0 0 16px; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                        <?php echo e($entry->description); ?>

                    </p>
                    <?php endif; ?>

                    
                    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:auto;">
                        <?php if($entry->video_url): ?>
                        <a href="<?php echo e($entry->video_url); ?>" target="_blank" rel="noopener"
                           style="display:inline-flex; align-items:center; gap:5px; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; padding:6px 12px; border-radius:8px; font-size:11px; font-weight:700; text-decoration:none;">
                            <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            Watch
                        </a>
                        <?php endif; ?>
                        <?php if($entry->external_link): ?>
                        <a href="<?php echo e($entry->external_link); ?>" target="_blank" rel="noopener"
                           style="display:inline-flex; align-items:center; gap:5px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; padding:6px 12px; border-radius:8px; font-size:11px; font-weight:700; text-decoration:none;">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Learn More
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

    </div>
</section>
<?php endif; ?>
<?php /**PATH /Users/moldokipgen/Projects/EHLOM DIGITAL/Ehlom CMS/resources/views/public/partials/home/hall-of-fame.blade.php ENDPATH**/ ?>