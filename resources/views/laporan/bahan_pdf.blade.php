<html>
<head>
    <title>Laporan Daftar Bahan - Merra Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
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
        }
        .date {
            text-align: right;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Merra Inventory">
        <div class="title">Laporan Daftar Bahan</div>
        <div class="sub-title">Merra Inventory Management System</div>
    </div>
    <div class="date">Tanggal Cetak: {{ date('d-m-Y') }}</div>
    <table>
        <thead>
            <tr>
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Jenis Bahan</th>
                <th>Kategori Bahan</th>
                <th>Sisa Stok</th>
                <th>Satuan</th>
                <th>Status</th>
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
                <td style="text-align: center;">{{ $bahan->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
