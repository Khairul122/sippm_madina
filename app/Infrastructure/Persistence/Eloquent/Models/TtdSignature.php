<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Profil tanda tangan resmi (satu baris aktif, selalu id=1) yang dipakai
 * di blok TTD laporan pengaduan yang dicetak — lihat LaporanController.
 */
#[Fillable(['nama_penandatangan', 'jabatan_penandatangan', 'pangkat', 'nip'])]
class TtdSignature extends Model
{
}
