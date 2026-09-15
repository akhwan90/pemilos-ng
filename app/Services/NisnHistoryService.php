<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class NisnHistoryService
{
    public function create($npsn, $nisn)
    {
        DB::table('nisn_histories')->insert([
            'npsn' => $npsn->npsn,
            'nisn' => $nisn
        ]);
    }
}