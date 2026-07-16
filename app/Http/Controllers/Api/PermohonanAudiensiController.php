<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSubmission\StorePermohonanAudiensiRequest;
use App\Http\Resources\PermohonanAudiensiResource;
use App\Models\PermohonanAudiensi;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;

class PermohonanAudiensiController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function store(StorePermohonanAudiensiRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle file upload
        $data['file_permohonan_audiensi'] = $this->fileUpload->uploadDocument(
            $request->file('file_permohonan_audiensi'),
            'surat_audiensi'
        );

        // Track IP and user agent
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        $audiensi = PermohonanAudiensi::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan audiensi berhasil dikirim.',
            'data' => new PermohonanAudiensiResource($audiensi),
        ], 201);
    }
}
