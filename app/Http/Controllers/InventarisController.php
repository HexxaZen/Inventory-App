<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Inventaris;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class InventarisController extends Controller
{
    public function index(Request $request)
{
    $sort = $request->query('sort', 'all');
    $kategoris = Kategori::all();
    
    // Filter berdasarkan sort
    $query = Inventaris::query();
    if ($sort === 'INVB') {
        $query->where('kode_inventaris', 'like', 'INVB%');
    } elseif ($sort === 'INVK') {
        $query->where('kode_inventaris', 'like', 'INVK%');
    } elseif ($sort === 'INVO') {
        $query->where('kode_inventaris', 'like', 'INVO%');
    }else{
        $query;
    }

    // Ambil hasil query
    $inventaris = $query->get();

    return view('inventaris.index', compact('inventaris', 'kategoris'));
}

    
public function cetakBarcode($id)
{
    $inventaris = Inventaris::findOrFail($id);

    // Generate QR Code menggunakan format SVG agar tidak butuh Imagick
    $qrCodeImage = base64_encode(QrCode::format('svg')->size(150)->generate(url('inventaris/' . $inventaris->id)));

    // Load ke PDF
    $pdf = Pdf::loadView('inventaris.barcode_pdf', compact('inventaris', 'qrCodeImage'));

    return $pdf->download('qrcode-inventaris-' . $inventaris->kode_inventaris . '.pdf');
}


    
    public function store(Request $request)
{
    $request->validate([
        'kode_inventaris' => 'required|unique:inventaris,kode_inventaris',
        'nama_inventaris' => 'required',
        'jumlah_inventaris' => 'required|integer',
        'satuan' => 'required',
        'kondisi' => 'required',
    ]);

    $inventaris = Inventaris::create([
        'kode_inventaris' => $request->kode_inventaris,
        'nama_inventaris' => $request->nama_inventaris,
        'jumlah_inventaris' => $request->jumlah_inventaris,
        'satuan' => $request->satuan,
        'kondisi' => $request->kondisi,
    ]);
    
    return redirect()->route('inventaris.index')->with('success');
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_inventaris' => 'required',
            'jumlah_inventaris'=> 'required',
            'satuan'=> 'required',
            'kondisi'=> 'required',
        ]);

        $inventaris = Inventaris::findOrFail($id);
        $kategoris = Kategori::where('kode_kategori', 'LIKE', 'INV%')->get();
        $inventaris->update([
            'nama_inventaris' => $request->nama_inventaris,
            'jumlah_inventaris' => $request->jumlah_inventaris,
            'satuan' => $request->satuan,
            'kondisi' => $request->kondisi,
        ]);

        return redirect()->route('inventaris.index')->with('success');
    }

    public function destroy($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        $inventaris->delete();

        return redirect()->route('inventaris.index')->with('success');
    }
}
