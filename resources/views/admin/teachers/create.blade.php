@extends('layouts.admin')
@section('page-title', 'Add Teacher')

@section('content')
<h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0 0 4px;">Add Teacher</h2>
<p style="font-size:12px; color:#64748b; margin:0 0 18px;">Add a teacher to the staff directory.</p>

@if($errors->any())
<div style="background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:11px 16px; margin-bottom:16px;">
    <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $e)<li style="font-size:12px; color:#b91c1c;">{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.teachers.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.teachers._form')
</form>
@endsection
