<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataSekolahController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));
        $search = $request->query('cari');
        $orderBy = $request->query('order_by');
        $filterBy = $request->query('filter_by');

        $query = DB::table('tb_sekolah')
            ->select([
                'tb_sekolah.*',
                DB::raw("(SELECT COUNT(tb_pilihan.id) FROM tb_pilihan WHERE tb_pilihan.npsn = tb_sekolah.npsn AND tb_pilihan.tahun = '{$tahun}') AS jml_kandidat"),
                DB::raw("(SELECT COUNT(tb_kelas.kd_kelas) FROM tb_kelas WHERE tb_kelas.npsn = tb_sekolah.npsn AND tb_kelas.is_hapus = 0) AS jml_tps"),
                DB::raw("(SELECT COUNT(tb_tps_setting.id) FROM tb_tps_setting WHERE tb_tps_setting.npsn = tb_sekolah.npsn AND tb_tps_setting.is_generate_token = 1 AND tb_tps_setting.tahun = '{$tahun}') AS jml_tps_generate_token"),
                DB::raw("(SELECT COUNT(tb_siswa_tps.id) FROM tb_siswa_tps WHERE tb_siswa_tps.npsn = tb_sekolah.npsn AND tb_siswa_tps.tahun = '{$tahun}') AS jml_dpt"),
                DB::raw("(SELECT COUNT(tb_siswa_tps.id) FROM tb_siswa_tps WHERE tb_siswa_tps.npsn = tb_sekolah.npsn AND tb_siswa_tps.tahun = '{$tahun}' AND tb_siswa_tps.pilihan IS NOT NULL) AS jml_memilih"),
                DB::raw("(SELECT COUNT(tb_siswa.id) FROM tb_siswa WHERE tb_siswa.npsn = tb_sekolah.npsn) AS jml_siswa")
            ])
            ->where('is_delete', 0);

        // Filter: Search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('tb_sekolah.nama_sekolah', 'like', "%{$search}%")
                  ->orWhere('tb_sekolah.alamat_sekolah', 'like', "%{$search}%")
                  ->orWhere('tb_sekolah.jenjang', 'like', "%{$search}%")
                  ->orWhere('tb_sekolah.npsn', 'like', "%{$search}%");
            });
        }

        // Filter: Jenjang/Tingkat
        if (!empty($filterBy)) {
            if ($filterBy === 'kemenag') {
                $query->where('tb_sekolah.is_kemenag', 1);
            } elseif ($filterBy === 'smp') {
                $query->where('tb_sekolah.jenjang', 'smp');
            } elseif ($filterBy === 'sma') {
                $query->whereIn('tb_sekolah.jenjang', ['sma', 'smk']);
            }
        }

        // Filter: Sorting
        if (!empty($orderBy)) {
            switch ($orderBy) {
                case 'jml_siswa_desc': $query->orderBy('jml_siswa', 'desc'); break;
                case 'jml_siswa_asc': $query->orderBy('jml_siswa', 'asc'); break;
                case 'jml_dpt_desc': $query->orderBy('jml_dpt', 'desc'); break;
                case 'jml_dpt_asc': $query->orderBy('jml_dpt', 'asc'); break;
                case 'jml_tps_desc': $query->orderBy('jml_tps', 'desc'); break;
                case 'jml_tps_asc': $query->orderBy('jml_tps', 'asc'); break;
                case 'jml_kandidat_desc': $query->orderBy('jml_kandidat', 'desc'); break;
                case 'jml_kandidat_asc': $query->orderBy('jml_kandidat', 'asc'); break;
                case 'jenjang_asc': $query->orderBy('jenjang', 'asc'); break;
                case 'jenjang_desc': $query->orderBy('jenjang', 'desc'); break;
                case 'nama_sekolah_asc': $query->orderBy('nama_sekolah', 'asc'); break;
                case 'nama_sekolah_desc': $query->orderBy('nama_sekolah', 'desc'); break;
                case 'npsn_asc': $query->orderBy('npsn', 'asc'); break;
                case 'npsn_desc': $query->orderBy('npsn', 'desc'); break;
                case 'persentase_dpt_desc':
                    $query->orderByRaw('(jml_dpt/jml_siswa) DESC')->orderBy('jml_siswa', 'desc');
                    break;
                case 'persentase_memilih_desc':
                    $query->orderByRaw('(jml_memilih/jml_dpt) DESC')->orderBy('jml_dpt', 'desc');
                    break;
                default: $query->orderBy('nama_sekolah', 'asc'); break;
            }
        } else {
            $query->orderBy('nama_sekolah', 'asc');
        }

        $sekolahs = $query->get()->map(function ($item) {
            $item->persentase_dpt = 0;
            $item->persentase_memilih = 0;
            $item->persentase_belum_memilih = 0;
            $item->is_over_capacity = false;

            if ($item->jml_siswa > 0) {
                $item->persentase_dpt = round((($item->jml_dpt / $item->jml_siswa) * 100), 2);
            }
            if ($item->jml_memilih > 0 && $item->jml_dpt > 0) {
                $item->persentase_memilih = round((($item->jml_memilih / $item->jml_dpt) * 100), 2);
                $item->persentase_belum_memilih = 100 - $item->persentase_memilih;
            }
            if ($item->jml_dpt > $item->jml_siswa) {
                $item->is_over_capacity = true;
            }
            return $item;
        });

        return response()->json($sekolahs);

    }

    public function store(Request $request)
    {
        $request->validate([
            'npsn' => 'required|numeric|unique:tb_sekolah,npsn',
            'nama_sekolah' => 'required|string|max:50',
            'jenjang' => 'required|string|max:5',
            'jenjang2' => 'required|string|max:10',
        ]);

        $dataInsert = [
            'npsn' => $request->npsn,
            'nama_sekolah' => $request->nama_sekolah,
            'alamat_sekolah' => $request->alamat_sekolah,
            'kepala_sekolah' => $request->kepala_sekolah,
            'jenjang' => $request->jenjang,
            'jenjang2' => $request->jenjang2 ?: $request->jenjang,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'negeri_or_swasta' => $request->negeri_or_swasta,
            'is_kemenag' => $request->is_kemenag ? 1 : 0,
            'is_delete' => 0,
            'is_jadwal_sendiri' => 0
        ];

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/logo_sekolah'), $filename);
            $dataInsert['logo'] = $filename;
        }

        DB::table('tb_sekolah')->insert($dataInsert);

        return response()->json([
            'success' => true,
            'message' => 'Data sekolah berhasil ditambahkan'
        ]);
    }

    public function show($npsn)
    {
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

    public function update(Request $request, $npsn)
    {
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
            // Simpan ke direktori yang sama dg CI3 lama
            $file->move(public_path('uploads/logo_sekolah'), $filename);

            if ($sekolah->logo && file_exists(public_path('uploads/logo_sekolah/' . $sekolah->logo))) {
                @unlink(public_path('uploads/logo_sekolah/' . $sekolah->logo));
            }
            $dataUpdate['logo'] = $filename;
        }

        DB::table('tb_sekolah')->where('npsn', $npsn)->update($dataUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Data sekolah berhasil diperbarui'
        ]);
    }
}
