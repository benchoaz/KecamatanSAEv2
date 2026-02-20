<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Desa\DesaSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MusdesController extends Controller
{
    /**
     * Tampilkan daftar Musdes desa yang login.
     * Hanya Musdes (modul = 'musdes').
     */
    public function index()
    {
        $musdesList = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->where('modul', 'musdes')
            ->latest()
            ->paginate(10);

        return view('desa.musdes.index', compact('musdesList'));
    }

    /**
     * Form input Musdes baru.
     */
    public function create()
    {
        return view('desa.musdes.create');
    }

    /**
     * Simpan Draft Musdes (Belum validasi ketat).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'periode' => 'required|string', // Tahun Anggaran
            'tanggal_pelaksanaan' => 'required|date',
            'lokasi' => 'required|string',
            'jenis_musdes' => 'required|string',
            'jumlah_undangan' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string|max:300',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Submission Induk
            $submission = DesaSubmission::create([
                'desa_id' => auth()->user()->desa_id,
                'modul' => 'musdes',
                'judul' => $validated['judul'],
                'periode' => $validated['periode'], // Tahun
                'status' => DesaSubmission::STATUS_DRAFT,
                'created_by' => auth()->id(),
            ]);

            // 2. Simpan Detail (EAV)
            $details = [
                'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
                'lokasi' => $validated['lokasi'],
                'jenis_musdes' => $validated['jenis_musdes'],
                'jumlah_undangan' => $validated['jumlah_undangan'] ?? 15,
                'keterangan' => $validated['keterangan'] ?? '',
            ];

            foreach ($details as $key => $val) {
                $submission->details()->create([
                    'field_key' => $key,
                    'field_value' => $val,
                ]);
            }

            DB::commit();

            // Redirect ke halaman edit untuk upload file
            return redirect()->route('desa.musdes.edit', $submission->id)
                ->with('success', 'Draft Musdes berhasil dibuat. Silakan upload dokumen bukti.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan draft: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman Edit (Upload File & Update Data).
     */
    public function edit($id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->where('modul', 'musdes')
            ->with(['details', 'files', 'notes'])
            ->findOrFail($id);

        if (!$submission->isEditable()) {
            return redirect()->route('desa.musdes.show', $id)
                ->with('warning', 'Data tidak dapat diedit karena status sudah ' . $submission->status_label);
        }

        // Helper untuk ambil detail value dengan mudah di view
        $details = $submission->details->pluck('field_value', 'field_key');

        return view('desa.musdes.edit', compact('submission', 'details'));
    }

    /**
     * Update Draft Musdes.
     */
    public function update(Request $request, $id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->findOrFail($id);

        if (!$submission->isEditable()) {
            abort(403, 'Akses ditolak. Data terkunci.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'periode' => 'required|string',
            'tanggal_pelaksanaan' => 'required|date',
            'lokasi' => 'required|string',
            'jenis_musdes' => 'required|string',
            'jumlah_undangan' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string|max:300',
        ]);

        DB::beginTransaction();
        try {
            // Update Induk
            $submission->update([
                'judul' => $validated['judul'],
                'periode' => $validated['periode'],
            ]);

            // Update Details (Hapus lalu buat baru agar simpel, atau update on duplicate key)
            // Cara simpel untuk EAV: hapus detail lama, insert baru (aman karena masih draft)
            $submission->details()->delete();

            $details = [
                'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
                'lokasi' => $validated['lokasi'],
                'jenis_musdes' => $validated['jenis_musdes'],
                'jumlah_undangan' => $validated['jumlah_undangan'] ?? 15,
                'keterangan' => $validated['keterangan'] ?? '',
            ];

            foreach ($details as $key => $val) {
                $submission->details()->create([
                    'field_key' => $key,
                    'field_value' => $val,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Perubahan data berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Upload Dokumen (Ajax Handler atau Direct Post).
     */
    public function uploadFile(Request $request, $id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->findOrFail($id);

        if (!$submission->isEditable()) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|max:5120', // Max 5MB
            'type' => 'required|string|in:berita_acara,daftar_hadir,foto_kegiatan',
        ]);

        $file = $request->file('file');
        $type = $request->type;

        // Simpan file
        $path = $file->storeAs(
            "musdes/{$submission->desa_id}/{$submission->id}",
            "{$type}_" . time() . "." . $file->getClientOriginalExtension(),
            'public'
        );

        // Record ke database
        $submission->files()->create([
            'file_type' => $type,
            'file_path' => $path,
        ]);

        return back()->with('success', 'File berhasil diupload.');
    }

    /**
     * Hapus File.
     */
    public function deleteFile($id, $fileId)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)->findOrFail($id);
        if (!$submission->isEditable())
            abort(403);

        $file = $submission->files()->findOrFail($fileId);

        // Hapus fisik file
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();
        return back()->with('success', 'File dihapus.');
    }

    /**
     * Submit (Kirim ke Kecamatan).
     * Validasi kelengkapan dokumen terjadi di sini.
     */
    public function submit($id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->with('files')
            ->findOrFail($id);

        if (!$submission->isEditable()) {
            abort(403, 'Laporan sudah dikirim atau selesai.');
        }

        // 1. Validasi Dokumen Wajib
        $files = $submission->files;
        $errors = [];

        if ($files->where('file_type', 'berita_acara')->isEmpty()) {
            $errors[] = 'Berita Acara Musdes belum diupload.';
        }
        if ($files->where('file_type', 'daftar_hadir')->isEmpty()) {
            $errors[] = 'Daftar Hadir belum diupload.';
        }
        if ($files->where('file_type', 'foto_kegiatan')->isEmpty()) {
            $errors[] = 'Foto Kegiatan belum diupload (minimal 1).';
        }

        if (count($errors) > 0) {
            return back()->with('error', 'Gagal mengirim laporan.')->with('validation_errors', $errors);
        }

        // 2. Jika lengkap, ubah status
        $submission->update([
            'status' => DesaSubmission::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return redirect()->route('desa.musdes.index')
            ->with('success', 'Laporan Musdes berhasil dikirim ke Kecamatan. Terima kasih!');
    }

    /**
     * Halaman Detail Read-Only.
     */
    public function show($id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->where('modul', 'musdes')
            ->with(['details', 'files', 'notes.creator'])
            ->findOrFail($id);

        $details = $submission->details->pluck('field_value', 'field_key');

        return view('desa.musdes.show', compact('submission', 'details'));
    }

    /**
     * Hapus Draft Musdes.
     */
    public function destroy($id)
    {
        $submission = DesaSubmission::where('desa_id', auth()->user()->desa_id)
            ->where('modul', 'musdes')
            ->with('files')
            ->findOrFail($id);

        // Hanya boleh hapus jika statusnya Draft
        if ($submission->status !== DesaSubmission::STATUS_DRAFT) {
            return back()->with('error', 'Hanya draf yang dapat dihapus. Data yang sudah terkirim tidak bisa dihapus.');
        }

        DB::beginTransaction();
        try {
            // 1. Hapus File Fisik
            foreach ($submission->files as $file) {
                if (Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
            }

            // 2. Hapus Folder Dokumen (Jika kosong atau hapus record detail & file dulu)
            $folderPath = "musdes/{$submission->desa_id}/{$submission->id}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            // 3. Hapus Record Database (Detail & File akan terhapus otomatis jika ada Cascade, tapi manual lebih aman di sini)
            $submission->details()->delete();
            $submission->files()->delete();
            $submission->notes()->delete();
            $submission->logs()->delete();
            $submission->delete();

            DB::commit();
            return redirect()->route('desa.musdes.index')->with('success', 'Draf laporan Musdes telah dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus draf: ' . $e->getMessage());
        }
    }

    /**
     * Unduh Templat Dokumen (BA atau Daftar Hadir).
     * Menggunakan teknik HTML-to-Word header.
     */
    public function downloadTemplate(Request $request, $type)
    {
        $desaName = auth()->user()->desa->nama_desa ?? '[NAMA DESA]';
        $kecName = 'Kecamatan SAE'; // Bisa dinamis jika ada fieldnya
        $year = date('Y');

        if ($type === 'ba') {
            $filename = "Draft_BA_Musdes_{$desaName}.doc";
            $content = "
                <h2 style='text-align:center;'>BERITA ACARA</h2>
                <h3 style='text-align:center;'>MUSYAWARAH DESA [NAMA KEGIATAN]</h3>
                <h3 style='text-align:center;'>DESA {$desaName} KECAMATAN {$kecName}</h3>
                <h3 style='text-align:center;'>TAHUN ANGGARAN {$year}</h3>
                <br>
                <p>Pada hari ini [Hari], Tanggal [Tanggal] Bulan [Bulan] Tahun {$year}, bertempat di [Lokasi], telah diselenggarakan Musyawarah Desa dalam rangka [Nama Kegiatan].</p>
                <p>Musyawarah ini dihadiri oleh Pemerintah Desa, BPD, Tokoh Masyarakat, serta unsur masyarakat lainnya sebagaimana tercantum dalam daftar hadir (terlampir).</p>
                <p><b>Materi yang dibahas dalam Musyawarah ini adalah:</b></p>
                <ol>
                    <li>[Agenda 1]</li>
                    <li>[Agenda 2]</li>
                </ol>
                <p><b>Hasil Keputusan Musyawarah:</b></p>
                <ul>
                    <li>[Keputusan 1]</li>
                    <li>[Keputusan 2]</li>
                </ul>
                <br>
                <p style='text-align:right;'>{$desaName}, " . date('d M Y') . "</p>
                <table width='100%'>
                    <tr>
                        <td width='50%' style='text-align:center;'>Mengetahui,<br><b>Kepala Desa {$desaName}</b><br><br><br><br>( ................................. )</td>
                        <td width='50%' style='text-align:center;'><br><b>Ketua BPD</b><br><br><br><br><br>( ................................. )</td>
                    </tr>
                </table>
            ";
        } elseif ($type === 'absen') {
            $filename = "Draft_Daftar_Hadir_Musdes_{$desaName}.doc";

            // Ambil jumlah undangan dari submission jika ada di request id
            $jumlahUndangan = 15;
            if ($request->submission_id) {
                $sub = DesaSubmission::find($request->submission_id);
                if ($sub) {
                    $jumlahUndangan = $sub->details->where('field_key', 'jumlah_undangan')->first()->field_value ?? 15;
                }
            }

            $content = "
                <h2 style='text-align:center;'>DAFTAR HADIR PESERTA</h2>
                <h3 style='text-align:center;'>MUSYAWARAH DESA [NAMA KEGIATAN]</h3>
                <h3 style='text-align:center;'>DESA {$desaName}</h3>
                <br>
                <table border='1' cellspacing='0' cellpadding='5' width='100%'>
                    <thead>
                        <tr>
                            <th width='5%'>NO</th>
                            <th width='40%'>NAMA</th>
                            <th width='30%'>JABATAN / ALAMAT</th>
                            <th width='25%'>TANDA TANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        " . implode('', array_map(fn($i) => "<tr><td style='text-align:center;'>{$i}</td><td></td><td></td><td>" . ($i % 2 != 0 ? "{$i}. " : "&nbsp;&nbsp;&nbsp;&nbsp;{$i}. ") . "</td></tr>", range(1, max(10, $jumlahUndangan)))) . "
                    </tbody>
                </table>
                <br><br>
                <div style='width:300px; margin-left:auto; text-align:center;'>
                    Mengetahui,<br><b>Kepala Desa {$desaName}</b><br><br><br><br>
                    ( ................................. )
                </div>
            ";
        } else {
            abort(404);
        }

        $headers = [
            "Content-type" => "application/vnd.ms-word",
            "Content-Disposition" => "attachment;Filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Expires" => "0"
        ];

        return response($content, 200, $headers);
    }
}
