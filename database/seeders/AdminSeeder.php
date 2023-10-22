<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            "name" => "Admin1",
            "phone_or_email" => "admin@gmail.com",
            "password" => bcrypt("admin1"),
        ]);
    }
}
