<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | Web\Auth\LoginController dan Api\AuthController sudah memakai pesan
    | Bahasa Indonesia hardcode sendiri untuk kegagalan login, jadi baris
    | ini jarang benar-benar tampil — disediakan agar tetap konsisten
    | kalau ada kode lain (mis. helper `Auth::` bawaan Laravel) yang
    | memicu key ini.
    |
    */

    'failed' => 'Email atau kata sandi salah.',
    'password' => 'Kata sandi yang dimasukkan salah.',
    'throttle' => 'Terlalu banyak percobaan masuk. Coba lagi dalam :seconds detik.',

];
