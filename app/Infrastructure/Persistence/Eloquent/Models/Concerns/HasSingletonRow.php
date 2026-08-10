<?php

namespace App\Infrastructure\Persistence\Eloquent\Models\Concerns;

/**
 * Untuk model "satu baris aktif, selalu id=1" (ManualBook, TtdSignature,
 * SiteSetting) — dipakai lewat updateOrCreate(['id' => 1], $data) di
 * masing-masing controller sebelumnya.
 *
 * BUG yang diperbaiki lewat trait ini: updateOrCreate(['id' => 1], $data)
 * HANYA menjamin id=1 kalau baris id=1 sudah ada (lewat where()->first()).
 * Kalau baris itu BELUM ada (tabel baru/kosong), Eloquent membuat instance
 * baru lewat `new static(['id' => 1])`, yang menjalankan fill(['id' => 1])
 * di constructor — dan karena `id` sengaja TIDAK ada di $fillable model
 * manapun (praktik keamanan yang benar, id tidak boleh mass-assignable),
 * atribut id=1 itu DIAM-DIAM DIBUANG oleh guard fillable. Hasilnya: baris
 * baru tersimpan tanpa id eksplisit, dan MySQL auto-increment memberi id
 * berapa pun nilai berikutnya (2, 3, dst — terutama kalau pernah ada baris
 * id=1 yang dihapus sebelumnya). Karena SEMUA controller terkait selalu
 * query find(1), baris dengan id selain 1 itu tidak akan pernah terbaca
 * aplikasi, walau data & file fisiknya valid.
 *
 * Fix: set `id` lewat assignment properti langsung ($model->id = 1), yang
 * memanggil setAttribute() dan TIDAK melalui pengecekan $fillable sama
 * sekali (guard fillable cuma berlaku untuk fill()/mass-assignment lewat
 * array, bukan untuk assignment properti satu-satu).
 */
trait HasSingletonRow
{
    public static function singleton(array $attributes): static
    {
        /** @var static $model */
        $model = static::query()->find(1) ?? new static();
        $model->id = 1;
        $model->fill($attributes);
        $model->save();

        return $model;
    }
}
