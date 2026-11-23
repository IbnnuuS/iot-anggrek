<?php

return [
    'required' => 'Kolom :attribute wajib diisi.',
    'email' => 'Kolom :attribute harus berupa alamat email yang valid.',
    'string' => 'Kolom :attribute harus berupa teks.',
    'min' => [
        'string' => 'Kolom :attribute minimal :min karakter.',
    ],
    'max' => [
        'string' => 'Kolom :attribute maksimal :max karakter.',
    ],
    'unique' => 'Kolom :attribute sudah terdaftar.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'password' => [
        'letters' => 'Kolom :attribute harus memuat setidaknya satu huruf.',
        'mixed' => 'Kolom :attribute harus memuat huruf besar dan huruf kecil.',
        'numbers' => 'Kolom :attribute harus memuat setidaknya satu angka.',
        'symbols' => 'Kolom :attribute harus memuat setidaknya satu simbol.',
        'uncompromised' => ':attribute pernah muncul dalam kebocoran data. Gunakan :attribute lain.',
    ],
    'attributes' => [
        'name' => 'nama lengkap',
        'email' => 'email',
        'password' => 'kata sandi',
    ],
];
