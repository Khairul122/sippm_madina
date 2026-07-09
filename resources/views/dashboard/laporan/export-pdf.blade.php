<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengaduan</title>
    <style>
        body { font-family: sans-serif; color: #2b2b2b; font-size: 12px; }
        .kop { width: 100%; border-bottom: 3px solid #16345c; padding-bottom: 10px; margin-bottom: 14px; }
        .kop table { width: 100%; }
        .kop td { vertical-align: middle; }
        .kop .logo { width: 60px; }
        .kop .logo img { width: 55px; }
        .kop .instansi h1 { margin: 0; font-size: 15px; color: #16345c; }
        .kop .instansi p { margin: 2px 0 0; font-size: 11px; color: #555; }
        h2.judul { text-align: center; text-decoration: underline; font-size: 14px; color: #16345c; margin: 6px 0 4px; }
        .filter-summary { text-align: center; font-size: 11px; color: #555; margin-bottom: 14px; }
        .meta { font-size: 10px; color: #888; margin-bottom: 10px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.data th, table.data td { border: 1px solid #ccc; padding: 5px 8px; text-align: left; font-size: 11px; }
        table.data th { background-color: #16345c; color: #fff; }
        table.data td.center { text-align: center; }
        .ttd-wrap { width: 100%; margin-top: 10px; }
        .ttd-block { width: 260px; float: right; text-align: center; font-size: 12px; }
        .ttd-space { height: 70px; }
        .ttd-name { text-decoration: underline; font-weight: bold; margin: 0; }
        .ttd-pangkat, .ttd-nip { margin: 2px 0 0; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="kop">
        <table>
            <tr>
                <td class="logo"><img src="{{ public_path('images/logo-madina.png') }}" alt="Logo"></td>
                <td class="instansi">
                    <h1>PEMERINTAH KABUPATEN MANDAILING NATAL</h1>
                    <p>Dinas Komunikasi dan Informatika · Sistem Informasi Pengaduan Masyarakat dan Pelaporan Kegiatan</p>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="judul">LAPORAN PENGADUAN</h2>
    @if(count($filters) > 0)
        <p class="filter-summary">
            Filter: {{ implode(' · ', array_map(fn ($key, $value) => "{$key}: {$value}", array_keys($filters), $filters)) }}
        </p>
    @endif
    <p class="meta">Dicetak: {{ $generatedAt->translatedFormat('d F Y, H:i') }} WIB · Total data: {{ $complaints->count() }}</p>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tiket</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tujuan</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
        @forelse($complaints as $index => $complaint)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $complaint->ticket_number }}</td>
                <td>{{ $complaint->title }}</td>
                <td>{{ ucfirst($complaint->category) }}</td>
                <td>{{ $complaint->status->label() }}</td>
                <td>
                    @if($complaint->target_type === 'opd')
                        {{ $opdNames->get($complaint->target_id, '-') }}
                    @elseif($complaint->target_type === 'camat')
                        {{ $kecamatanNames->get($complaint->target_id, '-') }}
                    @else
                        {{ ucfirst(str_replace('_', ' ', (string) $complaint->target_type)) }}
                    @endif
                </td>
                <td>{{ $complaint->created_at?->translatedFormat('d M Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="center">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="ttd-wrap">
        <div class="ttd-block">
            <p>Panyabungan, {{ $generatedAt->translatedFormat('d F Y') }}</p>
            <p>{{ $ttd?->jabatan_penandatangan ?? '-' }}</p>
            <div class="ttd-space"></div>
            <p class="ttd-name">{{ $ttd?->nama_penandatangan ?? '-' }}</p>
            @if($ttd?->pangkat)
                <p class="ttd-pangkat">{{ $ttd->pangkat }}</p>
            @endif
            <p class="ttd-nip">NIP. {{ $ttd?->nip ?? '-' }}</p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
