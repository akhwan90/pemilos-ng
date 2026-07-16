<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KandidatService;
use Exception;

class KandidatController extends Controller
{
    protected $kandidatService;

    public function __construct(KandidatService $kandidatService)
    {
        $this->kandidatService = $kandidatService;
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
            $this->kandidatService->create($request->user()->npsn, $request->all(), $request->file('photo'), $request->user()->id);
            return response()->json(['success' => true, 'message' => 'Kandidat baru berhasil ditambahkan']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
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
            $this->kandidatService->update($request->user()->npsn, $id, $request->all(), $request->file('photo'));
            return response()->json(['success' => true, 'message' => 'Data kandidat berhasil diperbarui']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->kandidatService->delete($request->user()->npsn, $id);
            return response()->json(['success' => true, 'message' => 'Kandidat berhasil dihapus']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}
