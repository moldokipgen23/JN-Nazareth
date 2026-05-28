@php
    $user = Auth::user();
    $isTeacher = $user->hasRole('teacher');
    $isAdmin = $user->hasRole('admin');
    $teacher = $user->teacher;
@endphp
@extends('layouts.admin')
@section('page-title', 'My Profile')

@push('styles')
<style>
    .pf-section{background:#fff;border-radius:14px;box-shadow:0 1px 4px rgba(15,23,42,.06);overflow:hidden;margin-bottom:16px}
    .pf-section-header{padding:16px 20px;border-bottom:1px solid #f1f5f9}
    .pf-section-title{font-size:15px;font-weight:700;color:#0f172a}
    .pf-section-sub{font-size:12px;color:#64748b;margin-top:2px}
    .pf-body{padding:16px 20px}
    .pf-label{display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px}
    .pf-input{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:14px;transition:border-color .15s;background:#fff}
    .pf-input:focus{outline:none;border-color:#0f766e;box-shadow:0 0 0 3px rgba(15,118,110,.1)}
    .pf-btn{background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:10px 22px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:opacity .15s}
    .pf-btn:hover{opacity:.9}
    .pf-btn-outline{background:#fff;color:#0f766e;border:1.5px solid #0f766e;padding:10px 22px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:all .15s}
    .pf-btn-outline:hover{background:#f0fdf4}
    .pf-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    @media(max-width:640px){.pf-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')

<div style="max-width:640px;margin:0 auto;">

    {{-- Profile header --}}
    <div class="pf-section" style="text-align:center;padding:28px 20px 24px;">
        @if($teacher && $teacher->photo)
            <img src="{{ Storage::url($teacher->photo) }}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:0 auto 12px;border:4px solid #f0fdf4;">
        @else
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#0f766e,#14b8a6);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:32px;font-weight:800;color:#fff;">{{ strtoupper(substr($user->name,0,1)) }}</div>
        @endif
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;">{{ $user->name }}</h1>
        <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:6px;">
            <span style="font-size:13px;color:#64748b;">{{ $user->email }}</span>
            <span style="width:4px;height:4px;background:#cbd5e1;border-radius:50%;display:inline-block;"></span>
            <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase;background:{{ $isAdmin?'#e0e7ff':'#ccfbf1' }};color:{{ $isAdmin?'#4338ca':'#0f766e' }};">
                {{ $isAdmin ? 'Admin' : ($isTeacher ? 'Teacher' : 'Staff') }}
            </span>
        </div>
        @if($teacher)
        <div style="margin-top:10px;display:flex;align-items:center;justify-content:center;gap:12px;font-size:12px;color:#64748b;">
            @if($teacher->phone)<span>📞 {{ $teacher->phone }}</span>@endif
            @if($teacher->qualification)<span>🎓 {{ $teacher->qualification }}</span>@endif
        </div>
        @endif
    </div>

    {{-- Profile Information --}}
    <div class="pf-section">
        <div class="pf-section-header">
            <div class="pf-section-title">Profile Information</div>
            <div class="pf-section-sub">Update your name and email address</div>
        </div>
        <div class="pf-body">
            <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf @method('patch')
                <div class="pf-grid">
                    <div>
                        <label class="pf-label" for="name">Full Name</label>
                        <input id="name" name="name" type="text" class="pf-input" value="{{ old('name', $user->name) }}" required>
                        @error('name')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="pf-label" for="email">Email Address</label>
                        <input id="email" name="email" type="email" class="pf-input" value="{{ old('email', $user->email) }}" required>
                        @error('email')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:12px;padding-top:4px;">
                    <button type="submit" class="pf-btn">Save Changes</button>
                    @if(session('status')==='profile-updated')
                        <span style="font-size:13px;font-weight:600;color:#16a34a;">✓ Saved</span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Teacher Details (only for teachers) --}}
    @if($teacher)
    <div class="pf-section">
        <div class="pf-section-header">
            <div class="pf-section-title">Teacher Details</div>
            <div class="pf-section-sub">Phone number and profile photo</div>
        </div>
        <div class="pf-body">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PATCH')

                <div class="pf-grid">
                    <div>
                        <label class="pf-label" for="phone">Phone Number</label>
                        <input id="phone" name="phone" type="text" class="pf-input" value="{{ old('phone', $teacher->phone) }}" placeholder="+91 98765 43210">
                        @error('phone')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="pf-label" for="qualification">Qualification</label>
                        <input id="qualification" name="qualification" type="text" class="pf-input" value="{{ old('qualification', $teacher->qualification) }}" placeholder="e.g. B.Ed, M.Sc">
                        @error('qualification')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="pf-label" for="photo">Profile Photo</label>
                    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                        <input id="photo" type="file" name="photo" accept="image/*" style="flex:1;min-width:160px;font-size:13px;color:#64748b;padding:6px 0;">
                        @if($teacher->photo)
                            <div style="display:flex;align-items:center;gap:8px;">
                                <img src="{{ Storage::url($teacher->photo) }}" style="width:44px;height:44px;border-radius:10px;object-fit:cover;border:2px solid #f1f5f9;">
                                <span style="font-size:11px;color:#94a3b8;">Current</span>
                            </div>
                        @endif
                    </div>
                    @error('photo')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <button type="submit" class="pf-btn">Save Teacher Details</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Update Password --}}
    <div class="pf-section">
        <div class="pf-section-header">
            <div class="pf-section-title">Update Password</div>
            <div class="pf-section-sub">Ensure your account is using a strong password</div>
        </div>
        <div class="pf-body">
            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                @csrf @method('put')
                <div class="pf-grid">
                    <div>
                        <label class="pf-label" for="update_password_current_password">Current Password</label>
                        <input id="update_password_current_password" name="current_password" type="password" class="pf-input" autocomplete="current-password">
                        @error('current_password','updatePassword')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="pf-label" for="update_password_password">New Password</label>
                        <input id="update_password_password" name="password" type="password" class="pf-input" autocomplete="new-password">
                        @error('password','updatePassword')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="pf-label" for="update_password_password_confirmation">Confirm New Password</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="pf-input" autocomplete="new-password">
                    @error('password_confirmation','updatePassword')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="display:flex;align-items:center;gap:12px;padding-top:4px;">
                    <button type="submit" class="pf-btn">Update Password</button>
                    @if(session('status')==='password-updated')
                        <span style="font-size:13px;font-weight:600;color:#16a34a;">✓ Password updated</span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Account info footer --}}
    <div style="padding:16px 4px;text-align:center;">
        <div style="font-size:12px;color:#94a3b8;">
            Member since {{ $user->created_at->format('F Y') }}
            @if($user->teacher && $user->teacher->staff_id)
                &middot; Staff ID: {{ $user->teacher->staff_id }}
            @endif
        </div>
    </div>

</div>

@endsection