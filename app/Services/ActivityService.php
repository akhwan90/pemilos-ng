<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent;

class ActivityService
{
    /**
     * Log an activity to the database.
     *
     * @param string $username
     * @param string $idAktifitas
     * @param string|null $keterangan
     * @return Activity
     */
    public function logActivity($username, $idAktifitas, $keterangan = null)
    {
        $agent = new Agent();

        // Jika keterangan kosong, ambil default keterangan dari config/aktivitas.php
        if (empty($keterangan)) {
            $keterangan = config("aktivitas.{$idAktifitas}");
        }

        $activity = new Activity();
        $activity->username = $username;
        $activity->id_aktifitas = $idAktifitas;
        $activity->keterangan = substr($keterangan, 0, 490); // Limit keterangan ke 255 karakter
        $activity->ip = Request::ip();
        
        $browser = $agent->browser();
        $activity->browser = $browser;
        $activity->browser_versi = $agent->version($browser);
        
        $activity->is_mobile = $agent->isMobile();
        $activity->mobile_ver = $agent->isMobile() ? $agent->device() : null;
        $activity->os = $agent->platform();
        
        $activity->waktu = now();

        $activity->save();

        return $activity;
    }
}
