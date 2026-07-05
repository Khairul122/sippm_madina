<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $opds = [
            ['name' => 'Dinas Kesehatan', 'code' => 'DINKES'],
            ['name' => 'Dinas Pendidikan', 'code' => 'DISDIK'],
            ['name' => 'Dinas Pekerjaan Umum dan Penataan Ruang', 'code' => 'DISPUPR'],
            ['name' => 'Dinas Sosial', 'code' => 'DINSOS'],
            ['name' => 'Dinas Pertanian', 'code' => 'DISTAN'],
        ];

        foreach ($opds as $opd) {
            Opd::firstOrCreate(['code' => $opd['code']], $opd);
        }
    }
}
