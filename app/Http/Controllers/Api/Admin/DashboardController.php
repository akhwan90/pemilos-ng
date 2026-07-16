<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AduanAspirasi;
use App\Models\PermohonanAudiensi;
use App\Models\TamuDprd;
use App\Models\TamuSetwan;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalAduan = AduanAspirasi::count();
        $totalTamuSetwan = TamuSetwan::count();
        $totalTamuDprd = TamuDprd::count();
        $totalAudiensi = PermohonanAudiensi::count();

        $aduanBaru = AduanAspirasi::where('status', 'baru')->count();
        $setwanBaru = TamuSetwan::where('status', 'baru')->count();
        $dprdBaru = TamuDprd::where('status', 'baru')->count();
        $audiensiBaru = PermohonanAudiensi::where('status', 'baru')->count();

        // Monthly trends (last 6 months)
        $months = collect(range(5, 0))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        });

        $trendAduan = $months->map(function ($month) {
            return AduanAspirasi::where('created_at', 'like', "$month%")->count();
        });

        $trendKunjungan = collect();
        foreach ($months as $month) {
            $trendKunjungan->push(
                TamuSetwan::where('created_at', 'like', "$month%")->count() +
                TamuDprd::where('created_at', 'like', "$month%")->count()
            );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'totals' => [
                    'total_aduan' => $totalAduan,
                    'total_tamu_setwan' => $totalTamuSetwan,
                    'total_tamu_dprd' => $totalTamuDprd,
                    'total_audiensi' => $totalAudiensi,
                    'total_semua' => $totalAduan + $totalTamuSetwan + $totalTamuDprd + $totalAudiensi,
                ],
                'new_submissions' => [
                    'aduan_baru' => $aduanBaru,
                    'setwan_baru' => $setwanBaru,
                    'dprd_baru' => $dprdBaru,
                    'audiensi_baru' => $audiensiBaru,
                    'total_baru' => $aduanBaru + $setwanBaru + $dprdBaru + $audiensiBaru,
                ],
                'trends' => [
                    'months' => $months,
                    'aduan' => $trendAduan,
                    'kunjungan' => $trendKunjungan,
                ],
            ],
        ]);
    }
}
