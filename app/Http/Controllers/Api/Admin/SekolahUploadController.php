<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class SekolahUploadController extends Controller
{
    // Mengambil list user (level 2) berdasarkan NPSN
    public function index(Request $request, $npsn)
    {
        $uploads = DB::table('upload_job')
            ->where('npsn', $npsn)
            ->whereYear('create_at', env('APP_TAHUN_AKTIF'))
            // Di CI3, level 2 adalah admin sekolah
            ->get();

        return response()->json([
            'success' => true,
            'data' => $uploads
        ]);
    }

    public function detil(Request $request, $npsn, $id) {
        $detilUpload = DB::table('upload_job_log')
        ->where('id_upload_job', $id)
        ->get();

        return response()->json([
            'success'=>true,
            'data'=>[
                'npsn'=>$npsn,
                'id'=>$id,
                'data'=>$detilUpload
            ]
        ]);
    }

    public function download(Request $request, $npsn, $id) {
        $job = DB::table('upload_job')
        ->where('id', $id)
        ->first();

        if (!$job || empty($job->file_excel)) {
            return response()->json([
                'message' => 'Data file tidak ditemukan'
            ], 404);
        }

        // 2. Tentukan path file di public_path
        $filePath = public_path('uploads/xlsx_temp/' . $job->file_excel);

        // 3. Pastikan fisik file ada di server
        if (!File::exists($filePath)) {
            return response()->json([
                'message' => 'Fisik file tidak ditemukan di server'
            ], 404);
        }

        // 4. Nama file yang akan muncul saat diunduh user
        $downloadName = 'data_sekolah_' . $npsn . '.xlsx';

        // 5. Kembalikan response download dengan header yang sesuai
        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Access-Control-Expose-Headers' => 'Content-Disposition',
        ]);
    }
}
