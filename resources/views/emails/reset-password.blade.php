<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reset Kata Sandi — LendFlow</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#334155;-webkit-text-size-adjust:100%;">

    <!-- Outer wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f5f9;padding:40px 16px;">
        <tr>
            <td align="center">

                <!-- Email Card -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 10px 25px -5px rgba(15,23,42,0.06);">

                    <!-- ═══════════════════════════════ HEADER ═══ -->
                    <tr>
                        <td style="background-color:#0f172a;padding:28px 40px 24px;text-align:center;border-bottom:3px solid #15803d;">
                            <!-- LendFlow Dark Mode Logo -->
                            <img src="{{ $message->embed(public_path('images/persegi-panjang-drak-mode.png')) }}"
                                 alt="LendFlow"
                                 width="180"
                                 style="max-width:180px;height:auto;display:inline-block;">
                            <div style="margin-top:10px;font-size:9px;color:#86efac;font-weight:700;text-transform:uppercase;letter-spacing:2px;">
                                Institutional Grade P2P Lending Platform
                            </div>
                        </td>
                    </tr>

                    <!-- ═══════════════════════════════ BODY ══════ -->
                    <tr>
                        <td style="padding:36px 40px 0;">

                            <!-- Greeting -->
                            <h1 style="margin:0 0 12px;font-size:19px;font-weight:800;color:#0f172a;line-height:1.3;">
                                Halo, {{ $user->profile->full_name ?? 'Pengguna LendFlow' }} 👋
                            </h1>

                            <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#475569;">
                                Kami menerima permintaan untuk mereset kata sandi akun <strong style="color:#0f172a;">LendFlow</strong> Anda.
                            </p>
                            <p style="margin:0 0 28px;font-size:14px;line-height:1.7;color:#475569;">
                                Klik tombol di bawah ini untuk membuat kata sandi baru secara langsung dan aman:
                            </p>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom:28px;">
                                        <a href="{{ $url }}"
                                           target="_blank"
                                           style="display:inline-block;padding:14px 36px;background-color:#15803d;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;border-radius:12px;box-shadow:0 4px 14px rgba(21,128,61,0.30);letter-spacing:0.3px;">
                                            Reset Password Sekarang &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                                <tr>
                                    <td style="border-top:1px solid #f1f5f9;height:1px;font-size:0;line-height:0;">&nbsp;</td>
                                </tr>
                            </table>

                            <!-- Security Notice -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                                <tr>
                                    <td style="background-color:#f0fdf4;border-left:4px solid #16a34a;border-radius:6px;padding:14px 16px;">
                                        <p style="margin:0;font-size:12px;color:#166534;line-height:1.6;">
                                            🔒 <strong>Keamanan Akun Anda:</strong> Tautan ini akan kadaluwarsa dalam
                                            <strong>{{ $expireMinutes }} menit</strong>. Jika Anda tidak meminta perubahan ini,
                                            abaikan email ini — akun Anda tetap aman dan tidak ada perubahan yang terjadi.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Fallback URL box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:36px;">
                                <tr>
                                    <td style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;">
                                        <p style="margin:0 0 6px;font-size:12px;font-weight:700;color:#475569;">
                                            Tombol tidak dapat diklik?
                                        </p>
                                        <p style="margin:0;font-size:11px;color:#64748b;word-break:break-all;line-height:1.5;">
                                            Salin dan tempel tautan berikut ke browser Anda:<br>
                                            <a href="{{ $url }}" style="color:#15803d;text-decoration:underline;">{{ $url }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- ═══════════════════════════════ FOOTER ════ -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #f1f5f9;">
                            <p style="margin:0 0 4px;font-size:11px;color:#94a3b8;line-height:1.5;">
                                &copy; 2026 <strong style="color:#64748b;">LendFlow Inc.</strong> — Platform Layanan Keuangan P2P Terintegrasi
                            </p>
                            <p style="margin:0;font-size:11px;color:#cbd5e1;">
                                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- END Email Card -->

            </td>
        </tr>
    </table>

</body>
</html>
