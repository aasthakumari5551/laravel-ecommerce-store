<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SetupRoles extends Command
{
    protected $signature   = 'app:setup-roles';
    protected $description = 'Create default application roles';

    public function handle(): void
    {
        $roles = ['admin', 'customer'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            $this->info("Role [{$role}] ready.");
        }

        $this->info('Done.');
    }
}