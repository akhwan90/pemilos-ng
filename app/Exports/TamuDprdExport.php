<?php

namespace App\Exports;

use App\Models\TamuDprd;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class TamuDprdExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from, $to, $status;
    public function __construct($from = null, $to = null, $status = null) { $this->from = $from; $this->to = $to; $this->status = $status; }

    public function collection(): Collection
    {
        $query = TamuDprd::query();
        if ($this->from) $query->whereDate('created_at', '>=', $this->from);
        if ($this->to) $query->whereDate('created_at', '<=', $this->to);
        if ($this->status) $query->where('status', $this->status);
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'Instansi', 'Hari', 'Jam', 'Tanggal', 'Alkap', 'Jml Peserta', 'Ketua Rombongan', 'HP', 'Email', 'Alamat', 'Tujuan', 'Status', 'Tanggal Daftar'];
    }

    public function map($row): array
    {
        return [$row->id, $row->nama, $row->instansi, $row->hari_berkunjung, $row->jam_berkunjung, $row->tanggal_berkunjung, $row->nama_alkap, $row->jumlah_peserta, $row->nama_jabatan_ketua_rombongan, $row->nomor_hp_narahubung, $row->email, $row->alamat_instansi, $row->tujuan_kunjungan, $row->status, $row->created_at->format('d/m/Y H:i')];
    }
}
