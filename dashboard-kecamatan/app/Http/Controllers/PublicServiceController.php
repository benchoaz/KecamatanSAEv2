<?php

namespace App\Http\Controllers;

use App\Models\PublicService;
use App\Models\Desa;
use App\Models\PengunjungKecamatan;
use App\Models\PublicServiceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PublicServiceController extends Controller
{
    public function submit(Request $request)
    {
        // 1. Honeypot check (simple anti-spam)
        if ($request->filled('website')) {
            return response()->json(['message' => 'Spam detected.'], 422);
        }

        // 2. Rate Limiting (2 reports / 24h per WA number)
        $count = PublicService::where('whatsapp', $request->whatsapp)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();
        if ($count >= 2) {
            return response()->json(['message' => 'Anda telah mencapai batas pengiriman laporan hari ini. Silakan coba lagi besok.'], 429);
        }

        // 3. Security Keyword filtering (Soft redirection to SP4N-LAPOR)
        $isHandoff = Str::startsWith($request->uraian, '[Diteruskan dari Bot FAQ]');

        if (!$isHandoff) {
            $securityKeywords = ['korupsi', 'suap', 'pencurian', 'pidana', 'dana desa'];
            foreach ($securityKeywords as $keyword) {
                if (Str::contains(strtolower($request->uraian), $keyword)) {
                    return response()->json([
                        'type' => 'security_referral',
                        'message' => 'Informasi: Untuk laporan terkait indikasi tata kelola keuangan atau penyimpangan berat, disarankan menggunakan kanal resmi SP4N-LAPOR! demi perlindungan data Anda.',
                        'link' => 'https://lapor.go.id'
                    ], 200);
                }
            }

            // 4. SIAK keyword filtering (Passive redirection)
            $siakKeywords = ['ktp', 'kk', 'kartu keluarga', 'akta', 'capil', 'siak', 'domisili'];
            foreach ($siakKeywords as $keyword) {
                if (Str::contains(strtolower($request->uraian), $keyword)) {
                    return response()->json([
                        'type' => 'siak_referral',
                        'message' => 'Informasi: Untuk layanan kependudukan (KTP, KK, Akta), silakan merujuk ke portal resmi SIAK atau layanan Dispendukcapil Kabupaten.',
                        'link' => 'https://siakterpusat.kemendagri.go.id'
                    ], 200);
                }
            }
        }

        // 5. FAQ Logic Integration
        if ($request->filled('uraian')) {
            $userQuestion = strtolower($request->uraian);
            $matchingFaq = \App\Models\PelayananFaq::where('is_active', true)->get()->first(function ($faq) use ($userQuestion) {
                $keywords = explode(',', strtolower($faq->keywords));
                foreach ($keywords as $kw) {
                    if (trim($kw) !== '' && str_contains($userQuestion, trim($kw))) {
                        return true;
                    }
                }
                return false;
            });

            if ($matchingFaq) {
                return response()->json([
                    'type' => 'faq_match',
                    'question' => $matchingFaq->question,
                    'message' => "Jawaban Otomatis:\n" . $matchingFaq->answer . "\n\nInformasi ini bersifat umum. Jika Anda masih ingin mengirim laporan resmi, silakan ubah sedikit deskripsi Anda atau sampaikan detail lainnya.",
                    'answer' => $matchingFaq->answer
                ], 200);
            }
        }

        // Honeypot check for bots
        if ($request->filled('website')) {
            return response()->json(['message' => 'Layanan tidak dapat diproses (Spam detected).'], 422);
        }

        // 5. Validation
        $validator = Validator::make($request->all(), [
            'jenis_layanan' => 'required|string',
            'desa_id' => 'nullable|string', // Changed to string to handle '999'
            'nama_pemohon' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:16',
            'uraian' => 'required|string|max:1000',
            'whatsapp' => 'required|string|regex:/^[0-9+]+$/',
            'foto.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $desaId = $request->desa_id;
        if ($desaId == '999') {
            $desaId = null;
        }

        // 6. Create record (Status: Menunggu Klarifikasi)
        $service = PublicService::create([
            'uuid' => (string) Str::uuid(),
            'nama_pemohon' => $request->nama_pemohon ?? 'Warga (Bot)',
            'nik' => $request->nik,
            'desa_id' => $desaId,
            'jenis_layanan' => $request->jenis_layanan,
            'uraian' => $request->uraian,
            'whatsapp' => $request->whatsapp,
            'is_agreed' => $request->boolean('is_agreed', true),
            'ip_address' => $request->ip(),
            'status' => PublicService::STATUS_MENUNGGU,
            'category' => PublicService::CATEGORY_PELAYANAN,
            'source' => $request->input('source', 'web_form')
        ]);

        // 6b. GUEST BOOK INTEGRATION: Create record in pengunjung_kecamatan
        try {
            PengunjungKecamatan::create([
                'nama' => $request->nama_pemohon ?? 'Warga (Bot)',
                'nik' => $request->nik,
                'desa_asal_id' => $desaId,
                'alamat_luar' => ($request->desa_id == '999') ? 'Luar Wilayah Kecamatan Besuk' : null,
                'no_hp' => $request->whatsapp,
                'tujuan_bidang' => 'Pelayanan Umum', // Aligned with visitor dropdown
                'keperluan' => '[' . $request->jenis_layanan . '] ' . $request->uraian,
                'jam_datang' => now(),
                'status' => 'menunggu'
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mencatat buku tamu (PublicService): ' . $e->getMessage());
        }

        // 7. Handle uploads (Dynamic Multi-File)
        if ($request->hasFile('foto')) {
            $files = $request->file('foto');
            $labels = $request->input('foto_labels', []);

            foreach ($files as $i => $file) {
                if ($file->isValid()) {
                    $path = $file->store('public_services', 'local');
                    $label = $labels[$i] ?? 'Berkas ' . ($i + 1);

                    PublicServiceAttachment::create([
                        'public_service_id' => $service->id,
                        'label' => $label,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType()
                    ]);

                    // Fallback for old system (Keep first 2 as file_path_1 and file_path_2 for basic compatibility)
                    if ($i === 0)
                        $service->update(['file_path_1' => $path]);
                    if ($i === 1)
                        $service->update(['file_path_2' => $path]);
                }
            }
        }

        return response()->json([
            'message' => 'Terima kasih. Laporan Anda telah kami terima dengan PIN Lacak: ' . $service->tracking_code . '. Status awal: "Menunggu Klarifikasi".',
            'uuid' => $service->uuid,
            'tracking_code' => $service->tracking_code,
            'receipt_url' => route('receipt.download', $service->uuid),
            'tracking_url' => route('public.tracking') . '?q=' . $service->tracking_code
        ]);
    }

    public function faqSearch(Request $request)
    {
        $query = strtolower($request->query('q', ''));
        if (empty($query)) {
            return response()->json(['answer' => null]);
        }

        // 0. Pre-processing & Synonyms (Fix for user expectation vs keyword mismatch)
        $synonyms = [
            'jam layanan' => 'jam pelayanan',
            'buka jam' => 'jam pelayanan',
            'tutup jam' => 'jam pelayanan',
            'jadwal' => 'jam',
            'syarat' => 'persyaratan'
        ];

        foreach ($synonyms as $from => $to) {
            if (str_contains($query, $from)) {
                $query = str_replace($from, $to, $query);
            }
        }

        // 0. Priority Checklist: Darurat Category from Database (User Managed Override)
        $emergencyFaqs = \App\Models\PelayananFaq::where('is_active', true)
            ->where('category', 'Darurat')
            ->get();

        foreach ($emergencyFaqs as $faq) {
            $keywords = explode(',', strtolower($faq->keywords));
            foreach ($keywords as $kw) {
                $trimmedKw = trim($kw);
                if ($trimmedKw !== '' && preg_match('/\b' . preg_quote($trimmedKw, '/') . '\b/i', $query)) {
                    return response()->json([
                        'found' => true,
                        'is_emergency' => true,
                        'results' => [['jawaban' => $faq->answer]]
                    ]);
                }
            }
        }

        // 1. Hardcoded Safety Fallbacks (If DB entry is missing or inactive)

        // 1.1 Criminal Emergency
        $criminalKeywords = [
            'maling',
            'pencurian',
            'perampokan',
            'dirampok',
            'kriminal',
            'kejahatan',
            'kekerasan',
            'curi',
            'jambret',
            'begal',
            'penodongan',
            'maling!',
            'pencuri'
        ];
        foreach ($criminalKeywords as $ckw) {
            if (preg_match('/\b' . preg_quote($ckw, '/') . '\b/i', $query)) {
                return response()->json([
                    'found' => true,
                    'is_emergency' => true,
                    'results' => [['jawaban' => "⚠️ Jika Anda mengalami atau melihat tindak pencurian atau kejahatan:\n\n1. Segera hubungi Kepolisian melalui nomor 110\n2. Atau laporkan langsung ke Polsek terdekat\n3. Mintalah Surat Tanda Lapor Polisi (STLP) jika diperlukan\n\nUtamakan keselamatan diri Anda."]]
                ]);
            }
        }

        // 1.2 Health Emergency
        $healthKeywords = [
            'pingsan',
            'sesak napas',
            'kejang',
            'kecelakaan',
            'luka berat',
            'darah banyak',
            'darurat kesehatan',
            'sakit parah',
            'serangan jantung',
            'melahirkan',
            'ambulan'
        ];
        foreach ($healthKeywords as $hkw) {
            if (preg_match('/\b' . preg_quote($hkw, '/') . '\b/i', $query)) {
                return response()->json([
                    'found' => true,
                    'is_emergency' => true,
                    'results' => [['jawaban' => "⚠️ Jika terjadi keadaan darurat kesehatan:\n\n1. Segera hubungi layanan darurat medis (119) atau fasilitas kesehatan terdekat\n2. Jika memungkinkan, minta bantuan warga sekitar\n3. Jika korban tidak sadar atau luka berat, jangan dipindahkan sembarangan\n\nUtamakan keselamatan dan pertolongan pertama."]]
                ]);
            }
        }

        // 1.3 Social Conflict
        $conflictKeywords = [
            'keributan',
            'tawuran',
            'bentrok',
            'perkelahian',
            'ancaman',
            'gangguan ketertiban',
            'konflik warga',
            'demo',
            'unjuk rasa',
            'rusuh'
        ];
        foreach ($conflictKeywords as $ckw) {
            if (preg_match('/\b' . preg_quote($ckw, '/') . '\b/i', $query)) {
                return response()->json([
                    'found' => true,
                    'is_emergency' => true,
                    'results' => [['jawaban' => "⚠️ Jika terjadi konflik atau gangguan ketertiban:\n\n1. Hindari lokasi kejadian demi keselamatan\n2. Segera laporkan ke aparat keamanan setempat\n3. Jangan melakukan tindakan balasan atau provokasi\n\nMari jaga keamanan dan ketertiban bersama."]]
                ]);
            }
        }

        // 1.4 Natural Disaster
        $disasterKeywords = [
            'banjir',
            'longsor',
            'gempa',
            'kebakaran',
            'angin kencang',
            'bencana alam',
            'evakuasi',
            'puting beliung',
            'tsunami',
            'gunung meletus',
            'damkar'
        ];
        foreach ($disasterKeywords as $dkw) {
            if (preg_match('/\b' . preg_quote($dkw, '/') . '\b/i', $query)) {
                return response()->json([
                    'found' => true,
                    'is_emergency' => true,
                    'results' => [['jawaban' => "⚠️ Jika terjadi bencana alam:\n\n1. Segera menjauh dari lokasi berbahaya\n2. Ikuti arahan petugas dan aparat setempat\n3. Siapkan dokumen penting dan kebutuhan darurat\n\nKeselamatan jiwa adalah yang utama.\nNO DARURAT PETUGAS DAMKAR 112"]]
                ]);
            }
        }

        // 1.5 General Backup
        $generalEmergency = ['darurat', 'begal', 'bantuan'];
        foreach ($generalEmergency as $ekw) {
            if (preg_match('/\b' . preg_quote($ekw, '/') . '\b/i', $query)) {
                return response()->json([
                    'found' => true,
                    'is_emergency' => true,
                    'results' => [['jawaban' => "⚠️ **Peringatan Darurat Keamanan/Keselamatan!**\n\nLayanan ini hanya untuk informasi administrasi. Untuk situasi darurat, segera hubungi:\n- **Polisi/Keadaan Darurat**: 110\n- **Ambulans/Medis**: 119\n- **Pemadam Kebakaran**: 113\n\nTetap tenang dan cari tempat aman."]]
                ]);
            }
        }

        // 4. Strict FAQ Matching
        // Phase A: Search by Question Title (Priority)
        $matchingFaq = \App\Models\PelayananFaq::where('is_active', true)
            ->whereRaw('LOWER(question) LIKE ?', ["%{$query}%"])
            ->first();

        // Phase B: Search by Keywords (Fallback)
        if (!$matchingFaq) {
            $matchingFaq = \App\Models\PelayananFaq::where('is_active', true)->get()->first(function ($faq) use ($query) {
                $keywords = explode(',', strtolower($faq->keywords));
                foreach ($keywords as $kw) {
                    $trimmedKw = trim($kw);
                    if ($trimmedKw !== '' && preg_match('/\b' . preg_quote($trimmedKw, '/') . '\b/i', $query)) {
                        return true;
                    }
                }
                return false;
            });
        }

        // 3. Verbatim Response
        if ($matchingFaq) {
            return response()->json([
                'found' => true,
                'question' => $matchingFaq->question,
                'results' => [['jawaban' => $matchingFaq->answer]]
            ]);
        }

        return response()->json([
            'found' => false,
            'answer' => "Maaf, informasi terkait hal tersebut tidak ditemukan dalam database FAQ resmi kami. Silakan coba kata kunci lain (seperti: KTP, KK, Pindah) atau datang langsung ke kantor Kecamatan pada jam kerja."
        ]);
    }

    /**
     * Public Tracking Page
     */
    public function trackingPage()
    {
        return view('public.tracking');
    }

    /**
     * Check Status via WA or UUID
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string'
        ]);

        $identifier = $request->identifier;
        $cleanIdentifier = preg_replace('/[^0-9]/', '', $identifier);

        // Try to find by Tracking PIN, UUID or WhatsApp
        // MUST be in category 'pelayanan' as per user request (focus on files/docs)
        $query = PublicService::where('category', PublicService::CATEGORY_PELAYANAN)
            ->where(function ($q) use ($identifier, $cleanIdentifier) {
                $q->where('tracking_code', $identifier)
                    ->orWhere('uuid', $identifier);

                // If it looks like a phone number (longer than 8 digits), use fuzzy match
                if (strlen($cleanIdentifier) >= 9) {
                    // Get the last 10 digits for robust matching (e.g. bypass +62 vs 08)
                    $suffix = substr($cleanIdentifier, -10);
                    $q->orWhere('whatsapp', 'LIKE', '%' . $suffix);
                } else {
                    $q->orWhere('whatsapp', $identifier);
                }
            });

        $service = $query->with(['desa', 'handler'])
            ->latest()
            ->first();

        if (!$service) {
            return response()->json([
                'found' => false,
                'message' => 'Berkas tidak ditemukan. Pastikan nomor WA atau ID berkas sudah benar.'
            ], 404);
        }

        // Build response
        $response = [
            'found' => true,
            'uuid' => $service->uuid,
            'tracking_code' => $service->tracking_code,
            'jenis_layanan' => $service->jenis_layanan,
            'status' => $service->status,
            'status_label' => $service->status_label,
            'status_color' => $service->status_color,
            'created_at' => $service->created_at->format('d M Y, H:i'),
            'public_response' => $service->effective_public_response,
            'completion_type' => $service->completion_type,
        ];

        // Digital completion
        if ($service->completion_type === 'digital' && $service->result_file_path) {
            $response['download_url'] = asset('storage/' . $service->result_file_path);
        }

        // Physical completion
        if ($service->completion_type === 'physical') {
            $response['pickup_info'] = [
                'ready_at' => $service->ready_at?->format('d M Y, H:i'),
                'pickup_person' => $service->pickup_person,
                'pickup_notes' => $service->pickup_notes,
            ];
        }

        return response()->json($response);
    }

    /**
     * Store PublicService from WhatsApp API Gateway
     * Called by the WhatsApp automation system via REST API
     */
    public function storeFromWhatsapp(Request $request)
    {
        // Validate incoming data from WhatsApp API
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|string|unique:public_services,uuid',
            'category' => 'required|in:pengaduan,pelayanan,umkm,loker',
            'whatsapp' => 'required|string',
            'nama_pemohon' => 'required|string|max:255',
            'uraian' => 'required|string',
            'jenis_layanan' => 'required|string',
            'status' => 'nullable|string',
            'source' => 'nullable|string',
            'desa_id' => 'nullable|integer|exists:desa,id',
            'nama_desa_manual' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create PublicService record
            $service = PublicService::create([
                'uuid' => $request->uuid,
                'category' => $request->category,
                'source' => $request->input('source', 'whatsapp'),
                'whatsapp' => $request->whatsapp,
                'nama_pemohon' => $request->nama_pemohon,
                'uraian' => $request->uraian,
                'jenis_layanan' => $request->jenis_layanan,
                'status' => $request->input('status', PublicService::STATUS_MENUNGGU),
                'desa_id' => $request->desa_id,
                'nama_desa_manual' => $request->nama_desa_manual,
                'ip_address' => $request->ip(),
            ]);

            // Log successful creation
            \Log::info('WhatsApp message received and stored', [
                'service_id' => $service->id,
                'category' => $service->category,
                'phone' => $service->whatsapp
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp message successfully stored. Tracking PIN: ' . $service->tracking_code,
                'data' => [
                    'id' => $service->id,
                    'uuid' => $service->uuid,
                    'tracking_code' => $service->tracking_code,
                    'category' => $service->category,
                    'status' => $service->status
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Failed to store WhatsApp message', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store message',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}

