{{-- resources/views/transaksis/create.blade.php --}}

@extends('layout')

@section('content')
    <h2>Input Transaksi</h2>

    {{-- TAMBAHKAN BLOK INI --}}
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
            {{-- Baris item pertama akan ditambahkan oleh JavaScript --}}
        </div>

        <button type="button" id="add-item" class="btn btn-success mb-3"><i class="fas fa-plus me-2"></i>Tambah Barang</button>
        <hr>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Transaksi</button>
        <a href="{{ route('transaksis.index') }}" class="btn btn-secondary">Lihat Riwayat</a>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 0;
    const barangs = @json($barangs);
    const transactionItems = document.getElementById('transaction-items');

    function createNewRow() {
        // Buat elemen baris baru
        const newRow = document.createElement('div');
        newRow.className = 'row item-row mb-3 align-items-end';

        // Opsi untuk dropdown
        let options = '<option value="">-- Pilih Barang --</option>';
        barangs.forEach(function(barang) {
            options += `<option value="${barang.id}" data-stok="${barang.stok}">${barang.nama_barang} (Stok: ${barang.stok})</option>`;
        });

        // HTML untuk baris baru
        newRow.innerHTML = `
            <div class="col-md-6">
                <label class="form-label">Pilih Barang:</label>
                <select name="items[${itemIndex}][barang_id]" class="form-select" required>
                    ${options}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Jumlah Keluar:</label>
                <input type="number" name="items[${itemIndex}][jumlah]" class="form-control" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-item w-100">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        `;

        transactionItems.appendChild(newRow);
        itemIndex++;
    }

    // Tambahkan baris pertama saat halaman dimuat
    createNewRow();

    // Event listener untuk tombol "Tambah Barang"
    document.getElementById('add-item').addEventListener('click', createNewRow);

    // Event listener untuk tombol "Hapus" (delegasi event)
    transactionItems.addEventListener('click', function (e) {
        if (e.target && e.target.closest('.remove-item')) {
            // Jangan hapus jika hanya ada satu baris tersisa
            if (transactionItems.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
            } else {
                alert('Minimal harus ada satu barang dalam transaksi.');
            }
        }
    });
});
</script>
@endpush
