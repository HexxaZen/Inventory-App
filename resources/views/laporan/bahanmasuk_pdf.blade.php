<html>

<head>
    <title>Laporan Bahan Masuk</title>
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
    <h2>Laporan Bahan Masuk</h2>
    <table>
        <thead style="align-content: center;">
            <tr>
                <th>Tanggal Masuk</th>
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Jumlah Masuk</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bahanMasuk as $item)
                <tr class="data-row" data-kode="{{ $item->kode_bahan }}">
                    <td>{{ $item->tanggal_masuk }}</td>
                    <td>{{ $item->kode_bahan }}</td>
                    <td>{{ $item->nama_bahan }}</td>
                    <td>{{ $item->jumlah_masuk }}</td>
                    <td>{{ $item->bahan->satuan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
