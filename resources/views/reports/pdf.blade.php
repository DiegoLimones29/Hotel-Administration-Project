<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1C1C1C; font-size: 12px; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        p.range { color: #78716C; font-size: 11px; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #F5F5F4; padding: 6px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: .04em; color: #57534E; border-bottom: 1px solid #D6D3D1; }
        td { padding: 6px 8px; border-bottom: 1px solid #E7E5E4; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="range">Periodo: {{ $startDate }} a {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                @foreach ($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headers) }}">Sin datos para este período</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
