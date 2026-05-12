<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('brand.name') }}</title>
    <style>
        body { margin:0; padding:0; background:#f5f4f0; font-family:'DM Sans',Helvetica,Arial,sans-serif; color:#1a1612; }
        .wrap { max-width:600px; margin:32px auto; background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e8e2d9; }
        .header { background:#1a1612; padding:28px 32px; }
        .header-brand { display:flex; align-items:center; gap:10px; }
        .header-mark { width:32px; height:32px; background:#d97706; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; }
        .header-name { color:#ffffff; font-size:20px; font-weight:700; letter-spacing:-0.02em; }
        .body { padding:32px; }
        .footer { background:#f5f4f0; padding:20px 32px; text-align:center; border-top:1px solid #e8e2d9; }
        .footer p { color:#a8987f; font-size:12px; margin:0; line-height:1.6; }
        .footer a { color:#d97706; text-decoration:none; }
        h1 { font-size:22px; font-weight:700; color:#1a1612; margin:0 0 16px; }
        p { font-size:14px; line-height:1.7; color:#4a3f31; margin:0 0 14px; }
        .btn { display:inline-block; background:#d97706; color:#ffffff !important; text-decoration:none; font-weight:600; font-size:14px; padding:12px 28px; border-radius:10px; margin:8px 0 16px; }
        table.order { width:100%; border-collapse:collapse; font-size:13px; margin:16px 0; }
        table.order th { background:#f5f4f0; color:#6b5c48; font-weight:600; padding:8px 12px; text-align:left; }
        table.order td { padding:10px 12px; border-bottom:1px solid #f0ede6; }
        .total-row td { font-weight:700; color:#1a1612; border-top:2px solid #e8e2d9; }
        .divider { height:1px; background:#f0ede6; margin:20px 0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <div class="header-brand">
            <div class="header-mark">
                <svg width="20" height="20" viewBox="0 0 32 32" fill="none">
                    <path d="M11 11L16 22L21 11" stroke="white" stroke-width="2.5"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="header-name">{{ config('brand.name') }}</span>
        </div>
    </div>
    <div class="body">
        {{ $slot }}
    </div>
    <div class="footer">
        <p>
            © {{ date('Y') }} {{ config('brand.name') }} ·
            <a href="{{ url('/') }}">Shop</a> ·
            <a href="{{ route('orders.index') }}">My Orders</a> ·
            <a href="mailto:{{ config('brand.support') }}">Support</a>
        </p>
        <p style="margin-top:6px;">{{ config('brand.address') }}</p>
    </div>
</div>
</body>
</html>