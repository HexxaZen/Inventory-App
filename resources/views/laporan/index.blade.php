@extends('layouts.master')

@section('Laporan')
@section('indexLaporan')
    <div class="container-fluid">
        <div class="page-inner" style="margin-top: 15ch;">
            <div class="container">
                <div class="page-inner">
                    <div class="row justify-content-center">
                        @php
                            $laporans = [
                                ['route' => 'laporan.pemantauan', 'icon' => 'fas fa-solid fa-magnifying-glass-chart', 'title' => 'Pemantauan Bahan Baku'],
                                ['route' => 'laporan.bahan', 'icon' => 'fas fa-file-alt', 'title' => 'Laporan Bahan'],
                                ['route' => 'laporan.bahanmasuk', 'icon' => 'fas fa-file-import', 'title' => 'Laporan Bahan Masuk'],
                                ['route' => 'laporan.bahankeluar', 'icon' => 'fas fa-file-export', 'title' => 'Laporan Bahan Keluar'],
                                ['route' => 'laporan.bahanakhir', 'icon' => 'fas fa-poll-h', 'title' => 'Laporan Bahan Akhir'],
                                ['route' => 'laporan.keseluruhanbahanbaku', 'icon' => 'fas fa-utensils', 'title' => 'Ringkasan Bahan Baku'],
                                ['route' => 'laporan.bahan', 'icon' => 'fas fa-box-open', 'title' => 'Laporan Inventaris'],
                            ];
                        @endphp

                        @foreach($laporans as $laporan)
                            <div class="col-sm-6 col-md-3 mb-3 d-flex">
                                <a href="{{ route($laporan['route']) }}" class="card card-stats card-round text-decoration-none w-100 h-100 d-flex align-items-center justify-content-center text-center">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small mb-3">
                                            <i class="{{ $laporan['icon'] }} fa-3x"></i>
                                        </div>
                                        <div class="numbers">
                                            <p class="card-category mb-1">{{ $laporan['title'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
