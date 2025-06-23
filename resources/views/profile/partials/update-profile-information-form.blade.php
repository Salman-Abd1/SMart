<section>
    <header>
        <h2 class="text-lg font-medium text-black dark:text-black">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-black dark:text-black">
            {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="'Nama User'" class="text-black dark:text-black" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-black" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-danger" :messages="$errors->get('name')" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="'Email'" class="text-black dark:text-black" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-black" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-danger" :messages="$errors->get('email')" />
        </div>

            {{-- Tautan verifikasi email --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-black dark:text-black">
                        {{ __('Alamat email Anda belum diverifikasi.') }}

                        <button form="send-verification" class="underline text-sm text-black dark:text-black hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif


        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-success">Simpan</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-black dark:text-black"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
