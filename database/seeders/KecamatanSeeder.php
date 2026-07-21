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
            ['name' => 'Batahan', 'code' => 'BTH'],
            ['name' => 'Batang Natal', 'code' => 'BTN'],
            ['name' => 'Bukit Malintang', 'code' => 'BML'],
            ['name' => 'Huta Bargot', 'code' => 'HBG'],
            ['name' => 'Kotanopan', 'code' => 'KTN'],
            ['name' => 'Lembah Sorik Marapi', 'code' => 'LSM'],
            ['name' => 'Lingga Bayu', 'code' => 'LGB'],
            ['name' => 'Muara Batang Gadis', 'code' => 'MBG'],
            ['name' => 'Muara Sipongi', 'code' => 'MSP'],
            ['name' => 'Naga Juang', 'code' => 'NGJ'],
            ['name' => 'Natal', 'code' => 'NTL'],
            ['name' => 'Pakantan', 'code' => 'PKT'],
            ['name' => 'Panyabungan', 'code' => 'PYB'],
            ['name' => 'Panyabungan Barat', 'code' => 'PAB'],
            ['name' => 'Panyabungan Selatan', 'code' => 'PAS'],
            ['name' => 'Panyabungan Timur', 'code' => 'PAT'],
            ['name' => 'Panyabungan Utara', 'code' => 'PAU'],
            ['name' => 'Puncak Sorik Marapi', 'code' => 'PSM'],
            ['name' => 'Ranto Baek', 'code' => 'RTB'],
            ['name' => 'Siabu', 'code' => 'SAB'],
            ['name' => 'Sinunukan', 'code' => 'SNN'],
            ['name' => 'Tambangan', 'code' => 'TMB'],
            ['name' => 'Ulu Pungkut', 'code' => 'UPK'],
        ];

        foreach ($kecamatans as $kecamatan) {
            Kecamatan::firstOrCreate(['code' => $kecamatan['code']], $kecamatan);
        }
    }
}
