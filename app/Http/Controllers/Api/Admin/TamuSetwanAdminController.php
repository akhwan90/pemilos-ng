<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\TamuSetwanExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStatusRequest;
use App\Http\Resources\TamuSetwanResource;
use App\Models\TamuSetwan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TamuSetwanAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TamuSetwan::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('instansi', 'like', "%{$request->search}%")
                  ->orWhere('nomor_hp_narahubung', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $sortField = $request->sort_field ?? 'created_at';
        $sortDir = $request->sort_dir ?? 'desc';
        $query->orderBy($sortField, $sortDir);

        $perPage = min((int) ($request->per_page ?? 15), 100);
        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => TamuSetwanResource::collection($data),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function show(TamuSetwan $tamuSetwan): JsonResponse
    {
        $tamuSetwan->load('notes.admin');

        return response()->json([
            'success' => true,
            'data' => new TamuSetwanResource($tamuSetwan),
        ]);
    }

    public function updateStatus(TamuSetwan $tamuSetwan, UpdateStatusRequest $request): JsonResponse
    {
        $tamuSetwan->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui.',
            'data' => new TamuSetwanResource($tamuSetwan),
        ]);
    }

    public function store(\App\Http\Requests\PublicSubmission\StoreTamuSetwanRequest $request, \App\Services\FileUploadService $fileUpload): JsonResponse
    {
        $data = $request->validated();
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        if ($request->hasFile('file_surat_kunjungan')) {
            $data['file_surat_kunjungan'] = $fileUpload->uploadDocument($request->file('file_surat_kunjungan'), 'surat_kunjungan');
        }
        if ($request->hasFile('file_spt')) {
            $data['file_spt'] = $fileUpload->uploadDocument($request->file('file_spt'), 'spt');
        }
        if ($request->hasFile('file_bukti_menginap')) {
            $data['file_bukti_menginap'] = $fileUpload->uploadMixed($request->file('file_bukti_menginap'), 'bukti_menginap');
        }

        $data['status'] = TamuSetwan::STATUS_BARU;
        $tamuSetwan = TamuSetwan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan.',
            'data' => new TamuSetwanResource($tamuSetwan),
        ], 201);
    }

    public function update(TamuSetwan $tamuSetwan, \App\Http\Requests\PublicSubmission\StoreTamuSetwanRequest $request, \App\Services\FileUploadService $fileUpload): JsonResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('file_surat_kunjungan')) {
            $data['file_surat_kunjungan'] = $fileUpload->uploadDocument($request->file('file_surat_kunjungan'), 'surat_kunjungan');
        }
        if ($request->hasFile('file_spt')) {
            $data['file_spt'] = $fileUpload->uploadDocument($request->file('file_spt'), 'spt');
        }
        if ($request->hasFile('file_bukti_menginap')) {
            $data['file_bukti_menginap'] = $fileUpload->uploadMixed($request->file('file_bukti_menginap'), 'bukti_menginap');
        }
        
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        $tamuSetwan->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'data' => new TamuSetwanResource($tamuSetwan),
        ]);
    }

    public function destroy(TamuSetwan $tamuSetwan): JsonResponse
    {
        $tamuSetwan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $year = $request->year ?? date('Y');

        // Statistik per rombongan (jumlah baris/record) per bulan
        $rombonganStats = TamuSetwan::selectRaw('MONTH(tanggal_berkunjung) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_berkunjung', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        // Statistik per jumlah peserta (sum dari field jumlah_peserta) per bulan
        $pesertaStats = TamuSetwan::selectRaw('MONTH(tanggal_berkunjung) as bulan, SUM(jumlah_peserta) as total')
            ->whereYear('tanggal_berkunjung', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        // Pastikan semua bulan (1-12) ada di array, kalau kosong diisi 0
        $formattedRombongan = [];
        $formattedPeserta = [];
        for ($i = 1; $i <= 12; $i++) {
            $formattedRombongan[] = isset($rombonganStats[$i]) ? (int) $rombonganStats[$i] : 0;
            $formattedPeserta[] = isset($pesertaStats[$i]) ? (int) $pesertaStats[$i] : 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'rombongan' => $formattedRombongan,
                'peserta' => $formattedPeserta,
            ]
        ]);
    }

    public function generateDocument(TamuSetwan $tamuSetwan, Request $request): JsonResponse
    {
        $request->validate([
            'jenis_dokumen' => 'required|in:daftar_hadir,ppid'
        ]);

        $documentType = $request->jenis_dokumen;
        $viewName = $documentType === 'daftar_hadir' ? 'pdf.daftar_hadir' : 'pdf.ppid';
        
        $kopPath = public_path('kop_setwan.png');
        $kopBase64 = null;
        if (file_exists($kopPath)) {
            $kopData = file_get_contents($kopPath);
            $kopType = pathinfo($kopPath, PATHINFO_EXTENSION);
            $kopBase64 = 'data:image/' . $kopType . ';base64,' . base64_encode($kopData);
        }
        
        // Ambil data pengaturan
        $penerimaKunjunganJabatan = \App\Models\Setting::getVal('penerima_kunjungan_jabatan', 'Penerima Kunjungan,');
        $penerimaKunjunganNama = \App\Models\Setting::getVal('penerima_kunjungan_nama', '');
        $penerimaKunjunganNip = \App\Models\Setting::getVal('penerima_kunjungan_nip', '');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
            'tamu' => $tamuSetwan,
            'kopBase64' => $kopBase64,
            'penerimaKunjunganJabatan' => $penerimaKunjunganJabatan,
            'penerimaKunjunganNama' => $penerimaKunjunganNama,
            'penerimaKunjunganNip' => $penerimaKunjunganNip,
        ]);
        
        // Generate a unique filename
        $fileName = 'generated_' . $documentType . '_' . $tamuSetwan->id . '_' . time() . '.pdf';
        $directory = 'templates'; // Disk 'public' root is already storage/app/public
        
        // Hapus file lama jika ada
        if ($documentType === 'daftar_hadir' && $tamuSetwan->file_daftar_hadir) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($tamuSetwan->file_daftar_hadir)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($tamuSetwan->file_daftar_hadir);
            }
        } else if ($documentType === 'ppid' && $tamuSetwan->file_dokumen_ppid) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($tamuSetwan->file_dokumen_ppid)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($tamuSetwan->file_dokumen_ppid);
            }
        }
        
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
        }
        
        $path = $directory . '/' . $fileName;
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $pdf->output());
        
        // Update model in database so the generated file is persisted
        if ($documentType === 'daftar_hadir') {
            $tamuSetwan->update(['file_daftar_hadir' => $path]);
        } else if ($documentType === 'ppid') {
            $tamuSetwan->update(['file_dokumen_ppid' => $path]);
        }
        
        $url = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil di-generate.',
            'url' => $url,
            'data' => new TamuSetwanResource($tamuSetwan)
        ]);
    }

    public function uploadBerkas(TamuSetwan $tamuSetwan, Request $request, \App\Services\FileUploadService $fileUpload): JsonResponse
    {
        $request->validate([
            'jenis_dokumen' => 'required|in:surat_kunjungan,spt,bukti_menginap,daftar_hadir_ttd,foto_kunjungan',
            'file' => 'required|file|max:5120|mimes:pdf,jpeg,jpg,png',
        ]);

        $field = 'file_' . $request->jenis_dokumen; 
        
        // Hapus file lama jika ada sebelum menimpa
        if ($tamuSetwan->$field) {
            $oldPath = str_replace(url('storage') . '/', '', $tamuSetwan->$field);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
        }

        $path = $fileUpload->uploadMixed($request->file('file'), $request->jenis_dokumen);
        
        $tamuSetwan->update([$field => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Berkas berhasil diunggah.',
            'data' => new TamuSetwanResource($tamuSetwan),
        ]);
    }

    public function hapusBerkas(TamuSetwan $tamuSetwan, Request $request): JsonResponse
    {
        $request->validate([
            'jenis_dokumen' => 'required|in:surat_kunjungan,spt,bukti_menginap,daftar_hadir_ttd,foto_kunjungan',
        ]);

        $field = 'file_' . $request->jenis_dokumen; 

        if ($tamuSetwan->$field) {
            $oldPath = str_replace(url('storage') . '/', '', $tamuSetwan->$field);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            $tamuSetwan->update([$field => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berkas berhasil dihapus.',
            'data' => new TamuSetwanResource($tamuSetwan),
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new TamuSetwanExport($request->from, $request->to, $request->status),
            'tamu-setwan-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
