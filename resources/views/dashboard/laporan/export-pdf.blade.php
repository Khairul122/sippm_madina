<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengaduan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            font-size: 11px;
            line-height: 1.4;
        }
        .kop {
            width: 100%;
            text-align: center;
            border-bottom: 4px double #000000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .kop h1 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop h2 {
            margin: 2px 0 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop p {
            margin: 2px 0 0;
            font-size: 10px;
        }
        .kop .address {
            margin-top: 3px;
        }
        h2.judul {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 5px;
            text-transform: capitalize;
            font-family: 'Times New Roman', Times, serif;
        }
        .filter-summary {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin-bottom: 10px;
        }
        .meta {
            font-size: 9px;
            color: #666;
            margin-bottom: 8px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
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
    <!-- Kop Surat Resmi (Centered, Double Border) -->
    <div class="kop">
        <h1>PEMERINTAH KABUPATEN MANDAILING NATAL</h1>
        <h2>DINAS KOMUNIKASI DAN INFORMATIKA</h2>
        <p class="address">KOMPLEK PERKANTORAN PAYALOTING, PANYABUNGAN SUMATERA UTARA, KODE POS 22978</p>
        <p>Telp. (0636) 326255, 326258 Fax: (0636) 326254</p>
        <p>E-mail : diskominfo@M.madina.go.id Website : www.diskominfo.madina.go.id</p>
    </div>

    <!-- Judul Dokumen -->
    <h2 class="judul">Laporan Pengaduan</h2>
    
    @if(count($filters) > 0)
        <p class="filter-summary">
            Filter: {{ implode(' · ', array_map(fn ($key, $value) => "{$key}: {$value}", array_keys($filters), $filters)) }}
        </p>
    @endif
    <p class="meta">Dicetak: {{ $generatedAt->translatedFormat('d F Y, H:i') }} WIB · Total data: {{ $complaints->count() }}</p>

    <!-- Tabel Data Pengaduan (Sesuai Struktur Gambar 1) -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Nama Pelapor</th>
                <th style="width: 15%;">Nama Desa</th>
                <th style="width: 15%;">Nama Kecamatan</th>
                <th style="width: 10%;">Tujuan</th>
                <th style="width: 25%;">Uraian Laporan</th>
                <th style="width: 15%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
        @forelse($complaints as $index => $complaint)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $complaint->user?->name ?? '-' }}</td>
                <td>{{ $complaint->user?->kecamatan?->desas?->first()?->name ?? '-' }}</td>
                <td>{{ $complaint->user?->kecamatan?->name ?? '-' }}</td>
                <td>
                    @if($complaint->target_type === 'opd')
                        OPD
                    @elseif($complaint->target_type === 'camat')
                        Kecamatan
                    @else
                        {{ ucfirst($complaint->target_type) }}
                    @endif
                </td>
                <td>{{ $complaint->description }}</td>
                <td>{{ $complaint->created_at?->translatedFormat('l, d F Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="center">Tidak ada data pengaduan.</td></tr>
        @endforelse
        </tbody>
    </table>

    <!-- Blok Tanda Tangan Resmi (Rata Kiri, Posisi Kanan) -->
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
