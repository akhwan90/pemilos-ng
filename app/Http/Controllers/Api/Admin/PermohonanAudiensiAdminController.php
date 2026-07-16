<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\PermohonanAudiensiExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateStatusRequest;
use App\Http\Resources\PermohonanAudiensiResource;
use App\Models\PermohonanAudiensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PermohonanAudiensiAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PermohonanAudiensi::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('nama_instansi_kelompok_paguyuban_komunitas', 'like', "%{$request->search}%")
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
            'data' => PermohonanAudiensiResource::collection($data),
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

    public function show(PermohonanAudiensi $permohonanAudiensi): JsonResponse
    {
        $permohonanAudiensi->load('notes.admin');

        return response()->json([
            'success' => true,
            'data' => new PermohonanAudiensiResource($permohonanAudiensi),
        ]);
    }

    public function updateStatus(PermohonanAudiensi $permohonanAudiensi, UpdateStatusRequest $request): JsonResponse
    {
        $permohonanAudiensi->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui.',
            'data' => new PermohonanAudiensiResource($permohonanAudiensi),
        ]);
    }
    
    public function update(PermohonanAudiensi $permohonanAudiensi, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'nullable|string',
            'nomor_surat_ppid' => 'nullable|string|max:255',
            'tanggal_pelaksanaan' => 'nullable|date',
            'jam_pelaksanaan' => 'nullable|date_format:H:i',
            'alkap_penerima' => 'nullable|string|max:255',
        ]);
        
        $permohonanAudiensi->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'data' => new PermohonanAudiensiResource($permohonanAudiensi),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $year = $request->year ?? date('Y');

        // Statistik per rombongan (jumlah baris/record) per bulan
        // Untuk audiensi kita gunakan field created_at (atau jika ada field tanggal eksekusi yang lebih tepat)
        $rombonganStats = PermohonanAudiensi::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')->toArray();

        // Statistik per jumlah peserta (sum dari field jumlah_peserta) per bulan
        $pesertaStats = PermohonanAudiensi::selectRaw('MONTH(created_at) as bulan, SUM(jumlah_peserta) as total')
            ->whereYear('created_at', $year)
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

    public function generateDocument(PermohonanAudiensi $permohonanAudiensi, Request $request): JsonResponse
    {
        $request->validate([
            'jenis_dokumen' => 'required|in:daftar_hadir,ppid'
        ]);

        $documentType = $request->jenis_dokumen;
        $viewName = $documentType === 'daftar_hadir' ? 'pdf.daftar_hadir_audiensi' : 'pdf.ppid_audiensi';
        
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

        // Sesuaikan dengan data yang akan dirender
        // Permohonan Audiensi fields are mapped to view variables matching $tamu
        // to simplify template reuse
        $tamuMock = new \stdClass();
        $tamuMock->id = $permohonanAudiensi->id;
        $tamuMock->nama = $permohonanAudiensi->nama;
        $tamuMock->instansi = $permohonanAudiensi->nama_instansi_kelompok_paguyuban_komunitas;
        $tamuMock->alamat_instansi = '-'; 
        $tamuMock->nomor_hp_narahubung = $permohonanAudiensi->nomor_hp_narahubung;
        $tamuMock->email = '-';
        $tamuMock->nama_alkap = $permohonanAudiensi->alkap_penerima ?? 'Pimpinan / Anggota DPRD';
        $tamuMock->jumlah_peserta = $permohonanAudiensi->jumlah_peserta;
        $tamuMock->nama_jabatan_ketua_rombongan = $permohonanAudiensi->nama_jabatan_ketua_rombongan;
        $tamuMock->tujuan_kunjungan = $permohonanAudiensi->maksud_tujuan_audiensi;
        $tamuMock->nomor_surat_ppid = $permohonanAudiensi->nomor_surat_ppid;
        $tamuMock->materi = $permohonanAudiensi->maksud_tujuan_audiensi;
        $tamuMock->tanggal_berkunjung = $permohonanAudiensi->tanggal_pelaksanaan ?? date('Y-m-d');
        $tamuMock->jam_berkunjung = $permohonanAudiensi->jam_pelaksanaan ?? date('H:i');
        
        // Terjemahan nama hari (Senin, Selasa...)
        $hariArr = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $hariEn = date('l', strtotime($tamuMock->tanggal_berkunjung));
        $tamuMock->hari_berkunjung = $hariArr[$hariEn] ?? '-';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
            'tamu' => $tamuMock,
            'kopBase64' => $kopBase64,
            'penerimaKunjunganJabatan' => $penerimaKunjunganJabatan,
            'penerimaKunjunganNama' => $penerimaKunjunganNama,
            'penerimaKunjunganNip' => $penerimaKunjunganNip,
        ]);
        
        // Generate a unique filename
        $fileName = 'generated_audiensi_' . $documentType . '_' . $permohonanAudiensi->id . '_' . time() . '.pdf';
        $directory = 'templates'; 
        
        // Hapus file lama jika ada
        if ($documentType === 'daftar_hadir' && $permohonanAudiensi->file_daftar_hadir) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($permohonanAudiensi->file_daftar_hadir)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($permohonanAudiensi->file_daftar_hadir);
            }
        } else if ($documentType === 'ppid' && $permohonanAudiensi->file_dokumen_ppid) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($permohonanAudiensi->file_dokumen_ppid)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($permohonanAudiensi->file_dokumen_ppid);
            }
        }
        
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
        }
        
        $path = $directory . '/' . $fileName;
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $pdf->output());
        
        // Update model
        if ($documentType === 'daftar_hadir') {
            $permohonanAudiensi->update(['file_daftar_hadir' => $path]);
        } else if ($documentType === 'ppid') {
            $permohonanAudiensi->update(['file_dokumen_ppid' => $path]);
        }
        
        $url = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil di-generate.',
            'url' => $url,
            'data' => new PermohonanAudiensiResource($permohonanAudiensi)
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new PermohonanAudiensiExport($request->from, $request->to, $request->status),
            'permohonan-audiensi-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
