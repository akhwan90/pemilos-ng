<?php

namespace App\Exports;

use App\Models\AduanAspirasi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AduanAspirasiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from;
    protected $to;
    protected $status;

    public function __construct($from = null, $to = null, $status = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->status = $status;
    }

    public function collection(): Collection
    {
        $query = AduanAspirasi::with('kategoriAduan');
        if ($this->from) $query->whereDate('created_at', '>=', $this->from);
        if ($this->to) $query->whereDate('created_at', '<=', $this->to);
        if ($this->status) $query->where('status', $this->status);
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'NIK', 'Alamat', 'Pekerjaan', 'No HP', 'Email', 'Kategori', 'Isi Aduan', 'Status', 'Tanggal'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nama,
            $row->nik,
            $row->alamat,
            $row->pekerjaan,
            $row->nomor_hp,
            $row->email,
            $row->kategoriAduan?->nama ?? '-',
            $row->isi_aduan,
            $row->status,
            $row->created_at->format('d/m/Y H:i'),
        ];
    }
}
