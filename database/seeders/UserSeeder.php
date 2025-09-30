<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'admin@mgi.test'],
            [
                'name' => 'SISTEMA',
                'password' => Hash::make('123456'),
                'cpf' => '00016688112',
                'telephone' => '(61) 3218-1400',
                'status' => Status::ACTIVE,
            ]);

        $user->roles()->sync([1]);
    }
}
