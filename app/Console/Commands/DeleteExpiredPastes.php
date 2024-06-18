<?php

namespace App\Console\Commands;

use App\Models\Paste;
use Illuminate\Console\Command;

class DeleteExpiredPastes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-expired-pastes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredPastes = Paste::where('expired_at', '<=', now())->get();

        foreach ($expiredPastes as $paste) {
            $paste->delete();
        }
    
        $this->info('Expired pastes deleted successfully.');
    }
}
