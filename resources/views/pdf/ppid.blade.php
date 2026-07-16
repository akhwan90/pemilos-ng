<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dokumen PPID - {{ $tamu->instansi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .title {
            font-size: 13pt;
            font-weight: bold;
        }
        .doc-no {
            font-size: 11pt;
            margin-top: 3px;
        }
        table.form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.form-table td {
            vertical-align: top;
            padding: 4px 0;
        }
        table.form-table td.label-col {
            width: 35%;
        }
        table.form-table td.colon-col {
            width: 2%;
            text-align: center;
        }
        table.form-table td.value-col {
            width: 63%;
        }
        
        .checkbox-container {
            margin-bottom: 3px;
        }
        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 8px;
            position: relative;
            top: 2px;
        }
        
        table.signature-table {
            width: 100%;
            margin-top: 40px;
            margin-bottom: 30px;
        }
        table.signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        
        .footnotes {
            font-size: 10pt;
            margin-top: 20px;
        }
        .footnotes p {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    @if(isset($kopBase64))
    <div style="text-align: center; margin-bottom: 15px; border-bottom: 3px solid #000; padding-bottom: 5px;">
        <img src="{{ $kopBase64 }}" style="max-width: 100%; height: auto; max-height: 120px;" alt="Kop Surat">
    </div>
    @endif

    <div class="header">
        <div class="title">FORMULIR PERMOHONAN INFORMASI</div>
        <div class="doc-no">No. Pendaftaran: {{ $tamu->nomor_surat_ppid }}</div>
    </div>

    <table class="form-table">
        <tr>
            <td class="label-col">Nama / Instansi*</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $tamu->nama }} / {{ $tamu->instansi }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $tamu->alamat_instansi }}</td>
        </tr>
        <tr>
            <td class="label-col">Nomor Telp./Hp</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $tamu->nomor_hp_narahubung }}</td>
        </tr>
        <tr>
            <td class="label-col">Email</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $tamu->email }}</td>
        </tr>
        <tr>
            <td class="label-col">Informasi yang dibutuhkan</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ empty($tamu->materi) ? "Data Kunjungan Kerja dari {$tamu->instansi} ({$tamu->nama_alkap})" : $tamu->materi }}</td>
        </tr>
        <tr>
            <td class="label-col">Tujuan Penggunaan<br>Informasi</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $tamu->tujuan_kunjungan }}</td>
        </tr>
        <tr>
            <td class="label-col">Cara Mendapatkan<br>Informasi**</td>
            <td class="colon-col">:</td>
            <td class="value-col">
                <div class="checkbox-container"><div class="checkbox-box"></div> Melihat/Membaca/Mendengarkan/Mencatat</div>
                <div class="checkbox-container"><div class="checkbox-box"></div> Mendapatkan salinan informasi (hardcopy/softcopy)</div>
            </td>
        </tr>
        <tr>
            <td class="label-col">Cara Mendapatkan<br>Salinan informasi**</td>
            <td class="colon-col">:</td>
            <td class="value-col">
                <div class="checkbox-container"><div class="checkbox-box"></div> Mengambil langsung</div>
                <div class="checkbox-container"><div class="checkbox-box"></div> Kurir</div>
                <div class="checkbox-container"><div class="checkbox-box"></div> Pos</div>
                <div class="checkbox-container"><div class="checkbox-box"></div> Faksimili</div>
                <div class="checkbox-container"><div class="checkbox-box"></div> E-mail</div>
                <div class="checkbox-container"><div class="checkbox-box"></div> Lain-lain ........................</div>
            </td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <p style="margin: 0; padding-top: 15px;">Petugas Pelayanan Informasi /<br>Pejabat Pengelola Informasi dan Dokumentasi</p>
                <br><br><br><br><br>
                <p style="margin: 0;">(................................................)</p>
            </td>
            <td>
                <p style="margin: 0; padding-bottom: 15px;">Kulon Progo, {{ \Carbon\Carbon::parse($tamu->tanggal_berkunjung)->translatedFormat('d F Y') }}<br>Pemohon Informasi</p>
                <br><br><br><br><br>
                @if(isset($tamu->nama))
                    <p style="margin: 0; text-decoration: underline; font-weight: bold;">{{ $tamu->nama }}</p>
                @else
                    <p style="margin: 0;">(................................................)</p>
                @endif
            </td>
        </tr>
    </table>

    <div class="footnotes">
        <p>* Wajib diisi</p>
        <p>** Pilih salah satu dengan memberi tanda (v)</p>
        <p>*** Coret yang tidak perlu</p>
    </div>
</body>
</html>