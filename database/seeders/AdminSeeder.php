<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $admin2 = new Admin();
        $admin2->email = 'kadai_review_admin@example.com'; // 新しいメールアドレス
        $admin2->password = Hash::make('password'); // 新しいパスワード
        $admin2->save();
    }
}
