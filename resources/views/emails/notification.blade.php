<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        .header {
            background-color: #4f46e5;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 30px;
            color: #374151;
            line-height: 1.6;
        }
        .content h2 {
            font-size: 18px;
            font-weight: 700;
            margin-top: 0;
            color: #111827;
        }
        .content p {
            font-size: 15px;
            margin-bottom: 24px;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
            transition: background-color 0.2s;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <img src="{{ asset('images/persegi-panjang-drak-mode.png') }}" alt="LendFlow Logo" style="height: 36px; max-width: 200px; object-fit: contain; margin-bottom: 4px;">
                <h1 style="font-size: 14px; opacity: 0.8; font-weight: 500; margin-top: 4px;">Notification System</h1>
            </div>
            <div class="content">
                <h2>{{ $notification->title }}</h2>
                <p>{{ $notification->body }}</p>

                @if(!empty($notification->data) && isset($notification->data['route']))
                    <div class="btn-container">
                        <a href="{{ url('/' . ltrim($notification->data['route'], '/')) }}" class="btn">
                            Lihat Selengkapnya
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} LendFlow P2P Lending. All rights reserved.<br>
            Ini adalah email otomatis, mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
