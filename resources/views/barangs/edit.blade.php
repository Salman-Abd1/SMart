@extends('layout')

@section('content')
    <h2>Edit Barang</h2>

    @if ($errors->any())
        <div>
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
        @method('PUT')

        <label>Nama Barang:</label><br>
        <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}"><br>

        <label>Kode Barang:</label><br>
        <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}"><br>

        <label>Stok:</label><br>
        <input type="number" name="stok" value="{{ old('stok', $barang->stok) }}"><br>

        <label>Harga:</label><br>
        <input type="number" step="0.01" name="harga" value="{{ old('harga', $barang->harga) }}"><br><br>

        <label>Tanggal Kadaluarsa:</label><br>
        <input type="date" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa', $barang->tanggal_kadaluarsa) }}"><br><br>

        <button type="submit">Update</button>
        <a href="{{ route('barangs.index') }}">Kembali</a>
    </form>
@endsection
