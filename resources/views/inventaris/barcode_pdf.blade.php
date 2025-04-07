<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .barcode { margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Inventaris: {{ $inventaris->nama_inventaris }}</h2>
    <h3>Kode: {{ $inventaris->kode_inventaris }}</h3>
    
    <div class="barcode">
        <h4>QR Code</h4>
        <img src="data:image/png;base64,{{ $qrCodeImage }}" alt="QR Code" width="150">
    </div>

    <p>Scan QR Code untuk melihat detail inventaris.</p>
</body>
</html>