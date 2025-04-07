<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bahan;
use App\Models\User;
use App\Notifications\LowStockNotification;

class CheckLowStock extends Command
{
    protected $signature = 'check:low-stock';
    protected $description = 'Cek bahan yang stoknya menipis dan kirim notifikasi email';

    public function handle()
    {
        $bahans = Bahan::whereColumn('sisa_stok', '<', 'batas_minimum')->get();
        $admins = User::role('admin')->get(); // Hanya user dengan role admin

        foreach ($bahans as $bahan) {
            foreach ($admins as $admin) {
                $admin->notify(new LowStockNotification($bahan));
            }
        }

        $this->info('Notifikasi stok menipis telah dikirim.');
    }
}
