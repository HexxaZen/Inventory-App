@extends('layouts.master')
@section('title', 'Dashboard | Merra Inventory')
@section('content')
    <div class="container-fluid">
        <div class="page-inner" style="margin-top: 15ch;">
            <div class="container">
                <div class="page-inner">
                    {{-- button --}}
                    <div class="row" style="justify-content: center;">
                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('bahan.index') }}" class="card card-stats card-round text-decoration-none">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category" style="color: black;">Data Bahan Baku</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('bahan.bahanmasuk') }}"
                                class="card card-stats card-round text-decoration-none">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-arrow-down"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"style="color: black;">Data Bahan Masuk</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('bahan.bahankeluar') }}"
                                class="card card-stats card-round text-decoration-none">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-arrow-up"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"style="color: black;">Data Bahan Keluar</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('bahan.bahanakhir') }}"
                                class="card card-stats card-round text-decoration-none">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                <i class="fas fa-clipboard-check"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"style="color: black;">Pendataan Bahan Akhir</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <a href="{{ route('menu.index') }}" class="card card-stats card-round text-decoration-none">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                    <i class="fas fa-utensils"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category"style="color: black;">Data Menu</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <a href="{{ route('menu.terjual.index') }}"
                                    class="card card-stats card-round text-decoration-none">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                                    <i class="fas fa-cash-register"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category"style="color: black;">Data Menu Terjual</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <a href="{{ route('inventaris.index') }}"
                                    class="card card-stats card-round text-decoration-none">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                                    <i class="fas fa-box-open"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category"style="color: black;">Data Inventaris</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @if (auth()->user()->hasRole('Admin'))
                                <div class="col-sm-6 col-md-3">
                                    <a href="{{ route('laporan.index') }}"
                                        class="card card-stats card-round text-decoration-none">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                        <i class="fa-solid fa-file"></i>
                                                    </div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category"style="color: black;">Laporan Bahan Baku</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                            
                        </div>
                    </div>

                    {{-- end button --}}
                    {{-- Menu Kosong --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title mb-0"style="color: aliceblue;">Unavailable Menu Karena Bahan Kosong</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="basic-datatables"
                                            class="table table-bordered table-striped table-hover">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Kode Menu</th>
                                                    <th>Nama Menu</th>
                                                    <th>Komposisi Menu</th>
                                                    <th>Status</th>
                                                    @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Admin'))
                                                        <th>Aksi</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($menu as $item)
                                                    @php
                                                        $bahanHabis = $item->bahans->where('sisa_stok', '<=', 0);
                                                    @endphp
                                                    @if ($bahanHabis->isNotEmpty())
                                                        {{-- Hanya tampilkan jika ada bahan habis --}}
                                                        <tr>
                                                            <td>{{ $item->kode_menu }}</td>
                                                            <td>{{ $item->nama_menu }}</td>
                                                            <td>{{ implode(', ', $item->bahans->pluck('nama_bahan')->toArray()) }}
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-danger p-2">
                                                                    Bahan Habis:
                                                                    {{ $bahanHabis->pluck('nama_bahan')->implode(', ') }}
                                                                </span>
                                                            </td>

                                                            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Admin'))
                                                                <td class="text-center">
                                                                    <button class="btn btn-warning btn-sm mb-1"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editmenuModal{{ $item->id }}">Edit</button>
                                                                    <form action="{{ route('menu.destroy', $item->id) }}"
                                                                        method="POST" class="d-inline"
                                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-danger btn-sm btn-delete btn-delete">Hapus</button>
                                                                    </form>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endsection
