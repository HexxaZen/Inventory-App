@extends('layouts.master')
@section('title','Data Bahan Akhir')
@section('databahanakhir')
<div class="container mt-5">
    <div class="card shadow-sm px-5 py-5">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h4 class="card-title mb-2">Data Bahan Akhir</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-4">
                @if ($data->isEmpty())
                    <div class="alert alert-warning text-center">Maaf, tidak ada data di tanggal ini</div>
                @else
                    <table class="table table-striped table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Last</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $data)
                                <tr>
                                    <td>{{ $data->tanggal_input }}</td>
                                    <td>{{ $data->kode_bahan }}</td>
                                    <td>{{ $data->nama_bahan }}</td>
                                    <td>{{ $data->stok_terakhir }}</td>
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