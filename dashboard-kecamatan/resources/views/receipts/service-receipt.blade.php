<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pengajuan Layanan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #0d9488;
            border-radius: 10px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .receipt-number {
            background: #f0fdfa;
            border-left: 4px solid #0d9488;
            padding: 15px 20px;
            margin: 20px;
        }

        .receipt-number .label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .receipt-number .value {
            font-size: 18px;
            font-weight: bold;
            color: #0d9488;
            font-family: 'Courier New', monospace;
        }

        .content {
            padding: 0 20px 20px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #99f6e4;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            padding: 8px 10px;
            width: 40%;
            font-weight: 600;
            color: #475569;
            background: #f8fafc;
        }

        .info-value {
            display: table-cell;
            padding: 8px 10px;
            color: #1e293b;
        }

        .info-row:nth-child(even) .info-label {
            background: #fff;
        }

        .qr-section {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .qr-section h3 {
            font-size: 16px;
            color: #0f766e;
            margin-bottom: 15px;
        }

        .qr-code {
            margin: 15px auto;
            padding: 10px;
            background: white;
            display: inline-block;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }

        .qr-code img {
            display: block;
        }

        .qr-instruction {
            font-size: 11px;
            color: #64748b;
            margin-top: 10px;
            line-height: 1.5;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-process {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-selesai {
            background: #d1fae5;
            color: #065f46;
        }

        .footer {
            background: #f1f5f9;
            padding: 15px 20px;
            margin-top: 20px;
            border-top: 2px solid #e2e8f0;
        }

        .footer-grid {
            display: table;
            width: 100%;
        }

        .footer-col {
            display: table-cell;
            vertical-align: top;
            padding: 5px;
        }

        .footer h4 {
            font-size: 12px;
            color: #0f766e;
            margin-bottom: 5px;
        }

        .footer p {
            font-size: 10px;
            color: #64748b;
            margin: 2px 0;
        }

        .important-note {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 12px 15px;
            margin: 20px;
            font-size: 11px;
            color: #991b1b;
        }

        .important-note strong {
            display: block;
            margin-bottom: 5px;
            color: #7f1d1d;
        }

        @page {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        {{-- Header --}}
        <div class="header">
            @if($appProfile->logo_path)
                <div class="logo">
                    <img src="{{ public_path('storage/' . $appProfile->logo_path) }}" alt="Logo"
                        style="width: 100%; height: 100%; object-fit: contain;">
                </div>
            @endif
            <h1>{{ $appProfile->region_name }}</h1>
            <p>Struk Pengajuan Layanan Publik</p>
        </div>

        {{-- Receipt Number --}}
        <div class="receipt-number">
            <div class="label">Nomor Pengajuan</div>
            <div class="value">{{ $service->uuid }}</div>
        </div>

        {{-- Content --}}
        <div class="content">
            {{-- Service Information --}}
            <div class="section">
                <div class="section-title">Informasi Layanan</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Jenis Layanan</div>
                        <div class="info-value">{{ $service->jenis_layanan }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Pengajuan</div>
                        <div class="info-value">{{ $service->created_at->format('d F Y, H:i') }} WIB</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $service->status)) }}">
                                {{ $service->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Applicant Information --}}
            <div class="section">
                <div class="section-title">Data Pemohon</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nama Pemohon</div>
                        <div class="info-value">{{ $service->nama_pemohon }}</div>
                    </div>
                    @if($service->nik)
                        <div class="info-row">
                            <div class="info-label">NIK</div>
                            <div class="info-value">{{ $service->nik }}</div>
                        </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">WhatsApp</div>
                        <div class="info-value">{{ $service->whatsapp }}</div>
                    </div>
                    @if($service->desa)
                        <div class="info-row">
                            <div class="info-label">Desa</div>
                            <div class="info-value">{{ $service->desa->nama_desa }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- QR Code Section --}}
            <div class="qr-section">
                <h3>🔍 Lacak Status Berkas Anda</h3>
                <div class="qr-code">
                    <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" width="200" height="200">
                </div>
                <div class="qr-instruction">
                    <strong>Cara menggunakan:</strong><br>
                    1. Scan QR Code di atas dengan kamera HP Anda<br>
                    2. Atau kunjungi: <strong>{{ parse_url($trackingUrl, PHP_URL_HOST) }}/lacak-berkas</strong><br>
                    3. Masukkan nomor WhatsApp atau ID di atas<br>
                    <br>
                    💡 <em>Simpan struk ini untuk cek status kapan saja</em>
                </div>
            </div>
        </div>

        {{-- Important Note --}}
        <div class="important-note">
            <strong>⚠️ PENTING!</strong>
            Simpan struk ini dengan baik. Anda memerlukan QR Code atau nomor pengajuan untuk melacak status berkas Anda.
            Jika berkas sudah selesai, Anda akan mendapat notifikasi melalui WhatsApp atau bisa cek langsung via
            tracking.
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>📍 Alamat</h4>
                    <p>{{ $appProfile->address ?? 'Kantor Kecamatan' }}</p>
                </div>
                <div class="footer-col">
                    <h4>📞 Kontak</h4>
                    <p>{{ $appProfile->phone ?? '-' }}</p>
                    <p>{{ $appProfile->email ?? '-' }}</p>
                </div>
                <div class="footer-col">
                    <h4>🕐 Jam Layanan</h4>
                    <p>{{ $appProfile->office_hours ?? 'Senin - Jumat, 08:00 - 14:00' }}</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>