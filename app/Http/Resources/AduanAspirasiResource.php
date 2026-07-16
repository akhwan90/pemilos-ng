<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AduanAspirasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nik' => $this->nik,
            'alamat' => $this->alamat,
            'pekerjaan' => $this->pekerjaan,
            'nomor_hp' => $this->nomor_hp,
            'email' => $this->email,
            'kategori_aduan' => new KategoriAduanResource($this->whenLoaded('kategoriAduan')),
            'isi_aduan' => $this->isi_aduan,
            'file_berkas_aduan' => $this->file_berkas_aduan ? url("storage/{$this->file_berkas_aduan}") : null,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'notes' => AdminNoteResource::collection($this->whenLoaded('notes')),
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }

    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'baru' => 'Baru',
            'diproses' => 'Sedang Diproses',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
            default => $this->status ?? 'Baru',
        };
    }
}
