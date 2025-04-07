@extends('layouts.master')
@section('title', 'Daftar Menu')

@section('menu')
    <div class="container">
        <h1 class="mb-4 mx-5 my-5">Daftar Menu</h1>

        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Admin'))
            <button class="btn btn-primary mb-3 float-end me-3" data-bs-toggle="modal" data-bs-target="#addmenuModal">
                <i class="fa fa-plus"></i> Tambah Menu
            </button>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive px-3">
            <table class="table table-bordered table-striped text-center">
                <thead style="align-content: center;">
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
                        <tr>
                            <td>{{ $item->kode_menu }}</td>
                            <td>{{ $item->nama_menu }}</td>
                            <td>
                                @if ($item->bahans->isEmpty())
                                    <span>Tidak ada bahan</span>
                                @else
                                    <ul>
                                        @foreach ($item->bahans as $bahan)
                                            <li>{{ $bahan->nama_bahan }} - {{ $bahan->pivot->gramasi }} gr</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>

                            <td>
                                @php
                                    $bahanHabis = $item->bahans->where('sisa_stok', '<=', 0);
                                    $warna = $bahanHabis->isNotEmpty() ? 'red' : '#3db641';
                                    $status = $bahanHabis->isNotEmpty()
                                        ? 'Bahan Habis: ' . $bahanHabis->pluck('nama_bahan')->implode(', ')
                                        : 'Menu Tersedia';
                                @endphp
                                <span class="badge"
                                    style="background-color: {{ $warna }}; color: white; font-weight: bold; padding: 7px 15px; border-radius: 10px;">
                                    {{ $status }}
                                </span>
                            </td>
                            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Admin'))
                                <td>
                                    <button class="btn btn-warning btn-sm mb-1" data-bs-toggle="modal"
                                        data-bs-target="#editmenuModal{{ $item->id }}">Edit</button>
                                    <form action="{{ route('menu.destroy', $item->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>

                        <!-- Modal Edit Menu -->
                        <div class="modal fade" id="editmenuModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="editmenuModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Menu</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('menu.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="form-label">Nama Menu</label>
                                                <input type="text" class="form-control" name="nama_menu"
                                                    value="{{ $item->nama_menu }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Komposisi Menu</label>
                                                <select class="ui search selection dropdown multi-select"
                                                    name="bahan_menu[]" multiple>
                                                    @foreach ($bahans as $bahan)
                                                        <option value="{{ $bahan->id }}"
                                                            {{ in_array($bahan->id, $item->bahans->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                            {{ $bahan->nama_bahan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Update Menu</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Menu -->
        <div class="modal fade" id="addmenuModal" tabindex="-1" aria-labelledby="addmenuModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('menu.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="kategori_id" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    @foreach ($kategoris as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->kode_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Menu</label>
                                <input type="text" class="form-control" name="nama_menu" placeholder="Masukkan nama menu"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Komposisi Menu</label>
                                <select class="form-control bahan-menu multi-select" name="bahan_menu[]" multiple
                                    data-placeholder="Pilih komposisi menu" required>
                                    @foreach ($bahans as $bahan)
                                        <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3" id="gramasi-container"></div>
                            <button type="submit" class="btn btn-primary w-100">Tambah Menu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        // alert
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                swal("Berhasil!", "{{ session('success') }}", "success");
            @endif
            $(document).ready(function() {
                // Inisialisasi dropdown multi-select
                $('.multi-select, .bahan-menu').dropdown();

                // Event listener untuk menambahkan input gramasi saat memilih bahan
                $('.bahan-menu').on('change', function() {
                    let selectedOptions = $(this).val() || []; // Ambil nilai terpilih
                    let gramasiContainer = $('#gramasi-container');

                    gramasiContainer.html(''); // Reset container sebelum menambah input baru

                    selectedOptions.forEach(bahanId => {
                        let bahanName = $(this).find(`option[value="${bahanId}"]`).text();

                        let inputGroup = `
                    <div class="mb-3">
                        <label class="form-label">Gramasi untuk ${bahanName}</label>
                        <input type="number" class="form-control" name="gramasi[${bahanId}]" required>
                    </div>
                `;
                        gramasiContainer.append(inputGroup);
                    });
                });
            });
        });
    </script>

@endsection
