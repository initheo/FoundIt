<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'icon' => 'devices'],
            ['name' => 'Dokumen', 'icon' => 'description'],
            ['name' => 'Aksesoris', 'icon' => 'watch'],
            ['name' => 'Tas & Dompet', 'icon' => 'backpack'],
            ['name' => 'Kunci', 'icon' => 'key'],
            ['name' => 'Pakaian', 'icon' => 'checkroom'],
            ['name' => 'Lainnya', 'icon' => 'category'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
