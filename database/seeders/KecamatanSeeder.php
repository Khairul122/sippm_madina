<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use Illuminate\Database\Seeder;

/**
 * Kecamatan (subdistrict) list for Kabupaten Mandailing Natal, Sumatera
 * Utara — a representative subset of the real subdistricts in the
 * regency.
 */
class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatans = [
            ['name' => 'Panyabungan', 'code' => 'PYB'],
            ['name' => 'Kotanopan', 'code' => 'KTN'],
            ['name' => 'Natal', 'code' => 'NTL'],
            ['name' => 'Muara Batang Gadis', 'code' => 'MBG'],
            ['name' => 'Batahan', 'code' => 'BTH'],
            ['name' => 'Lembah Sorik Marapi', 'code' => 'LSM'],
            ['name' => 'Bukit Malintang', 'code' => 'BML'],
            ['name' => 'Siabu', 'code' => 'SBU'],
        ];

        foreach ($kecamatans as $kecamatan) {
            Kecamatan::firstOrCreate(['code' => $kecamatan['code']], $kecamatan);
        }
    }
}
