@extends('layout')

@section('content')
    <h2>Daftar Barang</h2>
    <a href="{{ route('barangs.create') }}" class="btn btn-primary mb-3">Tambah Barang</a>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">

        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kode</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Tgl Kadaluarsa</th>
                <th>Aksi</th>
            </tr>
        </thead>

            <tbody>
                @foreach ($barangs as $key => $barang)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->stok }}</td>
                        <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                        <td>
                            {{ $barang->tanggal_kadaluarsa ? \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->format('d-m-Y') : '-' }}
                        </td>
                        <td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
