<?php

namespace App\Exports;

use App\Models\PermohonanAudiensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class PermohonanAudiensiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from, $to, $status;
    public function __construct($from = null, $to = null, $status = null) { $this->from = $from; $this->to = $to; $this->status = $status; }

    public function collection(): Collection
    {
        $query = PermohonanAudiensi::query();
        if ($this->from) $query->whereDate('created_at', '>=', $this->from);
        if ($this->to) $query->whereDate('created_at', '<=', $this->to);
        if ($this->status) $query->where('status', $this->status);
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'Instansi/Kelompok', 'Maksud & Tujuan', 'Ketua Rombongan', 'HP', 'Jumlah Peserta', 'Status', 'Tanggal'];
    }

    public function map($row): array
    {
        return [$row->id, $row->nama, $row->nama_instansi_kelompok_paguyuban_komunitas, $row->maksud_tujuan_audiensi, $row->nama_jabatan_ketua_rombongan, $row->nomor_hp_narahubung, $row->jumlah_peserta, $row->status, $row->created_at->format('d/m/Y H:i')];
    }
}
