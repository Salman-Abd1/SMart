<section class="mb-5">
    <header class="mb-4">
        <h2 class="text-lg font-medium text-dark">
            <i class="fas fa-user-circle me-2"></i>
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-2 text-muted">
            <i class="fas fa-info-circle me-1"></i>
            {{ __('Perbarui informasi profil dan alamat email akun Anda.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label text-dark">
                <i class="fas fa-user me-1"></i>
                {{ __('Nama User') }}
            </label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label text-dark">
                <i class="fas fa-envelope me-1"></i>
                {{ __('Email') }}
            </label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tautan verifikasi email --}}
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ __('Alamat email Anda belum diverifikasi.') }}
                <button form="send-verification" class="btn btn-link p-0 ms-2">
                    {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                </button>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                    </div>
                @endif
            </div>
        @endif

        <div class="d-flex align-items-center mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success ms-3 mb-0 py-2"
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)">
                    <i class="fas fa-check me-2"></i>
                    {{ __('Profil berhasil diperbarui.') }}
                </div>
            @endif
        </div>
    </form>
</section>
