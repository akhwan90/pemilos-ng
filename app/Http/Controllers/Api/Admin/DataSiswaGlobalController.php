<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataSiswaGlobalController extends Controller
{
    public function index(Request $request)
    {
        // Hanya boleh diakses Super Admin (Level 1)
        if ($request->user()->level != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tahun = env('TAHUN_AKTIF', date('Y'));
        $search = $request->query('cari');
        $filterNpsn = $request->query('npsn');
        $queryString = $request->query('query');

        $query = DB::table('tb_siswa')
            ->leftJoin('tb_sekolah', 'tb_siswa.npsn', '=', 'tb_sekolah.npsn')
            ->select('tb_siswa.*', 'tb_sekolah.nama_sekolah')
            // ->where('tb_siswa.status', 1)
            ;

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('tb_siswa.nm_siswa', 'like', "%{$search}%")
                  ->orWhere('tb_siswa.nisn', 'like', "%{$search}%")
                  ->orWhere('tb_siswa.kelas', 'like', "%{$search}%");
            });
        }

        if (!empty($filterNpsn)) {
            $query->where('tb_siswa.npsn', $filterNpsn);
        }

        $allowedColumns = [
            'tb_siswa.nisn',
            'tb_siswa.nm_siswa',
            'tb_siswa.jk',
            'tb_siswa.kelas',
            'tb_siswa.difabel',
            'tb_siswa.status',
            'tb_siswa.tahun',
            'tb_siswa.create_at',
            'tb_siswa.hapus_time',
            'tb_sekolah.nama_sekolah',
        ];

        if (!empty($queryString)) {
            // Regex memecah: (nama_kolom) (operator) '(nilai)'
            $pattern = '/^([a-zA-Z0-9_\.]+)\s+(like|=|>|<|>=|<=)\s+\'([^\']*)\'$/i';

            if (preg_match($pattern, trim($queryString), $matches)) {
                $column   = $matches[1];
                $operator = strtolower($matches[2]);
                $value    = $matches[3];

                if (in_array($column, $allowedColumns)) {
                    $query->where($column, $operator, $value);
                }
            }
        }

        $siswa = $query->orderBy('tb_sekolah.nama_sekolah', 'asc')
                      ->orderBy('tb_siswa.kelas', 'asc')
                      ->orderBy('tb_siswa.nm_siswa', 'asc')
                      ->paginate(50);

        return response()->json($siswa);
    }

    public function update(Request $request, $id) {
        // return response()->json(['nisn'=>$request->nisn, 'length'=>strlen($request->nisn)]);

        $validated  = $request->validate([
            'nisn' => 'required|digits_between:6,14|unique:tb_siswa,nisn,' . $id,
            'difabel' => 'nullable|in:0,1,2,3,4,5|numeric',
            'hapus_time' => 'nullable|date_format:Y-m-d H:i:s',
            'jk' => 'required|in:1,2|numeric',
            'kelas' => 'nullable',
            'nm_siswa' => 'required|min:3',
            'npsn'=> 'nullable|min:4|max:16|numeric',
            'status'=> 'required|in:0,1,2,3|numeric',
            'tahun'=>'required',
        ]);
        

        $siswa = DB::table('tb_siswa')
        ->where('id', $id)
        ->update($validated);

        return response()->json(['message'=>'Data berhasil diupdate']);
    }

    public function destroy(Request $request, $id)
    {
        // Hanya boleh diakses Super Admin (Level 1)
        if ($request->user()->level != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $deleted = DB::table('tb_siswa')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Data siswa berhasil dihapus secara permanen.']);
        }

        return response()->json(['message' => 'Data siswa tidak ditemukan.'], 404);
    }
}
