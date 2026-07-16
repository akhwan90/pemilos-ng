<?php

namespace App\Exports;

use App\Models\TamuSetwan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TamuSetwanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from, $to, $status;
    public function __construct($from = null, $to = null, $status = null) { $this->from = $from; $this->to = $to; $this->status = $status; }

    public function collection(): Collection
    {
        $query = TamuSetwan::query();
        if ($this->from) $query->whereDate('created_at', '>=', $this->from);
        if ($this->to) $query->whereDate('created_at', '<=', $this->to);
        if ($this->status) $query->where('status', $this->status);
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'Instansi', 'Hari', 'Jam', 'Jumlah Peserta', 'Ketua Rombongan', 'HP', 'Email', 'Alamat Instansi', 'Tujuan Kunjungan', 'Status', 'Tanggal'];
    }

    public function map($row): array
    {
        return [$row->id, $row->nama, $row->instansi, $row->hari_berkunjung, $row->jam_berkunjung, $row->jumlah_peserta, $row->nama_jabatan_ketua_rombongan, $row->nomor_hp_narahubung, $row->email, $row->alamat_instansi, $row->tujuan_kunjungan, $row->status, $row->created_at->format('d/m/Y H:i')];
    }
}
