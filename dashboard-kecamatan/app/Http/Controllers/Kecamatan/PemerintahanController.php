<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\DokumenDesa;
use App\Models\InventarisDesa;
use App\Models\LembagaDesa;
use App\Models\PengunjungKecamatan;
use App\Models\PerencanaanDesa;
use App\Models\PersonilDesa;
use App\Models\Submission;
use App\Repositories\Interfaces\SubmissionRepositoryInterface;
use App\Services\MasterDataService;
use Illuminate\Http\Request;
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
        abort_unless($user->desa_id === null, 403);

        $desa_id = request('desa_id');

        $pemerintahanMenus = [
            'A' => ['title' => 'Administrasi Perangkat Desa', 'icon' => 'fa-users-gear', 'route' => 'kecamatan.pemerintahan.detail.personil.index', 'desc' => 'Arsip data & SK pengangkatan/pemberhentian perangkat.'],
            'B' => ['title' => 'Administrasi BPD', 'icon' => 'fa-users-rectangle', 'route' => 'kecamatan.pemerintahan.detail.bpd.index', 'desc' => 'Arsip data pimpinan & anggota serta masa keanggotaan BPD.'],
            'C' => ['title' => 'Registrasi Lembaga Desa', 'icon' => 'fa-sitemap', 'route' => 'kecamatan.pemerintahan.detail.lembaga.index', 'desc' => 'Pendataan struktur & kepengurusan lembaga kemasyarakatan.'],
            'D' => ['title' => 'Arsip Perencanaan Desa', 'icon' => 'fa-calendar-check', 'route' => 'kecamatan.pemerintahan.detail.perencanaan.index', 'desc' => 'Penyimpanan dokumen Musrenbang & usulan pembangunan desa.'],
            'E' => ['title' => 'Monitoring LKPJ & LPPD', 'icon' => 'fa-file-signature', 'route' => 'kecamatan.pemerintahan.detail.laporan.index', 'desc' => 'Pemantauan status penyampaian & kelengkapan laporan tahunan.'],
            'F' => ['title' => 'Administrasi Inventaris', 'icon' => 'fa-boxes-stacked', 'route' => 'kecamatan.pemerintahan.detail.inventaris.index', 'desc' => 'Pendataan status administrasi aset barang & tanah milik desa.'],
            'G' => ['title' => 'Arsip Dokumen Perencanaan', 'icon' => 'fa-folder-open', 'route' => 'kecamatan.pemerintahan.detail.dokumen.index', 'desc' => 'Penyimpanan referensi dokumen RPJMDes & RKPDes (Tanpa APBDes).'],
            'H' => ['title' => 'Inventaris Peraturan Desa', 'icon' => 'fa-gavel', 'route' => 'kecamatan.pemerintahan.detail.peraturan.index', 'desc' => 'Daftar produk hukum & peraturan desa yang telah ditetapkan.'],
        ];

        $healthMetrics = $desa_id ? $this->calculateHealth($desa_id) : null;

        $recentSubmissions = Submission::when($desa_id, fn($q) => $q->where('desa_id', $desa_id))
            ->with(['menu', 'aspek'])
            ->latest()
            ->take(5)
            ->get();

        return view('kecamatan.pemerintahan.index', compact('pemerintahanMenus', 'healthMetrics', 'desa_id', 'recentSubmissions'));
    }

    public function personilStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'no_hp' => 'nullable|string|max:20',
            'jabatan' => 'required|string',
            'kategori' => 'required|in:perangkat,bpd',
            'nomor_sk' => 'nullable|string|max:255',
            'tanggal_sk' => 'nullable|date',
            'masa_jabatan_mulai' => 'nullable|date',
            'file_sk' => 'nullable|file|mimes:pdf|max:2048',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        $user = auth()->user();
        $desa_id = $user->desa_id ?? $request->desa_id;

        if (!$desa_id) {
            return back()->with('error', 'Desa tidak teridentifikasi.');
        }

        $personil = new PersonilDesa($validated);
        $personil->desa_id = $desa_id;
        $personil->is_active = true;

        if ($request->hasFile('file_sk')) {
            $path = $request->file('file_sk')->store('personil_sk', 'local');
            $personil->file_sk = $path;
        }

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('personil_foto', 'local');
            $personil->foto = $path;
        }

        $personil->save();

        return back()->with('success', 'Data personil berhasil disimpan.');
    }

    public function personilVerify(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:diterima,dikembalikan',
            'catatan' => 'nullable|string'
        ]);

        $personil = PersonilDesa::findOrFail($id);

        // Ensure user has access (kecamatan operator matches desa if needed, or global kecamatan)
        // Kecamatan user usually has access to all desas in their kecamatan
        // Assuming middleware handles basic role. 

        $personil->status = $validated['status'];
        if ($validated['status'] == 'dikembalikan') {
            $personil->catatan_revisi = $validated['catatan'];
        }
        $personil->save();

        return back()->with('success', 'Status personil berhasil diperbarui.');
    }

    public function personilIndex()
    {
        $desa_id = request('desa_id');
        $personils = [];
        $desas = [];

        if ($desa_id) {
            $personils = PersonilDesa::where('desa_id', $desa_id)
                ->where('kategori', 'perangkat')
                ->orderBy('is_active', 'desc')
                ->orderBy('jabatan')
                ->get();
        } else {
            $desas = Desa::withCount([
                'personil as kades_count' => function ($query) {
                    $query->where('kategori', 'perangkat')->where('jabatan', 'like', '%Kepala Desa%');
                },
                'personil as perangkat_count' => function ($query) {
                    $query->where('kategori', 'perangkat')->where('jabatan', 'not like', '%Kepala Desa%');
                }
            ])->orderBy('nama_desa')->get();
        }

        return view('kecamatan.pemerintahan.personil.index', compact('personils', 'desa_id', 'desas') + [
            'store_route' => route('kecamatan.pemerintahan.detail.personil.store')
        ]);
    }

    public function bpdIndex()
    {
        $desa_id = request('desa_id');
        $personils = [];
        $desas = [];

        if ($desa_id) {
            $personils = PersonilDesa::where('desa_id', $desa_id)
                ->where('kategori', 'bpd')
                ->orderBy('is_active', 'desc')
                ->get();
        } else {
            $desas = Desa::withCount([
                'personil as bpd_count' => function ($query) {
                    $query->where('kategori', 'bpd');
                }
            ])->orderBy('nama_desa')->get();
        }

        return view('kecamatan.pemerintahan.personil.index', [
            'personils' => $personils,
            'desa_id' => $desa_id,
            'desas' => $desas,
            'title' => 'Struktur BPD',
            'kategori' => 'bpd'
        ]);
    }

    public function inventarisIndex()
    {
        $desa_id = request('desa_id');
        $inventaris = [];
        $desas = [];

        if ($desa_id) {
            $inventaris = InventarisDesa::where('desa_id', $desa_id)
                ->orderBy('tipe_aset')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $desas = Desa::withCount('inventaris')->orderBy('nama_desa')->get();
        }

        return view('kecamatan.pemerintahan.inventaris.index', compact('inventaris', 'desa_id', 'desas'));
    }

    public function inventarisStore(Request $request)
    {
        $validated = $request->validate([
            'desa_id' => 'required|exists:desa,id',
            'tipe_aset' => 'required|in:barang,tanah',
            'nama_item' => 'required|string|max:255',
            'kode_item' => 'nullable|string|max:255',
            'tahun_perolehan' => 'nullable|digits:4',
            'sumber_dana' => 'nullable|string|max:255',
            'kondisi' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'luas' => 'nullable|string|max:255',
            'nomor_legalitas' => 'nullable|string|max:255',
            'status_sengketa' => 'required|in:aman,sengketa,klaim',
        ]);

        InventarisDesa::create($validated);
        return back()->with('success', 'Data inventaris berhasil disimpan.');
    }

    public function perencanaanIndex()
    {
        $desa_id = request('desa_id');
        $perencanaan = [];
        $desas = [];
        $currentPhase = $this->getCurrentPhase();

        if ($desa_id) {
            $perencanaan = PerencanaanDesa::where('desa_id', $desa_id)
                ->withCount('usulan')
                ->orderBy('tahun', 'desc')
                ->get();
        } else {
            $desas = Desa::withCount('perencanaan')->orderBy('nama_desa')->get();
        }

        return view('kecamatan.pemerintahan.perencanaan.index', compact('perencanaan', 'desa_id', 'desas', 'currentPhase'));
    }

    public function perencanaanShow($id)
    {
        $perencanaan = PerencanaanDesa::with(['usulan', 'desa'])->findOrFail($id);
        return view('kecamatan.pemerintahan.perencanaan.show', compact('perencanaan'));
    }

    public function perencanaanStore(Request $request)
    {
        $validated = $request->validate([
            'desa_id' => 'required|exists:desa,id',
            'tahun' => 'required|digits:4',
            'tanggal_kegiatan' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'file_ba' => 'required|file|mimes:pdf|max:2048',
            'file_absensi' => 'required|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'file_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'usulan' => 'required|array|min:1',
            'usulan.*.bidang' => 'required|string',
            'usulan.*.uraian' => 'required|string',
            'usulan.*.prioritas' => 'required|in:tinggi,sedang,rendah',
        ]);

        $perencanaan = new PerencanaanDesa([
            'desa_id' => $validated['desa_id'],
            'tahun' => $validated['tahun'],
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'lokasi' => $validated['lokasi'],
            'status_administrasi' => PerencanaanDesa::STATUS_LENGKAP,
        ]);

        if ($request->hasFile('file_ba')) {
            $perencanaan->file_ba = $request->file('file_ba')->store('perencanaan_ba', 'local');
        }
        if ($request->hasFile('file_absensi')) {
            $perencanaan->file_absensi = $request->file('file_absensi')->store('perencanaan_absensi', 'local');
        }
        if ($request->hasFile('file_foto')) {
            $perencanaan->file_foto = $request->file('file_foto')->store('perencanaan_foto', 'local');
        }

        $perencanaan->save();

        foreach ($validated['usulan'] as $u) {
            $perencanaan->usulan()->create([
                'bidang' => $u['bidang'],
                'uraian' => $u['uraian'],
                'prioritas' => $u['prioritas'],
            ]);
        }

        return back()->with('success', 'Data perencanaan dan usulan berhasil disimpan.');
    }

    public function perencanaanVerify(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,revision',
            'catatan' => 'nullable|string'
        ]);

        $perencanaan = PerencanaanDesa::findOrFail($id);
        $perencanaan->status_administrasi = $validated['status'];

        if ($validated['status'] == 'revision') {
            $perencanaan->catatan_kecamatan = $validated['catatan'];
        }

        $perencanaan->save();

        return back()->with('success', 'Status administrasi perencanaan telah diperbarui.');
    }

    public function laporanIndex()
    {
        $desa_id = request('desa_id');
        $laporans = [];
        $desas = [];

        if ($desa_id) {
            $laporans = DokumenDesa::where('desa_id', $desa_id)
                ->whereIn('tipe_dokumen', ['LKPJ', 'LPPD'])
                ->orderBy('tahun', 'desc')
                ->get();
        } else {
            $desas = Desa::withCount([
                'dokumens' => function ($q) {
                    $q->whereIn('tipe_dokumen', ['LKPJ', 'LPPD']);
                }
            ])->orderBy('nama_desa')->get();
        }

        return view('kecamatan.pemerintahan.laporan.index', compact('laporans', 'desa_id', 'desas'));
    }

    public function dokumenIndex()
    {
        $desa_id = request('desa_id');
        $dokumens = [];
        $desas = [];

        if ($desa_id) {
            $dokumens = DokumenDesa::where('desa_id', $desa_id)
                ->whereIn('tipe_dokumen', ['RPJMDes', 'RKPDes'])
                ->orderBy('tahun', 'desc')
                ->get();
        } else {
            $desas = Desa::withCount([
                'dokumens' => function ($q) {
                    $q->whereIn('tipe_dokumen', ['RPJMDes', 'RKPDes']);
                }
            ])->orderBy('nama_desa')->get();
        }

        return view('kecamatan.pemerintahan.dokumen.index', [
            'dokumens' => $dokumens,
            'desa_id' => $desa_id,
            'desas' => $desas,
            'title' => 'Arsip Dokumen Perencanaan',
            'tipe_filter' => 'RPJMDes & RKPDes',
            'desc' => 'Monitoring Dokumen RPJMDes (6 Tahunan) & RKPDes (Tahunan).',
            'desc_pilih_desa' => 'Pilih Desa untuk Memantau Kelengkapan Dokumen Perencanaan (RPJMDes/RKPDes).'
        ]);
    }

    public function peraturanIndex()
    {
        $desa_id = request('desa_id');
        $dokumens = [];
        $desas = [];

        if ($desa_id) {
            $dokumens = DokumenDesa::where('desa_id', $desa_id)
                ->where('tipe_dokumen', 'Peraturan Desa')
                ->orderBy('tahun', 'desc')
                ->get();
        } else {
            $desas = Desa::withCount([
                'dokumens' => function ($q) {
                    $q->where('tipe_dokumen', 'Peraturan Desa');
                }
            ])->orderBy('nama_desa')->get();
        }

        return view('kecamatan.pemerintahan.dokumen.index', [
            'dokumens' => $dokumens,
            'desa_id' => $desa_id,
            'desas' => $desas,
            'title' => 'Inventaris Peraturan Desa',
            'tipe_filter' => 'Peraturan Desa',
            'desc' => 'Daftar produk hukum & peraturan desa yang telah ditetapkan.',
            'desc_pilih_desa' => 'Pilih Desa untuk Memantau Kelengkapan Peraturan Desa.'
        ]);
    }

    public function dokumenStore(Request $request)
    {
        $validated = $request->validate([
            'desa_id' => 'required|exists:desa,id',
            'tipe_dokumen' => 'required|string',
            'tahun' => 'required|digits:4',
            'tanggal_penyampaian' => 'nullable|date',
            'file_dokumen' => 'required|file|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('file_dokumen')) {
            $path = $request->file('file_dokumen')->store('desa_dokumen', 'local');
            $validated['file_path'] = $path;
        }

        DokumenDesa::create($validated);
        return back()->with('success', 'Dokumen/Laporan berhasil diarsipkan.');
    }

    public function lembagaIndex()
    {
        $desa_id = request('desa_id');
        $lembagas = [];
        $desas = [];

        if ($desa_id) {
            $lembagas = LembagaDesa::where('desa_id', $desa_id)
                ->orderBy('tipe_lembaga')
                ->get();
        } else {
            $desas = Desa::with([
                'lembaga' => function ($q) {
                    $q->select('id', 'desa_id', 'nama_lembaga');
                }
            ])->orderBy('nama_desa')->get();
        }

        return view('kecamatan.pemerintahan.lembaga.index', compact('lembagas', 'desa_id', 'desas'));
    }

    public function lembagaStore(Request $request)
    {
        $validated = $request->validate([
            'tipe_lembaga' => 'required|string',
            'nama_lembaga' => 'required|string|max:255',
            'ketua' => 'nullable|string|max:255',
            'nomor_sk' => 'nullable|string|max:255',
            'tahun_pembentukan' => 'nullable|digits:4',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $user = auth()->user();
        $desa_id = $user->desa_id ?? $request->desa_id;

        if (!$desa_id) {
            return back()->with('error', 'Desa tidak teridentifikasi. Pilih desa terlebih dahulu.');
        }

        $lembaga = new LembagaDesa($validated);
        $lembaga->desa_id = $desa_id;
        $lembaga->is_active = true;

        if ($request->hasFile('file_sk')) {
            $path = $request->file('file_sk')->store('lembaga_sk', 'local');
            $lembaga->file_sk = $path;
        }

        $lembaga->save();

        return back()->with('success', 'Data lembaga berhasil disimpan.');
    }

    public function lembagaVerify(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:diterima,dikembalikan',
            'catatan' => 'nullable|string'
        ]);

        $lembaga = LembagaDesa::findOrFail($id);

        $lembaga->status = $validated['status'];
        if ($validated['status'] == 'dikembalikan') {
            $lembaga->catatan_revisi = $validated['catatan'];
        }
        $lembaga->save();

        return back()->with('success', 'Status lembaga berhasil diperbarui.');
    }


    public function exportAudit()
    {
        $desa_id = request('desa_id');
        $desa = Desa::find($desa_id);

        if (!$desa && auth()->user()->desa_id) {
            $desa = auth()->user()->desa;
        }

        abort_unless($desa, 404, 'Desa tidak ditemukan.');

        $zipFile = new ZipFile();
        $zipName = "Paket_Audit_" . str_replace(' ', '_', $desa->nama_desa) . "_" . date('Ymd') . ".zip";

        // 1. Perangkat & BPD SK
        $personils = PersonilDesa::where('desa_id', $desa->id)->whereNotNull('file_sk')->get();
        foreach ($personils as $p) {
            $fullPath = storage_path('app/local/' . $p->file_sk);
            if (file_exists($fullPath)) {
                $zipFile->addFile($fullPath, "A_B_Struktur_Organisasi/" . basename($p->file_sk));
            }
        }

        // 2. Lembaga SK
        $lembagas = LembagaDesa::where('desa_id', $desa->id)->whereNotNull('file_sk')->get();
        foreach ($lembagas as $l) {
            $fullPath = storage_path('app/local/' . $l->file_sk);
            if (file_exists($fullPath)) {
                $zipFile->addFile($fullPath, "C_Lembaga_Desa/" . basename($l->file_sk));
            }
        }

        // 3. Dokumen Inti & Laporan
        $dokumens = DokumenDesa::where('desa_id', $desa->id)->get();
        foreach ($dokumens as $d) {
            $fullPath = storage_path('app/local/' . $d->file_path);
            if (file_exists($fullPath)) {
                $folder = in_array($d->tipe_dokumen, ['LKPJ', 'LPPD']) ? "E_Laporan_Tahunan/" : "G_Dokumen_Inti/";
                $zipFile->addFile($fullPath, $folder . basename($d->file_path));
            }
        }

        // 4. Perencanaan (Musrenbang BA)
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
        $hasPerencanaan = PerencanaanDesa::where('desa_id', $desa_id)->where('tahun', date('Y'))->where('status_administrasi', '!=', 'draft')->exists();

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
