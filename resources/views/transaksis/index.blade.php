@extends('layout')

@section('content')
    <h2>Riwayat Transaksi</h2>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    
    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <a href="{{ route('transaksis.create') }}" class="btn btn-primary mb-3">Tambah Transaksi Baru</a><br><br>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksis as $key => $transaksi)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $transaksi->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $transaksi->jumlah }}</td>
                        <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
