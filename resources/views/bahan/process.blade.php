@extends('layouts.master')
@section('title', 'Proses Bahan')

@section('bahanProcess')
    <div class="container">
        <h1 class="mb-4 mx-5 my-5">Komposisi Bahan Proses</h1>
        {{-- TAMBAH BAHAN PROSES BAR --}}
        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headbar') || auth()->user()->hasRole('Bar'))
            <button class="btn btn-primary mb-3 float-end me-3" data-bs-toggle="modal" data-bs-target="#addProcessModalBAR">
                <i class="fa fa-plus"></i> Tambah Bahan Proses BAR
            </button>
        @endif
        {{-- END BAR --}}
        {{-- TAMBAH BAHAN PROSES KITCHEN --}}
        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headkitchen') || auth()->user()->hasRole('Kitchen'))
            <button class="btn btn-primary mb-3 float-end me-3" data-bs-toggle="modal" data-bs-target="#addProcessModalKTC">
                <i class="fa fa-plus"></i> Tambah Bahan Proses KITCHEN
            </button>
        @endif
        {{-- END BAR --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive px-3">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>Kode Bahan</th>
                        <th>Kategori Bahan</th>
                        <th>Nama Bahan Proses</th>
                        <th>Sisa Stok</th>
                        <th>Komposisi Bahan</th>
                        <th>Batas Minimum</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Headbar') || auth()->user()->hasRole('Headkitchen'))
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bahanProcesses as $item)
                        <tr>
                            <td>{{ $item->kode_bahan }}</td>
                            <td>{{ $item->kategori_bahan }}</td>
                            <td>{{ $item->nama_bahan }}</td>
                            <td>{{ $item->sisa_stok }}</td>
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
                                    <button class="btn btn-warning btn-sm mb-1" data-bs-toggle="modal"
                                        data-bs-target="#editProcessModal{{ $item->id }}">Edit</button>
                                    <!-- Modal Edit Proses -->
                                    <div class="modal fade" id="editProcessModal{{ $item->id }}" tabindex="-1"
                                        aria-labelledby="editProcessModalLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('bahan.process.update', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Proses Bahan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="nama_bahan" class="form-label">Nama Proses</label>
                                                            <input type="text" class="form-control" name="nama_bahan"
                                                                value="{{ $item->nama_bahan }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Komposisi Bahan</label>
                                                            <select class="form-control multi-select" name="bahan_proses[]"
                                                                multiple required>
                                                                @foreach ($bahans as $bahan)
                                                                    <option value="{{ $bahan->id }}"
                                                                        {{ in_array($bahan->id, $item->bahans->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                                        {{ $bahan->nama_bahan }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        @foreach ($bahans as $bahan)
                                                            @php
                                                                $pivot = $item->bahans->firstWhere('id', $bahan->id)
                                                                    ?->pivot;
                                                            @endphp
                                                            <div class="mb-3">
                                                                <label class="form-label">Gramasi untuk
                                                                    {{ $bahan->nama_bahan }}</label>
                                                                <input type="number" class="form-control" name="gramasi[]"
                                                                    value="{{ $pivot ? $pivot->gramasi : '' }}"
                                                                    {{ in_array($bahan->id, $item->bahans->pluck('id')->toArray()) ? '' : 'disabled' }}>
                                                            </div>
                                                        @endforeach
                                                        <div class="mb-3">
                                                            <label for="batas_minimum" class="form-label">Batas
                                                                Minimum</label>
                                                            <input type="number" class="form-control" id="batas_minimum"
                                                                name="batas_minimum" value="{{ $item->batas_minimum }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="satuan" class="form-label">Satuan</label>
                                                            <select class="form-select" name="satuan" required>
                                                                <option value="gram"
                                                                    {{ $item->satuan == 'gram' ? 'selected' : '' }}>
                                                                    Gram</option>
                                                                <option value="ml"
                                                                    {{ $item->satuan == 'ml' ? 'selected' : '' }}>ML
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary w-100" type="submit">Update
                                                            Proses</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('bahan.process.destroy', $item->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus proses bahan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Proses BAR -->
        <div class="modal fade" id="addProcessModalBAR" tabindex="-1" aria-labelledby="addProcessModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('bahan.process.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Proses Bahan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="kode_bahan" class="form-label">Kode Bahan</label>
                                <select class="form-control" id="kode_bahan_bar" name="kode_bahan" required>
                                    @foreach ($kategoris as $kategori)
                                        @if (str_starts_with($kategori->kode_kategori, 'BBAR'))
                                            @php
                                                // Generate angka random 4 digit
                                                $randomNumber = random_int(1000, 9999);

                                                //  kode kategori dengan angka random
                                                $kodeBahan = $kategori->kode_kategori . $randomNumber;
                                            @endphp
                                            <option value="{{ $kodeBahan }}">{{ $kodeBahan }} -
                                                {{ $kategori->nama_kategori }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="kategori_bahan" class="form-label">Kategori bahan</label>
                                <select class="form-control" id="kategori_bahan" name="kategori_bahan" required>
                                    @foreach ($kategoris as $kategori)
                                        @if (str_starts_with($kategori->kode_kategori, 'BBAR'))
                                            <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nama_bahan" class="form-label">Nama Proses</label>
                                <input type="text" class="form-control" name="nama_bahan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Komposisi Bahan</label>
                                <select class="form-control bahan-process multi-select" name="bahan_process[]" multiple
                                    required>
                                    @foreach ($bahans as $bahan)
                                        @if (str_starts_with($bahan->kode_bahan, 'BBAR'))
                                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 gramasi-container" data-target="bar"></div>
                            <div class="mb-3">
                                <label for="batas_minimum" class="form-label">Batas Minimum</label>
                                <input type="number" class="form-control" id="batas_minimum" name="batas_minimum"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select class="form-select" name="satuan" required>
                                    <option value="gram">Gram</option>
                                    <option value="ml">ML</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary w-100" type="submit">Proses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Tambah Proses KITCHEN -->
        <div class="modal fade" id="addProcessModalKTC" tabindex="-1" aria-labelledby="addProcessModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('bahan.process.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Proses Bahan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="kode_bahan" class="form-label">Kode Bahan</label>
                                <select class="form-control" id="kode_bahan_kitchen" name="kode_bahan" required>
                                    @foreach ($kategoris as $kategori)
                                        @if (str_starts_with($kategori->kode_kategori, 'BBKTC'))
                                            @php
                                                // Generate angka random 4 digit
                                                $randomNumber = random_int(1000, 9999);

                                                //  kode kategori dengan angka random
                                                $kodeBahan = $kategori->kode_kategori . $randomNumber;
                                            @endphp
                                            <option value="{{ $kodeBahan }}">{{ $kodeBahan }} -
                                                {{ $kategori->nama_kategori }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="kategori_bahan" class="form-label">Kategori bahan</label>
                                <select class="form-control" id="kategori_bahan" name="kategori_bahan" required>
                                    @foreach ($kategoris as $kategori)
                                        @if (str_starts_with($kategori->kode_kategori, 'BBKTC'))
                                            <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nama_bahan" class="form-label">Nama Proses</label>
                                <input type="text" class="form-control" name="nama_bahan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Komposisi Bahan</label>
                                <select class="form-control bahan-process multi-select" name="bahan_process[]" multiple
                                    required>
                                    @foreach ($bahans as $bahan)
                                        @if (str_starts_with($bahan->kode_bahan, 'BBKTC'))
                                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3" id="gramasi-container" data-target="kitchen"></div>
                            <div class="mb-3">
                                <label for="batas_minimum" class="form-label">Batas Minimum</label>
                                <input type="number" class="form-control" id="batas_minimum" name="batas_minimum"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select class="form-select" name="satuan" required>
                                    <option value="gram">Gram</option>
                                    <option value="ml">ML</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary w-100" type="submit">Proses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                swal("Berhasil!", "{{ session('success') }}", "success");
            @endif

            // Inisialisasi setelah semua elemen dimuat
            $('.multi-select, .bahan-process').each(function() {
                if ($(this).dropdown) {
                    $(this).dropdown(); // hanya jika pakai Semantic UI atau plugin sejenis
                }
            });

            // Handler untuk semua elemen dengan class 'bahan-process'
            $('.bahan-process').on('change', function() {
                const $modalBody = $(this).closest('.modal-body');
                const targetContainer = $modalBody.find('.gramasi-container, #gramasi-container');
                const selectedOptions = $(this).val() || [];

                targetContainer.html(''); // kosongkan container

                selectedOptions.forEach(bahanId => {
                    const bahanName = $(this).find(`option[value="${bahanId}"]`).text();

                    const inputGroup = `
                        <div class="mb-3">
                            <label class="form-label">Gramasi untuk ${bahanName}</label>
                            <input type="number" class="form-control" name="gramasi[${bahanId}]" required>
                        </div>
                    `;

                    targetContainer.append(inputGroup);
                });
            });
        });
    </script>

@endsection
