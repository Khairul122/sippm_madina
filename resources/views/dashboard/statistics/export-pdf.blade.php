<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Statistik Pengaduan</title>
    <style>
        body { font-family: sans-serif; color: #2b2b2b; }
        h1 { color: #16345c; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; font-size: 12px; }
        th { background-color: #16345c; color: #fff; }
        .meta { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <h1>SIPPM Madina — Statistik Pengaduan</h1>
    <p class="meta">Dicetak: {{ $generatedAt->translatedFormat('d F Y, H:i') }} WIB · Pemerintah Kabupaten Mandailing Natal</p>
    <table>
        <thead><tr><th>Status</th><th>Jumlah</th></tr></thead>
        <tbody>
        @foreach($rows as $status => $total)
            <tr>
                <td>{{ \App\Domain\Complaint\ValueObjects\ComplaintStatus::from($status)->label() }}</td>
                <td>{{ $total }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
