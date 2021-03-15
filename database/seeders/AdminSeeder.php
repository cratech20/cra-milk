<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrCreate([
            'email' => 'vipblogger@ya.ru'
        ], [
            'name' => 'admin',
            'email' => 'vipblogger@ya.ru',
            'password' => Hash::make('cra2020'),
        ]);

        $user->assignRole('admin');
    }
}
