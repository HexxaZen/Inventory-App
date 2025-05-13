@extends('layouts.master')
@section('title', 'Daftar Inventaris')
@section('inventaris')
    <div class="container">
        <h1 class="mb-4 mx-5 my-5">Daftar Inventaris</h1>
        <button class="btn btn-primary mb-3 float-end me-3" data-bs-toggle="modal" data-bs-target="#addInventarisModal">
            <i class="fa fa-plus"></i> Tambah Inventaris
        </button>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        {{-- sort by --}}
        <div class="mb-3 px-3">
            <label for="sortInventaris" class="form-label">Sort By:</label>
            <select class="form-control w-25" id="sortInventaris">
                <option value="all">Semua</option>
                <option value="INVB">Inventaris Bar</option>
                <option value="INVK">Inventaris Kitchen</option>
                <option value="INVO">Inventaris Operasional</option>
            </select>
        </div>
        {{-- sort by end --}}
        <div class="table-responsive px-3">
            <table class="table table-bordered table-striped text-center">
                <thead style="align-content: center;">
                    <tr>
                        <th>Kode Inventaris</th>
                        <th>Nama Inventaris</th>
                        <th>Jumlah</th>
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
                            <td>{{ $item->jumlah_inventaris }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>
                                @php
                                    $warna = match ($item->kondisi) {
                                        'Baik' => 'green',
                                        'Rusak Ringan' => 'yellow',
                                        'Rusak Berat' => 'red',
                                        default => 'gray',
                                    };
                                @endphp
                                <span
                                    style="
                                background-color: {{ $warna }};
                                font-weight: bold;
                                color: white;
                                padding: 7px 15px;
                                border-radius: 10px">
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
                                    <button type="submit" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Inventaris -->
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
                                                <select class="form-control" id="kondisi{{ $item->id }}" name="kondisi"
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

    <!-- Modal Tambah Inventaris -->
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
                            <select class="form-control" id="kode_inventaris" name="kode_inventaris" required>
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
                            <label for="jumlah_inventaris" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah_inventaris" name="jumlah_inventaris"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <select class="form-control" id="satuan" name="satuan" required>
                                <option value="pcs">Pcs</option>
                                <option value="pack">Pack</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kondisi" class="form-label">Kondisi</label>
                            <select class="form-control" id="kondisi" name="kondisi" required>
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
            document.getElementById("sortInventaris").addEventListener("change", function() {
                let sortValue = this.value;
                let url = "{{ route('inventaris.index') }}?sort=" + sortValue;

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
        // alert
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                swal("Berhasil!", "{{ session('success') }}", "success");
            @endif
        });
    </script>
@endsection
