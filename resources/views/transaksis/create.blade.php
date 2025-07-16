@extends('layout')

@section('content')
<div class="container mt-4">
    <!-- Elemen untuk menyimpan data barang dengan aman -->
    <div id="barang-data" data-barangs="{{ json_encode($barangs->keyBy('id')) }}" style="display: none;"></div>

    <div class="row">
        <!-- Kolom Utama Form Transaksi -->
        <div class="col-lg-8">
            <h2>Input Transaksi</h2>
            <p>Silakan cari dan pilih barang, lalu masukkan jumlah yang ingin dikeluarkan.</p>

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

            <form action="{{ route('transaksis.store') }}" method="POST" id="transaction-form">
                @csrf

                <div id="transaction-items">
                    {{-- Baris item akan ditambahkan oleh JavaScript --}}
                </div>

                <button type="button" id="add-item" class="btn btn-success mb-3" disabled>
                    <i class="fas fa-plus me-2"></i>Tambah Barang
                </button>
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Transaksi
                </button>
                <a href="{{ route('transaksis.index') }}" class="btn btn-secondary">Lihat Riwayat</a>
            </form>
        </div>

        <!-- Kolom Samping untuk Total dan Pembayaran -->
        <div class="col-lg-4">
            <div class="card shadow-sm" style="position: sticky; top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">Total Belanja</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="total-harga" class="form-label">Total Harga (Rp)</label>
                        <input type="text" id="total-harga" class="form-control form-control-lg text-end" value="0" readonly style="font-size: 2rem; font-weight: bold;">
                    </div>
                    <div class="mb-3">
                        <label for="pembayaran" class="form-label">Jumlah Bayar (Rp)</label>
                        <input type="number" id="pembayaran" class="form-control" placeholder="Masukkan jumlah uang...">
                    </div>
                    <div class="mb-2">
                        <label for="kembalian" class="form-label">Kembalian (Rp)</label>
                        <input type="text" id="kembalian" class="form-control" value="0" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Pustaka Select2 sudah dipanggil di layout utama --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 0;

    const barangDataEl = document.getElementById('barang-data');
    const barangs = JSON.parse(barangDataEl.dataset.barangs);

    const transactionItemsContainer = document.getElementById('transaction-items');
    const addItemButton = document.getElementById('add-item');
    const totalHargaEl = document.getElementById('total-harga');
    const pembayaranEl = document.getElementById('pembayaran');
    const kembalianEl = document.getElementById('kembalian');

    function initializeSelect2(selector) {
        $(selector).select2({
            theme: 'bootstrap-5',
            placeholder: 'Ketik untuk mencari barang...',
        });
    }

    function createNewRow() {
        const newRow = document.createElement('div');
        newRow.className = 'row item-row mb-3 align-items-end p-3 border rounded bg-light';
        newRow.setAttribute('data-index', itemIndex);

        let options = '<option></option>'; // Empty option for placeholder
        Object.values(barangs).forEach(function(barang) {
            options += `<option value="${barang.id}" data-stok="${barang.stok}" data-harga="${barang.harga}">${barang.kode_barang} - ${barang.nama_barang} (Stok: ${barang.stok})</option>`;
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
        initializeSelect2(`#barang_id_${itemIndex}`);

        // Event listener khusus untuk Select2
        $(`#barang_id_${itemIndex}`).on('select2:select', function (e) {
            validateStockAndCalculate();
        });

        itemIndex++;
        toggleAddItemButton();
    }

    function toggleAddItemButton() {
        const lastRow = transactionItemsContainer.querySelector('.item-row:last-child');
        if (!lastRow) {
            addItemButton.disabled = true;
            return;
        }

        const selectElement = lastRow.querySelector('.barang-select');
        const quantityElement = lastRow.querySelector('.jumlah-input');
        const stockAlertElement = lastRow.querySelector('.stock-alert');

        const isSelectFilled = selectElement && selectElement.value !== '';
        const isQuantityFilled = quantityElement && quantityElement.value !== '' && parseInt(quantityElement.value) > 0;
        const noStockAlert = stockAlertElement && stockAlertElement.style.display === 'none';

        addItemButton.disabled = !(isSelectFilled && isQuantityFilled && noStockAlert);
    }

    function validateStockAndCalculate() {
        let total = 0;
        const rows = transactionItemsContainer.querySelectorAll('.item-row');
        rows.forEach(row => {
            const selectElement = row.querySelector('.barang-select');
            const quantityElement = row.querySelector('.jumlah-input');
            const stockAlertElement = row.querySelector('.stock-alert');

            stockAlertElement.style.display = 'none';
            quantityElement.setCustomValidity('');

            if (selectElement.value && quantityElement.value) {
                const selectedOption = $(selectElement).find('option:selected');
                const availableStock = parseInt(selectedOption.data('stok'));
                const requestedQuantity = parseInt(quantityElement.value);
                const harga = parseFloat(selectedOption.data('harga'));

                if (isNaN(requestedQuantity) || requestedQuantity <= 0) {
                    stockAlertElement.textContent = 'Jumlah harus > 0.';
                    stockAlertElement.style.display = 'block';
                    quantityElement.setCustomValidity("Jumlah harus positif.");
                } else if (requestedQuantity > availableStock) {
                    stockAlertElement.textContent = `Stok: ${availableStock}. Permintaan melebihi stok!`;
                    stockAlertElement.style.display = 'block';
                    quantityElement.setCustomValidity("Jumlah melebihi stok.");
                } else {
                    total += harga * requestedQuantity;
                }
            }
        });

        totalHargaEl.value = new Intl.NumberFormat('id-ID').format(total);
        calculateChange();
        toggleAddItemButton();
    }

    function calculateChange() {
        const total = parseFloat(totalHargaEl.value.replace(/\./g, '')) || 0;
        const pembayaran = parseFloat(pembayaranEl.value) || 0;
        const kembalian = pembayaran - total;

        kembalianEl.value = kembalian >= 0 ? new Intl.NumberFormat('id-ID').format(kembalian) : '0';
    }

    function showSimpleNotification(message, type = 'danger') {
        const notificationId = 'simple-notification';
        let existingNotification = document.getElementById(notificationId);
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.id = notificationId;
        notification.className = `alert alert-${type} shadow-lg`;
        notification.style.position = 'fixed';
        notification.style.bottom = '20px';
        notification.style.left = '50%';
        notification.style.transform = 'translateX(-50%)';
        notification.style.zIndex = '2000';
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }


    createNewRow();

    addItemButton.addEventListener('click', createNewRow);

    // FIX: Removed the conflicting 'change' event listener.
    // The 'input' event listener below is sufficient for the quantity field.
    transactionItemsContainer.addEventListener('input', function (e) {
        if (e.target.classList.contains('jumlah-input')) {
            validateStockAndCalculate();
        }
    });

    pembayaranEl.addEventListener('input', calculateChange);

    transactionItemsContainer.addEventListener('click', function (e) {
        if (e.target.closest('.remove-item')) {
            if (transactionItemsContainer.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                validateStockAndCalculate();
            } else {
                showSimpleNotification('Minimal harus ada satu barang dalam transaksi.', 'warning');
            }
        }
    });

    toggleAddItemButton();
});
</script>
@endpush
 