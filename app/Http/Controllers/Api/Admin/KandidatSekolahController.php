<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KandidatSekolahController extends Controller
{
    // Get list kandidat
    public function index(Request $request, $npsn = null)
    {
        if ($request->user()->level == 2) {
            $npsn = $request->user()->npsn;
        }

        $tahun = env('TAHUN_AKTIF', date('Y'));

        $kandidat = DB::table('tb_pilihan')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->orderBy('no', 'asc') // Urutkan berdasarkan no urut kandidat
            ->get();

        // Tambahkan base URL untuk photo jika ada
        foreach ($kandidat as $k) {
            if ($k->photo) {
                // Asumsi photo disimpan di public/uploads/kandidat/ atau sesuai legacy
                $k->photo_url = url('/uploads/kandidat/' . $k->photo);
            } else {
                $k->photo_url = null;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $kandidat
        ]);
    }

    // Tambah Kandidat (Khusus untuk level Admin Sekolah, tapi disatukan)
    public function store(Request $request, $npsn = null)
    {
        if ($request->user()->level == 2) {
            $npsn = $request->user()->npsn;
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // max 2MB
        ]);

        $tahun = env('TAHUN_AKTIF', date('Y'));

        // Cek apakah No Urut sudah dipakai di sekolah tsb pada tahun yang sama
        $exists = DB::table('tb_pilihan')
            ->where('npsn', $npsn)
            ->where('tahun', $tahun)
            ->where('no', $request->no)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'No urut kandidat sudah dipakai!'], 400);
        }

        $dataInsert = [
            'npsn' => $npsn,
            'tahun' => $tahun,
            'no' => $request->no,
            'nama' => $request->nama,
            'nisn' => $request->nisn,
            'kampanye' => $request->kampanye,
            'visi' => $request->visi,
            'misi' => $request->misi,
            'proker' => $request->proker,
            'pengalaman' => $request->pengalaman,
            'prestasi' => $request->prestasi
            // 'waktu_input' => now(), // Di tabel tb_pilihan tidak ada kolom waktu_input
            // 'id_user' => $request->user()->id 
        ];

        // Handle upload file
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kandidat'), $filename);
            $dataInsert['photo'] = $filename;
        }

        DB::table('tb_pilihan')->insert($dataInsert);

        return response()->json([
            'success' => true,
            'message' => 'Kandidat baru berhasil ditambahkan'
        ]);
    }

    // Mengambil 1 detail kandidat untuk di-edit
    public function show(Request $request, $npsn = null, $id = null)
    {
        // Handle beda pola parameter dari Router:
        // Super Admin : /admin/data-sekolah/{npsn}/kandidat/{id} -> $npsn terisi, $id terisi
        // Admin Sek.  : /admin-sekolah/kandidat/{id}             -> $npsn diisi oleh nilai {id} dari URL, $id = null
        
        if ($request->user()->level == 2) {
            $id = $npsn; // Geser parameter dari npsn ke id
            $npsn = $request->user()->npsn;
        }

        $kandidat = DB::table('tb_pilihan')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->first();

        if (!$kandidat) {
            return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan'], 404);
        }

        if ($kandidat->photo) {
            $kandidat->photo_url = url('/uploads/kandidat/' . $kandidat->photo);
        }

        return response()->json([
            'success' => true,
            'data' => $kandidat
        ]);
    }

    // Update kandidat
    public function update(Request $request, $npsn = null, $id = null)
    {
        if ($request->user()->level == 2) {
            $id = $npsn; // Geser parameter
            $npsn = $request->user()->npsn;
        }

        $request->validate([
            'kampanye' => 'nullable|string|max:250',
            'nisn' => 'required|string|max:32',
            'nama' => 'required|string|max:100',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'proker' => 'nullable|string',
            'pengalaman' => 'nullable|string',
            'prestasi' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // max 2MB
        ]);

        $kandidat = DB::table('tb_pilihan')->where('id', $id)->where('npsn', $npsn)->first();

        if (!$kandidat) {
            return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan'], 404);
        }

        $dataUpdate = [
            'kampanye' => $request->kampanye,
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'visi' => $request->visi,
            'misi' => $request->misi,
            'proker' => $request->proker,
            'pengalaman' => $request->pengalaman,
            'prestasi' => $request->prestasi,
        ];

        // Handle upload file
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Kita simpan ke public/uploads/kandidat/ agar kompatibel dengan URL lama
            $file->move(public_path('uploads/kandidat'), $filename);
            
            // Hapus file lama jika ada (opsional, tergantung kebijakan storage)
            if ($kandidat->photo && file_exists(public_path('uploads/kandidat/' . $kandidat->photo))) {
                @unlink(public_path('uploads/kandidat/' . $kandidat->photo));
            }

            $dataUpdate['photo'] = $filename;
        }

        DB::table('tb_pilihan')->where('id', $id)->update($dataUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Data kandidat berhasil diperbarui'
        ]);
    }

    // Hapus kandidat
    public function destroy(Request $request, $npsn = null, $id = null)
    {
        if ($request->user()->level == 2) {
            $id = $npsn; // Geser parameter
            $npsn = $request->user()->npsn;
        }

        $kandidat = DB::table('tb_pilihan')->where('id', $id)->where('npsn', $npsn)->first();

        if (!$kandidat) {
            return response()->json(['success' => false, 'message' => 'Kandidat tidak ditemukan'], 404);
        }

        // Hapus file photo jika ada
        if ($kandidat->photo && file_exists(public_path('uploads/kandidat/' . $kandidat->photo))) {
            @unlink(public_path('uploads/kandidat/' . $kandidat->photo));
        }

        DB::table('tb_pilihan')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kandidat berhasil dihapus'
        ]);
    }
}
