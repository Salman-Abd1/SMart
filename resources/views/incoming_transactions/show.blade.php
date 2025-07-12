@extends('layout')

@section('content')
    <div class="container mt-4">
        <h2>Detail Transaksi Masuk</h2>
        <p>Informasi lengkap mengenai transaksi masuk #{{ $incomingTransaction->id }}.</p>
        <hr class="mb-4">

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Informasi Umum Transaksi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID Transaksi:</strong> {{ $incomingTransaction->id }}</p>
                        <p><strong>Supplier:</strong> {{ $incomingTransaction->supplier_name }}</p>
                        <p><strong>Nomor Referensi:</strong> {{ $incomingTransaction->reference_number ?? '-' }}</p>
                        <p><strong>Dicatat Oleh:</strong> {{ $incomingTransaction->user->name ?? 'Tidak Diketahui' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tanggal Transaksi:</strong> {{ $incomingTransaction->created_at->format('d M Y H:i:s') }}</p>
                        <p><strong>Total Nilai Transaksi:</strong> Rp {{ number_format($incomingTransaction->total_amount, 0, ',', '.') }}</p>
                        <p><strong>Catatan:</strong> {{ $incomingTransaction->notes ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Daftar Barang Masuk</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Kode Barang</th>
                                <th>Jumlah Masuk</th>
                                <th>Harga Beli/Unit</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($incomingTransaction->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->barang->nama_barang ?? 'Barang Dihapus' }}</td>
                                    <td>{{ $item->barang->kode_barang ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada barang dalam transaksi ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-success-subtle">
                            <tr>
                                <th colspan="5" class="text-end">Total Nilai Keseluruhan Barang Masuk:</th>
                                <th>Rp {{ number_format($incomingTransaction->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <a href="{{ route('incoming_transactions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Transaksi Masuk
        </a>
        {{-- Tombol edit/hapus untuk transaksi ini bisa ditambahkan di sini --}}
    </div>
@endsection
