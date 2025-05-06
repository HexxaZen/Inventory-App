@extends('layouts.master')
@section('title', 'Pemantauan Bahan Baku')
@section('pemantauan')
    <div class="container mt-5">
        <div class="card shadow-sm mx-5 my-5">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="card-title mb-0">Pemantauan Bahan Baku</h4>
            </div>
            <div class="card-body">
                {{-- Form Filter Tanggal --}}
                <form method="GET" action="{{ route('laporan.pemantauan') }}">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="dari_tanggal" class="form-label">Dari Tanggal:</label>
                            <input type="date" class="form-control" name="dari_tanggal" value="{{ request('dari_tanggal') }}" required>
                        </div>
                        <div class="col-md-5">
                            <label for="sampai_tanggal" class="form-label">Sampai Tanggal:</label>
                            <input type="date" class="form-control" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">Tampilkan</button>
                        </div>
                    </div>
                </form>

                {{-- Tabel dan Form Download PDF --}}
                @if (request('dari_tanggal') && request('sampai_tanggal'))
                    @if ($laporan->isEmpty() && request('dari_tanggal') != request('sampai_tanggal'))
                        <div class="alert alert-warning text-center mt-4">Maaf, tidak ada data di rentang tanggal ini</div>
                    @elseif ($laporan->isEmpty())
                        <div class="alert alert-warning text-center mt-4">Data tersedia, namun tidak ada pergerakan stok pada tanggal ini</div>
                    @else
                        <form method="POST" action="{{ route('laporan.pemantauan.pdf') }}" target="_blank">
                            @csrf
                            <input type="hidden" name="dari_tanggal" value="{{ request('dari_tanggal') }}">
                            <input type="hidden" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}">

                            <div class="table-responsive mt-4">
                                <table class="table table-striped table-hover text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Kode Bahan</th>
                                            <th>Nama Bahan</th>
                                            <th>Total Masuk</th>
                                            <th>Total Keluar</th>
                                            <th>Status Pemantauan</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($laporan as $index => $data)
                                            <tr>
                                                <td>
                                                    {{ $data['kode_bahan'] }}
                                                    <input type="hidden" name="kode_bahan[{{ $index }}]" value="{{ $data['kode_bahan'] }}">
                                                </td>
                                                <td>
                                                    {{ $data['nama_bahan'] }}
                                                    <input type="hidden" name="nama_bahan[{{ $index }}]" value="{{ $data['nama_bahan'] }}">
                                                </td>
                                                <td>
                                                    {{ $data['total_masuk'] }}
                                                    <input type="hidden" name="total_masuk[{{ $index }}]" value="{{ $data['total_masuk'] }}">
                                                </td>
                                                <td>
                                                    {{ $data['total_keluar'] }}
                                                    <input type="hidden" name="total_keluar[{{ $index }}]" value="{{ $data['total_keluar'] }}">
                                                </td>
                                                <td>
                                                    {{ $data['status_pemantauan'] }}
                                                    <input type="hidden" name="status_pemantauan[{{ $index }}]" value="{{ $data['status_pemantauan'] }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="keterangan[{{ $index }}]" class="form-control" placeholder="Masukkan keterangan">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-arrow-down"></i> DOWNLOAD PDF
                                </button>
                            </div>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const downloadForm = document.querySelector('form[action="{{ route('laporan.pemantauan.pdf') }}"]');
        const inputRows = document.querySelectorAll('input[name^="keterangan["]');

        if (downloadForm) {
            downloadForm.addEventListener('submit', function (e) {
                inputRows.forEach((input, index) => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `keterangan[${index}]`;
                    hiddenInput.value = input.value;
                    downloadForm.appendChild(hiddenInput);
                });
            });
        }
    });
</script>
@endpush

@endsection
