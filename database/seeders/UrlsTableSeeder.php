<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UrlsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\Url::factory()->create([
        //     'name' => fake()->name(),
        //     'created_at' => '1999-01-01',
        // ]);

        \App\Models\Url::factory(10)->create();
    }
}
