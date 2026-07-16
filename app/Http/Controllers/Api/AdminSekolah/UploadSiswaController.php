<?php

namespace App\Http\Controllers\Api\AdminSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\ImportSiswaJob;
use Illuminate\Support\Str;

class UploadSiswaController extends Controller
{
    public function history(Request $request)
    {
        $username = $request->user()->username;
        $tahun = env('TAHUN_AKTIF', date('Y'));

        $history = DB::table('upload_job')
            ->where('username', $username)
            ->whereYear('create_at', $tahun)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx|max:2048'
        ]);

        $user = $request->user();

        // Cek apakah sekolah sudah mulai memilih (dari tb_siswa_tps)
        $cekSudahMemilih = DB::table('tb_siswa_tps')
            ->where('npsn', $user->npsn)
            ->whereNotNull('pilihan')
            ->where('tahun', env('TAHUN_AKTIF', date('Y')))
            ->count();

        if ($cekSudahMemilih > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Sekolah ini sudah melakukan pemilihan. Data sudah tidak bisa ditambah/diedit via upload.'
            ], 403);
        }

        $file = $request->file('file_excel');
        $filename = time() . '_' . Str::random(10) . '.xlsx';
        
        // Pindahkan ke folder public/uploads/xlsx_temp
        $file->move(public_path('uploads/xlsx_temp'), $filename);

        // Insert ke tabel upload_job (antrean)
        $jobId = DB::table('upload_job')->insertGetId([
            'username' => $user->username,
            'file_excel' => $filename,
            'create_at' => date('Y-m-d H:i:s'),
            'is_selesai' => 0,
            'npsn' => $user->npsn,
            'pid' => 0 // Legacy field, diisi 0 karena sekarang pakai Laravel Queue
        ]);

        // Dispatch Job ke Queue Laravel
        ImportSiswaJob::dispatch($jobId, $user->username, $user->npsn, $filename);

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diupload dan masuk ke antrean pemrosesan'
        ]);
    }

    public function logs($id)
    {
        $logs = DB::table('upload_job_log')
            ->where('id_upload_job', $id)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
