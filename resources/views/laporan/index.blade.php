@extends('layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Laporan Penjualan</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="periode" class="form-label">Filter Berdasarkan Periode:</label>
                    <select name="periode" id="periode" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Waktu</option>
                        <option value="harian" {{ request('periode') == 'harian' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="mingguan" {{ request('periode') == 'mingguan' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="bulanan" {{ request('periode') == 'bulanan' ? 'selected' : '' }}>Bulan Ini</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary d-block">Reset Filter</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="fas fa-chart-line me-2"></i>Grafik Penjualan (30 Hari Terakhir)
        </div>
        <div class="card-body">
            <canvas id="grafikPenjualan"></canvas>
        </div>
    </div>

    <div class="table-responsive">
        {{-- Mengubah kelas tabel agar konsisten dengan yang lain --}}
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
                @forelse ($transaksis as $index => $transaksi)
                    <tr>
                        <td>{{ $transaksis->firstItem() + $index }}</td>
                        <td>{{ $transaksi->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $transaksi->jumlah }}</td>
                        <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data transaksi untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="alert alert-success fw-bold">
            Total Penjualan (Sesuai Filter): Rp {{ number_format($total, 0, ',', '.') }}
        </div>
        <div>
            {{-- Menampilkan link Paginasi --}}
            {{ $transaksis->links() }}
        </div>
    </div>
</div>
@endsection

{{-- Mendorong script Chart.js ke layout utama --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('grafikPenjualan').getContext('2d');
        const grafikPenjualan = new Chart(ctx, {
            type: 'line', // Tipe grafik: line, bar, dll.
            data: {
                labels: @json($labels), // Label tanggal dari controller
                datasets: [{
                    label: 'Total Penjualan',
                    data: @json($data), // Data penjualan dari controller
                    backgroundColor: 'rgba(74, 144, 226, 0.2)',
                    borderColor: 'rgba(74, 144, 226, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
