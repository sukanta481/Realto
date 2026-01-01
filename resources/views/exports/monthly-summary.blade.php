<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Summary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 24px; }
        .header h2 { color: #666; margin: 10px 0 0; font-size: 16px; font-weight: normal; }
        .header p { color: #999; margin: 5px 0 0; font-size: 10px; }
        .stats-grid { display: table; width: 100%; margin-top: 20px; }
        .stat-row { display: table-row; }
        .stat-card { display: table-cell; width: 33%; padding: 15px; text-align: center; border: 1px solid #e5e7eb; }
        .stat-value { font-size: 28px; font-weight: bold; color: #4f46e5; }
        .stat-label { color: #666; margin-top: 5px; font-size: 11px; }
        .revenue { color: #059669 !important; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $companyName }}</h1>
        <h2>Monthly Summary - {{ $stats['period'] }}</h2>
        <p>Generated on {{ $generatedAt }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['new_leads'] }}</div>
                <div class="stat-label">New Leads</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['converted_leads'] }}</div>
                <div class="stat-label">Converted Leads</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['new_properties'] }}</div>
                <div class="stat-label">New Properties</div>
            </div>
        </div>
        <div class="stat-row">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['deals_closed'] }}</div>
                <div class="stat-label">Deals Closed</div>
            </div>
            <div class="stat-card" style="width: 66%;">
                <div class="stat-value revenue">₹{{ number_format($stats['revenue']) }}</div>
                <div class="stat-label">Total Revenue (Commission)</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>RealtoCRM - Real Estate Made Simple</p>
    </div>
</body>
</html>
