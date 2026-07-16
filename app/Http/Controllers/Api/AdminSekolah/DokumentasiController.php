<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DokumentasiController extends Controller
{
    public function index(Request $request)
    {
        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $docs = DB::table('tb_dokumentasi')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($docs as $d) {
            $d->foto_url = $d->foto ? url('/uploads/dokumentasi/' . $d->foto) : null;
        }

        return response()->json([
            'success' => true,
            'data' => $docs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120' // Maks 5MB per foto
        ]);

        $npsn = $request->user()->npsn;
        $tahun = env('TAHUN_AKTIF', date('Y'));
        
        $file = $request->file('foto');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Simpan ke direktori legacy public/uploads/dokumentasi/
        $file->move(public_path('uploads/dokumentasi'), $filename);

        $id = DB::table('tb_dokumentasi')->insertGetId([
            'npsn' => $npsn,
            'tahun' => $tahun,
            'foto' => $filename,
            'created_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto dokumentasi berhasil diunggah',
            'data' => [
                'id' => $id,
                'foto_url' => url('/uploads/dokumentasi/' . $filename)
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $npsn = $request->user()->npsn;

        $doc = DB::table('tb_dokumentasi')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->first();

        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Dokumentasi tidak ditemukan'], 404);
        }

        // Hapus fisik gambar
        if ($doc->foto && file_exists(public_path('uploads/dokumentasi/' . $doc->foto))) {
            @unlink(public_path('uploads/dokumentasi/' . $doc->foto));
        }

        DB::table('tb_dokumentasi')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto dokumentasi berhasil dihapus'
        ]);
    }
}
