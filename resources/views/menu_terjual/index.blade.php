@extends('layouts.master')

@section('title', 'Daftar Menu Terjual')

@section('MenuTerjual')
    <div class="container">
        <h1 class="mb-4 px-3">Daftar Menu Terjual</h1>
        <button class="btn btn-primary mb-3 float-end" data-bs-toggle="modal" data-bs-target="#addMenuTerjualModal"
            style="margin-right: 30px;">
            <i class="fa fa-plus"></i> Tambah Menu Terjual
        </button>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive px-3">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>Nama Menu</th>
                        <th>Jumlah Terjual</th>
                        <th>Komposisi Menu</th>
                        <th>Total Komposisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menuTerjual as $item)
                        <tr>
                            <td>{{ $item->menu->nama_menu ?? 'Tidak Diketahui' }}</td>
                            <td>{{ $item->jumlah_terjual }}</td>
                            <td>
                                <ul>
                                    @foreach ($item->menu->bahans ?? [] as $bahan)
                                        <li>{{ $bahan->nama_bahan ?? 'Tidak Diketahui' }} - 
                                            {{ $bahan->pivot->gramasi ?? 0 }} {{$bahan->satuan}}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    @foreach ($item->menu->bahans ?? [] as $bahan)
                                        @php
                                            $hasil_seharusnya = $item->jumlah_terjual * ($bahan->pivot->gramasi ?? 0);
                                        @endphp
                                        <li>{{ $bahan->nama_bahan }}: {{ $hasil_seharusnya }} {{$bahan->satuan}}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"style="color:white;" 
                                    data-bs-target="#editMenuTerjualModal{{ $item->id }}">
                                    Edit
                                </button>
                                <form action="{{ route('menu.terjual.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Menu Terjual -->
                        <div class="modal fade" id="editMenuTerjualModal{{ $item->id }}" tabindex="-1" 
                            aria-labelledby="editMenuTerjualLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editMenuTerjualLabel{{ $item->id }}">Edit Jumlah Terjual</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('menu.terjual.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Terjual</label>
                                                <input type="number" class="form-control" name="jumlah_terjual" 
                                                    value="{{ $item->jumlah_terjual }}" min="1" required>
                                            </div>
                                            <button type="submit" class="btn btn-success">Update</button>
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

    <!-- Modal Tambah Menu Terjual -->
    <div class="modal fade" id="addMenuTerjualModal" tabindex="-1" aria-labelledby="addMenuTerjualLabel"
        aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMenuTerjualLabel">Tambah Menu Terjual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('menu.terjual.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Menu dan Jumlah Terjual</label>
                        <div class="list-group">
                            @foreach ($menus as $menu)
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-3">{{ $menu->nama_menu }}</span>
                                    <input type="hidden" name="menu_id[]" value="{{ $menu->id }}">
                                    <input type="number" class="form-control w-25 ms-auto" name="jumlah_terjual[]"  placeholder="Jumlah Terjual" required>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            swal("Berhasil!", "{{ session('success') }}", "success");
        @endif
    });
</script>
@endsection