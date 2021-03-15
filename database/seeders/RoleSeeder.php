<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate(['name' => 'client']);
        Role::firstOrCreate(['name' => 'employee']);
        Role::firstOrCreate(['name' => 'support']);
        Role::firstOrCreate(['name' => 'admin']);
    }
}
