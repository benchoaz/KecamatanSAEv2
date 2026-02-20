<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\DokumenDesa;
use App\Models\InventarisDesa;
use App\Models\LembagaDesa;
use App\Models\PerencanaanDesa;
use App\Models\PersonilDesa;
use App\Models\Submission;
use App\Repositories\Interfaces\SubmissionRepositoryInterface;
use App\Services\MasterDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpZip\ZipFile;

class PemerintahanController extends Controller
{
    protected $submissionRepo;
    protected $masterData;

    public function __construct(
        SubmissionRepositoryInterface $submissionRepo,
        MasterDataService $masterData
    ) {
        $this->submissionRepo = $submissionRepo;
        $this->masterData = $masterData;
    }

    public function index()
    {
        $user = auth()->user();
        abort_unless($user->desa_id !== null, 403);

        $desa_id = $user->desa_id;

        $pemerintahanMenus = [
            'A' => ['title' => 'Administrasi Perangkat Desa', 'icon' => 'fa-users-gear', 'route' => 'desa.pemerintahan.personil.index', 'desc' => 'Arsip data & SK pengangkatan/pemberhentian perangkat.'],
            'B' => ['title' => 'Administrasi BPD', 'icon' => 'fa-users-rectangle', 'route' => 'desa.pemerintahan.bpd.index', 'desc' => 'Arsip data pimpinan & anggota serta masa keanggotaan BPD.'],
            'C' => ['title' => 'Registrasi Lembaga Desa', 'icon' => 'fa-sitemap', 'route' => 'desa.pemerintahan.lembaga.index', 'desc' => 'Pendataan struktur & kepengurusan lembaga kemasyarakatan.'],
            'D' => ['title' => 'Arsip Perencanaan Desa', 'icon' => 'fa-calendar-check', 'route' => 'desa.pemerintahan.perencanaan.index', 'desc' => 'Penyimpanan dokumen Musrenbang & usulan pembangunan desa.'],
            'E' => ['title' => 'Monitoring LKPJ & LPPD', 'icon' => 'fa-file-signature', 'route' => 'desa.pemerintahan.laporan.index', 'desc' => 'Pemantauan status penyampaian & kelengkapan laporan tahunan.'],
            'F' => ['title' => 'Administrasi Inventaris', 'icon' => 'fa-boxes-stacked', 'route' => 'desa.pemerintahan.inventaris.index', 'desc' => 'Pendataan status administrasi aset barang & tanah milik desa.'],
            'G' => ['title' => 'Arsip Dokumen Perencanaan', 'icon' => 'fa-folder-open', 'route' => 'desa.pemerintahan.dokumen.index', 'desc' => 'Penyimpanan referensi dokumen RPJMDes & RKPDes (Tanpa APBDes).'],
        ];

        $healthMetrics = $this->calculateHealth($desa_id);

        $recentSubmissions = Submission::where('desa_id', $desa_id)
            ->with(['menu', 'aspek'])
            ->latest()
            ->take(5)
            ->get();

        return view('kecamatan.pemerintahan.index', compact('pemerintahanMenus', 'healthMetrics', 'desa_id', 'recentSubmissions'));
    }

    public function personilIndex()
    {
        $desa_id = auth()->user()->desa_id;
        $personils = PersonilDesa::where('desa_id', $desa_id)
            ->where('kategori', 'perangkat')
            ->orderBy('is_active', 'desc')
            ->orderBy('jabatan')
            ->get();

        return view('kecamatan.pemerintahan.personil.index', [
            'personils' => $personils,
            'desa_id' => $desa_id,
            'store_route' => route('desa.pemerintahan.detail.personil.store') // Garis bawah: Link domain desa tidak melompat
        ]);
    }

    public function bpdIndex()
    {
        $desa_id = auth()->user()->desa_id;
        $personils = PersonilDesa::where('desa_id', $desa_id)
            ->where('kategori', 'bpd')
            ->orderBy('is_active', 'desc')
            ->get();

        return view('kecamatan.pemerintahan.personil.index', [
            'personils' => $personils,
            'desa_id' => $desa_id,
            'title' => 'Struktur BPD',
            'kategori' => 'bpd'
        ]);
    }

    public function personilStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'nik' => 'required|digits:16',
            'jabatan' => 'required|string',
            'kategori' => 'required|in:perangkat,bpd',
            'nomor_sk' => 'nullable|string',
            'masa_jabatan_mulai' => 'nullable|date',
            'file_sk' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $validated['desa_id'] = auth()->user()->desa_id;

        if ($request->hasFile('file_sk')) {
            $path = $request->file('file_sk')->store('perangkat_sk', 'local');
            $validated['file_sk'] = $path;
        }

