<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KandidatService;
use App\Services\WaktuPemilihanService;
use Exception;

class KandidatController extends Controller
{
    protected $kandidatService;
    protected $waktuPemilihanService;

    public function __construct(KandidatService $kandidatService, WaktuPemilihanService $waktuPemilihanService)
    {
        $this->kandidatService = $kandidatService;
        $this->waktuPemilihanService = $waktuPemilihanService;
    }

    public function index(Request $request)
    {
        $data = $this->kandidatService->getAll($request->user()->npsn);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show(Request $request, $id)
    {
        $data = $this->kandidatService->find($request->user()->npsn, $id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $cek = $this->waktuPemilihanService->cekJadwalBuka('input_data_calon', $tahun, $npsn);
        if (!$cek['is_open']) {
            return response()->json(['success' => false, 'message' => 'Penambahan ditolak: ' . $cek['message']], 403);
        }

        $request->validate([
            'no' => 'required|integer',
            'nama' => 'required|string|max:100',
            'nisn' => 'required|string|max:32',
            'kampanye' => 'nullable|string|max:250',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'proker' => 'nullable|string',
            'pengalaman' => 'nullable|string',
            'prestasi' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $this->kandidatService->create($request->user(), $request->all(), $request->file('photo'), $request->user()->id);
            return response()->json(['success' => true, 'message' => 'Kandidat baru berhasil ditambahkan']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $cek = $this->waktuPemilihanService->cekJadwalBuka('input_data_calon', $tahun, $npsn);
        if (!$cek['is_open']) {
            return response()->json(['success' => false, 'message' => 'Pembaruan ditolak: ' . $cek['message']], 403);
        }

        $request->validate([
            'no' => 'required|integer',
            'kampanye' => 'nullable|string|max:250',
            'nisn' => 'required|string|max:32',
            'nama' => 'required|string|max:100',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'proker' => 'nullable|string',
            'pengalaman' => 'nullable|string',
            'prestasi' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $this->kandidatService->update($request->user(), $id, $request->all(), $request->file('photo'));
            return response()->json(['success' => true, 'message' => 'Data kandidat berhasil diperbarui']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroy(Request $request, $id)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $cek = $this->waktuPemilihanService->cekJadwalBuka('input_data_calon', $tahun, $npsn);
        if (!$cek['is_open']) {
            return response()->json(['success' => false, 'message' => 'Penghapusan ditolak: ' . $cek['message']], 403);
        }

        try {
            $this->kandidatService->delete($request->user(), $id);
            return response()->json(['success' => true, 'message' => 'Kandidat berhasil dihapus']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}
