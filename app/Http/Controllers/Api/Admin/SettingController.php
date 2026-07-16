<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Get all settings
     */
    public function index(): JsonResponse
    {
        // Get specific settings we care about
        $keys = [
            'penerima_kunjungan_jabatan',
            'penerima_kunjungan_nama',
            'penerima_kunjungan_nip',
            'nomor_whatsapp_aduan'
        ];
        
        $settings = Setting::whereIn('key', $keys)->get()->pluck('value', 'key');
        
        // Ensure defaults are present if not in DB
        $data = [
            'penerima_kunjungan_jabatan' => $settings['penerima_kunjungan_jabatan'] ?? 'Pejabat Pengelola Informasi dan Dokumentasi',
            'penerima_kunjungan_nama' => $settings['penerima_kunjungan_nama'] ?? '',
            'penerima_kunjungan_nip' => $settings['penerima_kunjungan_nip'] ?? '',
            'nomor_whatsapp_aduan' => $settings['nomor_whatsapp_aduan'] ?? '',
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'penerima_kunjungan_jabatan' => 'required|string|max:255',
            'penerima_kunjungan_nama' => 'required|string|max:255',
            'penerima_kunjungan_nip' => 'nullable|string|max:50',
            'nomor_whatsapp_aduan' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan.',
            'data' => $validated
        ]);
    }
}