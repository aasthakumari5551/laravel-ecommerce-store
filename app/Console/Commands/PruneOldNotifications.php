<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneOldNotifications extends Command
{
    protected $signature   = 'app:prune-notifications {--days=30}';
    protected $description = 'Delete read notifications older than N days';

    public function handle(): void
    {
        $days    = (int) $this->option('days');
        $deleted = DB::table('notifications')
                     ->whereNotNull('read_at')
                     ->where('created_at', '<', now()->subDays($days))
                     ->delete();

        $this->info("Pruned {$deleted} old notifications.");
    }
}