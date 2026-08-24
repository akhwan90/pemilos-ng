<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // Tambahkan ini
use PhpOffice\PhpSpreadsheet\Style\NumberFormat; // Tambahkan ini
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents; // Tambahkan ini
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataSiswaSekolah implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithColumnFormatting, WithTitle, WithEvents
{
    protected $siswa;


    public function __construct(Collection $siswa)
    {
        $this->siswa = $siswa;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->siswa;
    }

    public function title(): string
    {
        return 'Data Siswa';
    }

    /**
     * Mendefinisikan heading kolom untuk file Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'NISN',
            'Nama Siswa',
            'Jenis Kelamin',
            'Kelas',
            'Status Difabel',
        ];
    }

    /**
     * Memetakan data dari collection ke kolom Excel.
     *
     * @return array
     */
    public function map($siswa): array
    {
        return [
            $siswa->nisn,
            $siswa->nm_siswa,
            $siswa->jk,
            // $this->getJenisKelamin($siswa->jk),
            $siswa->kelas,
            ($siswa->difabel == null ? '0' : $siswa->difabel),
            // $this->getStatusDifabel($siswa->difabel),
        ];
    }

    /**
     * Helper untuk konversi jenis kelamin.
     */
    private function getJenisKelamin($jk)
    {
        if ($jk == 1) return 'Laki-laki';
        if ($jk == 2) return 'Perempuan';
        return '-';
    }

    /**
     * Helper untuk konversi status difabel.
     */
    private function getStatusDifabel($difabel)
    {
        switch ($difabel) {
            case 1:
                return 'Disabilitas Fisik';
            case 2:
                return 'Disabilitas Intelektual';
            case 3:
                return 'Disabilitas Mental';
            case 4:
                return 'Disabilitas Sensorik Wicara';
            case 5:
                return 'Disabilitas Sensorik Rungu';
            case 6:
                return 'Disabilitas Sensorik Netra';
            default:
                return 'Tidak Difabel';
        }
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Kolom A (NISN) diformat sebagai teks
            // Anda bisa menambahkan format lain di sini jika diperlukan
            // 'B' => NumberFormat::FORMAT_DATE9, // Contoh format tanggal
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $worksheet = $event->sheet->getDelegate(); // Dapatkan objek Worksheet

                // Keterangan Jenis Kelamin
                // $worksheet->insertNewRowBefore(1, 1); // Sisipkan baris baru di atas
                $worksheet->setCellValue('G1', 'Keterangan Jenis Kelamin:');
                $worksheet->setCellValue('G2', '1: Laki-laki, 2: Perempuan');
                $worksheet->getStyle('G1')->getFont()->setBold(true);

                // Keterangan Status Disabilitas
                // $worksheet->insertNewRowBefore(2, 1); // Sisipkan baris baru lagi
                $worksheet->setCellValue('G3', 'Keterangan Status Disabilitas:');
                $worksheet->setCellValue('G4', '0: Bukan Difabel, 1: Disabilitas Fisik, 2: Disabilitas Intelektual, 3: Disabilitas Mental, 4: Disabilitas Sensorik Wicara, 5: Disabilitas Sensorik Rungu, 6: Disabilitas Sensorik Netra');
                $worksheet->getStyle('G3')->getFont()->setBold(true);

                // Sisipkan baris kosong setelah keterangan sebelum header
                // $worksheet->insertNewRowBefore(3, 1);

                // Sesuaikan header agar berada di baris ke-4 (setelah 3 baris baru yang disisipkan)
                // $sheet->getDelegate()->getStyle('A4:G4')->getFont()->setBold(true); // Ini bisa dilakukan di $worksheet juga
                // $worksheet->getStyle('A4:G4')->getFont()->setBold(true);
                // $worksheet->getStyle('A4:G4')->getFill()->getStartColor()->setRGB('EEEEEE'); // Warna latar belakang abu-abu untuk header

                // Sesuaikan kolom NISN agar tetap teks setelah penyisipan baris
                // Gunakan $worksheet->getColumnDimension()
                // $worksheet->getColumnDimension('A')->setFormat(NumberFormat::FORMAT_TEXT);
            },
        ];
    }
}
