
<?php if(\App\Helpers\Settings::get('sec_show_blog', '1')): ?>
<?php $secTitleBlog = \App\Helpers\Settings::get('sec_title_blog', 'Latest Blog Posts'); ?>
<section style="padding:56px 0; background:#fff;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; flex-wrap:wrap; gap:10px;">
            <div>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--primary); display:block; margin-bottom:3px;"><?php echo e(\App\Helpers\Settings::get('sec_label_blog', 'From the Church')); ?></span>
                <h2 style="font-size:22px; font-weight:800; color:#1c1917; margin:0;"><?php echo e($secTitleBlog); ?></h2>
                <?php $secSubBlog = \App\Helpers\Settings::get('sec_sub_blog', ''); ?>
                <?php if($secSubBlog): ?><p style="font-size:13px; color:#78716c; margin:4px 0 0;"><?php echo e($secSubBlog); ?></p><?php endif; ?>
            </div>
            <a href="<?php echo e(route('blogs')); ?>" style="font-size:12px; font-weight:600; color:var(--primary); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">All Posts <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
        </div>
        <?php if(isset($blogs) && $blogs->count()): ?>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:12px;">
            <?php $__currentLoopData = $blogs->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('blogs.show', $blog->slug ?? $blog->id)); ?>" style="background:#fff; border-radius:12px; border:1px solid #f1f5f9; box-shadow:0 2px 8px rgba(0,0,0,.05); display:flex; gap:12px; align-items:center; padding:12px 14px; text-decoration:none; transition:box-shadow .2s,transform .2s;" onmouseover="this.style.boxShadow='0 6px 22px rgba(0,0,0,.11)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,.05)';this.style.transform=''">
                <div style="flex-shrink:0; width:60px; height:60px; border-radius:10px; overflow:hidden; background:#f1f5f9;">
                    <?php if($blog->image): ?>
                        <img src="<?php echo e(\App\Helpers\Settings::storageUrl($blog->image)); ?>" alt="<?php echo e($blog->title); ?>" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f0fdf4,#dcfce7);"><svg width="22" height="22" fill="none" stroke="#0f766e" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/></svg></div>
                    <?php endif; ?>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:10px; color:#94a3b8; font-weight:600; margin-bottom:2px;"><?php echo e(\Carbon\Carbon::parse($blog->created_at)->format('d M Y')); ?></div>
                    <div style="font-size:13px; font-weight:700; color:#1c1917; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;"><?php echo e($blog->title); ?></div>
                    <div style="font-size:11px; color:var(--primary); font-weight:600; margin-top:3px;">Read more &rarr;</div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div style="text-align:center; padding:36px; background:var(--cream); border-radius:12px;"><p style="font-size:13px; color:#94a3b8;">No blog posts yet.</p></div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php /**PATH /Users/moldokipgen/Projects/EHLOM DIGITAL/Ehlom CMS/resources/views/public/partials/home/blog.blade.php ENDPATH**/ ?>