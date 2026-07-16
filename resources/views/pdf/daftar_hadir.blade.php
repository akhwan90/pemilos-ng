<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir - {{ $tamu->instansi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt; /* Kurangi dari 12pt ke 11pt */
            line-height: 1.3; /* Rapatkan sedikit */
        }
        #page1 {
            font-size: 12pt;
        }
        #page2 {
            font-size: 10pt;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
        }
        .subtitle {
            font-size: 12pt;
        }
        table.info {
            width: 100%;
            margin-bottom: 0;
        }
        table.info td {
            vertical-align: top;
            padding: 0;
        }
        table.info td:first-child {
            width: 30%;
        }
        table.info td:nth-child(2) {
            width: 2%;
        }
        table.peserta {
            width: 100%;
            border-collapse: collapse;
        }
        table.peserta th, table.peserta td {
            border: 1px solid #000;
            text-align: left;
        }
        table.peserta th {
            text-align: center;
            background-color: #f2f2f2;
        }
        .footer {
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 300px;
            text-align: center;
        }
        .clear {
            clear: both;
        }

        .no-border {
            border: none !important;
        }
        .no-border td {
            border: none !important;
        }

        .p-menjorok {
            text-align: justify;
            text-indent: 30px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div id="page1">
        <h2 style="text-align: center;">SURAT PERNYATAAN</h2>

        <p class="p-menjorok">Yang bertanda tangan di bawah ini:</p>

        <p class="p-menjorok">
            Menyatakan dengan sesungguhnya bahwa kunjungan tersebut telah sesuai dengan Surat Tugas.
        </p>

        <p>
            <table class="no-border" style="width: 100%">
                <tr><td width="40%">Nama</td><td width="2%">:</td><td>{{ $tamu->nama }}</td></tr>
                <tr><td>Asal</td><td>:</td><td>{{ $tamu->instansi }}</td></tr>
                <tr><td>Alamat Kantor</td><td>:</td><td>{{ $tamu->alamat_instansi }}</td></tr>
                <tr><td>Tanggal Kunjungan</td><td>:</td><td>{{ \Carbon\Carbon::parse($tamu->tanggal_berkunjung)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td></tr>
                <tr><td>Jam</td><td>:</td><td>{{ $tamu->jam_berkunjung }} WIB</td></tr>
                <tr><td>Alkap/Komisi</td><td>:</td><td>{{ $tamu->nama_alkap }}</td></tr>
                <tr><td>Peserta</td><td>:</td><td>{{ $tamu->jumlah_peserta }} orang</td></tr>
                <tr><td>Ketua Rombongan </td><td>:</td><td>{{ $tamu->nama_jabatan_ketua_rombongan }}</td></tr>
                <tr><td>Materi Kunjungan </td><td>:</td><td>{{ $tamu->tujuan_kunjungan }}</td></tr>
                <tr><td>No. HP </td><td>:</td><td>{{ $tamu->nomor_hp_narahubung }}</td></tr>
            </table>
        </p>

        <p class="p-menjorok">
            Demikian surat pernyataan ini saya buat dengan sebenarnya tanpa ada unsur paksaan dari Pihak manapun. Apabila di kemudian hari ternyata tidak sesuai dengan kondisi sebenarnya atau pernyataan ini tidak benar, maka Saya siap menerima konsekuensinya sesuai peraturan perundang-undangan yang berlaku.
        </p>

        <p>
            <div class="signature-box">
                Pengasih, {{ \Carbon\Carbon::parse($tamu->tanggal_berkunjung)->locale('id')->isoFormat('D MMMM YYYY') }}<br/>
                Yang membuat pernyataan<br/>
                <br/><br/><br/><br/>
                {{ $tamu->nama }}
            </div>
        </p>
    </div>

    <div id="page2" class="page-break">
        @if(isset($kopBase64))
        <div style="text-align: center; border-bottom: 3px solid #000; padding-bottom: 5px;">
            <img src="{{ $kopBase64 }}" style="max-width: 100%; height: auto; max-height: 120px;" alt="Kop Surat">
        </div>
        @endif

        <div class="title" style="text-align: center;">DAFTAR HADIR</div>
        
        <table class="info">
            <tr><td width="40%">Hari Tanggal</td><td width="2%">:</td><td>{{ \Carbon\Carbon::parse($tamu->tanggal_berkunjung)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td></tr>
            <tr><td>Jam</td><td>:</td><td>{{ $tamu->jam_berkunjung }} WIB</td></tr>
            <tr><td>Tempat</td><td>:</td><td>Gedung DPRD Kabupaten Kulon Progo.</td></tr>
            <tr><td>Acara</td><td>:</td><td>Menerima Kunjungan Kerja {{ $tamu->nama_alkap }} dari {{ $tamu->instansi }}</td></tr>
        </table>

        <table class="peserta">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="35%">Nama</th>
                    <th width="30%">Jabatan</th>
                    <th width="30%">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 20; $i++)
                    <tr>
                        <td align="center">{{ $i }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $i }}.</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="footer">
            <div class="signature-box">
                Wates, {{ \Carbon\Carbon::parse($tamu->tanggal_berkunjung)->locale('id')->isoFormat('D MMMM YYYY') }}<br/>
                {{ $penerimaKunjunganJabatan ?? 'Penerima Kunjungan,' }}<br/>
                <br><br>
                @if(!empty($penerimaKunjunganNama))
                    <p style="text-decoration: underline; font-weight: bold; margin-bottom: 0;">{{ $penerimaKunjunganNama }}</p>
                @else
                    <p>(................................................)</p>
                @endif
                
                @if(!empty($penerimaKunjunganNip))
                    <p style="margin-top: 0;">NIP. {{ $penerimaKunjunganNip }}</p>
                @endif
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>