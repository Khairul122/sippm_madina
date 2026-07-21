<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\ComplaintCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComplaintCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Infrastruktur',
            'Pelayanan Publik',
            'Kebersihan & Lingkungan',
            'Kesehatan',
            'Pendidikan',
            'Keamanan & Ketertiban',
            'Sosial & Kesejahteraan',
            'Lain-lain',
        ];

        foreach ($categories as $category) {
            ComplaintCategory::firstOrCreate(
                ['slug' => Str::slug($category)],
                ['name' => $category]
            );
        }
    }
}
