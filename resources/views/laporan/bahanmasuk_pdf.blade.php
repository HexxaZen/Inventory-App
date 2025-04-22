<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Bahan Masuk - Merra Inventory</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 40px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header img {
            width: 80px;
            height: auto;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .sub-title {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .date {
            margin-top: 20px;
            text-align: right;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        th, td {
            padding: 8px;
            font-size: 12px;
        }
        td {
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .signature {
            margin-top: 60px;
            width: 100%;
        }
        .signature .right {
            float: right;
            width: 50%;
            text-align: center;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('landingpage/images/logomerra.png') }}" alt="Logo Merra">
        <div class="title">Laporan Bahan Masuk</div>
        <div class="sub-title">Merra Inventory Management System</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal Masuk</th>
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Jumlah Masuk</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bahanMasuk as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') }}</td>
                    <td>{{ $item->kode_bahan }}</td>
                    <td>{{ $item->nama_bahan }}</td>
                    <td style="text-align: right;">{{ $item->jumlah_masuk }}</td>
                    <td>{{ $item->bahan->satuan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data bahan masuk untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="date">Kudus, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>

    <div class="signature">
        <div class="right">
            <p>Penanggung Jawab</p>
            <br><br><br>
            <p><strong>Owner</strong></p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
