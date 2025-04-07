<html>
<head>
    <title>Laporan Daftar Bahan Akhir - Merra Inventory</title>
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
        <div class="title">Laporan Daftar Bahan Akhir</div>
        <div class="sub-title">Merra Inventory Management System</div>
    </div>
    <div class="date">Tanggal Cetak: {{ date('d-m-Y') }}</div>
    <table>
        <thead class="table-dark">
            <tr>
                <th>Tanggal</th>
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Last</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $data)
                <tr>
                    <td>{{ $data->tanggal_input }}</td>
                    <td>{{ $data->kode_bahan }}</td>
                    <td>{{ $data->nama_bahan }}</td>
                    <td>{{ $data->stok_terakhir }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
