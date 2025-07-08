@extends('layout')

@section('content')
    <h2>Edit Barang: {{ $barang->nama_barang }}</h2>

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

    <form action="{{ route('barangs.update', $barang->id) }}" method="POST">
        @csrf
        @method('PUT')  {{-- Method PUT untuk proses update --}}

        <div class="mb-3">
            <label class="form-label">Nama Barang:</label>
            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $barang->nama_barang) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Kode Barang:</label>
            <input type="text" name="kode_barang" class="form-control" value="{{ old('kode_barang', $barang->kode_barang) }}">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Stok Saat Ini:</label>
                <input type="number" name="stok" class="form-control" value="{{ old('stok', $barang->stok) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Stok Minimal:</label>
                <input type="number" name="minimal_stok" class="form-control" value="{{ old('minimal_stok', $barang->minimal_stok) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga:</label>
            <input type="number" step="0.01" name="harga" class="form-control" value="{{ old('harga', $barang->harga) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Kadaluarsa:</label>
            <input type="date" name="tanggal_kadaluarsa" class="form-control" value="{{ old('tanggal_kadaluarsa', $barang->tanggal_kadaluarsa) }}">
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('barangs.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection
