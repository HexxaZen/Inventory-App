@extends('layouts.master')

@section('title', 'Daftar Bahan Akhir')

@section('BahanAkhir')
    <div class="container mt-4">
        <h2 class="mb-4 text-center">Data Bahan Akhir</h2>

        <!-- Form Input Tanggal -->
        <form action="{{ route('bahan.akhir.tanggal') }}" method="POST" class="mb-4">
            @csrf
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <label for="tanggal_input">Tanggal Input (Otomatis):</label>
                    <input type="date" id="tanggal_input" name="tanggal_input" class="form-control"
                        value="{{ now()->format('Y-m-d') }}" readonly>
                </div>
            </div>
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Input Data Bahan</button>
            </div>
        </form>

        <!-- Form Tampilkan Data -->
        <form action="{{ route('bahan.akhir.tampilkan') }}" method="GET" class="mb-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <label for="tanggal_tampil">Pilih Tanggal:</label>
                    <input type="date" id="tanggal_tampil" name="tanggal" class="form-control"
                        value="{{ request('tanggal', now()->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Tampilkan Data</button>
            </div>
        </form>

        @isset($tanggal)
            <h3 class="mt-4 text-center">Data untuk Tanggal: {{ $tanggal }}</h3>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3 text-center" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('bahan.akhir.update') }}" method="POST">
                @csrf

                {{-- Bahan Non-Proses --}}
                <h4 class="mt-4 mx-5 my-5">Bahan Non-Proses</h4>
                <div class="table-responsive px-3 my-5">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Kategori Bahan</th>
                                <th>Stok Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataNonProses as $item)
                                <tr>
                                    <td><input type="text" class="form-control-plaintext text-center"
                                            value="{{ $item->kode_bahan }}" readonly></td>
                                    <td><input type="text" class="form-control-plaintext text-center"
                                            value="{{ $item->nama_bahan }}" readonly></td>
                                    <td><input type="text" class="form-control-plaintext text-center"
                                            value="{{ $item->kategori_bahan }}" readonly></td>
                                    <td>
                                        <input type="number" class="form-control stok-input text-center"
                                            name="sisa_stok[{{ $item->id }}]"
                                            value="{{ old('sisa_stok.' . $item->id, $item->sisa_stok) }}" min="0"
                                            required>
                                        @error('sisa_stok.' . $item->id)
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data bahan non-proses.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Bahan Proses --}}
                <h4 class="mt-5 mx-5 my-5">Bahan Proses</h4>
                <div class="table-responsive px-3 my-5">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Kategori Bahan</th>
                                <th>Stok Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataProses as $item)
                                @php $inputName = 'p_' . $item->id; @endphp
                                <tr>
                                    <td><input type="text" class="form-control-plaintext text-center"
                                            value="{{ $item->kode_bahan }}" readonly></td>
                                    <td><input type="text" class="form-control-plaintext text-center"
                                            value="{{ $item->nama_bahan }}" readonly></td>
                                    <td><input type="text" class="form-control-plaintext text-center"
                                            value="{{ $item->kategori_bahan }}" readonly></td>
                                    <td>
                                        <input type="number" class="form-control stok-input text-center"
                                            name="sisa_stok[{{ $inputName }}]"
                                            value="{{ old('sisa_stok.' . $inputName, $item->sisa_stok) }}" min="0"
                                            required>
                                        @error('sisa_stok.' . $inputName)
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data bahan proses.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </div>
            </form>
        @endisset
    </div>

    <!-- SweetAlert dan validasi -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                swal("Berhasil!", "{{ session('success') }}", "success");
            @endif

            document.querySelectorAll('.stok-input').forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value < 0) {
                        this.value = 0;
                    }
                });
            });
        });
    </script>
@endsection
