<?php

namespace App\Http\Controllers;

use App\Models\PublicService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReceiptController extends Controller
{
    /**
     * Generate PDF Receipt with QR Code
     */
    public function generateReceipt($uuid)
    {
        $service = PublicService::where('uuid', $uuid)
            ->with(['desa', 'handler'])
            ->firstOrFail();

        // Generate QR Code as base64
        $trackingUrl = route('public.tracking') . '?q=' . $service->uuid;
        $qrCode = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($trackingUrl));

        // Prepare data for PDF
        $data = [
            'service' => $service,
            'qrCode' => $qrCode,
            'trackingUrl' => $trackingUrl,
            'appProfile' => app('App\Services\ApplicationProfileService'),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('receipts.service-receipt', $data)
            ->setPaper('a4', 'portrait');

        // Download filename
        $filename = 'Struk_Pengajuan_' . $service->uuid . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Preview Receipt (HTML view)
     */
    public function previewReceipt($uuid)
    {
        $service = PublicService::where('uuid', $uuid)
            ->with(['desa', 'handler'])
            ->firstOrFail();

        $trackingUrl = route('public.tracking') . '?q=' . $service->uuid;
        $qrCode = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($trackingUrl));

        $data = [
            'service' => $service,
            'qrCode' => $qrCode,
            'trackingUrl' => $trackingUrl,
            'appProfile' => app('App\Services\ApplicationProfileService'),
        ];

        return view('receipts.service-receipt', $data);
    }

    /**
     * Generate QR Code Image Only
     */
    public function generateQrCode($uuid)
    {
        $service = PublicService::where('uuid', $uuid)->firstOrFail();
        $trackingUrl = route('public.tracking') . '?q=' . $service->uuid;

        return QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($trackingUrl);
    }
}
