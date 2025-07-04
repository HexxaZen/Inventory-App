@extends('layouts.master')
@section('title', 'Daftar Bahan')
@section('daftarbahan')
    <div class="container">
        <h1 class="mb-4 mx-5 my-5">Data Bahan Baku</h1>
        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tombol Tambah Bahan Baku --}}
        <div class="row mb-3 gx-2 justify-content-end">
            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headbar'))
                <div class="col-auto mb-2"> {{-- Added mb-2 for spacing if buttons wrap --}}
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addBahanBarModal">
                        <i class="fa fa-plus"></i> Tambah Bahan Baku Bar
                    </button>
                </div>
            @endif
        
            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headkitchen'))
                <div class="col-auto mb-2"> {{-- Added mb-2 for spacing if buttons wrap --}}
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addBahanKitchenModal">
                        <i class="fa fa-plus"></i> Tambah Bahan Baku Kitchen
                    </button>
                </div>
            @endif
        </div>        
        {{-- Form Filter dan Pencarian --}}
        <form method="GET" action="{{ route('bahan.index') }}" id="filterForm">
            <div class="row g-3 align-items-end mb-4 m-2"> {{-- Added mb-4 for spacing below the form --}}
                @if (auth()->user()->hasRole('Admin'))
                    <div class="col-md-4 col-lg-3"> {{-- Adjusted column sizing for better responsiveness --}}
                        <label for="sortBahan" class="form-label">Sortir Kategori Bahan</label> {{-- Removed mx-3 --}}
                        <select class="form-select" name="kategori_bahan" id="sortBahan" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua</option>
                            <option value="BBAR" {{ request('kategori_bahan') == 'BBAR' ? 'selected' : '' }}>Bahan Baku Bar</option>
                            <option value="BBKTC" {{ request('kategori_bahan') == 'BBKTC' ? 'selected' : '' }}>Bahan Baku Kitchen</option>
                        </select>
                    </div>
                @endif
        
                <div class="col-md-5 col-lg-4"> {{-- Adjusted column sizing for better responsiveness --}}
                    <label for="search" class="form-label">Cari Nama Bahan</label> {{-- Removed mx-3 --}}
                    <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan nama bahan..." value="{{ request('search') }}">
                </div>
        
                <div class="col-md-3 col-lg-2 d-grid"> {{-- Used d-grid for full width button on small screens and adjusted column sizing --}}
                    <button type="submit" class="btn btn-secondary">
                        <i class="fa fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
                {{-- tabel --}}
        @php
            use Illuminate\Support\Str;
        @endphp
        <div class="table-responsive px-3" style="overflow-x: auto;margin-top:20px;">
            <table class="table table-bordered table-striped text-center">
                <thead style="align-content: center;">
                    <tr>
                        <th>Kode Bahan</th>
                        <th>Nama Bahan</th>
                        <th>Jenis</th>
                        {{-- <th>Kategori</th> --}}
                        <th>Sisa Stok</th>
                        <th>Batas Minimum</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headbar') || auth()->user()->hasRole('Headkitchen'))
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bahan->sortBy(function ($item) {
            if ($item->sisa_stok <= 0) {
                return 0;
            } elseif ($item->sisa_stok <= $item->batas_minimum) {
                return 1;
            } else {
                return 2;
            }
        }) as $item)
                        @php
                            $user = Auth::user();
                            $canSee = false;

                            if ($user->hasRole('Admin')) {
                                $canSee = true;
                            } elseif (
                                ($user->hasRole('Headbar') || $user->hasRole('Bar')) &&
                                Str::startsWith($item->kode_bahan, 'BBAR')
                            ) {
                                $canSee = true;
                            } elseif (
                                ($user->hasRole('Headkitchen') || $user->hasRole('Kitchen')) &&
                                Str::startsWith($item->kode_bahan, 'BBKTC')
                            ) {
                                $canSee = true;
                            }
                        @endphp

                        @if ($canSee)
                            <tr>
                                <td>{{ $item->kode_bahan }}</td>
                                <td>{{ $item->nama_bahan }}</td>
                                <td>{{ $item->jenis_bahan }}</td>
                                {{-- <td>{{ $item->kategori_bahan }}</td> --}}
                                <td>{{ $item->sisa_stok }}</td>
                                <td>{{ $item->batas_minimum }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td>
                                    @if ($item->sisa_stok > $item->batas_minimum)
                                        <span class="badge bg-success">AMAN</span>
                                    @elseif($item->sisa_stok <= $item->batas_minimum && $item->sisa_stok > 0)
                                        <span class="badge bg-warning">MENIPIS</span>
                                    @else
                                        <span class="badge bg-danger">HABIS</span>
                                    @endif
                                </td>
                                @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headbar') || auth()->user()->hasRole('Headkitchen'))
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editBahanModal{{ $item->id }}">Edit</button>
                                        <form action="{{ route('bahan.destroy', $item->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btn-delete">Hapus</button>
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
    <!-- Modal Edit Bahan -->
    @foreach ($bahan as $item)
        <div class="modal fade" id="editBahanModal{{ $item->id }}" tabindex="-1"
            aria-labelledby="editBahanModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBahanModalLabel{{ $item->id }}">Edit Bahan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('bahan.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="nama_bahan{{ $item->id }}" class="form-label">Nama
                                    Bahan</label>
                                <input type="text" class="form-control w-100" id="nama_bahan{{ $item->id }}"
                                    name="nama_bahan" value="{{ $item->nama_bahan }}" required>
                            </div>
                            @if (auth()->user()->hasRole('Headkitchen'))
                                <div class="mb-3">
                                    <label for="jenis_bahan{{ $item->id }}" class="form-label">Jenis Bahan</label>
                                    <select name="jenis_bahan" class="form-select w-100"
                                        id="jenis_bahan{{ $item->id }}" required>
                                        <option value="pasar" {{ $item->jenis_bahan == 'pasar' ? 'selected' : '' }}>Pasar
                                        </option>
                                        <option value="luar" {{ $item->jenis_bahan == 'luar' ? 'selected' : '' }}>
                                            Luar
                                        </option>
                                        <option value="frozen" {{ $item->jenis_bahan == 'frozen' ? 'selected' : '' }}>
                                            Frozen
                                        </option>
                                    </select>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="batas_minimum{{ $item->id }}" class="form-label">Batas
                                    Minimum</label>
                                <input type="number" class="form-control w-100" id="batas_minimum{{ $item->id }}"
                                    name="batas_minimum" value="{{ $item->batas_minimum }}">
                            </div>
                            @if (auth()->user()->hasRole('Headkitchen') || auth()->user()->hasRole('Headbar'))
                            <div class="mb-3">
                                <label for="sisa_stok{{ $item->id }}" class="form-label">Sisa Stok</label>
                                <input type="number" class="form-control w-100" id="sisa_stok{{ $item->id }}"
                                    name="sisa_stok" value="{{ $item->sisa_stok }}">
                            </div>
                            @endif
                            <div class="mb-3">
                                <label for="satuan{{ $item->id }}" class="form-label">Satuan
                                    Bahan</label>
                                <select class="form-select w-100" id="satuan{{ $item->id }}" name="satuan"
                                    required>
                                    <option value="pack" {{ $item->satuan == 'pack' ? 'selected' : '' }}>
                                        Pack</option>
                                    <option value="pcs" {{ $item->satuan == 'pcs' ? 'selected' : '' }}>
                                        Pcs</option>
                                    <option value="gram" {{ $item->satuan == 'gram' ? 'selected' : '' }}>
                                        Gram/ml</option>
                                    <option value="buah" {{ $item->satuan == 'buah' ? 'selected' : '' }}>
                                        Buah</option>
                                    <option value="kg" {{ $item->satuan == 'kg' ? 'selected' : '' }}>
                                        Kg</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- end --}}
    <!-- Modal Tambah Bahan Bar -->
    <div class="modal fade" id="addBahanBarModal" tabindex="-1" aria-labelledby="addBahanBarModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBahanBarModalLabel">Tambah Bahan Bar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('bahan.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="kode_bahan" class="form-label">Kode Bahan</label>
                            <select class="form-control" id="kode_bahan" name="kode_bahan" required>
                                @foreach ($kategoris as $kategori)
                                    @if (str_starts_with($kategori->kode_kategori, 'BB'))
                                        @php
                                            $lastBahan = App\Models\Bahan::where(
                                                'kode_bahan',
                                                'like',
                                                "$kategori->kode_kategori%",
                                            )
                                                ->orderBy('kode_bahan', 'desc')
                                                ->first();
                                            $nextNumber = $lastBahan
                                                ? ((int) substr($lastBahan->kode_bahan, -4)) + 1
                                                : 1;
                                            $kodeBahan =
                                                $kategori->kode_kategori . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                                        @endphp
                                        <option value="{{ $kodeBahan }}">{{ $kodeBahan }} -
                                            {{ $kategori->nama_kategori }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nama_bahan" class="form-label">Nama Bahan</label>
                            <input type="text" class="form-control" id="nama_bahan" name="nama_bahan" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipe" class="form-label">Tipe Bahan</label>
                            <select name="tipe" class="form-control" required readonly>
                                {{-- <option value="process">Process</option> --}}
                                <option value="non-process">Non-Process</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jenis_bahan" class="form-label">Jenis Bahan</label>
                            <select class="form-control" id="jenis_bahan" name="jenis_bahan" required>
                                <option value="" disabled selected hidden>Pilih Jenis Bahan</option>
                                <option value="kopi">Kopi</option>
                                <option value="milk">Milk</option>
                                <option value="syrup">Syrup</option>
                                <option value="poultry">Poultry</option>
                                <option value="powder">Powder</option>
                                <option value="tehsprite">Teh & Sprite</option>
                                <option value="topping">Topping</option>
                                <option value="buah">Buah</option>
                                <option value="equip">Equipment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kategori_bahan" class="form-label">Kategori bahan</label>
                            <select class="form-control" id="kategori_bahan" name="kategori_bahan" required readonly>
                                @foreach ($kategoris as $kategori)
                                    @if (str_starts_with($kategori->kode_kategori, 'BBAR'))
                                        <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan Bahan</label>
                            <select class="form-control" id="satuan" name="satuan" required>
                                <option value="pack">Pack</option>
                                <option value="pcs">Pcs</option>
                                <option value="gram">Gram/ml</option>
                                <option value="buah">Buah</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- end --}}
    {{-- Modal Tambah Bahan Kitchen --}}
    <div class="modal fade" id="addBahanKitchenModal" tabindex="-1" aria-labelledby="addBahanKitchenModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBahanKitchenModalLabel">Tambah Bahan Kitchen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('bahan.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="kode_bahan" class="form-label">Kode Bahan</label>
                            <select class="form-control" id="kode_bahan" name="kode_bahan" required>
                                @foreach ($kategoris as $kategori)
                                    @if (str_starts_with($kategori->kode_kategori, 'BBKTC'))
                                        @php
                                            $lastBahan = App\Models\Bahan::where(
                                                'kode_bahan',
                                                'like',
                                                "$kategori->kode_kategori%",
                                            )
                                                ->orderBy('kode_bahan', 'desc')
                                                ->first();
                                            $nextNumber = $lastBahan
                                                ? ((int) substr($lastBahan->kode_bahan, -4)) + 1
                                                : 1;
                                            $kodeBahan =
                                                $kategori->kode_kategori . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                                        @endphp
                                        <option value="{{ $kodeBahan }}">{{ $kodeBahan }} -
                                            {{ $kategori->nama_kategori }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nama_bahan" class="form-label">Nama Bahan</label>
                            <input type="text" class="form-control" id="nama_bahan" name="nama_bahan" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipe" class="form-label">Tipe Bahan</label>
                            <select name="tipe" class="form-control" required readonly>
                                <option value="non-process">Non-Process</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_bahan" class="form-label">Jenis Bahan</label>
                            <select class="form-control" id="jenis_bahan" name="jenis_bahan" required>
                                <option value="" disabled selected hidden>Pilih Jenis Bahan</option>
                                <option value="pasar">Pasar</option>
                                <option value="luar">Luar</option>
                                <option value="frozen">Frozen</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kategori_bahan" class="form-label">Kategori bahan</label>
                            <select class="form-control" id="kategori_bahan" name="kategori_bahan" required readonly>
                                @foreach ($kategoris as $kategori)
                                    @if (str_starts_with($kategori->kode_kategori, 'BBKTC'))
                                        <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan Bahan</label>
                            <select class="form-control" id="satuan" name="satuan" required>
                                <option value="kg">Kg</option>
                                <option value="gram">Gram</option>
                                <option value="liter">Liter</option>
                                <option value="ml">Ml</option>
                                <option value="pack">Pack</option>
                                <option value="pcs">Pcs</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- end --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        @if (session('success'))
            swal("Berhasil!", "{{ session('success') }}", "success");
        @endif

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("sortBahan").addEventListener("change", function() {
                let sortValue = this.value;
                let url = "{{ route('bahan.index') }}?sort=" + sortValue;
                fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        document.querySelector("tbody").innerHTML =
                            new DOMParser().parseFromString(data, "text/html")
                            .querySelector("tbody").innerHTML;
                    })
                    .catch(error => console.error("Error fetching sorted data:", error));
            });
        });
    </script>
    <style>
        @media (max-width: 768px) {
            .table {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .table {
                font-size: 0.75rem;
            }

            .btn {
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
            }
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
@endsection
