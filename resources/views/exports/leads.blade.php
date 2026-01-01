<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 18px; }
        .header p { color: #666; margin: 5px 0 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #4f46e5; color: white; padding: 8px 4px; text-align: left; font-size: 9px; }
        td { padding: 6px 4px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 20px; text-align: center; color: #999; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on {{ $generatedAt }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->phone }}</td>
                    <td>{{ $item->email ?? 'N/A' }}</td>
                    <td>{{ $item->status->name ?? 'N/A' }}</td>
                    <td>{{ $item->budget_range ?? 'N/A' }}</td>
                    <td>{{ $item->location_preference ?? 'N/A' }}</td>
                    <td>{{ $item->property_type ?? 'N/A' }}</td>
                    <td>{{ $item->source ?? 'N/A' }}</td>
                    <td>{{ $item->assignedTo->name ?? 'Unassigned' }}</td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Records: {{ count($data) }} | RealtoCRM Export</p>
    </div>
</body>
</html>
