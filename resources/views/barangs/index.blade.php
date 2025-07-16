@extends('layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Daftar Barang</h2>
        <a href="{{ route('barangs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Barang
        </a>
    </div>

    <!-- Formulir Filter dan Pencarian -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('barangs.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode barang..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-4">
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-primary w-100 me-2">Filter</button>
                    <a href="{{ route('barangs.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Akhir Formulir -->

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Tgl Kadaluarsa</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangs as $key => $barang)
                    <tr>
                        <td>{{ $barangs->firstItem() + $key }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->category->name ?? 'Tanpa Kategori' }}</td>
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
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            Tidak ada barang yang ditemukan. Coba reset filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Link Paginasi -->
    <div class="d-flex justify-content-center mt-4">
        {{ $barangs->links() }}
    </div>
@endsection
