<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanKeluar extends Model
{
    use HasFactory;

    protected $table = 'bahan_keluar';

    protected $fillable = [
        'tanggal_keluar',
        'kode_bahan',
        'nama_bahan',
        'jumlah_keluar',
        'satuan',
        'bahan_masuk_id', // Relasi dengan BahanMasuk
        'bahan_id'        // Relasi dengan Bahan atau BahanProcess
    ];

    /**
     * Relasi ke model Bahan.
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    /**
     * Relasi ke model BahanProcess, jika keluar bahan berasal dari proses.
     */
    public function bahanProcess()
    {
        return $this->belongsTo(BahanProcess::class, 'bahan_id'); // Relasi dengan bahan proses
    }

    /**
     * Relasi ke model BahanMasuk, untuk mengaitkan keluar bahan dengan input bahan.
     */
    public function bahanMasuk()
    {
        return $this->belongsTo(BahanMasuk::class, 'bahan_masuk_id');
    }

    /**
     * Accessor untuk menghitung hasil_seharusnya berdasarkan jumlah terjual dan gramasi.
     */
    public function getHasilSeharusnyaAttribute()
    {
        if (!$this->bahan || !$this->bahan->menus) {
            return 0;
        }

        return $this->bahan->menus->sum(function ($menu) {
            return optional($menu->pivot)->gramasi * ($menu->menuTerjual->sum('jumlah_terjual') ?? 0);
        });
    }

    /**
     * Accessor untuk menghitung hasil_akhir.
     */
    public function getHasilAkhirAttribute()
    {
        $hasil_seharusnya = $this->hasil_seharusnya;
        $jumlah_keluar = $this->jumlah_keluar ?? 0;

        if ($jumlah_keluar == $hasil_seharusnya) {
            return 'Balance';
        } elseif ($jumlah_keluar > $hasil_seharusnya) {
            return 'Waste: ' . abs($jumlah_keluar - $hasil_seharusnya) . ' ' . $this->satuan;
        } else {
            return 'Plus: +' . abs($hasil_seharusnya - $jumlah_keluar) . ' ' . $this->satuan;
        }
    }

    /**
     * Menghitung jumlah keluar berdasarkan gramasi bahan proses.
     * Diperlukan untuk mengupdate BahanKeluar dengan komposisi bahan.
     */
    public static function updateBahanKeluar($bahanMasuk, $selisih, $gramasi)
    {
        $bahanProcess = BahanProcess::where('kode_bahan', $bahanMasuk->kode_bahan)->first();

        if ($bahanProcess) {
            foreach ($bahanProcess->bahans as $bahanBaku) {
                $jumlahKeluar = $gramasi * $selisih;

                // Cek jika sudah ada data keluar bahan, maka update
                $keluar = BahanKeluar::where('bahan_masuk_id', $bahanMasuk->id)
                    ->where('bahan_id', $bahanBaku->id)
                    ->first();

                if ($keluar) {
                    $keluar->jumlah_keluar += $jumlahKeluar;
                    $keluar->save();
                } else {
                    // Tambah BahanKeluar baru jika belum ada
                    BahanKeluar::create([
                        'tanggal_keluar' => now(),
                        'kode_bahan' => $bahanBaku->kode_bahan,
                        'nama_bahan' => $bahanBaku->nama_bahan,
                        'jumlah_keluar' => $jumlahKeluar,
                        'satuan' => $bahanBaku->satuan,
                        'bahan_masuk_id' => $bahanMasuk->id, // Relasi ke BahanMasuk
                        'bahan_id' => $bahanBaku->id // Relasi ke Bahan
                    ]);
                }
            }
        }
    }
}
