@extends('layouts.master')
@section('title', 'Daftar Inventaris')
@section('inventaris')
    <div class="container py-4"> {{-- Added py-4 for vertical padding --}}
        <h1 class="mb-4 text-center">Daftar Inventaris</h1> {{-- Centered title --}}

        <div class="d-flex justify-content-end mb-3"> {{-- Flexbox for button alignment --}}
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventarisModal">
                <i class="fa fa-plus"></i> Tambah Inventaris
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert"> {{-- Added dismissible alert --}}
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Filter and Search Section --}}
        <div class="row mb-3 g-3"> {{-- Bootstrap row with gutter --}}
            <div class="col-md-6 col-lg-4"> {{-- Column for Sort By --}}
                <label for="sortInventaris" class="form-label">Urutkan Berdasarkan:</label>
                <select class="form-select" id="sortInventaris"> {{-- Used form-select for better styling --}}
                    <option value="all">Semua</option>
                    <option value="INVB">Inventaris Bar</option>
                    <option value="INVK">Inventaris Kitchen</option>
                    <option value="INVO">Inventaris Operasional</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4"> {{-- Column for Search --}}
                <label for="searchInventaris" class="form-label">Cari Nama Inventaris:</label>
                <input type="text" class="form-control" id="searchInventaris" placeholder="Cari berdasarkan nama...">
            </div>
        </div>
        {{-- End Filter and Search Section --}}

        <div class="table-responsive"> {{-- Ensures table is scrollable on small screens --}}
            <table class="table table-bordered table-striped text-center align-middle"> {{-- Added align-middle for vertical alignment --}}
                <thead class="table-dark"> {{-- Darker header for better contrast --}}
                    <tr>
                        <th>Kode Inventaris</th>
                        <th>Nama Inventaris</th>
                        <th>Satuan</th>
                        <th>Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventaris as $item)
                        <tr>
                            <td>
                                {{ $item->kode_inventaris }} <br>
                                @php
                                    $qrCode = DNS2D::getBarcodePNG(route('inventaris.scan', $item->id), 'QRCODE');
                                @endphp
                                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" width="100">
                                <br>
                                <a href="{{ route('inventaris.cetakBarcode', $item->id) }}"
                                    class="btn btn-success btn-sm mt-2">Cetak QR Code</a>
                            </td>
                            <td>{{ $item->nama_inventaris }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>
                                @php
                                    $warna = match ($item->kondisi) {
                                        'Baik' => 'bg-success',
                                        'Rusak Ringan' => 'bg-warning text-dark',
                                        'Rusak Berat' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $warna }} p-2"> {{-- Using Bootstrap badge class --}}
                                    {{ $item->kondisi }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm mb-1" data-bs-toggle="modal"
                                    data-bs-target="#editInventarisModal{{ $item->id }}">Edit</button>
                                <form action="{{ route('inventaris.destroy', $item->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus inventaris ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editInventarisModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="editInventarisModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editInventarisModalLabel{{ $item->id }}">Edit
                                            Inventaris</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('inventaris.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="nama_inventaris{{ $item->id }}" class="form-label">Nama
                                                    Inventaris</label>
                                                <input type="text" class="form-control"
                                                    id="nama_inventaris{{ $item->id }}" name="nama_inventaris"
                                                    value="{{ $item->nama_inventaris }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="jumlah_inventaris{{ $item->id }}" class="form-label">Jumlah
                                                    Inventaris</label>
                                                <input type="number" class="form-control"
                                                    id="jumlah_inventaris{{ $item->id }}" name="jumlah_inventaris"
                                                    value="{{ $item->jumlah_inventaris }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="satuan{{ $item->id }}" class="form-label">Satuan
                                                    Inventaris</label>
                                                <input type="text" class="form-control" id="satuan{{ $item->id }}"
                                                    name="satuan" value="{{ $item->satuan }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="kondisi{{ $item->id }}" class="form-label">Kondisi
                                                    Inventaris</label>
                                                <select class="form-select" id="kondisi{{ $item->id }}" name="kondisi"
                                                    required>
                                                    <option value="Baik"
                                                        {{ $item->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                    <option value="Rusak Ringan"
                                                        {{ $item->kondisi == 'Rusak Ringan' ? 'selected' : '' }}>Rusak
                                                        Ringan</option>
                                                    <option value="Rusak Berat"
                                                        {{ $item->kondisi == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat
                                                    </option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-warning">Update Inventaris</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addInventarisModal" tabindex="-1" aria-labelledby="addInventarisModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInventarisModalLabel">Tambah Inventaris</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('inventaris.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="kode_inventaris" class="form-label">Kode Inventaris</label>
                            <select class="form-select" id="kode_inventaris" name="kode_inventaris" required>
                                @foreach ($kategoris as $kategori)
                                    @if (str_starts_with($kategori->kode_kategori, 'INV'))
                                        @php
                                            $lastInventaris = App\Models\Inventaris::where(
                                                'kode_inventaris',
                                                'like',
                                                "$kategori->kode_kategori%",
                                            )
                                                ->orderBy('kode_inventaris', 'desc')
                                                ->first();
                                            $nextNumber = $lastInventaris
                                                ? ((int) substr($lastInventaris->kode_inventaris, -4)) + 1
                                                : 1;
                                            $kodeInventaris =
                                                $kategori->kode_kategori . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                                        @endphp
                                        <option value="{{ $kodeInventaris }}">{{ $kodeInventaris }} -
                                            {{ $kategori->nama_kategori }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nama_inventaris" class="form-label">Nama Inventaris</label>
                            <input type="text" class="form-control" id="nama_inventaris" name="nama_inventaris"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <select class="form-select" id="satuan" name="satuan" required>
                                <option value="pcs">Pcs</option>
                                <option value="pack">Pack</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kondisi" class="form-label">Kondisi</label>
                            <select class="form-select" id="kondisi" name="kondisi" required>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Inventaris</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sort by functionality
            document.getElementById("sortInventaris").addEventListener("change", function() {
                let sortValue = this.value;
                let url = "{{ route('inventaris.index') }}?sort=" + sortValue;

                fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        // Replace the table body with the sorted data
                        document.querySelector("tbody").innerHTML =
                            new DOMParser().parseFromString(data, "text/html")
                            .querySelector("tbody").innerHTML;
                    })
                    .catch(error => console.error("Error fetching sorted data:", error));
            });

            // Search functionality
            document.getElementById("searchInventaris").addEventListener("keyup", function() {
                let searchValue = this.value.toLowerCase();
                let tableRows = document.querySelectorAll("tbody tr");

                tableRows.forEach(function(row) {
                    // Assuming the 'Nama Inventaris' is in the second <td> (index 1)
                    let inventoryName = row.children[1].textContent.toLowerCase();
                    if (inventoryName.includes(searchValue)) {
                        row.style.display = ""; // Show the row
                    } else {
                        row.style.display = "none"; // Hide the row
                    }
                });
            });
        });

        // SweetAlert for success messages
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                swal("Berhasil!", "{{ session('success') }}", "success");
            @endif
        });
    </script>
@endsection