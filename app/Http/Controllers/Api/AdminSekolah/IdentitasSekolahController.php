<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdentitasSekolahController extends Controller
{
    public function show(Request $request)
    {
        // Ambil NPSN dari token Sanctum user yang sedang login
        $npsn = $request->user()->npsn;

        $sekolah = DB::table('tb_sekolah')->where('npsn', $npsn)->first();
        if (!$sekolah) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        if ($sekolah->logo) {
            $sekolah->logo_url = url('/uploads/logo_sekolah/' . $sekolah->logo);
        } else {
            $sekolah->logo_url = null;
        }

        return response()->json([
            'success' => true,
            'data' => $sekolah
        ]);
    }

    public function update(Request $request)
    {
        // Ambil NPSN dari token Sanctum user yang sedang login
        $npsn = $request->user()->npsn;

        $sekolah = DB::table('tb_sekolah')->where('npsn', $npsn)->first();
        if (!$sekolah) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $dataUpdate = [
            'nama_sekolah' => $request->nama_sekolah,
            'alamat_sekolah' => $request->alamat_sekolah,
            'kepala_sekolah' => $request->kepala_sekolah,
            'jenjang' => $request->jenjang,
            'jenjang2' => $request->jenjang2 ?: $request->jenjang,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'negeri_or_swasta' => $request->negeri_or_swasta,
            'is_kemenag' => $request->is_kemenag ? 1 : 0,
        ];

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/logo_sekolah'), $filename);
            
            if ($sekolah->logo && file_exists(public_path('uploads/logo_sekolah/' . $sekolah->logo))) {
                @unlink(public_path('uploads/logo_sekolah/' . $sekolah->logo));
            }
            $dataUpdate['logo'] = $filename;
        }

        DB::table('tb_sekolah')->where('npsn', $npsn)->update($dataUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Identitas sekolah berhasil diperbarui'
        ]);
    }
}
