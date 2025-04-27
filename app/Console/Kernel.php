<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\BahanController;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $bahans = \App\Models\Bahan::where('sisa_stok', '<=', 5)->get();

        if ($bahans->count()) {
            \Mail::to('owner@coffeeshop.com')->send(new \App\Mail\StokMenipisNotification($bahans));
        }
    })->daily(); // bisa diganti hourly, weekly, dll.
}




    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    
}
