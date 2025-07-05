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
            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Kode Barang:</label>
            <input type="text" name="kode_barang" class="form-control" value="{{ old('kode_barang') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Stok:</label>
            <input type="number" name="stok" class="form-control" value="{{ old('stok') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Harga:</label>
            <input type="number" step="0.01" name="harga" class="form-control" value="{{ old('harga') }}">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('barangs.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
@endsection
