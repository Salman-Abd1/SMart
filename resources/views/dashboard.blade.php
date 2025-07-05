@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="alert alert-primary d-flex justify-content-between align-items-center" role="alert">
        <div>
            Selamat datang, <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role }})
        </div>
    </div>

    <div class="row mt-4">
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'pemilik')
        <div class="col-md-3 mb-3">
            <a href="{{ route('barangs.index') }}" class="text-decoration-none">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body text-center text-primary">
                        <i class="fas fa-boxes fa-2x mb-2"></i>
                        <h5 class="card-title">Daftar Barang</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('barangs.create') }}" class="text-decoration-none">
                <div class="card border-info shadow-sm h-100">
                    <div class="card-body text-center text-info">
                        <i class="fas fa-plus-circle fa-2x mb-2"></i>
                        <h5 class="card-title">Input Barang</h5>
                    </div>
                </div>
            </a>
        </div>
        @endif

        @if(Auth::user()->role === 'kasir' || Auth::user()->role === 'pemilik')
        <div class="col-md-3 mb-3">
            <a href="{{ route('transaksis.index') }}" class="text-decoration-none">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body text-center text-success">
                        <i class="fas fa-history fa-2x mb-2"></i>
                        <h5 class="card-title">Riwayat Transaksi</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('transaksis.create') }}" class="text-decoration-none">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body text-center text-warning">
                        <i class="fas fa-cash-register fa-2x mb-2"></i>
                        <h5 class="card-title">Input Transaksi</h5>
                    </div>
                </div>
            </a>
        </div>
        @endif

        @if(Auth::user()->role === 'pemilik')
        <div class="col-md-4 mb-3">
            <a href="{{ route('laporan.index') }}" class="text-decoration-none">
                <div class="card border-danger shadow-sm h-100">
                    <div class="card-body text-center text-danger">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <h5 class="card-title">Laporan</h5>
                    </div>
                </div>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
@section('title', 'Dashboard')
