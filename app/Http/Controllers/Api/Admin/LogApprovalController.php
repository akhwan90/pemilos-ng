<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogApprovalController extends Controller
{
    /**
     * Tampilkan data log approval perpindahan siswa untuk tahun berjalan.
     */
    public function index(Request $request)
    {
        $query = DB::table('aproval_pindah_sekolah')
            ->leftJoin('tb_sekolah as sekolah_pemohon', 'aproval_pindah_sekolah.user_pemohon_npsn', '=', 'sekolah_pemohon.npsn')
            ->leftJoin('tb_sekolah as sekolah_tujuan', 'aproval_pindah_sekolah.npsn', '=', 'sekolah_tujuan.npsn')
            ->select(
                'aproval_pindah_sekolah.id',
                'aproval_pindah_sekolah.user_pemohon_npsn',
                'sekolah_pemohon.nama_sekolah as nama_sekolah_pemohon',
                'aproval_pindah_sekolah.nisn',
                'aproval_pindah_sekolah.npsn',
                'sekolah_tujuan.nama_sekolah as nama_sekolah_tujuan',
                'aproval_pindah_sekolah.nama_baru',
                'aproval_pindah_sekolah.jk_baru',
                'aproval_pindah_sekolah.kelas_baru',
                'aproval_pindah_sekolah.status',
                'aproval_pindah_sekolah.created_at',
                'aproval_pindah_sekolah.disetujui_at'
            )
            ->whereYear('aproval_pindah_sekolah.created_at', date('Y'));

        // Search logic
        if ($request->has('cari') && !empty($request->cari)) {
            $cari = $request->cari;
            $query->where(function($q) use ($cari) {
                $q->where('aproval_pindah_sekolah.nisn', 'like', "%{$cari}%")
                  ->orWhere('aproval_pindah_sekolah.nama_baru', 'like', "%{$cari}%");
            });
        }
        
        // Filter status (opsional jika dibutuhkan di frontend nanti)
        if ($request->has('status') && $request->status !== '') {
            $query->where('aproval_pindah_sekolah.status', $request->status);
        }

        $logs = $query->orderBy('aproval_pindah_sekolah.created_at', 'desc')->paginate(15);

        return response()->json($logs);
    }
}
