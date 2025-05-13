@extends('layouts.master')
@section('title', 'Detail Inventaris')

@section('detailInventaris')
    <!-- Modal Detail Inventaris -->
    <div class="modal fade show" id="detailModal" tabindex="-1" style="display:block;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Inventaris</h5>
                </div>
                <div class="modal-body">
                    <p><strong>Kode:</strong> {{ $inventaris->kode_inventaris }}</p>
                    <p><strong>Nama:</strong> {{ $inventaris->nama_inventaris }}</p>
                    <p><strong>Jumlah:</strong> {{ $inventaris->jumlah_inventaris }}</p>
                    <p><strong>Satuan:</strong> {{ $inventaris->satuan }}</p>
                    <p><strong>Kondisi:</strong> {{ $inventaris->kondisi }}</p>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('inventaris.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: backdrop blur -->
    <div class="modal-backdrop fade show"></div>
@endsection
