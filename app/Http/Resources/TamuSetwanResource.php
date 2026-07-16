<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TamuSetwanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'instansi' => $this->instansi,
            'hari_berkunjung' => $this->hari_berkunjung,
            'jam_berkunjung' => $this->jam_berkunjung,
            'tanggal_berkunjung' => $this->tanggal_berkunjung,
            'jumlah_peserta' => $this->jumlah_peserta,
            'nama_jabatan_ketua_rombongan' => $this->nama_jabatan_ketua_rombongan,
            'nomor_hp_narahubung' => $this->nomor_hp_narahubung,
            'email' => $this->email,
            'alamat_instansi' => $this->alamat_instansi,
            'tujuan_kunjungan' => $this->tujuan_kunjungan,
            'file_surat_kunjungan' => $this->file_surat_kunjungan ? url("storage/{$this->file_surat_kunjungan}") : null,
            'file_spt' => $this->file_spt ? url("storage/{$this->file_spt}") : null,
            'file_bukti_menginap' => $this->file_bukti_menginap ? url("storage/{$this->file_bukti_menginap}") : null,
            'file_daftar_hadir' => $this->file_daftar_hadir ? url("storage/{$this->file_daftar_hadir}") : null,
            'file_dokumen_ppid' => $this->file_dokumen_ppid ? url("storage/{$this->file_dokumen_ppid}") : null,
            'file_daftar_hadir_ttd' => $this->file_daftar_hadir_ttd ? url("storage/{$this->file_daftar_hadir_ttd}") : null,
            'file_foto_kunjungan' => $this->file_foto_kunjungan ? url("storage/{$this->file_foto_kunjungan}") : null,
            'nomor_surat_ppid' => $this->nomor_surat_ppid,
            'materi' => $this->materi,
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
