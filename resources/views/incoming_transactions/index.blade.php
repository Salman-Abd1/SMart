@extends('layout')

@section('content')
    <div class="container mt-4">
        <h2>Daftar Transaksi Masuk</h2>
        <p>Riwayat pembelian barang dari supplier.</p>
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

        <a href="{{ route('incoming_transactions.create') }}" class="btn btn-primary mb-3">
            <i class="fas fa-plus me-2"></i>Catat Transaksi Masuk Baru
        </a>


                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Nomor Referensi</th>
                                <th>Total Nilai</th>
                                <th>Dicatat Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($incomingTransactions as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $transaction->supplier_name }}</td>
                                    <td>{{ $transaction->reference_number ?? '-' }}</td>
                                    <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                    <td>{{ $transaction->user->name ?? 'User Tidak Ditemukan' }}</td>
                                    <td>
                                        <a href="{{ route('incoming_transactions.show', $transaction->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        {{-- Tombol edit/hapus akan ditambahkan di sini --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada transaksi masuk yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Link paginasi --}}
                <div class="d-flex justify-content-center">
                    {{ $incomingTransactions->links() }}
                </div>
    </div>
@endsection
