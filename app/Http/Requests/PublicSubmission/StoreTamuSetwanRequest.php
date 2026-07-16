<?php

namespace App\Http\Requests\PublicSubmission;

use Illuminate\Foundation\Http\FormRequest;

class StoreTamuSetwanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'instansi' => ['required', 'string', 'max:255'],
            // 'hari_berkunjung' => ['required', 'string', 'max:20'],
            'jam_berkunjung' => ['required', 'date_format:H:i'],
            'tanggal_berkunjung' => ['required', 'date'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'nama_jabatan_ketua_rombongan' => ['required', 'string', 'max:255'],
            'nomor_hp_narahubung' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'alamat_instansi' => ['required', 'string'],
            'tujuan_kunjungan' => ['required', 'string'],
            'nomor_surat_ppid' => ['nullable', 'string', 'max:255'],
            'materi' => ['nullable', 'string'],
            'file_surat_kunjungan' => ['required', 'file', 'max:5120', 'mimes:pdf'],
            'file_spt' => ['required', 'file', 'max:5120', 'mimes:pdf'],
            'file_bukti_menginap' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpeg,jpg,png'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['file_surat_kunjungan'] = ['nullable', 'file', 'max:5120', 'mimes:pdf'];
            $rules['file_spt'] = ['nullable', 'file', 'max:5120', 'mimes:pdf'];
        } else {
            $rules['tanggal_berkunjung'][] = 'after_or_equal:today';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'instansi.required' => 'Nama instansi wajib diisi.',
            // 'hari_berkunjung.required' => 'Hari berkunjung wajib diisi.',
            'jam_berkunjung.required' => 'Jam berkunjung wajib diisi.',
            'tanggal_berkunjung.required' => 'Tanggal berkunjung wajib diisi.',
            'tanggal_berkunjung.after_or_equal' => 'Tanggal berkunjung tidak boleh kurang dari hari ini.',
            'jam_berkunjung.date_format' => 'Format jam tidak valid (HH:MM).',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1.',
            'nama_jabatan_ketua_rombongan.required' => 'Nama & jabatan ketua rombongan wajib diisi.',
            'nomor_hp_narahubung.required' => 'Nomor HP narahubung wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'alamat_instansi.required' => 'Alamat instansi wajib diisi.',
            'tujuan_kunjungan.required' => 'Tujuan kunjungan wajib diisi.',
            'file_surat_kunjungan.required' => 'Surat kunjungan wajib diunggah.',
            'file_surat_kunjungan.mimes' => 'Surat kunjungan harus berupa PDF.',
            'file_spt.required' => 'Surat Perintah Tugas (SPT) wajib diunggah.',
            'file_spt.mimes' => 'SPT harus berupa PDF.',
            'file_bukti_menginap.mimes' => 'Bukti menginap harus berupa PDF, JPG, atau PNG.',
            '*.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}
