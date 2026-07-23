<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Setting Jenis Jadwal Pemilihan
    |--------------------------------------------------------------------------
    |
    | Anda dapat menambah, mengubah label, atau menyesuaikan deskripsi
    | jadwal pemilihan di sini tanpa perlu mengubah struktur database.
    |
    */
    'jenis_jadwal' => [
        'input_data_dps' => [
            'label' => 'Input Data Siswa',
            'deskripsi' => 'Periode untuk mengelola data siswa, seperti menghapus data siswa yang pindah atau lulus, menambahkan siswa baru.'
        ],
        'pengumuman_data_dps' => [
            'label' => 'Pengumuman Data DPS',
            'deskripsi' => 'Periode untuk mempublikasikan dan memberikan masa sanggah untuk Data Pemilih Sementara.'
        ],
        'input_data_dpt' => [
            'label' => 'Input Data DPT',
            'deskripsi' => 'Periode untuk mengolah dan menetapkan Data Pemilih Tetap (DPT).'
        ],
        'pengumuman_data_dpt' => [
            'label' => 'Pengumuman Data DPT',
            'deskripsi' => 'Periode untuk mengumumkan Data Pemilih Tetap kepada siswa secara final.'
        ],
        'input_data_calon' => [
            'label' => 'Input Data Calon',
            'deskripsi' => 'Periode pendaftaran dan pengelolaan profil/foto kandidat ketua dan wakil ketua.'
        ],
        'kampanye' => [
            'label' => 'Masa Kampanye',
            'deskripsi' => 'Periode kampanye, penyampaian visi misi, dan program kerja bagi para kandidat.'
        ],
        'generate_token' => [
            'label' => 'Generate Token',
            'deskripsi' => 'Pembuatan token atau PIN akses untuk pemilih agar bisa masuk ke bilik suara.'
        ],
        'pemilihan' => [
            'label' => 'Pelaksanaan Pemilihan',
            'deskripsi' => 'Waktu inti pelaksanaan e-voting (pencoblosan) oleh para pemilih.'
        ],
    ]
];
