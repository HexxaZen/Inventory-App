@component('mail::message')
# Peringatan Stok Bahan

Berikut adalah daftar bahan yang **stoknya menipis** atau **sudah habis**:

@component('mail::table')
| Nama Bahan       | Kategori       | Sisa Stok |
| ---------------- | -------------- | --------- |
@foreach ($bahans as $bahan)
| {{ $bahan->nama_bahan }} | {{ $bahan->kategori }} | {{ $bahan->sisa_stok }} |
@endforeach
@endcomponent

Segera lakukan pengecekan dan pengadaan kembali.

Terima kasih,  
{{ config('app.name') }}
@endcomponent
