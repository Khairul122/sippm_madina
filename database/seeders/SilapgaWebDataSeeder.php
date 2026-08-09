<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Seeder to import complete database records from silapga_web.sql
 * with all @demo.test emails replaced with @gmail.com.
 */
class SilapgaWebDataSeeder extends Seeder
{
    public function run(): void
    {
        $sqlPath = base_path('silapga_web.sql');
        if (! File::exists($sqlPath)) {
            if (isset($this->command)) {
                $this->command->error("File silapga_web.sql tidak ditemukan di: {$sqlPath}");
            }
            return;
        }

        $sql = File::get($sqlPath);
        $sql = str_replace('@demo.test', '@gmail.com', $sql);

        // Nonaktifkan Foreign Key Checks untuk import massal
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        preg_match_all('/INSERT INTO `([^`]+)`[\s\S]*?;/i', $sql, $matches, PREG_SET_ORDER);

        $tablesCleared = [];
        foreach ($matches as $match) {
            $tableName = $match[1];

            // Truncate tabel sekali sebelum pengisian data (abaikan tabel migrations)
            if ($tableName !== 'migrations' && ! in_array($tableName, $tablesCleared)) {
                DB::table($tableName)->truncate();
                $tablesCleared[] = $tableName;
            }

            if ($tableName !== 'migrations') {
                DB::unprepared($match[0]);
            }
        }

        // Aktifkan kembali Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        if (isset($this->command)) {
            $this->command->info('Berhasil mengimpor '.count($tablesCleared).' tabel data dari silapga_web.sql dengan akun @gmail.com!');
        }
    }
}
