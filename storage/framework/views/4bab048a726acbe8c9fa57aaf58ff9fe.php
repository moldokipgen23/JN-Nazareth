<?php $__env->startSection('page-title', '🏆 Hall of Fame'); ?>

<?php $__env->startSection('content'); ?>

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">🏆 Hall of Fame</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Showcase notable achievements and community pioneers</p>
    </div>
    <a href="<?php echo e(route('admin.hall-of-fame.create')); ?>"
       style="display:inline-flex; align-items:center; gap:7px; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; padding:10px 18px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; box-shadow:0 4px 12px rgba(20,184,166,.3);">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Achievement
    </a>
</div>

<?php if(session('success')): ?>
<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:11px 16px; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
    <svg width="15" height="15" fill="none" stroke="#15803d" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
    <span style="font-size:13px; font-weight:600; color:#15803d;"><?php echo e(session('success')); ?></span>
</div>
<?php endif; ?>

<?php if($items->isEmpty()): ?>
<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:60px 20px; text-align:center;">
    <div style="font-size:48px; margin-bottom:16px;">🏆</div>
    <p style="font-size:15px; font-weight:600; color:#334155; margin:0 0 6px;">No entries yet</p>
    <p style="font-size:13px; color:#94a3b8; margin:0 0 20px;">Add the first Hall of Fame entry.</p>
    <a href="<?php echo e(route('admin.hall-of-fame.create')); ?>"
       style="display:inline-flex; align-items:center; gap:7px; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add First Achievement
    </a>
</div>
<?php else: ?>
<div style="display:flex; flex-direction:column; gap:12px;" id="firsts-list">
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div data-id="<?php echo e($item->id); ?>" style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid <?php echo e($item->active ? '#f1f5f9' : '#fef2f2'); ?>; padding:16px 20px; display:flex; align-items:center; gap:16px;">

        
        <div class="drag-handle" style="cursor:grab; color:#94a3b8; flex-shrink:0; padding:4px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
            </svg>
        </div>

        
        <div style="flex-shrink:0; width:56px; height:56px; border-radius:10px; overflow:hidden; background:#f1f5f9; border:1px solid #e2e8f0;">
            <?php if($item->photo): ?>
            <img src="<?php echo e(\App\Helpers\Settings::storageUrl($item->photo)); ?>" alt="<?php echo e($item->name); ?>" style="width:100%; height:100%; object-fit:cover;">
            <?php else: ?>
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:22px;">🏆</div>
            <?php endif; ?>
        </div>

        
        <div style="flex:1; min-width:0;">
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:3px;">
                <span style="font-size:14px; font-weight:700; color:#0f172a;"><?php echo e($item->name); ?></span>
                <?php if($item->featured): ?>
                <span style="background:#fef3c7; color:#92400e; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px;">⭐ Featured</span>
                <?php endif; ?>
                <?php if(!$item->active): ?>
                <span style="background:#fef2f2; color:#ef4444; font-size:10px; font-weight:600; padding:2px 8px; border-radius:20px;">Hidden</span>
                <?php endif; ?>
            </div>
            <div style="font-size:12px; color:#0f766e; font-weight:600;"><?php echo e($item->achievement_title); ?></div>
            <?php if($item->year): ?>
            <div style="font-size:11px; color:#94a3b8; margin-top:2px;"><?php echo e($item->year); ?></div>
            <?php endif; ?>
            <?php if($item->description): ?>
            <div style="font-size:11px; color:#64748b; margin-top:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:400px;"><?php echo e($item->description); ?></div>
            <?php endif; ?>
        </div>

        
        <div style="display:flex; gap:6px; flex-shrink:0;">
            <a href="<?php echo e(route('admin.hall-of-fame.edit', $item)); ?>"
               style="display:inline-flex; align-items:center; gap:5px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form method="POST" action="<?php echo e(route('admin.hall-of-fame.toggle', $item)); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit"
                        style="display:inline-flex; align-items:center; gap:5px; background:<?php echo e($item->active ? '#fef9c3' : '#f0fdf4'); ?>; color:<?php echo e($item->active ? '#854d0e' : '#15803d'); ?>; border:1px solid <?php echo e($item->active ? '#fde68a' : '#bbf7d0'); ?>; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;">
                    <?php echo e($item->active ? 'Hide' : 'Show'); ?>

                </button>
            </form>
            <form method="POST" action="<?php echo e(route('admin.hall-of-fame.destroy', $item)); ?>"
                  onsubmit="return confirm('Delete this entry? This cannot be undone.')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit"
                        style="display:inline-flex; align-items:center; background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; padding:6px 10px; border-radius:8px; font-size:12px; cursor:pointer;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div style="margin-top:14px; padding:12px 16px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; font-size:12px; color:#0369a1; line-height:1.6;">
    <strong>Tip:</strong> Drag rows to reorder how they appear on the homepage. Changes save automatically.
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    const list = document.getElementById('firsts-list');
    if (!list) return;
    let dragSrc = null;

    list.querySelectorAll('[data-id]').forEach(function(item) {
        item.setAttribute('draggable', 'true');
        item.addEventListener('dragstart', function(e) {
            dragSrc = this;
            this.style.opacity = '.4';
            e.dataTransfer.effectAllowed = 'move';
        });
        item.addEventListener('dragend', function() {
            this.style.opacity = '1';
            list.querySelectorAll('[data-id]').forEach(function(el) { el.style.background = ''; });
            saveOrder();
        });
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.background = '#f0fdf4';
        });
        item.addEventListener('dragleave', function() { this.style.background = ''; });
        item.addEventListener('drop', function(e) {
            e.stopPropagation();
            if (dragSrc !== this) {
                const all = Array.from(list.querySelectorAll('[data-id]'));
                const si = all.indexOf(dragSrc), di = all.indexOf(this);
                if (si < di) list.insertBefore(dragSrc, this.nextSibling);
                else list.insertBefore(dragSrc, this);
            }
            this.style.background = '';
        });
    });

    function saveOrder() {
        const order = Array.from(list.querySelectorAll('[data-id]')).map(el => el.dataset.id);
        fetch('<?php echo e(route("admin.hall-of-fame.reorder")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ order: JSON.stringify(order) })
        });
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/moldokipgen/Projects/EHLOM DIGITAL/Ehlom CMS/resources/views/admin/hall-of-fame/index.blade.php ENDPATH**/ ?>