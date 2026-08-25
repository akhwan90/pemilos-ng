<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApprovalPindahSekolah; // Assuming this model exists, if not we use DB facade

class ApprovalPindahController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $npsn = $user->npsn;
        $tahunSekarang = date('Y');

        $data = DB::table('aproval_pindah_sekolah')
            ->select(
                'aproval_pindah_sekolah.*', 
                'tb_siswa.nm_siswa as nama_siswa_asal',
                'sekolah_tujuan.nama_sekolah as nama_sekolah_tujuan',
                'sekolah_asal.nama_sekolah as nama_sekolah_asal'
            )
            ->leftJoin('tb_siswa', 'aproval_pindah_sekolah.nisn', '=', 'tb_siswa.nisn')
            ->leftJoin('tb_sekolah AS sekolah_tujuan', 'aproval_pindah_sekolah.npsn', '=', 'sekolah_tujuan.npsn')
            ->leftJoin('tb_sekolah AS sekolah_asal', 'aproval_pindah_sekolah.user_pemohon_npsn', '=', 'sekolah_asal.npsn')
            ->where(function ($query) {
                $query->where('aproval_pindah_sekolah.user_pemohon_npsn', auth()->user()->npsn)
                ->orWhere('aproval_pindah_sekolah.npsn', auth()->user()->npsn);
            })
            ->whereYear('aproval_pindah_sekolah.created_at', $tahunSekarang)
            ->orderBy('aproval_pindah_sekolah.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function approve(Request $request, $id, ActivityService $activityService)
    {
        $user = auth()->user();
        $npsn = $user->npsn;

        // Ensure the approval record exists and belongs to the currently logged in school
        $approval = DB::table('aproval_pindah_sekolah')
            ->where('id', $id)
            ->where('npsn', $npsn)
            ->where('status', 0) // only allow pending approvals
            ->first();

        if (!$approval) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data permohonan tidak ditemukan atau tidak valid.'
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

            $activityService->logActivity($user->username, 24, json_encode([
                'nisn' => $approval->nisn,
                'npsn_sekolah_asal' => $npsn,
                'npsn_sekolah_baru' => $approval->user_pemohon_npsn
            ]));

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
