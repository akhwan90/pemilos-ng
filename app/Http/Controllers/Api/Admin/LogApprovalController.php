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
                'aproval_pindah_sekolah.disetujui_at',
                'aproval_pindah_sekolah.user_pengapprove'
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

    public function approve(Request $request, $id)
    {
        $user = auth()->user();

        // Ensure the approval record exists
        $approval = DB::table('aproval_pindah_sekolah')
            ->where('id', $id)
            ->where('status', 0) // only allow pending approvals
            ->first();

        if (!$approval) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data permohonan tidak ditemukan atau sudah diproses.'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Update the approval record
            DB::table('aproval_pindah_sekolah')
                ->where('id', $id)
                ->update([
                    'status' => 1,
                    'disetujui_at' => now(),
                    'user_pengapprove' => $user->username
                ]);

            // Update the student record
            DB::table('tb_siswa')
                ->where('nisn', $approval->nisn)
                ->update([
                    'npsn' => $approval->user_pemohon_npsn,
                    'kelas' => $approval->kelas_baru,
                    'nm_siswa' => $approval->nama_baru,
                    'jk' => $approval->jk_baru,
                    'difabel' => $approval->difabel_baru,
                    'no_wa' => $approval->nomor_wa_baru,
                    'email' => $approval->email_baru
                ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Permohonan pindah sekolah berhasil disetujui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server saat memproses persetujuan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
