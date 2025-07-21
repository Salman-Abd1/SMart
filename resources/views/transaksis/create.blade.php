@extends('layout')

@section('content')
<div class="container mt-4">
    <!-- Elemen untuk menyimpan data barang dengan aman -->
    <div id="barang-data" data-barangs='{!! json_encode($barangs->keyBy('id')) !!}' style="display: none;"></div>

    <div class="row">
        <!-- Kolom Utama Form Transaksi -->
        <div class="col-lg-8">
            <div class="shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Input Transaksi</h4>
                    <p class="card-subtitle mb-4 text-muted">Silakan cari dan pilih barang, lalu masukkan jumlah yang ingin dikeluarkan.</p>

                    <!-- Area Input Utama -->
                    <div class="row g-2 mb-3 align-items-center p-3 bg-light rounded border">
                        <div class="col-md-5">
                            <label for="barang-selector" class="form-label">Pilih Barang</label>
                            <select id="barang-selector" class="form-select"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="jumlah-input" class="form-label">Jumlah</label>
                            <input type="number" id="jumlah-input" class="form-control" min="1" placeholder="Jumlah">
                        </div>
                        <div class="col-md-3 d-grid">
                             <label class="form-label d-block">&nbsp;</label>
                            <button type="button" id="add-to-cart-btn" class="btn btn-success" disabled>
                                <i class="fas fa-plus me-2"></i>Tambah
                            </button>
                        </div>
                        <div class="col-12">
                             <small id="stock-info" class="text-danger d-block" style="display:none;"></small>
                        </div>
                    </div>

                    <hr>

                    <!-- Tabel untuk menampilkan item yang ditambahkan -->
                    <h5>Keranjang Belanja</h5>
                    <form action="{{ route('transaksis.store') }}" method="POST" id="transaction-form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items-body">
                                    <!-- Item yang ditambahkan akan muncul di sini -->
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Samping untuk Total dan Pembayaran -->
        <div class="col-lg-4">
            <div class="shadow-sm" style="position: sticky; top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Total Belanja</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="total-harga" class="form-label">Total Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="total-harga" class="form-control form-control-lg text-end" value="0" readonly style="font-size: 1.75rem; font-weight: bold;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pembayaran" class="form-label">Jumlah Bayar</label>
                         <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="pembayaran" class="form-control" placeholder="0">
                        </div>
                        <small id="pembayaran-warning" class="text-danger mt-1 d-block" style="display: none;"></small>
                    </div>
                    <div class="mb-3">
                        <label for="kembalian" class="form-label">Kembalian</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="kembalian" class="form-control" value="0" readonly>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" form="transaction-form" id="simpan-transaksi-btn" class="btn btn-primary btn-lg" disabled>
                            <i class="fas fa-save me-2"></i>Simpan Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const barangDataEl = document.getElementById('barang-data');
    const barangs = JSON.parse(barangDataEl.dataset.barangs);

    const barangOptions = Object.values(barangs).map(barang => ({
        id: barang.id,
        text: `${barang.kode_barang} - ${barang.nama_barang} (Stok: ${barang.stok})`,
        stok: barang.stok,
        harga: barang.harga,
        // Data tambahan untuk custom matcher
        kode_barang: barang.kode_barang,
        nama_barang: barang.nama_barang
    }));

    const barangSelector = $('#barang-selector');
    const jumlahInput = $('#jumlah-input');
    const addToCartBtn = $('#add-to-cart-btn');
    const cartItemsBody = $('#cart-items-body');
    const stockInfo = $('#stock-info');

    const totalHargaEl = $('#total-harga');
    const pembayaranEl = $('#pembayaran');
    const kembalianEl = $('#kembalian');
    const simpanBtn = $('#simpan-transaksi-btn');
    const pembayaranWarning = $('#pembayaran-warning');

    let itemIndex = 0;

    // Inisialisasi Select2 dengan custom matcher
    barangSelector.select2({
        data: barangOptions,
        theme: 'bootstrap-5',
        placeholder: 'Ketik untuk mencari barang...',
        width: '100%',
        // FIX: Added a custom matcher to search only by product name and code, ignoring stock.
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }
            const term = params.term.toLowerCase();
            const kode = data.kode_barang.toLowerCase();
            const nama = data.nama_barang.toLowerCase();
            if (kode.indexOf(term) > -1 || nama.indexOf(term) > -1) {
                return data;
            }
            return null;
        }
    }).val(null).trigger('change');

    function validateInput() {
        const selectedBarang = barangSelector.select2('data')[0];
        const jumlah = parseInt(jumlahInput.val());
        let isValid = true;
        stockInfo.hide();

        if (!selectedBarang || !selectedBarang.id || isNaN(jumlah) || jumlah <= 0) {
            isValid = false;
        } else if (jumlah > selectedBarang.stok) {
            stockInfo.text(`Jumlah melebihi stok yang tersedia (${selectedBarang.stok})`).show();
            isValid = false;
        }
        addToCartBtn.prop('disabled', !isValid);
    }

    function validateSaveButton() {
        const cartItemCount = cartItemsBody.find('tr').length;
        const total = parseFloat(totalHargaEl.val().replace(/\./g, '')) || 0;
        const pembayaran = parseFloat(pembayaranEl.val()) || 0;
        let isInvalid = false;

        pembayaranWarning.hide();

        if (cartItemCount === 0) {
            isInvalid = true;
        } else if (pembayaranEl.val().trim() === '') {
            isInvalid = true;
        } else if (pembayaran < total) {
            pembayaranWarning.text('Jumlah bayar kurang.').show();
            isInvalid = true;
        }
        simpanBtn.prop('disabled', isInvalid);
    }

    function calculateTotal() {
        let total = 0;
        cartItemsBody.find('tr').each(function() {
            total += parseFloat($(this).data('subtotal'));
        });
        totalHargaEl.val(new Intl.NumberFormat('id-ID').format(total));
        calculateChange();
    }

    function calculateChange() {
        const total = parseFloat(totalHargaEl.val().replace(/\./g, '')) || 0;
        const pembayaran = parseFloat(pembayaranEl.val()) || 0;
        const kembalian = pembayaran - total;
        kembalianEl.val(kembalian >= 0 ? new Intl.NumberFormat('id-ID').format(kembalian) : '0');
        validateSaveButton();
    }

    function showSimpleNotification(message, type = 'success') {
        const notificationId = 'simple-notification';
        let existingNotification = document.getElementById(notificationId);
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.id = notificationId;
        notification.className = `alert alert-${type} shadow-lg`;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.left = '50%';
        notification.style.transform = 'translateX(-50%)';
        notification.style.zIndex = '2000';
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    addToCartBtn.on('click', function() {
        const selectedBarang = barangSelector.select2('data')[0];
        const jumlah = parseInt(jumlahInput.val());

        if (cartItemsBody.find(`tr[data-id="${selectedBarang.id}"]`).length > 0) {
            showSimpleNotification('Barang ini sudah ada di keranjang.', 'warning');
            return;
        }

        const subtotal = selectedBarang.harga * jumlah;
        const newRow = `
            <tr data-id="${selectedBarang.id}" data-subtotal="${subtotal}">
                <td>
                    ${selectedBarang.text}
                    <input type="hidden" name="items[${itemIndex}][barang_id]" value="${selectedBarang.id}">
                </td>
                <td class="text-center">
                    ${jumlah}
                    <input type="hidden" name="items[${itemIndex}][jumlah]" value="${jumlah}">
                </td>
                <td class="text-end">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        cartItemsBody.append(newRow);
        itemIndex++;
        calculateTotal();
        barangSelector.val(null).trigger('change');
        jumlahInput.val('');
        validateInput();
    });

    barangSelector.on('select2:select', () => {
        validateInput();
        jumlahInput.focus();
    });
    barangSelector.on('select2:unselect', validateInput);
    barangSelector.on('select2:open', () => setTimeout(() => document.querySelector('.select2-search__field').focus(), 10));
    jumlahInput.on('input', validateInput);
    pembayaranEl.on('input', calculateChange);

    cartItemsBody.on('click', '.remove-item-btn', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    @if (session('success'))
        showSimpleNotification("{{ session('success') }}", 'success');
    @endif
    @if (session('error'))
        showSimpleNotification("{{ session('error') }}", 'danger');
    @endif
});
</script>
@endpush
