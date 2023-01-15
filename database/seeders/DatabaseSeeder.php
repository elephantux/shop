<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        Brand::factory(20)->create();
        Category::factory(50)
            ->has(Product::factory(rand(10, 35)))
            ->create();
        UserFactory::new()->create([
            'name' => 'Yury Boichuk',
            'email' => 'slon@offline.lv',
            'password' => bcrypt('123123'),
        ]);
    }
}
