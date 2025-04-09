<html>

<head>
    <title>Laporan Bahan Keluar</title>
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
    <h2>Laporan Bahan Keluar</h2>
    <table>
        <thead style="align-content: center;">
            <tr>
                <th>Tanggal Keluar</th>
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Jumlah Keluar</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataKeluar as $item)
            <tr>
                <td>{{ $item['tanggal_keluar'] }}</td>
                <td>{{ $item['kode_bahan'] }}</td>
                <td>{{ $item['nama_bahan'] }}</td>
                <td>{{ $item['jumlah_keluar'] }}</td>
                <td>{{ $item['satuan'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data bahan keluar untuk hari ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
