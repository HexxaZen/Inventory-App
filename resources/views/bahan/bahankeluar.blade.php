@extends('layouts.master')

@section('title', 'Daftar Bahan Keluar')

@section('BahanKeluar')
<div class="container ">
    <div class="row">
        <div class="col">
            <h1 class="mb-4 my-5 mx-5 text-center text-md-start">Daftar Bahan Keluar</h1>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
{{-- sort by --}}
<div class="mb-3 px-3">
    <label for="sortBahan" class="form-label">Sort By:</label>
    <select class="form-control w-25" id="sortBahan">
        <option value="all">Semua</option>
        <option value="BBAR">Bahan Baku Bar</option>
        <option value="BBKTC">Bahan Baku Kitchen</option>
    </select>
</div>
{{-- sort by end --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal Keluar</th>
                            <th>Kode Bahan</th>
                            <th>Nama Bahan</th>
                            <th>Jumlah Keluar</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataKeluar as $item)
                        <tr>
                            <td>{{ $item['tanggal_keluar'] }}</td>
                            <td>{{ $item['kode_bahan'] }}</td>
                            <td>{{ $item['nama_bahan'] }}</td>
                            <td>{{ $item['jumlah_keluar'] }}</td>
                            <td>{{ $item['satuan'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center fw-bold text-danger">Tidak ada data bahan keluar untuk hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    @if(session('success'))
        swal("Berhasil!", "{{ session('success') }}", "success");
    @endif

</script>

<style>
    @media (max-width: 768px) {
        .table { font-size: 0.85rem; }
    }
    @media (max-width: 576px) {
        .table { font-size: 0.75rem; }
    }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
</style>
@endsection
