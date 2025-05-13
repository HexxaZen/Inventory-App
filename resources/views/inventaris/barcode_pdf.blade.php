<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code Inventaris</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 30px;
        }

        .title {
            margin-bottom: 10px;
            font-size: 22px;
        }

        .subtitle {
            margin-bottom: 30px;
            font-size: 18px;
        }

        .barcode {
            margin: 0 auto;
            padding: 10px;
            border: 2px dashed #333;
            width: fit-content;
            display: inline-block;
        }

        .barcode img {
            width: 150px;
            height: 150px;
        }

        .info {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }

        footer {
            margin-top: 50px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <img src="{{ public_path('landingpage/images/logomerra.png') }}" height="90px">
    <div class="title">Inventaris: {{ $inventaris->nama_inventaris }}</div>
    <div class="subtitle">Kode: {{ $inventaris->kode_inventaris }}</div>

    <div class="barcode">
        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('inventaris.scan', $inventaris->id), 'QRCODE') }}" alt="QR Code">
    </div>

    <div class="info">
        Scan QR Code di atas untuk melihat detail inventaris.
    </div>

    <footer>
        Dicetak pada {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
    </footer>
</body>
</html>
