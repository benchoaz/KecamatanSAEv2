@extends('layouts.kecamatan')
@section('title', 'Kelola Node Layanan')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div class="d-flex align-items-start justify-content-between mb-4 gap-3">
        <div>
            <a href="{{ route('kecamatan.pelayanan.layanan.index') }}"
               class="d-inline-flex align-items-center gap-2 text-slate-400 text-decoration-none small fw-bold mb-2 hover-text-primary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Layanan
            </a>
            <h4 class="fw-black text-slate-800 mb-1">
                <i class="fas fa-sitemap me-2 text-primary opacity-75"></i>
                Decision Tree: <span class="text-primary">{{ $layanan->nama_layanan }}</span>
            </h4>
            <p class="text-slate-500 small mb-0">Kelola hierarki pilihan layanan untuk warga. Setiap titik akhir (leaf) punya syarat berkas yang berbeda.</p>
        </div>
        <button data-bs-toggle="modal" data-bs-target="#addNodeModal"
                data-parent="" data-depth="0"
                class="btn btn-primary rounded-pill px-4 shadow-sm flex-shrink-0 add-node-btn">
            <i class="fas fa-plus me-2"></i> Tambah Kelompok
        </button>
    </div>

    {{-- ── Contoh Visual (DEMO — Status Kawin vs Pendidikan) ──── --}}
    <div class="alert border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg,#f0f9ff,#e0f2fe); border-left: 4px solid #0ea5e9 !important;">
        <div class="d-flex align-items-start gap-3">
            <div class="flex-shrink-0 mt-1">
                <i class="fas fa-lightbulb text-sky-500 fs-5"></i>
            </div>
            <div>
                <p class="fw-bold mb-1 text-sky-800 small">Cara Kerja Decision Tree</p>
                <p class="mb-0 small text-sky-700">
                    Node <strong>Perubahan Status Kawin</strong> → tampilkan syarat: <em>Surat Nikah/Cerai + KK Asli + Akta</em><br>
                    Node <strong>Perubahan Pendidikan</strong> → tampilkan syarat: <em>Ijazah Terakhir + KK Asli</em><br>
                    <span class="text-sky-500 fw-bold">Berbeda node = berbeda syarat berkas. Tanpa ubah satu baris kode pun.</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ── Tree Builder Area ───────────────────────────────── --}}
    <div class="row g-4">

        {{-- Kolom Kiri: Visual Tree --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0">Struktur Pohon Layanan</h6>
                        <p class="text-slate-400 small mb-0">Klik node untuk kelola syarat berkas</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                        {{ $nodes->count() }} node root
                    </span>
                </div>
                <div class="card-body py-2 px-3" style="min-height: 420px;">
                    @if($nodes->isEmpty())
                    <div class="text-center py-5">
                        <div class="w-16 h-16 bg-slate-100 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px;">
                            <i class="fas fa-sitemap text-slate-300 fs-3"></i>
                        </div>
                        <p class="text-slate-400 small mb-3">Belum ada struktur node.</p>
                        <button data-bs-toggle="modal" data-bs-target="#addNodeModal" data-parent="" data-depth="0"
                                class="btn btn-sm btn-outline-primary rounded-pill px-4 add-node-btn">
                            <i class="fas fa-plus me-1"></i> Buat Node Pertama
                        </button>
                    </div>
                    @else
                        <div class="tree-container py-3">
                            @include('kecamatan.pelayanan.layanan.partials.tree-nodes', ['nodes' => $nodes, 'depth' => 0])
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Requirements Detail Panel --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100" id="requirementsPanel">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h6 class="fw-bold mb-0" id="reqPanelTitle">
                        <i class="fas fa-list-check me-2 text-primary"></i>
                        Syarat Berkas Node
                    </h6>
                    <p class="text-slate-400 small mb-0" id="reqPanelSubtitle">Klik node leaf (🍃) di sebelah kiri untuk mengelola syaratnya</p>
                </div>
                <div class="card-body" id="reqPanelBody">
                    {{-- Empty state --}}
                    <div id="reqEmpty" class="text-center py-5">
                        <div class="opacity-30 mb-3" style="font-size:3rem;">🍃</div>
                        <p class="text-slate-400 small">Pilih node leaf untuk melihat<br>dan mengelola daftar syarat berkasnya</p>
                    </div>

                    {{-- Requirement list (dipopulasi via AJAX) --}}
                    <div id="reqList" class="d-none">
                        <div id="reqItems" class="space-y-2 mb-4"></div>
                        <button id="addReqBtn" class="btn btn-sm btn-outline-primary w-100 rounded-3 py-2">
                            <i class="fas fa-plus me-1"></i> Tambah Syarat Berkas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ──────────────────────────────────────────────────────────────
     Modal: Tambah / Edit Node
────────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="addNodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-slate-50 px-4 py-3">
                <h5 class="modal-title fw-bold" id="addNodeModalTitle">Tambah Node Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="nodeForm" method="POST" action="{{ route('kecamatan.pelayanan.layanan.nodes.store') }}">
                @csrf
                <input type="hidden" name="_method" id="nodeMethod" value="POST">
                <input type="hidden" name="parent_id" id="nodeParentId" value="">
                <input type="hidden" name="depth" id="nodeDepth" value="0">
                <input type="hidden" name="master_layanan_id" id="nodeMasterLayananId" value="{{ $layanan->id }}">
                <div class="modal-body px-4 py-4 space-y-3">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-slate-700">Nama Node <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="nodeName"
                               class="form-control rounded-3 bg-slate-50 border-slate-200"
                               placeholder="Contoh: Perubahan Status Kawin" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-slate-700">Deskripsi (Opsional)</label>
                        <textarea name="description" id="nodeDesc" rows="2"
                                  class="form-control rounded-3 bg-slate-50 border-slate-200"
                                  placeholder="Keterangan tambahan untuk warga..."></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-8">
                            <label class="form-label fw-bold small text-slate-700">Ikon (FontAwesome)</label>
                            <input type="text" name="ikon" id="nodeIkon" value="fa-folder"
                                   class="form-control rounded-3 bg-slate-50 border-slate-200"
                                   placeholder="fa-folder">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold small text-slate-700">Urutan</label>
                            <input type="number" name="urutan" id="nodeUrutan" value="0"
                                   class="form-control rounded-3 bg-slate-50 border-slate-200" min="0">
                        </div>
                    </div>

                    <div class="form-check form-switch mt-3 pt-2 border-top border-slate-100">
                        <input class="form-check-input" type="checkbox" name="is_leaf" id="nodeIsLeaf" value="1">
                        <label class="form-check-label fw-bold small text-slate-700" for="nodeIsLeaf">
                            🍃 Ini adalah Node Akhir (Leaf)
                        </label>
                        <div class="text-slate-400 small mt-1">Aktifkan jika node ini langsung menampilkan form pengajuan + syarat berkas</div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="nodeIsActive" value="1" checked>
                        <label class="form-check-label fw-bold small text-slate-700" for="nodeIsActive">Aktif (tampil ke warga)</label>
                    </div>

                    {{-- ── Leaf Configurations (Only if is_leaf active) ── --}}
                    <div id="leafConfigs" class="d-none border-top border-slate-100 mt-3 pt-3">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="show_identity_form" id="nodeShowIdentity" value="1" checked>
                            <label class="form-check-label fw-bold small text-slate-700" for="nodeShowIdentity">
                                <i class="fas fa-user-check me-1 text-primary"></i> Tampilkan Form Identitas Pemohon
                            </label>
                            <div class="text-slate-400 small mt-1">Sertakan (Nama, NIK, WA, Desa) saat warga mengajukan</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-slate-700">
                                <i class="fas fa-info-circle me-1 text-primary"></i> Persyaratan (Teks/Instruksi)
                            </label>
                            <textarea name="requirement_text" id="nodeReqText" class="form-control form-control-sm rounded-3 border-slate-200" 
                                      rows="4" placeholder="Contoh: Lampirkan fotokopi KK asli. Pastikan foto dokumen tidak blur..."></textarea>
                            <div class="text-slate-400 small mt-1">Keterangan ini akan tampil di bagian atas formulir warga.</div>
                        </div>

                        <div class="mb-2 d-flex align-items-center justify-content-between">
                            <label class="form-label fw-bold small text-slate-700 mb-0">
                                <i class="fas fa-paperclip me-1 text-primary"></i> Upload Berkas Mandiri (opsional)
                            </label>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-1 px-3" id="addRequirementField">
                                <i class="fas fa-plus me-1"></i> Tambah Field Upload
                            </button>
                        </div>
                        <div id="requirementRepeater" class="space-y-2 mt-2">
                            {{-- Container for requirements --}}
                        </div>
                        <p class="text-slate-400 small mt-2">Tentukan berkas apa saja yang harus di-upload oleh warga saat memilih layanan ini.</p>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-slate-50 px-4 py-3 gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-5 fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> Simpan Node
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ──────────────────────────────────────────────────────────────
     Modal: Tambah / Edit Requirement
────────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="addReqModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-slate-50 px-4 py-3">
                <h5 class="modal-title fw-bold">Tambah Syarat Berkas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reqForm" method="POST">
                @csrf
                <input type="hidden" name="node_id" id="reqNodeId">
                <div class="modal-body px-4 py-4">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-slate-700">Tipe Syarat <span class="text-danger">*</span></label>
                        <select name="type" id="reqType" class="form-select rounded-3 bg-slate-50 border-slate-200" required>
                            <option value="file_upload">📎 Upload File</option>
                            <option value="text_info">ℹ️ Informasi Teks</option>
                            <option value="checkbox">✅ Pernyataan (Checkbox)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-slate-700">Label / Nama Syarat <span class="text-danger">*</span></label>
                        <input type="text" name="label" id="reqLabel"
                               class="form-control rounded-3 bg-slate-50 border-slate-200"
                               placeholder="Contoh: Surat Nikah/Cerai" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-slate-700">Keterangan untuk Warga (Opsional)</label>
                        <textarea name="description" id="reqDesc" rows="2"
                                  class="form-control rounded-3 bg-slate-50 border-slate-200"
                                  placeholder="Contoh: Scan halaman depan yang terlihat jelas..."></textarea>
                    </div>

                    <div id="fileOptions">
                        <div class="row g-3">
                            <div class="col-8">
                                <label class="form-label fw-bold small text-slate-700">Format Diterima</label>
                                <input type="text" name="accepted_types" id="reqAccepted" value="jpg,png,pdf"
                                       class="form-control rounded-3 bg-slate-50 border-slate-200"
                                       placeholder="jpg,png,pdf">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold small text-slate-700">Maks. (MB)</label>
                                <input type="number" name="max_size_mb" id="reqMaxSize" value="5" min="1" max="20"
                                       class="form-control rounded-3 bg-slate-50 border-slate-200">
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mt-3 pt-2 border-top border-slate-100">
                        <input class="form-check-input" type="checkbox" name="is_required" id="reqIsRequired" value="1" checked>
                        <label class="form-check-label fw-bold small text-slate-700" for="reqIsRequired">Wajib Diunggah</label>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-slate-50 px-4 py-3 gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-5 fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> Simpan Syarat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ── Tree Visual Styles ────────────────────────────── */
.tree-container { position: relative; }

.tree-node-wrapper {
    position: relative;
}
.tree-node-wrapper::before {
    content: '';
    position: absolute;
    left: -16px;
    top: 20px;
    width: 16px;
    height: 1px;
    background: #e2e8f0;
}
.tree-node-wrapper::after {
    content: '';
    position: absolute;
    left: -16px;
    top: -14px;
    bottom: 20px;
    width: 1px;
    background: #e2e8f0;
}
.tree-node-wrapper:last-child::after { display: none; }

.node-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 14px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}
.node-card:hover { border-color: #6366f1; background: #fafbff; box-shadow: 0 4px 12px rgba(99,102,241,0.08); }
.node-card.leaf { border-color: #d1fae5; background: #f0fdf4; }
.node-card.leaf:hover { border-color: #10b981; }
.node-card.selected { border-color: #6366f1; background: #eef2ff; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.node-card.leaf.selected { border-color: #10b981; background: #dcfce7; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }

.node-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 14px;
    background: #f1f5f9; color: #64748b;
}
.node-card.leaf .node-icon { background: #d1fae5; color: #059669; }

.node-actions { display: none; gap: 4px; margin-left: auto; }
.node-card:hover .node-actions { display: flex; }

.req-item {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    background: #fafafa;
    transition: all 0.2s;
}
.req-item:hover { border-color: #6366f1; background: white; }
.req-type-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.badge-file { background: #dbeafe; color: #1d4ed8; }
.badge-info { background: #fef9c3; color: #92400e; }
.badge-check { background: #dcfce7; color: #166534; }

/* Repeater Styles */
.repeater-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}
</style>
@endpush

@push('scripts')
<script>
// Pindahkan modal ke body untuk menghindari isu z-index/overlay
document.addEventListener('DOMContentLoaded', function() {
    document.body.appendChild(document.getElementById('addNodeModal'));
    document.body.appendChild(document.getElementById('addReqModal'));
});

// ── State ─────────────────────────────────────────────────
let activeNodeId = null;

// ── Add Node Modal Setup ──────────────────────────────────
document.querySelectorAll('.add-node-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const parentId = this.dataset.parent || '';
        const depth    = this.dataset.depth  || 0;
        const route    = '{{ route("kecamatan.pelayanan.layanan.nodes.store") }}';

        document.getElementById('addNodeModalTitle').textContent = parentId
            ? 'Tambah Sub-Node' : 'Tambah Kelompok Baru';
        document.getElementById('nodeParentId').value = parentId;
        document.getElementById('nodeDepth').value    = depth;
        document.getElementById('nodeForm').action    = route;
        document.getElementById('nodeMethod').value   = 'POST';
        document.getElementById('nodeName').value     = '';
        document.getElementById('nodeDesc').value     = '';
        document.getElementById('nodeIkon').value     = 'fa-folder';
        document.getElementById('nodeUrutan').value   = '0';
        document.getElementById('nodeIsLeaf').checked = false;
        document.getElementById('nodeIsActive').checked = true;
        document.getElementById('nodeShowIdentity').checked = true;
        document.getElementById('nodeReqText').value = '';
        document.getElementById('leafConfigs').classList.add('d-none');
        document.getElementById('requirementRepeater').innerHTML = '';
    });
});

// ── Toggle Leaf Configs ───────────────────────────────────
document.getElementById('nodeIsLeaf').addEventListener('change', function() {
    const configs = document.getElementById('leafConfigs');
    if (this.checked) {
        configs.classList.remove('d-none');
        // Add initial requirement if empty
        const repeater = document.getElementById('requirementRepeater');
        if (repeater.children.length === 0) addRequirementRow();
    } else {
        configs.classList.add('d-none');
    }
});

// ── Repeater Logic ────────────────────────────────────────
function addRequirementRow(value = '') {
    const container = document.getElementById('requirementRepeater');
    const div = document.createElement('div');
    div.className = 'repeater-item mb-2';
    div.innerHTML = `
        <input type="text" name="requirements[]" class="form-control form-control-sm rounded-3 border-slate-200" 
               placeholder="Contoh: Foto Ijazah Asli" value="${value}" required>
        <button type="button" class="btn btn-sm btn-ghost text-rose-500 border-0 p-0 px-2" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

document.getElementById('addRequirementField').addEventListener('click', () => addRequirementRow());

// ── Edit Node Modal Setup ──────────────────────────────────
document.querySelectorAll('.edit-node-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const nodeId = this.getAttribute('data-node-id');
        const nodeName = this.getAttribute('data-node-name');
        const nodeDesc = this.getAttribute('data-node-desc') || '';
        const nodeIkon = this.getAttribute('data-node-ikon') || 'fa-folder';
        const nodeUrutan = this.getAttribute('data-node-urutan') || '0';
        const nodeReqText = this.getAttribute('data-node-req-text') || '';
        const isLeaf = this.getAttribute('data-is-leaf') === '1';
        const isActive = this.getAttribute('data-is-active') === '1';
        const showIdentity = this.getAttribute('data-show-identity') !== '0';
        
        const route = '{{ route("kecamatan.pelayanan.layanan.nodes.update", "DUMMY_ID") }}'.replace('DUMMY_ID', nodeId);

        document.getElementById('addNodeModalTitle').textContent = 'Edit Node: ' + nodeName;
        document.getElementById('nodeForm').action    = route;
        document.getElementById('nodeMethod').value   = 'PUT';
        document.getElementById('nodeName').value     = nodeName;
        document.getElementById('nodeDesc').value     = nodeDesc;
        document.getElementById('nodeIkon').value     = nodeIkon;
        document.getElementById('nodeUrutan').value   = nodeUrutan;
        document.getElementById('nodeIsLeaf').checked = isLeaf;
        document.getElementById('nodeIsActive').checked = isActive;
        document.getElementById('nodeShowIdentity').checked = showIdentity;
        document.getElementById('nodeReqText').value  = nodeReqText;

        if (isLeaf) {
            document.getElementById('leafConfigs').classList.remove('d-none');
            // Fetch and populate requirements
            fetchRequirementsForModal(nodeId);
        } else {
            document.getElementById('leafConfigs').classList.add('d-none');
            document.getElementById('requirementRepeater').innerHTML = '';
        }
    });
});

async function fetchRequirementsForModal(nodeId) {
    const container = document.getElementById('requirementRepeater');
    container.innerHTML = '<div class="text-center py-2"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
    
    try {
        const res = await fetch(`/api/layanan/nodes/${nodeId}/requirements`);
        const data = await res.json();
        container.innerHTML = '';
        if (data.requirements && data.requirements.length > 0) {
            data.requirements.forEach(req => addRequirementRow(req.label));
        } else {
            addRequirementRow();
        }
    } catch (e) {
        container.innerHTML = '<p class="text-danger small">Gagal memuat syarat</p>';
    }
}

// ── Node Card Click → Load Requirements ──────────────────
document.querySelectorAll('.node-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.closest('.node-actions')) return; // jangan trigger saat klik aksi
        const nodeId = this.dataset.nodeId;
        const isLeaf = this.dataset.isLeaf === '1';

        document.querySelectorAll('.node-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');

        if (isLeaf) {
            activeNodeId = nodeId;
            loadRequirements(nodeId, nodeName);
        } else {
            document.getElementById('reqEmpty').classList.remove('d-none');
            document.getElementById('reqList').classList.add('d-none');
            document.getElementById('reqPanelTitle').innerHTML =
                `<i class="fas fa-folder-open me-2 text-amber-500"></i> ${this.dataset.nodeName}`;
            document.getElementById('reqPanelSubtitle').textContent = 'Ini bukan node leaf — tambah sub-node di dalamnya';
        }
    });
});

function loadRequirements(nodeId, nodeName) {
    document.getElementById('reqPanelTitle').innerHTML =
        `<i class="fas fa-list-check me-2 text-emerald-500"></i> ${nodeName}`;
    document.getElementById('reqPanelSubtitle').textContent = 'Daftar syarat berkas yang harus diunggah warga';
    document.getElementById('reqEmpty').classList.add('d-none');
    document.getElementById('reqList').classList.remove('d-none');
    document.getElementById('reqNodeId').value = nodeId;

    // Load via AJAX
    fetch(`/api/layanan/nodes/${nodeId}/requirements`)
        .then(r => r.json())
        .then(data => {
            renderRequirements(data.requirements);
        });
}

function renderRequirements(reqs) {
    const container = document.getElementById('reqItems');
    if (!reqs || reqs.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-slate-400 small">
                <i class="fas fa-inbox fs-3 mb-2 d-block opacity-30"></i>
                Belum ada syarat berkas. Klik tombol di bawah untuk menambahkan.
            </div>`;
        return;
    }
    container.innerHTML = reqs.map((req, i) => `
        <div class="req-item mb-2 d-flex align-items-start gap-3">
            <div class="flex-shrink-0 mt-1">
                ${req.type === 'file_upload' ? '📎' : req.type === 'text_info' ? 'ℹ️' : '✅'}
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="fw-bold small text-slate-700">${req.label}</span>
                    ${req.is_required
                        ? '<span class="badge bg-danger-subtle text-danger" style="font-size:9px">WAJIB</span>'
                        : '<span class="badge bg-slate-100 text-slate-500" style="font-size:9px">Opsional</span>'}
                </div>
                ${req.description ? `<p class="text-slate-500 mb-1" style="font-size:12px">${req.description}</p>` : ''}
                ${req.type === 'file_upload'
                    ? `<span class="req-type-badge badge-file">${req.accepted_types} · maks ${req.max_size_mb}MB</span>`
                    : ''}
            </div>
            <button class="btn btn-xs btn-ghost text-rose-400 flex-shrink-0"
                    onclick="deleteRequirement(${req.id})" title="Hapus">
                <i class="fas fa-trash-alt" style="font-size:12px"></i>
            </button>
        </div>
    `).join('');
}

// ── Add Requirement ───────────────────────────────────────
document.getElementById('addReqBtn').addEventListener('click', function() {
    const modal = new bootstrap.Modal(document.getElementById('addReqModal'));
    document.getElementById('reqNodeId').value = activeNodeId;
    modal.show();
});

document.getElementById('reqType').addEventListener('change', function() {
    document.getElementById('fileOptions').style.display =
        this.value === 'file_upload' ? '' : 'none';
});

document.getElementById('reqForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);

    const resp = await fetch('{{ route("kecamatan.pelayanan.layanan.requirements.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: data
    });
    const result = await resp.json();

    if (result.success) {
        bootstrap.Modal.getInstance(document.getElementById('addReqModal')).hide();
        loadRequirements(activeNodeId, document.getElementById('reqPanelTitle').textContent.trim());
        form.reset();
    }
});

async function deleteRequirement(id) {
    if (!confirm('Hapus syarat berkas ini?')) return;
    const resp = await fetch(`/api/layanan/requirements/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    });
    const result = await resp.json();
    if (result.success) loadRequirements(activeNodeId, document.getElementById('reqPanelTitle').textContent.trim());
}
</script>
@endpush
@endsection
