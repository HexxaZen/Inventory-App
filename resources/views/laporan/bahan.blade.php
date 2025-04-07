@extends('layouts.master')
@section('title','Laporan Bahan')
@section('laporanbahan')
<div class="container mt-5">
    <div class="card shadow-sm p-3">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h4 class="card-title mb-2">Laporan Daftar Bahan Baku</h4>
            <button class="btn btn-primary" onclick="window.location.href='{{ route('laporan.bahan.pdf') }}'">
                <i class="fas fa-arrow-down"></i> DOWNLOAD PDF
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.bahan') }}" class="row g-3">
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
            </form>
            
            <div class="table-responsive mt-4">
                @if ($bahans->isEmpty())
                    <div class="alert alert-warning text-center">Maaf, tidak ada data di tanggal ini</div>
                @else
                    <table class="table table-striped table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Jenis Bahan</th>
                                <th>Kategori Bahan</th>
                                <th>Sisa Stok</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bahans as $bahan)
                                <tr>
                                    <td>{{ $bahan->kode_bahan }}</td>
                                    <td>{{ $bahan->nama_bahan }}</td>
                                    <td>{{ $bahan->jenis_bahan }}</td>
                                    <td>{{ $bahan->kategori_bahan }}</td>
                                    <td>{{ $bahan->sisa_stok }}</td>
                                    <td>{{ $bahan->satuan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection