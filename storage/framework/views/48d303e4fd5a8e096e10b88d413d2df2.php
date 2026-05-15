
<?php
    $ytRaw = \App\Helpers\Settings::get('story_video_url') ?: \App\Helpers\Settings::get('hero_youtube_url', '');
    $ytVid = '';
    if ($ytRaw) {
        if (preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $ytRaw, $m)) {
            $ytVid = $m[1];
        } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', trim($ytRaw))) {
            $ytVid = trim($ytRaw);
        }
    }
    $storyVideoTitle = \App\Helpers\Settings::get('story_video_title', '');
    $storyVideoSub   = \App\Helpers\Settings::get('story_video_subtitle', '');
?>

<?php if($ytVid && \App\Helpers\Settings::get('sec_show_story_video', '1')): ?>
<section style="background:#111; width:100%; overflow:hidden; line-height:0; position:relative; font-size:0;">

    <?php if($storyVideoTitle || $storyVideoSub): ?>
    <div style="position:absolute; bottom:0; left:0; right:0; z-index:10; padding:24px 32px; background:linear-gradient(to top, rgba(0,0,0,.75) 0%, transparent 100%); pointer-events:none;">
        <?php if($storyVideoTitle): ?>
        <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.2rem,2.5vw,2rem); font-weight:800; color:#fff; margin:0 0 6px; text-shadow:0 2px 8px rgba(0,0,0,.5);"><?php echo e($storyVideoTitle); ?></h2>
        <?php endif; ?>
        <?php if($storyVideoSub): ?>
        <p style="color:rgba(255,255,255,.8); font-size:.95rem; margin:0; text-shadow:0 1px 4px rgba(0,0,0,.5);"><?php echo e($storyVideoSub); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div style="position:relative; width:100%; padding-top:56.25%; overflow:hidden;">
        <iframe
            src="https://www.youtube.com/embed/<?php echo e($ytVid); ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo e($ytVid); ?>&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&playsinline=1&disablekb=1&fs=0&enablejsapi=0"
            allow="autoplay; encrypted-media"
            allowfullscreen
            style="position:absolute; top:50%; left:50%; width:120%; height:120%; transform:translate(-50%,-50%); border:0; display:block; pointer-events:none;">
        </iframe>
        <div style="position:absolute; inset:0; z-index:1;"></div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH /Users/moldokipgen/Projects/EHLOM DIGITAL/Ehlom CMS/resources/views/public/partials/home/story-video.blade.php ENDPATH**/ ?>