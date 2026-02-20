<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\DokumenDesa;
use App\Models\LembagaDesa;
use App\Models\PersonilDesa;
use App\Models\RiwayatJabatanPersonil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministrasiController extends Controller
{
    /**
     * Menu Utama Administrasi Desa
     */
    public function index()
    {
        $desaId = auth()->user()->desa_id;

        // Hitung Summary Status
        $counts = [
            'perangkat' => [
                'total' => PersonilDesa::where('desa_id', $desaId)->where('kategori', 'perangkat')->count(),
                'draft' => PersonilDesa::where('desa_id', $desaId)->where('kategori', 'perangkat')->where('status', 'draft')->count(),
                'revisi' => PersonilDesa::where('desa_id', $desaId)->where('kategori', 'perangkat')->where('status', 'dikembalikan')->count(),
            ],
            'bpd' => [
                'total' => PersonilDesa::where('desa_id', $desaId)->where('kategori', 'bpd')->count(),
                'draft' => PersonilDesa::where('desa_id', $desaId)->where('kategori', 'bpd')->where('status', 'draft')->count(),
            ],
            'lembaga' => [
                'total' => LembagaDesa::where('desa_id', $desaId)->count(),
                'draft' => LembagaDesa::where('desa_id', $desaId)->where('status', 'draft')->count(),
            ],
            'dokumen' => [
                'total' => DokumenDesa::where('desa_id', $desaId)->count(),
                'draft' => DokumenDesa::where('desa_id', $desaId)->where('status', 'draft')->count(),
            ]
        ];

        return view('desa.administrasi.index', compact('counts'));
    }

    // =========================================================================
    // MODUL: PERSONIL DESA (Perangkat & BPD)
    // =========================================================================

    public function personilIndex(Request $request)
    {
        $kategori = $request->query('kategori', 'perangkat'); // perangkat or bpd
        $desaId = auth()->user()->desa_id;

        $personils = PersonilDesa::where('desa_id', $desaId)
            ->where('kategori', $kategori)
            ->latest()
            ->paginate(10);

        return view('desa.administrasi.personil.index', compact('personils', 'kategori'));
    }

    public function personilCreate(Request $request)
    {
        $kategori = $request->query('kategori', 'perangkat');
        return view('desa.administrasi.personil.create', compact('kategori'));
    }

    public function personilStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jabatan' => 'required|string',
            'masa_jabatan_mulai' => $request->kategori == 'perangkat' ? 'required|date' : 'nullable|date',
            'nomor_sk' => 'required|string',
            'tanggal_sk' => 'required|date',
            'file_sk' => 'required|file|mimes:pdf|max:2048',
            'kategori' => 'required|in:perangkat,bpd'
        ]);

        DB::transaction(function () use ($request) {
            $path = $request->file('file_sk')->store('sk_personil', 'local');

            $personil = new PersonilDesa($request->except('file_sk'));
            $personil->desa_id = auth()->user()->desa_id;
            $personil->file_sk = $path;
            $personil->status = 'draft'; // Default status
            $personil->save();

            // Log Riwayat Awal
            RiwayatJabatanPersonil::create([
                'personil_desa_id' => $personil->id,
                'jabatan_baru' => $request->jabatan,
                'tmt_baru' => $request->masa_jabatan_mulai,
                'sk_baru' => $request->nomor_sk,
                'keterangan' => 'Pengangkatan Awal / Input Data Baru',
                'created_by' => auth()->id()
            ]);
        });

        return redirect()->route('desa.administrasi.personil.index', ['kategori' => $request->kategori])
            ->with('success', 'Data berhasil disimpan sebagai Draft.');
    }

    public function personilSubmit($id)
    {
        $personil = PersonilDesa::findOrFail($id);
        abort_unless($personil->isEditable(), 403, 'Data tidak dapat dikirim (Status Read-only).');

        $personil->update([
            'status' => 'dikirim',
            'tanggal_pengajuan' => now()
        ]);

        return back()->with('success', 'Data berhasil dikirim ke Kecamatan untuk verifikasi.');
    }

    public function personilEdit($id)
    {
        $personil = PersonilDesa::findOrFail($id);
        $kategori = $personil->kategori;

        // Cek akses desa
        abort_unless($personil->desa_id == auth()->user()->desa_id, 403);

        return view('desa.administrasi.personil.edit', compact('personil', 'kategori'));
    }

    public function personilUpdate(Request $request, $id)
    {
        $personil = PersonilDesa::findOrFail($id);
        abort_unless($personil->isEditable(), 403, 'Data terkunci dan tidak dapat diedit.');

        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jabatan' => 'required|string',
            'masa_jabatan_mulai' => $personil->kategori == 'perangkat' ? 'required|date' : 'nullable|date',
            'nomor_sk' => 'required|string',
            'tanggal_sk' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf|max:2048', // Nullable on update
        ]);

        DB::transaction(function () use ($request, $personil) {
            $data = $request->except(['file_sk', 'kategori']); // Kategori tidak boleh ubah

            // Handle File Upload
            if ($request->hasFile('file_sk')) {
                $data['file_sk'] = $request->file('file_sk')->store('sk_personil', 'local');
            }

            // Record History if specific fields change (Jabatan/SK)
            if ($personil->jabatan != $request->jabatan || $personil->nomor_sk != $request->nomor_sk) {
                RiwayatJabatanPersonil::create([
                    'personil_desa_id' => $personil->id,
                    'jabatan_lama' => $personil->jabatan,
                    'jabatan_baru' => $request->jabatan,
                    'tmt_lama' => $personil->masa_jabatan_mulai,
                    'tmt_baru' => $request->masa_jabatan_mulai,
                    'sk_lama' => $personil->nomor_sk,
                    'sk_baru' => $request->nomor_sk,
                    'keterangan' => 'Perubahan Data / Edit',
                    'created_by' => auth()->id()
                ]);
            }

            $personil->update($data);
        });

        return redirect()->route('desa.administrasi.personil.index', ['kategori' => $personil->kategori])
            ->with('success', 'Perubahan berhasil disimpan.');
    }

    public function personilDestroy($id)
    {
        $personil = PersonilDesa::findOrFail($id);
        abort_unless($personil->isEditable(), 403, 'Data tidak dapat dihapus.');

        DB::transaction(function () use ($personil) {
            // Hapus file SK jika ada
            if ($personil->file_sk && \Illuminate\Support\Facades\Storage::disk('local')->exists($personil->file_sk)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($personil->file_sk);
            }

            // Hapus riwayat (Cascade di DB sebenarnya sudah handle, tapi explicit lebih aman)
            RiwayatJabatanPersonil::where('personil_desa_id', $personil->id)->delete();

            $personil->delete();
        });

        return redirect()->route('desa.administrasi.personil.index', ['kategori' => $personil->kategori])
            ->with('success', 'Data berhasil dihapus.');
    }

    // =========================================================================
    // MODUL: LEMBAGA DESA
    // =========================================================================

    // =========================================================================
    // MODUL: LEMBAGA DESA
    // =========================================================================

    public function lembagaIndex(Request $request)
    {
        $desaId = auth()->user()->desa_id;
        $lembagas = LembagaDesa::where('desa_id', $desaId)
            ->latest()
            ->paginate(10);

        return view('desa.administrasi.lembaga.index', compact('lembagas'));
    }

    public function lembagaCreate()
    {
        return view('desa.administrasi.lembaga.create');
    }

    public function lembagaStore(Request $request)
    {
        $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'sk_pendirian' => 'required|string',
            'file_sk' => 'required|file|mimes:pdf|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $path = $request->file('file_sk')->store('sk_lembaga', 'local');

            $lembaga = new LembagaDesa($request->except('file_sk'));
            $lembaga->desa_id = auth()->user()->desa_id;
            $lembaga->file_sk = $path;
            $lembaga->status = 'draft';
            $lembaga->save();
        });

        return redirect()->route('desa.administrasi.lembaga.index')
            ->with('success', 'Data Lembaga berhasil disimpan sebagai Draft.');
    }

    public function lembagaEdit($id)
    {
        $lembaga = LembagaDesa::findOrFail($id);
        abort_unless($lembaga->desa_id == auth()->user()->desa_id, 403);

        return view('desa.administrasi.lembaga.edit', compact('lembaga'));
    }

    public function lembagaUpdate(Request $request, $id)
    {
        $lembaga = LembagaDesa::findOrFail($id);
        abort_unless($lembaga->isEditable(), 403, 'Data terkunci dan tidak dapat diedit.');

        $request->validate([
            'nama_lembaga' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'sk_pendirian' => 'required|string',
            'file_sk' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        DB::transaction(function () use ($request, $lembaga) {
            $data = $request->except(['file_sk']);

            if ($request->hasFile('file_sk')) {
                if ($lembaga->file_sk && \Illuminate\Support\Facades\Storage::disk('local')->exists($lembaga->file_sk)) {
                    \Illuminate\Support\Facades\Storage::disk('local')->delete($lembaga->file_sk);
                }
                $data['file_sk'] = $request->file('file_sk')->store('sk_lembaga', 'local');
            }

            $lembaga->update($data);
        });

        return redirect()->route('desa.administrasi.lembaga.index')
            ->with('success', 'Perubahan data Lembaga berhasil disimpan.');
    }

    public function lembagaDestroy($id)
    {
        $lembaga = LembagaDesa::findOrFail($id);
        abort_unless($lembaga->isEditable(), 403, 'Data tidak dapat dihapus.');

        DB::transaction(function () use ($lembaga) {
            if ($lembaga->file_sk && \Illuminate\Support\Facades\Storage::disk('local')->exists($lembaga->file_sk)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($lembaga->file_sk);
            }
            $lembaga->delete();
        });

        return redirect()->route('desa.administrasi.lembaga.index')
            ->with('success', 'Data Lembaga berhasil dihapus.');
    }

    public function lembagaSubmit($id)
    {
        $lembaga = LembagaDesa::findOrFail($id);
        abort_unless($lembaga->isEditable(), 403, 'Data tidak dapat dikirim (Status Read-only).');

        $lembaga->update([
            'status' => 'dikirim',
            'tanggal_pengajuan' => now()
        ]);

        return back()->with('success', 'Data Lembaga berhasil dikirim ke Kecamatan.');
    }

    // =========================================================================
    // MODUL: DOKUMEN DESA (Perdes, LPPD, LKPJ, etc)
    // =========================================================================

    public function dokumenIndex(Request $request)
    {
        $desaId = auth()->user()->desa_id;
        $tipe = $request->query('tipe'); // optional filter: Perdes, Laporan, etc

        $query = DokumenDesa::where('desa_id', $desaId);

        if ($tipe == 'perdes') {
            $query->whereIn('tipe_dokumen', ['Perdes', 'Perkades']);
        } elseif ($tipe == 'laporan') {
            $query->whereIn('tipe_dokumen', ['LKPJ', 'LPPD', 'APBDes']);
        }

        $dokumens = $query->latest()->paginate(10);

        return view('desa.administrasi.dokumen.index', compact('dokumens', 'tipe'));
    }

    public function dokumenCreate(Request $request)
    {
        $tipe = $request->query('tipe', 'Perdes');
        return view('desa.administrasi.dokumen.create', compact('tipe'));
    }

    public function dokumenStore(Request $request)
    {
        $request->validate([
            'tipe_dokumen' => 'required|string',
            'nomor_dokumen' => 'required|string',
            'perihal' => 'required|string',
            'tahun' => 'required|digits:4',
            'file_dokumen' => 'required|file|mimes:pdf|max:5120',
        ]);

        DB::transaction(function () use ($request) {
            $path = $request->file('file_dokumen')->store('dokumen_desa', 'local');

            $dokumen = new DokumenDesa($request->except('file_dokumen'));
            $dokumen->desa_id = auth()->user()->desa_id;
            $dokumen->file_path = $path;
            $dokumen->status = 'draft';
            $dokumen->save();
        });

        $redirectTipe = in_array($request->tipe_dokumen, ['Perdes', 'Perkades']) ? 'perdes' : 'laporan';
        return redirect()->route('desa.administrasi.dokumen.index', ['tipe' => $redirectTipe])
            ->with('success', 'Dokumen berhasil disimpan sebagai Draft.');
    }

    public function dokumenEdit($id)
    {
        $dokumen = DokumenDesa::findOrFail($id);
        abort_unless($dokumen->desa_id == auth()->user()->desa_id, 403);

        return view('desa.administrasi.dokumen.edit', compact('dokumen'));
    }

    public function dokumenUpdate(Request $request, $id)
    {
        $dokumen = DokumenDesa::findOrFail($id);
        abort_unless($dokumen->isEditable(), 403, 'Data terkunci dan tidak dapat diedit.');

        $request->validate([
            'tipe_dokumen' => 'required|string',
            'nomor_dokumen' => 'required|string',
            'perihal' => 'required|string',
            'tahun' => 'required|digits:4',
            'file_dokumen' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        DB::transaction(function () use ($request, $dokumen) {
            $data = $request->except(['file_dokumen']);

            if ($request->hasFile('file_dokumen')) {
                if ($dokumen->file_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($dokumen->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('local')->delete($dokumen->file_path);
                }
                $data['file_path'] = $request->file('file_dokumen')->store('dokumen_desa', 'local');
            }

            $dokumen->update($data);
        });

        $redirectTipe = in_array($dokumen->tipe_dokumen, ['Perdes', 'Perkades']) ? 'perdes' : 'laporan';
        return redirect()->route('desa.administrasi.dokumen.index', ['tipe' => $redirectTipe])
            ->with('success', 'Perubahan dokumen berhasil disimpan.');
    }

    public function dokumenDestroy($id)
    {
        $dokumen = DokumenDesa::findOrFail($id);
        abort_unless($dokumen->isEditable(), 403, 'Data tidak dapat dihapus.');

        DB::transaction(function () use ($dokumen) {
            if ($dokumen->file_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($dokumen->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($dokumen->file_path);
            }
            $dokumen->delete();
        });

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function dokumenSubmit($id)
    {
        $dokumen = DokumenDesa::findOrFail($id);
        abort_unless($dokumen->isEditable(), 403, 'Data tidak dapat dikirim.');

        $dokumen->update([
            'status' => 'dikirim',
            'tanggal_pengajuan' => now()
        ]);

        return back()->with('success', 'Dokumen berhasil dikirim ke Kecamatan.');
    }
}
