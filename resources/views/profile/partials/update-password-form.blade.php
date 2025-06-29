<section class="mb-5">
    <header class="mb-4">
        <h2 class="text-lg font-medium text-dark">
            <i class="fas fa-key me-2"></i>
            {{ __('Perbarui Kata Sandi') }}
        </h2>

        <p class="mt-2 text-muted">
            <i class="fas fa-shield-alt me-1"></i>
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk menjaga keamanan.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label text-dark">
                <i class="fas fa-lock me-1"></i>
                {{ __('Kata Sandi Saat Ini') }}
            </label>
            <input
                type="password"
                id="update_password_current_password"
                name="current_password"
                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                autocomplete="current-password"
            >
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label text-dark">
                <i class="fas fa-lock-open me-1"></i>
                {{ __('Kata Sandi Baru') }}
            </label>
            <input
                type="password"
                id="update_password_password"
                name="password"
                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                autocomplete="new-password"
            >
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label text-dark">
                <i class="fas fa-check-circle me-1"></i>
                {{ __('Konfirmasi Kata Sandi') }}
            </label>
            <input
                type="password"
                id="update_password_password_confirmation"
                name="password_confirmation"
                class="form-control"
                autocomplete="new-password"
            >
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success ms-3 mb-0 py-2"
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)">
                    <i class="fas fa-check me-2"></i>
                    {{ __('Kata sandi berhasil diperbarui.') }}
                </div>
            @endif
        </div>
    </form>
</section>
