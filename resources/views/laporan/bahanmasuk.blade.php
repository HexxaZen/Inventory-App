@extends('layouts.master')
@section('title','Laporan Bahan Masuk')
@section('laporanbahanmasuk')
<div class="container mt-5">
    <div class="card shadow-sm mx-5 my-5">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Laporan Bahan Baku Masuk</h4>
            <button class="btn btn-primary" onclick="window.location.href='{{ route('laporan.bahanmasuk.pdf') }}'">
                <i class="fas fa-arrow-down"></i> DOWNLOAD PDF
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.bahanmasuk') }}">
                <div class="row g-3">
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
            
            <div class="table-responsive mt-4">
                @if ($bahanMasuk->isEmpty())
                    <div class="alert alert-warning text-center">Maaf, tidak ada data di tanggal ini</div>
                @else
                    <table class="table table-striped table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal Masuk</th>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Jumlah Masuk</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bahanMasuk as $item)
                            <tr>
                                <td>{{ $item->tanggal_masuk }}</td>
                                <td>{{ $item->kode_bahan }}</td>
                                <td>{{ $item->nama_bahan }}</td>
                                <td>{{ $item->jumlah_masuk }}</td>
                                <td>{{ $item->bahan->satuan ?? '-' }}</td>
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
