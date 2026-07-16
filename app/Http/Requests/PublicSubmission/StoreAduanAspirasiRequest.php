<?php

namespace App\Http\Requests\PublicSubmission;

use Illuminate\Foundation\Http\FormRequest;

class StoreAduanAspirasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'alamat' => ['required', 'string'],
            'pekerjaan' => ['required', 'string', 'max:255'],
            'nomor_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'kategori_aduan_id' => ['required', 'exists:kategori_aduan,id'],
            'isi_aduan' => ['required', 'string'],
            'file_berkas_aduan' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpeg,jpg,png'],
        ];

        // Jika metode PUT (Update), file tidak wajib
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['file_berkas_aduan'] = ['nullable', 'file', 'max:5120', 'mimes:pdf,jpeg,jpg,png'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'alamat.required' => 'Alamat wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'nomor_hp.required' => 'Nomor HP wajib diisi.',
            'nomor_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'kategori_aduan_id.required' => 'Kategori aduan wajib dipilih.',
            'kategori_aduan_id.exists' => 'Kategori aduan tidak valid.',
            'isi_aduan.required' => 'Isi aduan wajib diisi.',
            'file_berkas_aduan.max' => 'Ukuran file maksimal 5MB.',
            'file_berkas_aduan.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
        ];
    }
}
