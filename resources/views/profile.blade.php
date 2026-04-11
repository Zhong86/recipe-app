@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
{{-- ── Profile Hero ─────────────────────────────── --}}
<div class="profile-hero">
    <div class="profile-hero-inner">

        {{-- Avatar --}}
        <div class="profile-avatar-wrap">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="profile-avatar">
            @else
                <div class="profile-avatar-initials">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
            @endif

            {{-- Clicking pencil scrolls to avatar section --}}
            <a href="#avatar" class="profile-avatar-edit-btn" title="Change photo">✎</a>
        </div>

        <div class="profile-hero-info">
            <div class="profile-hero-name">{{ $user->name }}</div>
            <div class="profile-hero-email">{{ $user->email }}</div>
        </div>
    </div>

    <div class="profile-tabs">
        <div class="profile-tabs-inner">
            <a href="#profile"  class="profile-tab active">Profile</a>
            <a href="#password" class="profile-tab">Security</a>
            <a href="#avatar"   class="profile-tab">Photo</a>
        </div>
    </div>
</div>

{{-- Flash messages --}}
<div class="profile-flash">
    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif
</div>

{{-- ── Settings grid ────────────────────────────── --}}
<div class="profile-body">

    {{-- ── 1. Username ──────────────────────────── --}}
    <div class="setting-card" id="profile">
        <div class="setting-card-head">
            <div class="setting-card-icon">👤</div>
            <div>
                <div class="setting-card-title">Display Name</div>
                <div class="setting-card-desc">How your name appears across the site</div>
            </div>
        </div>
        <div class="setting-card-body">
            <form action="{{ route('profile.name') }}" method="POST">
                @csrf

                <div class="field">
                    <label for="name">Username</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        autocomplete="nickname"
                        required
                    >
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="email_display">Email</label>
                    <input type="email" id="email_display" value="{{ $user->email }}" readonly>
                    <span class="field-hint">Email cannot be changed here.</span>
                </div>

                <div class="setting-actions">
                    <button type="submit" class="btn-primary btn-primary-terra">Save name</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── 2. Profile picture ───────────────────── --}}
<div class="setting-card" id="avatar">
    <div class="setting-card-head">
        <div class="setting-card-icon">🖼️</div>
        <div>
            <div class="setting-card-title">Profile Picture</div>
            <div class="setting-card-desc">JPG, PNG or WebP — max 2 MB</div>
        </div>
    </div>
    <div class="setting-card-body">
        <div class="avatar-preview-wrap">
            <div class="avatar-preview" id="avatarPreview">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatarPreviewImg">
                @else
                    <span id="avatarPreviewInitials">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                @endif
            </div>
            <div class="avatar-preview-meta">
                <div class="avatar-preview-name">{{ $user->name }}</div>
            </div>
        </div>

        {{-- Delete form OUTSIDE upload form --}}
        @if ($user->avatar_url)
        <form action="{{ route('profile.avatar.delete') }}" method="POST" style="margin:0 0 1rem 0">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger btn-delete-avatar"
                onclick="return confirm('Remove your profile picture?')">
                Remove photo
            </button>
        </form>
        @endif

        <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label class="file-dropzone" id="dropzone">
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" id="avatarInput">
                <div class="dropzone-icon" id="dropzoneIcon">📷</div>
                <div class="dropzone-label">Click or drag to upload</div>
                <div class="dropzone-hint">JPG, PNG, WebP up to 2 MB</div>
                <div class="dropzone-filename" id="dropzoneFilename"></div>
            </label>
            @error('avatar')
                <span class="field-error" style="display:block;margin-top:.5rem">{{ $message }}</span>
            @enderror
            <div class="setting-actions">
                <button type="submit" class="btn-primary btn-primary-terra">Upload photo</button>
            </div>
        </form>
    </div>
</div>

    {{-- ── 3. Change password ───────────────────── --}}
    <div class="setting-card" id="password">
        <div class="setting-card-head">
            <div class="setting-card-icon">🔒</div>
            <div>
                <div class="setting-card-title">Change Password</div>
                <div class="setting-card-desc">Use a strong password you don't use elsewhere</div>
            </div>
        </div>
        <div class="setting-card-body">
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf

                <div class="field">
                    <label for="current_password">Current password</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                        required
                    >
                    @error('current_password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">New password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        required
                    >
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm new password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="setting-actions">
                    <button type="submit" class="btn-primary btn-primary-terra">Update password</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── 4. Account actions ───────────────────── --}}
    <div class="setting-card setting-card-danger">
        <div class="setting-card-head">
            <div class="setting-card-icon">⚙️</div>
            <div>
                <div class="setting-card-title">Account</div>
                <div class="setting-card-desc">Session and account management</div>
            </div>
        </div>
        <div class="setting-card-body">

            {{-- Logout --}}
            <div class="danger-row">
                <div class="danger-row-info">
                    <div class="danger-row-title">Sign out</div>
                    <div class="danger-row-desc">End your current session on this device</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin:0">
                    @csrf
                    <button type="submit" class="btn-danger btn-logout">Sign out</button>
                </form>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// ── Avatar live preview ────────────────────────
const avatarInput    = document.getElementById('avatarInput');
const dropzone       = document.getElementById('dropzone');
const dropzoneName   = document.getElementById('dropzoneFilename');
const previewWrap    = document.getElementById('avatarPreview');

avatarInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    dropzoneName.style.display = 'block';
    dropzoneName.textContent   = file.name;

    const reader = new FileReader();
    reader.onload = e => {
        // Replace whatever is inside the preview with an img
        previewWrap.innerHTML = `<img src="${e.target.result}" alt="preview" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
        // Also update the hero avatar
        const heroAvatars = document.querySelectorAll('.profile-avatar, .profile-avatar-initials');
        heroAvatars.forEach(el => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'profile-avatar';
            img.alt = 'preview';
            el.replaceWith(img);
        });
    };
    reader.readAsDataURL(file);
});

// Drag-over highlight
dropzone.addEventListener('dragover',  e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
dropzone.addEventListener('drop',      () => dropzone.classList.remove('drag-over'));

// ── Smooth scroll for tab links ───────────────
document.querySelectorAll('.profile-tab').forEach(tab => {
    tab.addEventListener('click', function (e) {
        document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
@endpush
