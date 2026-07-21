<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kegiatan</title>
    <style>
        @page {
            margin: 40px 40px 60px 40px;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            font-size: 11px;
            line-height: 1.4;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 10px;
        }
        .kop-table td {
            border: none !important;
            vertical-align: middle;
        }
        .kop-line {
            border-top: 3px solid #000000;
            border-bottom: 1px solid #000000;
            height: 2px;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        h2.judul {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0 5px;
            text-transform: uppercase;
        }
        .filter-summary {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin-bottom: 10px;
        }
        .footer {
            position: fixed;
            bottom: -45px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: right;
            font-size: 8px;
            color: #666;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        table.data th, table.data td {
            border: 1px solid #000000;
            padding: 5px 6px;
            font-size: 10px;
            vertical-align: top;
        }
        table.data th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        table.data td.center {
            text-align: center;
        }
        .ttd-wrap {
            width: 100%;
            margin-top: 15px;
        }
        .ttd-block {
            width: 300px;
            float: right;
            text-align: left;
            font-size: 11px;
            line-height: 1.5;
        }
        .ttd-block p {
            margin: 0;
        }
        .ttd-space {
            height: 65px;
        }
        .ttd-name {
            font-weight: bold;
            text-transform: uppercase;
        }
        .ttd-pangkat {
            text-transform: uppercase;
        }
        .ttd-nip {
            text-transform: uppercase;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <!-- Info Cetak Kanan Bawah Kertas (Footer) -->
    <div class="footer">
        Dicetak: {{ $generatedAt->translatedFormat('d F Y, H:i') }} WIB &middot; Total data: {{ $activities->count() }}
    </div>

    <!-- Kop Surat Resmi (Centered, Double Border with Logo) -->
    <table class="kop-table">
        <tr>
            <td style="width: 12%; text-align: center;">
                <img src="{{ public_path('images/logo-madina.png') }}" style="height: 70px; width: auto;">
            </td>
            <td style="width: 88%; text-align: center; padding-right: 50px;">
                <h3 style="margin: 0; font-size: 14pt; font-weight: normal; text-transform: uppercase;">PEMERINTAH KABUPATEN MANDAILING NATAL</h3>
                <h2 style="margin: 2px 0 0; font-size: 18pt; font-weight: bold; text-transform: uppercase;">DINAS KOMUNIKASI DAN INFORMATIKA</h2>
                <p style="margin: 2px 0 0; font-size: 8pt; font-style: italic;">KOMPLEK PERKANTORAN PAYALOTING, PANYABUNGAN SUMATERA UTARA, KODE POS 22978</p>
                <p style="margin: 1px 0 0; font-size: 8pt;">Telp. (0636) 326255, 326258 Fax: (0636) 326254</p>
                <p style="margin: 1px 0 0; font-size: 8pt;">E-mail : diskominfo@madina.go.id &nbsp;&nbsp;&nbsp;&nbsp; Website : www.diskominfo.madina.go.id</p>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <!-- Judul Dokumen -->
    <h2 class="judul">Laporan Publikasi Kegiatan</h2>
    
    @if(count($filters) > 0)
        <p class="filter-summary">
            Filter: {{ implode(' · ', array_map(fn ($key, $value) => "{$key}: {$value}", array_keys($filters), $filters)) }}
        </p>
    @endif

    <!-- Tabel Data Kegiatan -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Judul Kegiatan</th>
                <th style="width: 25%;">Penerbit (OPD/Kecamatan)</th>
                <th style="width: 15%;">Lokasi</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($activities as $index => $activity)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $activity->title }}</td>
                <td>{{ $activity->actor ? $activity->actor->name : '-' }}</td>
                <td>{{ $activity->location ?? '-' }}</td>
                <td>{{ $activity->date->translatedFormat('d M Y') }}</td>
                <td class="center">{{ $activity->status->label() }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="center">Tidak ada data kegiatan.</td></tr>
        @endforelse
        </tbody>
    </table>

    <!-- Blok Tanda Tangan Resmi -->
    <div class="ttd-wrap">
        <div class="ttd-block">
            <p>Panyabungan, {{ $generatedAt->translatedFormat('d F Y') }}</p>
            <p class="ttd-name" style="font-weight: bold;">{{ $ttd?->jabatan_penandatangan ?? 'KEPALA DINAS' }}</p>
            <div class="ttd-space"></div>
            
            <p class="ttd-name">{{ $ttd?->nama_penandatangan ?? 'NAMA PEJABAT' }}</p>
            @if($ttd?->pangkat)
                <p class="ttd-pangkat">{{ $ttd->pangkat }}</p>
            @endif
            
            @php
                $rawNip = str_replace(' ', '', $ttd?->nip ?? '');
                if (strlen($rawNip) === 18) {
                    $formattedNip = substr($rawNip, 0, 8) . ' ' . substr($rawNip, 8, 6) . ' ' . substr($rawNip, 14, 1) . ' ' . substr($rawNip, 15, 3);
                } else {
                    $formattedNip = $ttd?->nip ?? '-';
                }
            @endphp
            <p class="ttd-nip">NIP. {{ $formattedNip }}</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
