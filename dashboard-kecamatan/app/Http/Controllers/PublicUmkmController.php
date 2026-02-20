<?php

namespace App\Http\Controllers;

use App\Models\UmkmLocal;
use Illuminate\Http\Request;

class PublicUmkmController extends Controller
{
    public function index(Request $request)
    {
        $query = UmkmLocal::where('is_active', true);

        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('product', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        $umkms = $query->latest()->paginate(12);

        return view('public.umkm.index', compact('umkms'));
    }

    public function show($id)
    {
        $umkm = UmkmLocal::where('is_active', true)->findOrFail($id);

        // WhatsApp message formatting
        $message = "Halo " . $umkm->name . ", saya tertarik dengan produk " . $umkm->product . " yang saya lihat di Website Kecamatan Besuk.";
        if ($umkm->price) {
            $message .= " Apakah stok seharga Rp " . number_format((float) $umkm->price, 0, ',', '.') . " masih tersedia?";
        }

        $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $umkm->contact_wa) . "?text=" . urlencode($message);

        return view('public.umkm.show', compact('umkm', 'waUrl'));
    }
}
