<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermohonanAudiensiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nama_instansi_kelompok_paguyuban_komunitas' => $this->nama_instansi_kelompok_paguyuban_komunitas,
            'maksud_tujuan_audiensi' => $this->maksud_tujuan_audiensi,
            'nama_jabatan_ketua_rombongan' => $this->nama_jabatan_ketua_rombongan,
            'nomor_hp_narahubung' => $this->nomor_hp_narahubung,
            'jumlah_peserta' => $this->jumlah_peserta,
            'file_permohonan_audiensi' => $this->file_permohonan_audiensi ? url("storage/{$this->file_permohonan_audiensi}") : null,
            'file_daftar_hadir' => $this->file_daftar_hadir ? url("storage/{$this->file_daftar_hadir}") : null,
            'file_dokumen_ppid' => $this->file_dokumen_ppid ? url("storage/{$this->file_dokumen_ppid}") : null,
            'nomor_surat_ppid' => $this->nomor_surat_ppid,
            'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
            'jam_pelaksanaan' => $this->jam_pelaksanaan ? \Carbon\Carbon::parse($this->jam_pelaksanaan)->format('H:i') : null,
            'alkap_penerima' => $this->alkap_penerima,
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
