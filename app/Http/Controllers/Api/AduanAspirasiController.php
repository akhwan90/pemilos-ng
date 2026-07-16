<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSubmission\StoreAduanAspirasiRequest;
use App\Http\Resources\AduanAspirasiResource;
use App\Models\AduanAspirasi;
use App\Models\KategoriAduan;
use App\Models\Setting;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AduanAspirasiController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function store(StoreAduanAspirasiRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('file_berkas_aduan')) {
            $data['file_berkas_aduan'] = $this->fileUpload->uploadMixed(
                $request->file('file_berkas_aduan'),
                'berkas_aduan'
            );
        } else {
            $data['file_berkas_aduan'] = null; // Pastikan null jika tidak ada file
        }

        // Track IP and user agent
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        $aduan = AduanAspirasi::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Aduan/aspirasi berhasil dikirimkan.',
            'data' => new AduanAspirasiResource($aduan),
        ], 201);
    }

    public function kategori(): JsonResponse
    {
        $kategori = KategoriAduan::where('is_active', true)->get();
        $settingWa = Setting::where('key', 'nomor_whatsapp_aduan')->first();

        return response()->json([
            'success' => true,
            'data' => $kategori,
            'whatsapp' => $settingWa ? $settingWa->value : null
        ]);
    }
}
