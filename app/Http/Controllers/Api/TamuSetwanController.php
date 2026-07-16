<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSubmission\StoreTamuSetwanRequest;
use App\Http\Resources\TamuSetwanResource;
use App\Models\TamuSetwan;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;

class TamuSetwanController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function store(StoreTamuSetwanRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle file uploads
        $data['file_surat_kunjungan'] = $this->fileUpload->uploadDocument(
            $request->file('file_surat_kunjungan'),
            'surat_setwan'
        );

        $data['file_spt'] = $this->fileUpload->uploadDocument(
            $request->file('file_spt'),
            'spt_setwan'
        );

        if ($request->hasFile('file_bukti_menginap')) {
            $data['file_bukti_menginap'] = $this->fileUpload->uploadMixed(
                $request->file('file_bukti_menginap'),
                'bukti_menginap_setwan'
            );
        }

        // Track IP and user agent
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        $tamu = TamuSetwan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran kunjungan Setwan berhasil dikirim.',
            'data' => new TamuSetwanResource($tamu),
        ], 201);
    }
}
