@extends('layouts.master')

@section('title', 'Stok Proses')

@section('stokProses')
    <div class="container mx-5">
        <div class="card shadow">
            <div class="card-body mx-5 my-5">
                <h1 class="mb-4 ">Data Stok Proses</h1>
                @if ($stokProsesList->isEmpty())
                    <div class="alert alert-warning text-center">
                        Maaf, tidak ada data di tanggal ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Kode Bahan</th>
                                    <th>Nama Bahan</th>
                                    <th>Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stokProsesList as $item)
                                    <tr>
                                        <td>{{ $item->bahanProses->kode_bahan ?? '-' }}</td>
                                        <td>{{ $item->bahanProses->nama_bahan ?? '-' }}</td>
                                        <td>{{ number_format($item->stok_hasil) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SweetAlert --}}
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        @if (session('success'))
            swal("Berhasil!", "{{ session('success') }}", "success");
        @endif
    </script>

    {{-- Responsive Table Styling --}}
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
