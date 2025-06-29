@extends('layout')

@section('title', 'Profil Saya')
@section('icon', 'fa-user-circle')

@section('content')
<div class="container">
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="fas fa-user-circle me-2"></i>
            Profil Saya
        </h1>
    </div>

    {{-- Status sukses --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            Profil berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Update Informasi Profil --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center">
            <i class="fas fa-user-edit me-2"></i>
            <span>Update Informasi Profil</span>
        </div>
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Form Update Password --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center">
            <i class="fas fa-key me-2"></i>
            <span>Ubah Password</span>
        </div>
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Form Hapus Akun --}}
    <div class="card border-danger">
        <div class="card-header d-flex align-items-center text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span>Hapus Akun</span>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <i class="fas fa-info-circle me-2"></i>
                Setelah akun dihapus, semua data dan resource yang terkait akan dihapus permanen.
            </div>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
