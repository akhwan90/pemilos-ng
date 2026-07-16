<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KandidatService;
use Exception;

class KandidatSekolahController extends Controller
{
    protected $kandidatService;

    public function __construct(KandidatService $kandidatService)
    {
        $this->kandidatService = $kandidatService;
    }

    public function index($npsn)
    {
        $data = $this->kandidatService->getAll($npsn);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($npsn, $id)
    {
        $data = $this->kandidatService->find($npsn, $id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, $npsn, $id)
    {
        $request->validate([
            'no' => 'nullable|integer',
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
            $this->kandidatService->update($npsn, $id, $request->all(), $request->file('photo'));
            return response()->json(['success' => true, 'message' => 'Data kandidat berhasil diperbarui']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroy($npsn, $id)
    {
        try {
            $this->kandidatService->delete($npsn, $id);
            return response()->json(['success' => true, 'message' => 'Kandidat berhasil dihapus']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}
