@extends('layout')

@section('content')
    <div class="container mt-4">
        <h2>Input Transaksi</h2>
        <p>Silakan pilih barang dan jumlah yang ingin dikeluarkan. Pastikan stok barang mencukupi.</p>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Oops! Ada beberapa masalah dengan input Anda:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transaksis.store') }}" method="POST">
            @csrf

            <div id="transaction-items">
                {{-- Baris item akan ditambahkan oleh JavaScript --}}
            </div>

            <button type="button" id="add-item" class="btn btn-success mb-3" disabled><i class="fas fa-plus me-2"></i>Tambah Barang</button>
            <hr>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Transaksi</button>
            <a href="{{ route('transaksis.index') }}" class="btn btn-secondary">Lihat Riwayat</a>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 0;
    const barangs = @json($barangs); // Data barang dari controller
    const transactionItemsContainer = document.getElementById('transaction-items');
    const addItemButton = document.getElementById('add-item');

    // Fungsi untuk membuat dan menambahkan baris input barang baru
    function createNewRow() {
        const newRow = document.createElement('div');
        newRow.className = 'row item-row mb-3 align-items-end p-3 border rounded bg-light'; // Tambahkan kelas styling

        let options = '<option value="">-- Pilih Barang --</option>';
        barangs.forEach(function(barang) {
            options += `<option value="${barang.id}" data-stok="${barang.stok}">${barang.nama_barang} (Stok: ${barang.stok})</option>`;
        });

        newRow.innerHTML = `
            <div class="col-md-5">
                <label for="barang_id_${itemIndex}" class="form-label">Pilih Barang:</label>
                <select name="items[${itemIndex}][barang_id]" id="barang_id_${itemIndex}" class="form-select barang-select" required>
                    ${options}
                </select>
            </div>
            <div class="col-md-4">
                <label for="jumlah_${itemIndex}" class="form-label">Jumlah Keluar:</label>
                <input type="number" name="items[${itemIndex}][jumlah]" id="jumlah_${itemIndex}" class="form-control jumlah-input" min="1" required>
                <small class="text-danger stock-alert" style="display:none;"></small>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-item w-100">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        `;

        transactionItemsContainer.appendChild(newRow);
        itemIndex++; // Increment index untuk baris berikutnya

        // Fokus ke select barang yang baru ditambahkan
        newRow.querySelector('.barang-select').focus();
        // Nonaktifkan tombol tambah barang setelah baris baru dibuat
        toggleAddItemButton();
    }

    // Fungsi untuk mengaktifkan/menonaktifkan tombol "Tambah Barang"
    function toggleAddItemButton() {
        const lastRow = transactionItemsContainer.querySelector('.item-row:last-child');
        if (!lastRow) {
            addItemButton.disabled = true; // Jika tidak ada baris sama sekali
            return;
        }

        const selectElement = lastRow.querySelector('.barang-select');
        const quantityElement = lastRow.querySelector('.jumlah-input');
        const stockAlertElement = lastRow.querySelector('.stock-alert');

        // Pastikan kedua input ada dan terisi, serta tidak ada alert stok
        const isSelectFilled = selectElement && selectElement.value !== '';
        const isQuantityFilled = quantityElement && quantityElement.value !== '' && parseInt(quantityElement.value) > 0;
        const noStockAlert = stockAlertElement && stockAlertElement.style.display === 'none';

        // Aktifkan tombol jika semua kondisi terpenuhi
        if (isSelectFilled && isQuantityFilled && noStockAlert) {
            addItemButton.disabled = false;
        } else {
            addItemButton.disabled = true;
        }
    }

    // Fungsi untuk memvalidasi stok di sisi klien
    function validateStock(row) {
        const selectElement = row.querySelector('.barang-select');
        const quantityElement = row.querySelector('.jumlah-input');
        const stockAlertElement = row.querySelector('.stock-alert');

        stockAlertElement.style.display = 'none'; // Sembunyikan alert sebelumnya
        quantityElement.setCustomValidity(''); // Hapus pesan validasi sebelumnya

        if (selectElement.value && quantityElement.value) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const availableStock = parseInt(selectedOption.dataset.stok);
            const requestedQuantity = parseInt(quantityElement.value);

            if (isNaN(requestedQuantity) || requestedQuantity <= 0) {
                stockAlertElement.textContent = 'Jumlah harus lebih dari 0.';
                stockAlertElement.style.display = 'block';
                quantityElement.setCustomValidity("Jumlah harus positif.");
            } else if (requestedQuantity > availableStock) {
                stockAlertElement.textContent = `Stok tersedia: ${availableStock}. Permintaan melebihi stok!`;
                stockAlertElement.style.display = 'block';
                quantityElement.setCustomValidity("Jumlah melebihi stok tersedia.");
            }
        }
        toggleAddItemButton(); // Perbarui status tombol setelah validasi
    }

    // --- Event Listeners ---

    // Tambahkan baris pertama saat halaman dimuat
    createNewRow();

    // Event listener untuk tombol "Tambah Barang"
    addItemButton.addEventListener('click', function () {
        createNewRow();
    });

    // Event listener untuk perubahan pada input barang dan jumlah (delegasi event)
    transactionItemsContainer.addEventListener('change', function (e) {
        if (e.target.classList.contains('barang-select') || e.target.classList.contains('jumlah-input')) {
            validateStock(e.target.closest('.item-row'));
        }
    });

    // Event listener untuk input (ketika user mengetik) pada jumlah
    transactionItemsContainer.addEventListener('input', function (e) {
        if (e.target.classList.contains('jumlah-input')) {
            validateStock(e.target.closest('.item-row'));
        }
        // Juga panggil toggleAddItemButton untuk setiap input/perubahan
        // karena status tombol "tambah" bergantung pada validitas input terakhir
        toggleAddItemButton();
    });


    // Event listener untuk tombol "Hapus" (delegasi event)
    transactionItemsContainer.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-item')) {
            // Jangan hapus jika hanya ada satu baris tersisa
            if (transactionItemsContainer.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                // Setelah menghapus, perbarui status tombol tambah barang
                toggleAddItemButton();
            } else {
                alert('Minimal harus ada satu barang dalam transaksi.');
            }
        }
    });

    // Panggil toggleAddItemButton saat halaman dimuat untuk menginisialisasi
    toggleAddItemButton();
});
</script>
@endpush
