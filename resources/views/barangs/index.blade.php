@extends('layout')

@section('content')
    <h2>Daftar Barang</h2>
    <a href="{{ route('barangs.create') }}" class="btn btn-primary mb-3">Tambah Barang</a>
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th style="width: 5%;">No</th>
                <th>Nama</th>
                <th>Kode</th>
                <th>Kategori</th> {{-- <-- KOLOM BARU --}}
                <th>Stok</th>
                <th>Harga</th>
                <th>Tgl Kadaluarsa</th>
                <th style="width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barangs as $key => $barang)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->kode_barang }}</td>
                    <td>{{ $barang->category->name ?? 'Tanpa Kategori' }}</td> {{-- <-- DATA KATEGORI --}}
                    <td>{{ $barang->stok }}</td>
                    <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                    <td>
                        {{ $barang->tanggal_kadaluarsa ? \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->format('d-m-Y') : '-' }}
                    </td>
                    <td>
                        <a href="{{ route('barangs.edit', $barang->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('barangs.destroy', $barang->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
