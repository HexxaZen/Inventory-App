@extends('layouts.master')
@section('title','Laporan Bahan Keluar')
@section('laporanbahankeluar')
<div class="container mt-5">
    <div class="card shadow-sm mx-5 my-5">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Laporan Bahan Baku Keluar</h4>
            <button class="btn btn-primary" onclick="window.location.href='{{ route('laporan.bahankeluar.pdf') }}'">
                <i class="fas fa-arrow-down"></i> DOWNLOAD PDF
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.bahankeluar') }}" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-5">
                        <label for="dari_tanggal" class="form-label">Dari Tanggal:</label>
                        <input type="date" class="form-control" name="dari_tanggal" value="{{ request('dari_tanggal') }}" required>
                    </div>
                    <div class="col-md-5">
                        <label for="sampai_tanggal" class="form-label">Sampai Tanggal:</label>
                        <input type="date" class="form-control" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
            
            <div class="table-responsive">
                @if ($dataKeluar->isEmpty())
                    <div class="alert alert-warning text-center">Maaf, tidak ada data di tanggal ini</div>
                @else
                    <table class="table table-bordered table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal Keluar</th>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Jumlah Keluar</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataKeluar as $item)
                            <tr>
                                <td>{{ $item['tanggal_keluar'] }}</td>
                                <td>{{ $item['kode_bahan'] }}</td>
                                <td>{{ $item['nama_bahan'] }}</td>
                                <td>{{ $item['jumlah_keluar'] }}</td>
                                <td>{{ $item['satuan'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data bahan keluar untuk hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection