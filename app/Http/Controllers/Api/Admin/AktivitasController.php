<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AktivitasController extends Controller
{
    public function index(Request $request)
    {
        // Hanya boleh diakses Super Admin (Level 1)
        if ($request->user()->level != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $search = $request->query('search');

        $query = DB::table('activity')
            ->select('activity.*')
            ;

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('activity.username', 'like', "%{$search}%")
                  ->orWhere('activity.keterangan', 'like', "%{$search}%")
                  ;
            });
        }

        $siswa = $query->orderBy('activity.id', 'desc')
                      ->paginate(100);

        return response()->json($siswa);
    }
}
