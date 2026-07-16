<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\AduanAspirasiExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStatusRequest;
use App\Http\Resources\AduanAspirasiResource;
use App\Models\AduanAspirasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AduanAspirasiAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AduanAspirasi::with('kategoriAduan');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nik', 'like', "%{$request->search}%")
                  ->orWhere('nomor_hp', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Sort
        $sortField = $request->sort_field ?? 'created_at';
        $sortDir = $request->sort_dir ?? 'desc';
        $query->orderBy($sortField, $sortDir);

        $perPage = min((int) ($request->per_page ?? 15), 100);
        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AduanAspirasiResource::collection($data),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
        ]);
    }

    public function show(AduanAspirasi $aduanAspirasi): JsonResponse
    {
        $aduanAspirasi->load(['kategoriAduan', 'notes.admin']);

        return response()->json([
            'success' => true,
            'data' => new AduanAspirasiResource($aduanAspirasi),
        ]);
    }

    public function updateStatus(AduanAspirasi $aduanAspirasi, UpdateStatusRequest $request): JsonResponse
    {
        $aduanAspirasi->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui.',
            'data' => new AduanAspirasiResource($aduanAspirasi),
        ]);
    }

    public function store(\App\Http\Requests\PublicSubmission\StoreAduanAspirasiRequest $request, \App\Services\FileUploadService $fileUpload): JsonResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('file_berkas_aduan')) {
            $data['file_berkas_aduan'] = $fileUpload->uploadMixed($request->file('file_berkas_aduan'), 'berkas_aduan');
        }
        
        $data['status'] = $request->status ?? 'baru';
        
        $aduan = AduanAspirasi::create($data);
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'data' => new AduanAspirasiResource($aduan),
        ], 201);
    }

    public function update(AduanAspirasi $aduanAspirasi, \App\Http\Requests\PublicSubmission\StoreAduanAspirasiRequest $request, \App\Services\FileUploadService $fileUpload): JsonResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('file_berkas_aduan')) {
            $data['file_berkas_aduan'] = $fileUpload->uploadMixed($request->file('file_berkas_aduan'), 'berkas_aduan');
            // Todo: unlink/delete old file if exists
        }
        
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        $aduanAspirasi->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'data' => new AduanAspirasiResource($aduanAspirasi),
        ]);
    }

    public function destroy(AduanAspirasi $aduanAspirasi): JsonResponse
    {
        // Todo: unlink file if exists
        $aduanAspirasi->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    public function export(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $status = $request->status;

        return Excel::download(
            new AduanAspirasiExport($from, $to, $status),
            'aduan-aspirasi-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
