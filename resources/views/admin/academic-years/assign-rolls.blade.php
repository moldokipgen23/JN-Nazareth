@extends('layouts.admin')
@section('page-title','Assign Roll Numbers')
@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('admin.academic-years.index') }}"
       style="padding:6px;border-radius:8px;color:#64748b;display:flex;align-items:center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Assign Roll Numbers</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">
            <strong>{{ $academicYear->name }}</strong> — Drag to reorder students. Position = roll number.
        </div>
    </div>
</div>

{{-- Class selector --}}
<div style="background:#fff;border-radius:12px;padding:14px 18px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <label style="font-size:13px;font-weight:600;color:#0f172a;">Class:</label>
    <form method="GET" action="{{ route('admin.academic-years.assign-rolls', $academicYear) }}" style="display:flex;gap:10px;align-items:center;">
        <select name="class" onchange="this.form.submit()" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 12px;font-size:13px;min-width:160px;">
            <option value="">— Select class —</option>
            @foreach($classes as $c)
                <option value="{{ $c }}" {{ $selectedClass === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </form>
    @if($selectedClass && $enrollments->isNotEmpty())
    <span style="font-size:12px;color:#64748b;">{{ $enrollments->count() }} student(s) · Drag to reorder</span>
    @endif
</div>

@if(!$selectedClass)
<div style="background:#fff;border-radius:12px;padding:48px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:36px;opacity:.3;margin-bottom:10px;">🔢</div>
    <div style="font-weight:600;color:#475569;">Select a class to assign roll numbers.</div>
</div>

@elseif($enrollments->isEmpty())
<div style="background:#fff;border-radius:12px;padding:48px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-weight:600;color:#475569;">No active students in {{ $selectedClass }} for {{ $academicYear->name }}.</div>
</div>

@else
<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="padding:12px 16px;background:#f8fafc;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <span style="font-size:13px;font-weight:700;color:#0f172a;">{{ $selectedClass }}</span>
        <div style="display:flex;gap:8px;">
            <button type="button" onclick="autoAssignByRank()"
                    style="background:#dbeafe;border:none;padding:5px 12px;border-radius:6px;font-size:11px;font-weight:600;color:#1d4ed8;cursor:pointer;">
                Auto-assign by Name
            </button>
            <button type="button" onclick="saveRollOrder()"
                    style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:5px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">
                Save Roll Numbers
            </button>
        </div>
    </div>

    <form id="roll-order-form" method="POST" action="{{ route('admin.academic-years.assign-rolls.save', $academicYear) }}">
        @csrf
        <input type="hidden" name="class" value="{{ $selectedClass }}">
        <div id="sortable-list" style="padding:0;">
            @foreach($enrollments as $enrollment)
            <div class="roll-row" data-id="{{ $enrollment->id }}" draggable="true"
                 style="display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f1f5f9;cursor:grab;background:#fff;transition:background .1s;user-select:none;"
                 ondragstart="onDragStart(event)" ondragover="onDragOver(event)" ondrop="onDrop(event)" ondragend="onDragEnd(event)">
                <div style="width:24px;text-align:center;flex-shrink:0;">
                    <svg width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path d="M8 6h8M8 12h8M8 18h8"/></svg>
                </div>
                <div class="roll-number" style="width:36px;height:36px;border-radius:50%;background:#f0fdfa;color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;flex-shrink:0;">
                    {{ $loop->iteration }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;color:#0f172a;font-size:14px;">{{ $enrollment->student?->name }}</div>
                    <div style="font-size:11px;color:#94a3b8;">
                        Roll {{ $enrollment->roll_number ?? '—' }}
                        @if($enrollment->section) · Sec {{ $enrollment->section }} @endif
                    </div>
                </div>
                <div style="font-size:11px;color:#94a3b8;flex-shrink:0;">
                    <span style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-weight:600;">#<span class="pos-label">{{ $loop->iteration }}</span></span>
                </div>
            </div>
            @endforeach
        </div>
        <input type="hidden" name="roll_order" id="roll_order" value="{{ $enrollments->pluck('id')->join(',') }}">
    </form>
</div>

<script>
let dragSrcEl = null;

function onDragStart(e) {
    dragSrcEl = this;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', '');
    this.style.opacity = '.5';
    this.style.background = '#f0fdfa';
}

function onDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    var list = document.getElementById('sortable-list');
    var after = null;
    for (var i = 0; i < list.children.length; i++) {
        var child = list.children[i];
        if (child === dragSrcEl) continue;
        var rect = child.getBoundingClientRect();
        var mid = rect.top + rect.height / 2;
        if (e.clientY < mid) { after = child; break; }
    }
    if (after) {
        list.insertBefore(dragSrcEl, after);
    } else {
        list.appendChild(dragSrcEl);
    }
}

function onDrop(e) {
    e.preventDefault();
    updateRollNumbers();
}

function onDragEnd(e) {
    this.style.opacity = '1';
    this.style.background = '#fff';
    dragSrcEl = null;
}

function updateRollNumbers() {
    var rows = document.querySelectorAll('.roll-row');
    var order = [];
    rows.forEach(function(row, index) {
        var num = index + 1;
        row.querySelector('.roll-number').textContent = num;
        row.querySelector('.pos-label').textContent = num;
        order.push(row.dataset.id);
    });
    document.getElementById('roll_order').value = order.join(',');
}

function autoAssignByRank() {
    var list = document.getElementById('sortable-list');
    var rows = Array.from(list.querySelectorAll('.roll-row'));
    rows.sort(function(a, b) {
        var nameA = a.querySelector('div > div:first-child').textContent.trim().toLowerCase();
        var nameB = b.querySelector('div > div:first-child').textContent.trim().toLowerCase();
        return nameA.localeCompare(nameB);
    });
    rows.forEach(function(row) { list.appendChild(row); });
    updateRollNumbers();
}

function saveRollOrder() {
    document.getElementById('roll-order-form').submit();
}
</script>
@endif

<style>
.roll-row.dragging { opacity: .3; }
.roll-row.drag-over { border-top: 2px solid #0f766e !important; }
</style>

@endsection
