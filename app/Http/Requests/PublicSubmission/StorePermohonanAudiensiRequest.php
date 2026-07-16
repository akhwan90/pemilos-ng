<?php

namespace App\Http\Requests\PublicSubmission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermohonanAudiensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'nama_instansi_kelompok_paguyuban_komunitas' => ['required', 'string', 'max:255'],
            'maksud_tujuan_audiensi' => ['required', 'string'],
            'nama_jabatan_ketua_rombongan' => ['required', 'string', 'max:255'],
            'nomor_hp_narahubung' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'file_permohonan_audiensi' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpeg,jpg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama_instansi_kelompok_paguyuban_komunitas.required' => 'Nama instansi/kelompok/komunitas wajib diisi.',
            'maksud_tujuan_audiensi.required' => 'Maksud dan tujuan audiensi wajib diisi.',
            'nama_jabatan_ketua_rombongan.required' => 'Nama & jabatan ketua rombongan wajib diisi.',
            'nomor_hp_narahubung.required' => 'Nomor HP narahubung wajib diisi.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1.',
            'file_permohonan_audiensi.mimes' => 'Surat permohonan harus berupa PDF, JPG, atau PNG.',
            'file_permohonan_audiensi.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}
