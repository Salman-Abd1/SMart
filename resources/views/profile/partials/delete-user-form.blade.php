<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-black dark:text-black">
            Hapus Akun
        </h2>

        <p class="mt-1 text-sm text-black dark:text-black">
            Setelah akun Anda dihapus, semua data akan dihapus secara permanen. Sebelum menghapus akun, harap unduh data penting yang ingin Anda simpan.
        </p>
    </header>

    <button class="btn btn-danger" x-data x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-black dark:text-black">
                Apakah Anda yakin ingin menghapus akun?
            </h2>

            <p class="mt-1 text-sm text-black dark:text-black">
                Setelah akun dihapus, semua sumber daya dan data akan dihapus secara permanen. Silakan masukkan kata sandi untuk konfirmasi.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Kata Sandi" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 text-black"
                    placeholder="Kata Sandi"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-danger" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <button type="submit" class="btn btn-danger">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
