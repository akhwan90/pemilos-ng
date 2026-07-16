<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSubmission\StoreTamuDprdRequest;
use App\Http\Resources\TamuDprdResource;
use App\Models\TamuDprd;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;

class TamuDprdController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function store(StoreTamuDprdRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle file uploads
        $data['file_surat_kunjungan'] = $this->fileUpload->uploadDocument(
            $request->file('file_surat_kunjungan'),
            'surat_dprd'
        );

        $data['file_spt'] = $this->fileUpload->uploadDocument(
            $request->file('file_spt'),
            'spt_dprd'
        );

        if ($request->hasFile('file_bukti_menginap')) {
            $data['file_bukti_menginap'] = $this->fileUpload->uploadMixed(
                $request->file('file_bukti_menginap'),
                'bukti_menginap_dprd'
            );
        }

        // Track IP and user agent
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        $tamu = TamuDprd::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran kunjungan DPRD berhasil dikirim.',
            'data' => new TamuDprdResource($tamu),
        ], 201);
    }
}
