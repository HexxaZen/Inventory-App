<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Bahan Baku - Merra Inventory</title>
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
            width: 100px;
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

        table,
        th,
        td {
            border: 1px solid black;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        th,
        td {
            padding: 8px;
            font-size: 12px;
        }

        td {
            vertical-align: top;
        }

        .signature {
            margin-top: 60px;
            width: 100%;
        }

        .signature .left {
            float: left;
            width: 50%;
            text-align: center;
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
        <img src="{{ public_path('landingpage/images/logomerra.png') }}">
        <div class="title">Laporan Bahan Baku</div>
        <div class="sub-title">Merra Inventory Management System</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Jenis Bahan</th>
                <th>Kategori Bahan</th>
                <th>Sisa Stok</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bahans as $bahan)
                <tr>
                    <td>{{ $bahan->kode_bahan }}</td>
                    <td>{{ $bahan->nama_bahan }}</td>
                    <td>{{ $bahan->jenis_bahan }}</td>
                    <td>{{ $bahan->kategori_bahan }}</td>
                    <td style="text-align: right;">{{ $bahan->sisa_stok }}</td>
                    <td>{{ $bahan->satuan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="date">Kudus, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>

    <div class="signature">
        <div class="right">
            <p>Penanggung Jawab</p>
            <br><br><br>
            <p><strong>Head</strong></p>
        </div>
        <div class="clear"></div>
    </div>
</body>

</html>
