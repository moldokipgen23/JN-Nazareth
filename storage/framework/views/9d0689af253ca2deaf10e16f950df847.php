
<?php
    $statsData = $stats ?? ['members' => 0, 'events' => 0, 'blogs' => 0];
    $estYear   = 2024;
    $yearsCalc = (int) date('Y') - $estYear;
    $yearsLabel = $yearsCalc > 0 ? $yearsCalc . '+' : 'Est. ' . $estYear;

    $statItems = [
        [
            'val'   => \App\Helpers\Settings::get('stat_1_value') ?: number_format($statsData['members']) . '+',
            'label' => \App\Helpers\Settings::get('stat_1_label') ?: 'Church Members',
        ],
        [
            'val'   => \App\Helpers\Settings::get('stat_2_value') ?: number_format($statsData['events']),
            'label' => \App\Helpers\Settings::get('stat_2_label') ?: 'Events Held',
        ],
        [
            'val'   => \App\Helpers\Settings::get('stat_3_value') ?: number_format($statsData['blogs']),
            'label' => \App\Helpers\Settings::get('stat_3_label') ?: 'Blog Posts',
        ],
        [
            'val'   => \App\Helpers\Settings::get('stat_4_value') ?: $yearsLabel,
            'label' => \App\Helpers\Settings::get('stat_4_label') ?: 'Years of Grace',
        ],
    ];
?>
<?php if(\App\Helpers\Settings::get('sec_show_stats', '1')): ?>
<section style="background:#fff; border-bottom:1px solid #ede8e0; position:relative; z-index:10;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4" style="divide-color:#f0ebe2;">
            <?php $__currentLoopData = $statItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="padding:28px 16px; text-align:center; border-right:1px solid #ede8e0;">
                <div style="font-family:'Playfair Display',serif; font-size:2.2rem; font-weight:800; color:var(--primary); line-height:1;"><?php echo e($stat['val']); ?></div>
                <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#a8a29e; margin-top:6px;"><?php echo e($stat['label']); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH /Users/moldokipgen/Projects/EHLOM DIGITAL/Ehlom CMS/resources/views/public/partials/home/stats.blade.php ENDPATH**/ ?>