@extends('layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Profil Saya</h2>

    {{-- Status sukses --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success text-white">
            Profil berhasil diperbarui.
        </div>
    @endif

    {{-- Form Update Informasi Profil --}}
    <div class="card mb-4">
        <div class="card-header text-white">Update Informasi</div>
        <div class="card-body text-white">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Form Update Password --}}
    <div class="card mb-4">
        <div class="card-header text-white">Ubah Password</div>
        <div class="card-body text-white">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Form Hapus Akun --}}
    <div class="card mb-4 border-danger">
        <div class="card-header text-white">Hapus Akun</div>
        <div class="card-body text-white">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
