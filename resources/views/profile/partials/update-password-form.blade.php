<section>
    <header>
        <h2 class="text-lg font-medium text-black dark:text-black">
            {{ __('Perbarui Kata Sandi') }}
        </h2>

        <p class="mt-1 text-sm text-black dark:text-black">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk menjaga keamanan.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="'Kata Sandi Saat Ini'" class="text-black dark:text-black" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full text-black" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-danger" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="'Kata Sandi Baru'" class="text-black dark:text-black" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full text-black" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-danger" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="'Konfirmasi Kata Sandi'" class="text-black dark:text-black" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-black" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-danger" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-success">Simpan</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-black dark:text-black"
                >{{ __('Kata sandi berhasil diperbarui.') }}</p>
            @endif
        </div>
    </form>
</section>
