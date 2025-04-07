@extends('layouts.master')
@section('title','Pemantauan Bahan Baku')
@section('pemantauan')
<div class="container-fluid mt-4 py-5">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Pemantauan Bahan Baku</h4>
            @if(request('dari_tanggal') && request('sampai_tanggal'))
                <a href="{{ route('laporan.pemantauan.pdf', ['dari_tanggal' => request('dari_tanggal'), 'sampai_tanggal' => request('sampai_tanggal')]) }}" class="btn btn-primary btn-round">
                    <i class="fas fa-arrow-down"></i> DOWNLOAD PDF
                </a>
            @endif
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.pemantauan') }}">
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
            
            @if(request('dari_tanggal') && request('sampai_tanggal'))
                <div class="table-responsive mt-4">
                    @if ($laporan->isEmpty() && request('dari_tanggal') != request('sampai_tanggal'))
                        <div class="alert alert-warning text-center">Maaf, tidak ada data di rentang tanggal ini</div>
                    @elseif ($laporan->isEmpty())
                        <div class="alert alert-warning text-center">Data tersedia, namun tidak ada pergerakan stok pada tanggal ini</div>
                    @else
                        <table class="table table-striped table-hover text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Kode Bahan</th>
                                    <th>Nama Bahan</th>
                                    <th>Total Bahan Masuk</th>
                                    <th>Total Bahan Keluar</th>
                                    <th>Status Pemantauan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($laporan as $data)
                                    <tr>
                                        <td>{{ $data['kode_bahan'] }}</td>
                                        <td>{{ $data['nama_bahan'] }}</td>
                                        <td>{{ $data['total_masuk'] }}</td>
                                        <td>{{ $data['total_keluar'] }}</td>
                                        <td>{{ $data['status_pemantauan'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
