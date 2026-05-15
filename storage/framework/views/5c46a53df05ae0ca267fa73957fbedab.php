
<?php
    $locMapLink   = \App\Helpers\Settings::get('location_map_link', 'https://maps.google.com');
    $locEmbedUrl  = \App\Helpers\Settings::get('location_embed_url', '');

    $routeSteps = json_decode(\App\Helpers\Settings::get('location_route_steps', ''), true) ?: [
        ['place'=>'City Centre',  'note'=>'Starting Point',      'dist'=>'~10 km', 'icon'=>'🏙️', 'color'=>'#2d6a4f'],
        ['place'=>'Main Road',    'note'=>'Take the highway',    'dist'=>'~5 km',  'icon'=>'🚗',  'color'=>'#0369a1'],
        ['place'=>'Local Area',   'note'=>'Turn at the junction','dist'=>'~2 km',  'icon'=>'🏘️', 'color'=>'#b45309'],
        ['place'=>'Our Location', 'note'=>'Final Destination 📍','dist'=>'Arrived','icon'=>'📍', 'color'=>'#166534'],
    ];

    $nearbyPlaces = json_decode(\App\Helpers\Settings::get('location_nearby_places', ''), true) ?: [
        ['name'=>'Community Park',    'desc'=>'A great place to relax and enjoy nature.',        'link'=>'https://maps.google.com', 'icon'=>'🌳'],
        ['name'=>'Local Market',      'desc'=>'Fresh produce and local goods every weekend.',    'link'=>'https://maps.google.com', 'icon'=>'🛒'],
        ['name'=>'Town Hall',         'desc'=>'Community events and local government offices.',  'link'=>'https://maps.google.com', 'icon'=>'🏛️'],
        ['name'=>'Recreation Centre', 'desc'=>'Sports facilities and community activities.',     'link'=>'https://maps.google.com', 'icon'=>'🏃'],
    ];
?>
<section style="padding:88px 0; background:var(--cream);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div style="text-align:center; margin-bottom:52px;">
            <span class="section-label">Travel Guide</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,3.5vw,2.8rem); font-weight:800; color:#1c1917; margin:12px 0 12px; line-height:1.2;">
                <?php echo e(\App\Helpers\Settings::get('sec_title_location', 'How to Find Us')); ?>

            </h2>
            <?php if(\App\Helpers\Settings::get('map_section_subtitle')): ?>
            <p style="color:#78716c; font-size:.95rem; max-width:500px; margin:0 auto; line-height:1.7;">
                <?php echo e(\App\Helpers\Settings::get('map_section_subtitle')); ?>

            </p>
            <?php endif; ?>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:40px; align-items:start;">

            <div>
                <div style="background:#fff; border-radius:20px; padding:28px; box-shadow:0 4px 24px rgba(0,0,0,.08); border:1px solid #e7dfd4; margin-bottom:24px;">
                    <h3 style="font-size:.75rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:var(--accent); margin:0 0 22px;">Route from Major Cities</h3>

                    <div style="position:relative;">
                        <?php if(count($routeSteps) > 1): ?>
                        <div style="position:absolute; left:18px; top:24px; bottom:24px; width:2px; background:linear-gradient(to bottom, <?php echo e($routeSteps[0]['color'] ?? '#2d6a4f'); ?>, <?php echo e($routeSteps[count($routeSteps)-1]['color'] ?? '#166534'); ?>); border-radius:2px; z-index:0;"></div>
                        <?php endif; ?>

                        <div style="display:flex; flex-direction:column; gap:0;">
                            <?php $__currentLoopData = $routeSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:flex; align-items:center; gap:16px; padding:10px 0; position:relative; z-index:1;">
                                <div style="width:38px; height:38px; border-radius:50%; background:<?php echo e($step['color'] ?? '#2d6a4f'); ?>; border:3px solid #fff; box-shadow:0 0 0 2px <?php echo e($step['color'] ?? '#2d6a4f'); ?>40, 0 4px 12px rgba(0,0,0,.15); display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; z-index:2;">
                                    <?php echo e($step['icon'] ?? '📍'); ?>

                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div style="font-weight:800; font-size:.95rem; color:#1c1917;"><?php echo e($step['place'] ?? ''); ?></div>
                                    <div style="font-size:.75rem; color:#78716c; margin-top:1px;"><?php echo e($step['note'] ?? ''); ?></div>
                                </div>
                                <div style="font-size:.75rem; font-weight:700; color:<?php echo e($step['color'] ?? '#2d6a4f'); ?>; background:<?php echo e($step['color'] ?? '#2d6a4f'); ?>18; padding:3px 10px; border-radius:20px; white-space:nowrap; flex-shrink:0;">
                                    <?php echo e($step['dist'] ?? ''); ?>

                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="<?php echo e($locMapLink); ?>" target="_blank" rel="noopener"
                       style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:var(--primary); color:#fff; font-weight:700; font-size:.85rem; padding:12px 20px; border-radius:12px; text-decoration:none; box-shadow:0 4px 16px rgba(45,106,79,.35);">
                        <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        Open in Google Maps
                    </a>
                    <a href="<?php echo e($locMapLink); ?>" target="_blank" rel="noopener"
                       style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#fff; color:var(--primary); border:2px solid var(--primary); font-weight:700; font-size:.85rem; padding:12px 20px; border-radius:12px; text-decoration:none;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Get Directions
                    </a>
                </div>
            </div>

            <div>
                <div style="border-radius:18px; overflow:hidden; box-shadow:0 6px 28px rgba(0,0,0,.12); border:1px solid #e7dfd4; margin-bottom:24px;">
                    <iframe
                        src="<?php echo e($locEmbedUrl); ?>"
                        width="100%" height="280"
                        style="border:0; display:block;"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="<?php echo e(\App\Helpers\Settings::get('sec_title_location', 'How to Find Us')); ?>">
                    </iframe>
                </div>

                <?php if(count($nearbyPlaces)): ?>
                <h3 style="font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:var(--accent); margin:0 0 14px;">Nearby Places to Visit</h3>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <?php $__currentLoopData = $nearbyPlaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $place): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="background:#fff; border-radius:14px; padding:14px 16px; border:1px solid #e7dfd4; display:flex; align-items:center; gap:14px; box-shadow:0 2px 10px rgba(0,0,0,.05);">
                        <div style="width:40px; height:40px; border-radius:12px; background:var(--cream); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
                            <?php echo e($place['icon'] ?? '📍'); ?>

                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:700; font-size:.88rem; color:#1c1917;"><?php echo e($place['name'] ?? ''); ?></div>
                            <div style="font-size:.75rem; color:#78716c; line-height:1.4; margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo e($place['desc'] ?? ''); ?></div>
                        </div>
                        <?php if(!empty($place['link'])): ?>
                        <a href="<?php echo e($place['link']); ?>" target="_blank" rel="noopener"
                           style="flex-shrink:0; font-size:.7rem; font-weight:700; color:var(--primary); text-decoration:none; background:#f0fdf4; border:1px solid #bbf7d0; padding:5px 12px; border-radius:20px; white-space:nowrap;">
                            View Map
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /Users/moldokipgen/Projects/EHLOM DIGITAL/Ehlom CMS/resources/views/public/partials/home/location.blade.php ENDPATH**/ ?>