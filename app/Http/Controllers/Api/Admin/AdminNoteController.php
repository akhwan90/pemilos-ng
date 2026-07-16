<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNoteRequest;
use App\Http\Resources\AdminNoteResource;
use App\Models\AdminNote;
use App\Models\AduanAspirasi;
use App\Models\PermohonanAudiensi;
use App\Models\TamuDprd;
use App\Models\TamuSetwan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNoteController extends Controller
{
    public function index(Request $request, string $type, int $id): JsonResponse
    {
        $model = $this->resolveModel($type, $id);

        return response()->json([
            'success' => true,
            'data' => AdminNoteResource::collection($model->notes()->with('admin')->get()),
        ]);
    }

    public function store(StoreNoteRequest $request, string $type, int $id): JsonResponse
    {
        $model = $this->resolveModel($type, $id);

        $note = $model->notes()->create([
            'admin_id' => $request->user()->id,
            'note' => $request->note,
        ]);

        $note->load('admin');

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil ditambahkan.',
            'data' => new AdminNoteResource($note),
        ], 201);
    }

    public function destroy(string $type, int $id, AdminNote $adminNote): JsonResponse
    {
        $adminNote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil dihapus.',
        ]);
    }

    private function resolveModel(string $type, int $id): AduanAspirasi|TamuSetwan|TamuDprd|PermohonanAudiensi
    {
        return match ($type) {
            'aduan' => AduanAspirasi::findOrFail($id),
            'setwan' => TamuSetwan::findOrFail($id),
            'dprd' => TamuDprd::findOrFail($id),
            'audiensi' => PermohonanAudiensi::findOrFail($id),
            default => abort(404, 'Tipe data tidak valid.'),
        };
    }
}
