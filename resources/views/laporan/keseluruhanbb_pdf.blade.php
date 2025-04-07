<html>

<head>
    <title>Laporan Bahan Baku Keseluruhan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h2>Laporan Bahan Baku Keseluruhan</h2>
    <img src="{{ asset('images/logo_perusahaan.png') }}" class="logo" alt="Logo Perusahaan">
    <div class="header" style="justify-content:center;">
        <h2>Merra Coffee & Talk</h2>
        <h4>Periode: {{ \Carbon\Carbon::parse(request('dari_tanggal'))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('sampai_tanggal'))->format('d M Y') }}</h4>    </div>

    {{-- <div class="info">
        <p>Alamat: Jalan Raya Berkah No.123, Jakarta</p>
        <p>Nomor Telepon: (021) 123-4567</p>
        <p>Email: info@maju-jaya.com | Website: www.maju-jaya.com</p>
    </div> --}}
    <table>
        <thead style="align-content: center;">
            <tr>
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Total Masuk</th>
                <th>Total Keluar</th>
                <th>Stok Terakhir</th>
                <th>Sisa Stok Terbaru</th>
                <th>Tanggal Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $data)
                <tr>
                    <td>{{ $data->kode_bahan }}</td>
                    <td>{{ $data->nama_bahan }}</td>
                    <td>{{ $data->total_masuk }}</td>
                    <td>{{ $data->total_keluar }}</td>
                    <td>{{ $data->stok_terakhir }}</td>
                    <td>{{ $data->sisa_stok }}</td>
                    <td>{{ $data->tanggal_terakhir }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
