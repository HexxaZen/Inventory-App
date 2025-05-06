<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemantauan Bahan Baku - Merra Inventory</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 40px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .sub-title {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .periode {
            font-size: 13px;
            margin-top: 5px;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
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
        .text-right {
            text-align: right;
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
        <div class="title">Laporan Pemantauan Bahan Baku</div>
        <div class="sub-title">Merra Coffee & Talk</div>
        <div class="periode">
            Periode: {{ \Carbon\Carbon::parse(request('dari_tanggal'))->format('d M Y') }} - 
                     {{ \Carbon\Carbon::parse(request('sampai_tanggal'))->format('d M Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Bahan</th>
                <th>Nama Bahan</th>
                <th>Total Bahan Masuk</th>
                <th>Total Bahan Keluar</th>
                <th>Status Pemantauan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $index=>$data)
                <tr>
                    <td>{{ $data['kode_bahan'] }}</td>
                    <td>{{ $data['nama_bahan'] }}</td>
                    <td class="text-right">{{ $data['total_masuk'] }}</td>
                    <td class="text-right">{{ $data['total_keluar'] }}</td>
                    <td class="text-center">{{ $data['status_pemantauan'] }}</td>
                    <td class="text-center">{{$data['keterangan']}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data pemantauan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <div class="right">
            <p>Kudus, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <br><br><br>
            <p><strong>Owner</strong></p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
