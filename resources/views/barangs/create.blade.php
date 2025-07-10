@extends('layout')

@section('content')
    <h2>Tambah Barang Baru</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Ada kesalahan saat input:<br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('barangs.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Barang:</label>
            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kode Barang:</label>
            <input type="text" name="kode_barang" class="form-control" value="{{ old('kode_barang') }}" required>
        </div>

        {{-- PERUBAHAN DI SINI: Stok dan Stok Minimal dibuat berdampingan --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Stok Awal:</label>
                <input type="number" name="stok" class="form-control" value="{{ old('stok') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Stok Minimal (untuk notifikasi):</label>
                <input type="number" name="minimal_stok" class="form-control" value="{{ old('minimal_stok', 10) }}" required>
            </div>
        </div>
        {{-- AKHIR PERUBAHAN --}}

        <div class="mb-3">
            <label class="form-label">Harga:</label>
            <input type="number" step="0.01" name="harga" class="form-control" value="{{ old('harga') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Kadaluarsa:</label>
            <input type="date" name="tanggal_kadaluarsa" class="form-control" value="{{ old('tanggal_kadaluarsa') }}">
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
        <a href="{{ route('barangs.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
@endsection
