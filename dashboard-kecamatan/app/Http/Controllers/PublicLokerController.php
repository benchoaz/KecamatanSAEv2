<?php

namespace App\Http\Controllers;

use App\Models\Loker;
use App\Models\Desa;
use App\Models\PublicService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicLokerController extends Controller
{
    /**
     * Display listing of Loker
     */
    public function index(Request $request)
    {
        $query = Loker::where('status', Loker::STATUS_AKTIF)
            ->where('is_sensitive', false);

        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('job_category', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('desa') && $request->desa != '') {
            $query->where('desa_id', $request->desa);
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('job_category', $request->category);
        }

        $lokers = $query->latest()->paginate(12);
        $desas = Desa::orderBy('nama_desa')->get();

        // Categories for filter
        $categories = [
            'Buruh tani',
            'Tukang bangunan',
            'Tukang pijet',
            'ART',
            'Ojek / becak',
            'Lainnya'
        ];

        return view('public.loker.index', compact('lokers', 'desas', 'categories'));
    }

    /**
     * Show registration form
     */
    public function create()
    {
        $desas = Desa::orderBy('nama_desa')->get();
        $categories = [
            'Buruh tani',
            'Tukang bangunan',
            'Tukang pijet',
            'ART',
            'Ojek / becak',
            'Lainnya'
        ];
        return view('public.loker.create', compact('desas', 'categories'));
    }

    /**
     * Store new Loker registration
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'job_category' => 'required|string',
            'contact_wa' => 'required|string|max:20',
            'desa_id' => 'nullable|exists:desa,id',
            'nama_desa_manual' => 'nullable|string|max:255',
            'work_time' => 'nullable|string',
        ]);

        $loker = Loker::create([
            'title' => $request->title,
            'job_category' => $request->job_category,
            'contact_wa' => $request->contact_wa,
            'desa_id' => $request->desa_id,
            'nama_desa_manual' => $request->nama_desa_manual,
            'description' => $request->description,
            'work_time' => $request->work_time,
            'is_available_today' => $request->has('is_available_today') || $request->is_available_today == '1',
            'status' => Loker::STATUS_MENUNGGU,
            'source' => 'web_form'
        ]);

        // Create Public Service entry for Inbox
        PublicService::create([
            'uuid' => (string) Str::uuid(),
            'nama_pemohon' => $loker->title,
            'desa_id' => $request->desa_id,
            'nama_desa_manual' => $request->nama_desa_manual,
            'jenis_layanan' => 'Pendaftaran Loker',
            'uraian' => "Pendaftaran Loker Baru: {$loker->title}. Kategori: {$loker->job_category}. Kontak: {$loker->contact_wa}. Waktu: {$loker->work_time}.",
            'whatsapp' => $loker->contact_wa,
            'status' => PublicService::STATUS_MENUNGGU,
            'category' => PublicService::CATEGORY_LOKER,
            'source' => 'web_form'
        ]);

        return redirect()->route('public.loker.index')->with('success', 'Terima kasih. Info kerja Anda akan ditampilkan setelah diverifikasi kecamatan.');
    }
}