        PersonilDesa::create($validated);
        return back()->with('success', 'Data personil berhasil ditambahkan.');
    }

    public function inventarisIndex()
    {
        $desa_id = auth()->user()->desa_id;
        $inventaris = InventarisDesa::where('desa_id', $desa_id)
            ->orderBy('tipe_aset')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kecamatan.pemerintahan.inventaris.index', compact('inventaris', 'desa_id'));
    }

    public function inventarisStore(Request $request)
    {
        $validated = $request->validate([
            'tipe_aset' => 'required|in:barang,tanah',
            'nama_item' => 'required|string',
            'tahun_perolehan' => 'nullable|digits:4',
            'kondisi' => 'nullable|string',
            'status_sengketa' => 'required|string',
            'luas' => 'nullable|string',
            'nomor_legalitas' => 'nullable|string',
        ]);

        $validated['desa_id'] = auth()->user()->desa_id;
        InventarisDesa::create($validated);

        return back()->with('success', 'Data inventaris berhasil disimpan.');
    }

    public function perencanaanIndex()
    {
        $desa_id = auth()->user()->desa_id;
        $perencanaan = PerencanaanDesa::where('desa_id', $desa_id)
            ->withCount('usulan')
            ->orderBy('tahun', 'desc')
            ->get();
        $currentPhase = $this->getCurrentPhase();

        return view('kecamatan.pemerintahan.perencanaan.index', compact('perencanaan', 'desa_id', 'currentPhase'));
    }

    public function perencanaanShow($id)
    {
        $perencanaan = PerencanaanDesa::with(['usulan', 'desa'])->findOrFail($id);
        abort_unless($perencanaan->desa_id === auth()->user()->desa_id, 403);
        return view('kecamatan.pemerintahan.perencanaan.show', compact('perencanaan'));
    }

    public function perencanaanStore(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|digits:4',
            'tanggal_kegiatan' => 'required|date',
            'lokasi' => 'required|string',
            'file_ba' => 'required|file|mimes:pdf|max:2048',
            'file_absensi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'usulan' => 'required|array|min:1',
            'usulan.*.bidang' => 'required|string',
            'usulan.*.uraian' => 'required|string',
            'usulan.*.prioritas' => 'required|in:tinggi,sedang,rendah',
        ]);

        $desa_id = auth()->user()->desa_id;

        DB::beginTransaction();
        try {
            $perencanaan = PerencanaanDesa::create([
                'desa_id' => $desa_id,
                'tahun' => $validated['tahun'],
                'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
                'lokasi' => $validated['lokasi'],
                'status_administrasi' => 'lengkap'
            ]);

            if ($request->hasFile('file_ba')) {
                $perencanaan->file_ba = $request->file('file_ba')->store('perencanaan/ba', 'local');
            }
            if ($request->hasFile('file_absensi')) {
                $perencanaan->file_absensi = $request->file('file_absensi')->store('perencanaan/absensi', 'local');
            }
            if ($request->hasFile('file_foto')) {
                $perencanaan->file_foto = $request->file('file_foto')->store('perencanaan/foto', 'local');
            }
            $perencanaan->save();

            foreach ($validated['usulan'] as $u) {
                $perencanaan->usulan()->create($u);
            }

            DB::commit();
            return back()->with('success', 'Data Musrenbang & Usulan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function laporanIndex()
    {
        $desa_id = auth()->user()->desa_id;
        $laporans = DokumenDesa::where('desa_id', $desa_id)
            ->whereIn('tipe_dokumen', ['LKPJ', 'LPPD'])
            ->orderBy('tahun', 'desc')
            ->get();

        return view('kecamatan.pemerintahan.laporan.index', compact('laporans', 'desa_id'));
    }

    public function dokumenIndex()
    {
        $desa_id = auth()->user()->desa_id;
        $dokumens = DokumenDesa::where('desa_id', $desa_id)
            ->whereIn('tipe_dokumen', ['RPJMDes', 'RKPDes'])
            ->orderBy('tahun', 'desc')
            ->get();

        return view('kecamatan.pemerintahan.dokumen.index', compact('dokumens', 'desa_id'));
    }

    public function dokumenStore(Request $request)
    {
        $validated = $request->validate([
            'tipe_dokumen' => 'required|string',
            'tahun' => 'required|digits:4',
            'tanggal_penyampaian' => 'nullable|date',
            'file_dokumen' => 'required|file|mimes:pdf|max:5120',
        ]);

        $validated['desa_id'] = auth()->user()->desa_id;

        if ($request->hasFile('file_dokumen')) {
            $path = $request->file('file_dokumen')->store('desa_dokumen', 'local');
            $validated['file_path'] = $path;
        }

        DokumenDesa::create($validated);
        return back()->with('success', 'Dokumen berhasil diarsipkan.');
    }

    public function lembagaIndex()
    {
        $desa_id = auth()->user()->desa_id;
        $lembagas = LembagaDesa::where('desa_id', $desa_id)
            ->orderBy('tipe_lembaga')
            ->get();

        return view('kecamatan.pemerintahan.lembaga.index', compact('lembagas', 'desa_id'));
    }

    public function lembagaStore(Request $request)
    {
        $validated = $request->validate([
            'nama_lembaga' => 'required|string',
            'tipe_lembaga' => 'required|string',
            'ketua' => 'nullable|string',
            'nomor_sk' => 'nullable|string',
            'tahun_pembentukan' => 'nullable|digits:4',
            'file_sk' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $validated['desa_id'] = auth()->user()->desa_id;

        if ($request->hasFile('file_sk')) {
            $path = $request->file('file_sk')->store('lembaga_sk', 'local');
            $validated['file_sk'] = $path;
        }

        LembagaDesa::create($validated);
        return back()->with('success', 'Data lembaga berhasil ditambahkan.');
    }

    public function exportAudit()
    {
        $desa = auth()->user()->desa;
        abort_unless($desa, 404);

        $zipFile = new ZipFile();
        $zipName = "Paket_Audit_" . str_replace(' ', '_', $desa->nama_desa) . "_" . date('Ymd') . ".zip";

        $personils = PersonilDesa::where('desa_id', $desa->id)->whereNotNull('file_sk')->get();
        foreach ($personils as $p) {
            $fullPath = storage_path('app/local/' . $p->file_sk);
            if (file_exists($fullPath)) {
                $zipFile->addFile($fullPath, "A_B_Struktur_Organisasi/" . basename($p->file_sk));
            }
        }

        $lembagas = LembagaDesa::where('desa_id', $desa->id)->whereNotNull('file_sk')->get();
        foreach ($lembagas as $l) {
            $fullPath = storage_path('app/local/' . $l->file_sk);
            if (file_exists($fullPath)) {
                $zipFile->addFile($fullPath, "C_Lembaga_Desa/" . basename($l->file_sk));
            }
        }

        $dokumens = DokumenDesa::where('desa_id', $desa->id)->get();
        foreach ($dokumens as $d) {
            $fullPath = storage_path('app/local/' . $d->file_path);
            if (file_exists($fullPath)) {
                $folder = in_array($d->tipe_dokumen, ['LKPJ', 'LPPD']) ? "E_Laporan_Tahunan/" : "G_Dokumen_Inti/";
                $zipFile->addFile($fullPath, $folder . basename($d->file_path));
            }
        }

        $perencanaans = PerencanaanDesa::where('desa_id', $desa->id)->whereNotNull('file_ba')->get();
        foreach ($perencanaans as $pr) {
            $fullPath = storage_path('app/local/' . $pr->file_ba);
            if (file_exists($fullPath)) {
                $zipFile->addFile($fullPath, "D_Perencanaan/" . basename($pr->file_ba));
            }
        }

        $zipFile->saveAsFile(storage_path('app/temp/' . $zipName));
        $zipFile->close();

        return response()->download(storage_path('app/temp/' . $zipName))->deleteFileAfterSend(true);
    }

    protected function calculateHealth($desa_id)
    {
        $hasKades = PersonilDesa::where('desa_id', $desa_id)->where('jabatan', 'Kepala Desa')->where('is_active', true)->exists();
        $hasSekdes = PersonilDesa::where('desa_id', $desa_id)->where('jabatan', 'Sekretaris Desa')->where('is_active', true)->exists();
        $hasBpd = PersonilDesa::where('desa_id', $desa_id)->where('kategori', 'bpd')->where('is_active', true)->exists();
        $hasPerencanaan = PerencanaanDesa::where('desa_id', $desa_id)->where('tahun', date('Y'))->where('status', '!=', 'draft')->exists();

        $lastAssetUpdate = InventarisDesa::where('desa_id', $desa_id)->latest('updated_at')->first();
        $hasAssetUpdate = $lastAssetUpdate && $lastAssetUpdate->updated_at->diffInMonths(now()) < 12;

        return [
            'perangkat' => $hasKades && $hasSekdes,
            'bpd' => $hasBpd,
            'perencanaan' => $hasPerencanaan,
            'aset' => $hasAssetUpdate,
            'summary' => [
                'has_kades' => $hasKades,
                'has_sekdes' => $hasSekdes,
                'last_asset' => $lastAssetUpdate ? $lastAssetUpdate->updated_at->format('d/m/Y') : '-'
            ]
        ];
    }

    private function getCurrentPhase()
    {
        $month = (int) date('n');
        if ($month >= 1 && $month <= 6)
            return 'musdes';
        if ($month >= 7 && $month <= 9)
            return 'rkp';
        return 'apbdes';
    }
}
