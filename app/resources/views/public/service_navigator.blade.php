@extends('layouts.public')
@section('title', $layanan->nama_layanan . ' — Panduan Layanan')

@section('content')
@php
    $waRaw = preg_replace('/[^0-9]/', '', appProfile()->whatsapp_bot ?? '628123456789');
    $waLink = str_starts_with($waRaw,'0') ? '62'.substr($waRaw,1) : $waRaw;
@endphp

<div class="sn-wrapper">

    {{-- ═══════════════════════════════════════════════════
         HERO HEADER
    ═══════════════════════════════════════════════════ --}}
    <div class="sn-hero">
        <div class="sn-hero-inner">
            {{-- Back --}}
            <a href="{{ route('layanan') }}" class="sn-back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Semua Layanan</span>
            </a>

            {{-- Icon + Title --}}
            <div class="sn-hero-title-row">
                <div class="sn-hero-icon" style="background:{{ str_replace('bg-','',str_replace('-50','',$layanan->warna_bg??'')) }}">
                    <i class="fas {{ $layanan->ikon ?? 'fa-file-alt' }}"></i>
                </div>
                <div>
                    <h1 class="sn-hero-h1">{{ $layanan->nama_layanan }}</h1>
                    @if($layanan->estimasi_waktu)
                    <span class="sn-hero-eta">
                        <i class="far fa-clock"></i> Estimasi {{ $layanan->estimasi_waktu }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Breadcrumb --}}
            <div class="sn-breadcrumb" id="snBreadcrumb">
                <span class="sn-bc-item active">Pilih Jenis</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         STEP PROGRESS BAR
    ═══════════════════════════════════════════════════ --}}
    <div class="sn-progress-bar" id="snProgressBar">
        <div class="sn-progress-fill" id="snProgressFill" style="width:0%"></div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         MAIN CONTENT AREA
    ═══════════════════════════════════════════════════ --}}
    <div class="sn-main">
        <div class="sn-container">

            {{-- ─── PANEL: Node Selector (tampil saat belum di leaf) ─── --}}
            <div id="snNodePanel">
                <p class="sn-panel-hint" id="snPanelHint">Silakan pilih jenis layanan yang Anda butuhkan:</p>
                <div class="sn-node-grid" id="snNodeGrid">
                    @foreach($rootNodes as $node)
                    <button class="sn-node-card"
                            onclick="selectNode({{ $node->id }}, '{{ addslashes($node->name) }}', {{ $node->is_leaf ? 'true' : 'false' }})">
                        <div class="sn-node-card-icon">
                            <i class="fas {{ $node->ikon ?? 'fa-folder' }}"></i>
                        </div>
                        <div class="sn-node-card-text">
                            <span class="sn-node-card-label">{{ $node->name }}</span>
                            @if($node->description)
                            <span class="sn-node-card-desc">{{ $node->description }}</span>
                            @endif
                        </div>
                        <div class="sn-node-card-arrow">
                            @if($node->is_leaf)
                                <span class="sn-leaf-badge">Ajukan</span>
                            @else
                                <i class="fas fa-chevron-right"></i>
                            @endif
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- ─── PANEL: Leaf Form (tampil saat node is_leaf=true) ─── --}}
            <div id="snLeafPanel" class="d-none">

                {{-- Requirements Summary --}}
                <div class="sn-req-summary" id="snReqSummary"></div>

                {{-- Form --}}
                <form id="snForm" class="sn-form" novalidate>
                    @csrf
                    <input type="hidden" name="node_id" id="snNodeId">
                    <input type="hidden" name="master_layanan_id" value="{{ $layanan->id }}">
                    <input type="hidden" name="jenis_layanan" id="snJenisLayanan" value="{{ $layanan->nama_layanan }}">

                    {{-- ── Bagian 1: Identitas ── --}}
                    <div class="sn-form-section">
                        <div class="sn-form-section-label">
                            <i class="fas fa-user-circle"></i> Data Pemohon
                        </div>
                        <div class="sn-form-grid">
                            <div class="sn-field col-span-2">
                                <label class="sn-label">Nama Lengkap <span>*</span></label>
                                <input type="text" name="nama_pemohon" class="sn-input" placeholder="Sesuai KTP" required>
                            </div>
                            <div class="sn-field">
                                <label class="sn-label">NIK (16 digit) <span>*</span></label>
                                <input type="tel" name="nik" class="sn-input" placeholder="35XXXXXXXXXXXXXX"
                                       minlength="16" maxlength="16" pattern="\d{16}" required>
                            </div>
                            <div class="sn-field">
                                <label class="sn-label">WhatsApp Aktif <span>*</span></label>
                                <div class="sn-input-prefix">
                                    <span>+62</span>
                                    <input type="tel" name="whatsapp" class="sn-input" placeholder="8123456789" required>
                                </div>
                            </div>
                            <div class="sn-field col-span-2">
                                <label class="sn-label">Desa Domisili <span>*</span></label>
                                <select name="desa_id" class="sn-input sn-select" required>
                                    <option value="">— Pilih Desa —</option>
                                    @foreach($desas as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_desa }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ── Bagian 2: Upload Berkas (dinamis per node) ── --}}
                    <div class="sn-form-section" id="snUploadSection">
                        <div class="sn-form-section-label">
                            <i class="fas fa-paperclip"></i> Upload Berkas Pendukung
                        </div>
                        <div id="snUploadSlots"></div>
                    </div>

                    {{-- ── Bagian 3: Catatan & Konfirmasi ── --}}
                    <div class="sn-form-section">
                        <div class="sn-form-section-label">
                            <i class="fas fa-comment-dots"></i> Catatan Tambahan
                        </div>
                        <textarea name="uraian" class="sn-input sn-textarea"
                                  placeholder="Opsional — tuliskan detail atau permintaan khusus Anda..."></textarea>
                    </div>

                    {{-- Pernyataan --}}
                    <label class="sn-agreement">
                        <input type="checkbox" name="is_agreed" required>
                        <span>Saya menyatakan data yang diberikan adalah benar dan saya setuju untuk diproses demi keperluan administrasi.</span>
                    </label>

                    {{-- Submit --}}
                    <button type="submit" class="sn-submit-btn" id="snSubmitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Permohonan</span>
                    </button>
                </form>
            </div>

        </div>{{-- /sn-container --}}
    </div>{{-- /sn-main --}}

    {{-- ─── Floating Help Button (Mobile) ─── --}}
    <a href="https://wa.me/{{ $waLink }}" target="_blank" class="sn-wa-float">
        <i class="fab fa-whatsapp"></i>
        <span>Butuh Bantuan?</span>
    </a>

</div>{{-- /sn-wrapper --}}

{{-- ═══════════════════════════════════════════════════
     SUCCESS MODAL
═══════════════════════════════════════════════════ --}}
<div class="sn-modal-overlay" id="snSuccessModal">
    <div class="sn-modal">
        <div class="sn-modal-icon success">
            <i class="fas fa-check"></i>
        </div>
        <h2 class="sn-modal-h2">Permohonan Terkirim!</h2>
        <p class="sn-modal-p">Simpan PIN berikut untuk melacak status berkas Anda melalui halaman Lacak Status.</p>
        <div class="sn-pin-box">
            <div class="sn-pin-label">PIN LACAK BERKAS</div>
            <div class="sn-pin-code" id="snPinDisplay">——————</div>
            <button class="sn-pin-copy" id="snPinCopy" onclick="copyPin()">
                <i class="fas fa-copy"></i> Salin PIN
            </button>
        </div>
        <a href="{{ route('layanan') }}" class="sn-modal-btn primary">
            Cek Status Sekarang <i class="fas fa-arrow-right"></i>
        </a>
        <a href="{{ route('layanan') }}" class="sn-modal-btn ghost">Kembali ke Beranda</a>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════ --}}
@push('styles')
<style>
/* ── Root & Wrapper ───────────────────────────────── */
:root {
    --primary: #0f766e;
    --primary-light: #ccfbf1;
    --primary-dark: #064e3b;
    --accent: #6366f1;
    --accent-light: #eef2ff;
    --surface: #ffffff;
    --surface-2: #f8fafc;
    --border: #e2e8f0;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --text-light: #94a3b8;
    --danger: #ef4444;
    --radius-sm: 10px;
    --radius-md: 16px;
    --radius-lg: 24px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 16px rgba(0,0,0,.08);
    --shadow-lg: 0 10px 40px rgba(0,0,0,.12);
}

* { box-sizing: border-box; }

.sn-wrapper {
    min-height: 100vh;
    background: var(--surface-2);
    font-family: 'Poppins', system-ui, sans-serif;
    padding-bottom: 120px;
}

/* ── Hero ─────────────────────────────────────────── */
.sn-hero {
    background: linear-gradient(135deg, #0f766e 0%, #0e7490 60%, #1e40af 100%);
    padding: 80px 0 48px;
    position: relative;
    overflow: hidden;
}
.sn-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='24'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.sn-hero-inner {
    max-width: 720px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
}
.sn-back-btn {
    display: inline-flex; align-items: center; gap: 8px;
    color: rgba(255,255,255,.75);
    text-decoration: none;
    font-size: 13px; font-weight: 600;
    margin-bottom: 24px;
    transition: color .2s;
}
.sn-back-btn:hover { color: #fff; }

.sn-hero-title-row {
    display: flex; align-items: center; gap: 16px;
    margin-bottom: 20px;
}
.sn-hero-icon {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #fff;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
    border: 1.5px solid rgba(255,255,255,.25);
}
.sn-hero-h1 {
    font-size: clamp(1.25rem,4vw,1.75rem);
    font-weight: 800; color: #fff; margin: 0;
    line-height: 1.25;
}
.sn-hero-eta {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600;
    color: rgba(255,255,255,.7);
    margin-top: 4px;
}

/* Breadcrumb */
.sn-breadcrumb {
    display: flex; align-items: center; flex-wrap: wrap;
    gap: 4px;
    font-size: 12px; font-weight: 700;
    color: rgba(255,255,255,.5);
}
.sn-bc-item { transition: color .2s; }
.sn-bc-item.active { color: rgba(255,255,255,.9); }
.sn-bc-sep { opacity: .4; }
.sn-bc-item:not(.active) { cursor: pointer; }
.sn-bc-item:not(.active):hover { color: rgba(255,255,255,.85); }

/* ── Progress Bar ─────────────────────────────────── */
.sn-progress-bar {
    height: 4px;
    background: rgba(99,102,241,.15);
    position: sticky; top: 0; z-index: 100;
}
.sn-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    transition: width .5s cubic-bezier(.4,0,.2,1);
    border-radius: 0 4px 4px 0;
}

/* ── Main ─────────────────────────────────────────── */
.sn-main { padding: 28px 16px; }
.sn-container { max-width: 720px; margin: 0 auto; }

.sn-panel-hint {
    font-size: 14px; color: var(--text-muted); font-weight: 500;
    margin-bottom: 16px;
}

/* ── Node Grid ────────────────────────────────────── */
.sn-node-grid {
    display: flex; flex-direction: column; gap: 10px;
}
.sn-node-card {
    width: 100%; background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 16px 18px;
    display: flex; align-items: center; gap: 14px;
    cursor: pointer; text-align: left;
    transition: all .2s ease;
    box-shadow: var(--shadow-sm);
    -webkit-tap-highlight-color: transparent;
}
.sn-node-card:hover, .sn-node-card:active {
    border-color: var(--primary);
    background: linear-gradient(135deg, #f0fdfa, #eff6ff);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.sn-node-card-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: var(--primary-light);
    color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
    transition: all .2s;
}
.sn-node-card:hover .sn-node-card-icon {
    background: var(--primary);
    color: #fff;
}
.sn-node-card-text {
    flex: 1; min-width: 0;
}
.sn-node-card-label {
    display: block;
    font-size: 15px; font-weight: 700; color: var(--text-main);
    line-height: 1.3;
}
.sn-node-card-desc {
    display: block;
    font-size: 12px; color: var(--text-muted);
    margin-top: 3px; font-weight: 500;
}
.sn-node-card-arrow {
    color: var(--text-light);
    font-size: 13px; flex-shrink: 0;
}
.sn-leaf-badge {
    display: inline-block;
    background: var(--accent-light);
    color: var(--accent);
    font-size: 11px; font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid #c7d2fe;
    white-space: nowrap;
}

/* ── Requirement Summary ──────────────────────────── */
.sn-req-summary {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 18px 20px;
    margin-bottom: 20px;
}
.sn-req-summary h3 {
    font-size: 13px; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .5px;
    margin: 0 0 12px;
}
.sn-req-list { display: flex; flex-direction: column; gap: 8px; margin: 0; padding: 0; list-style: none; }
.sn-req-list li {
    display: flex; align-items: flex-start; gap: 10px;
    font-size: 13px; color: var(--text-main); font-weight: 500;
}
.sn-req-list li .sn-req-dot {
    width: 20px; height: 20px; border-radius: 50%;
    background: var(--primary-light);
    color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; flex-shrink: 0; margin-top: 1px;
}
.sn-req-list li .sn-req-dot.opt { background: #fef9c3; color: #ca8a04; }

/* ── Form ─────────────────────────────────────────── */
.sn-form { display: flex; flex-direction: column; gap: 20px; }

.sn-form-section {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 20px;
}
.sn-form-section-label {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .6px;
    color: var(--text-muted);
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}
.sn-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
.sn-field { display: flex; flex-direction: column; gap: 6px; }
.sn-field.col-span-2 { grid-column: span 2; }

.sn-label {
    font-size: 12px; font-weight: 700; color: var(--text-main);
}
.sn-label span { color: var(--danger); }

.sn-input {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 11px 14px;
    font-size: 14px; font-weight: 500;
    color: var(--text-main);
    background: var(--surface-2);
    outline: none;
    width: 100%;
    transition: all .2s;
    font-family: inherit;
    -webkit-appearance: none;
}
.sn-input:focus {
    border-color: var(--primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(15,118,110,.1);
}
.sn-select { cursor: pointer; }
.sn-textarea { min-height: 90px; resize: vertical; }

.sn-input-prefix {
    display: flex; align-items: stretch;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface-2);
    overflow: hidden;
    transition: all .2s;
}
.sn-input-prefix:focus-within {
    border-color: var(--primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(15,118,110,.1);
}
.sn-input-prefix span {
    display: flex; align-items: center;
    padding: 0 12px;
    font-size: 13px; font-weight: 700;
    color: var(--text-muted);
    background: var(--border);
    border-right: 1.5px solid var(--border);
    white-space: nowrap;
}
.sn-input-prefix .sn-input {
    border: none; border-radius: 0;
    background: transparent; box-shadow: none;
    flex: 1;
}

/* ── Upload Slots ─────────────────────────────────── */
.sn-upload-slot {
    border: 1.5px dashed var(--border);
    border-radius: var(--radius-sm);
    padding: 14px 16px;
    margin-bottom: 10px;
    background: var(--surface-2);
    transition: border-color .2s;
}
.sn-upload-slot.has-file { border-color: var(--primary); background: #f0fdfa; }
.sn-upload-slot-header {
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 10px;
}
.sn-upload-slot-icon {
    width: 36px; height: 36px;
    border-radius: 10px; background: var(--surface);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
    border: 1.5px solid var(--border);
}
.sn-upload-slot-info { flex: 1; }
.sn-upload-slot-label {
    font-size: 13px; font-weight: 700; color: var(--text-main);
    display: flex; align-items: center; gap: 6px;
}
.sn-required-tag {
    font-size: 10px; font-weight: 700; color: var(--danger);
    background: #fee2e2; padding: 1px 7px; border-radius: 20px;
}
.sn-optional-tag {
    font-size: 10px; font-weight: 700; color: #92400e;
    background: #fef3c7; padding: 1px 7px; border-radius: 20px;
}
.sn-upload-slot-hint {
    font-size: 11px; color: var(--text-muted);
    margin-top: 2px; font-weight: 500;
}

/* Custom File Input Button */
.sn-file-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: 12px; font-weight: 700; color: var(--primary);
    cursor: pointer; position: relative; overflow: hidden;
    transition: all .2s;
}
.sn-file-btn:hover { background: var(--primary-light); border-color: var(--primary); }
.sn-file-btn input[type=file] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer;
}
.sn-file-chosen {
    font-size: 12px; color: var(--primary); font-weight: 600;
    margin-top: 6px; display: none;
}

/* ── Agreement ────────────────────────────────────── */
.sn-agreement {
    display: flex; align-items: flex-start; gap: 12px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 16px 18px;
    cursor: pointer;
    transition: border-color .2s;
}
.sn-agreement:hover { border-color: var(--primary); }
.sn-agreement input[type=checkbox] {
    width: 18px; height: 18px; flex-shrink: 0;
    margin-top: 1px; cursor: pointer; accent-color: var(--primary);
}
.sn-agreement span {
    font-size: 12px; line-height: 1.6; color: var(--text-muted); font-weight: 500;
}

/* ── Submit Button ────────────────────────────────── */
.sn-submit-btn {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; padding: 16px;
    background: linear-gradient(135deg, var(--primary), #0e7490);
    color: #fff; border: none; border-radius: var(--radius-md);
    font-size: 16px; font-weight: 800;
    cursor: pointer; font-family: inherit;
    box-shadow: 0 8px 24px rgba(15,118,110,.3);
    transition: all .2s;
    -webkit-tap-highlight-color: transparent;
}
.sn-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(15,118,110,.4); }
.sn-submit-btn:active { transform: translateY(0); }
.sn-submit-btn:disabled { opacity: .7; cursor: not-allowed; transform: none; }

/* ── Floating WA Button ───────────────────────────── */
.sn-wa-float {
    position: fixed; bottom: 24px; right: 20px; z-index: 50;
    display: flex; align-items: center; gap: 8px;
    background: #25d366; color: #fff;
    padding: 12px 18px 12px 14px;
    border-radius: 50px;
    text-decoration: none;
    font-size: 13px; font-weight: 700;
    box-shadow: 0 6px 20px rgba(37,211,102,.4);
    transition: all .25s;
}
.sn-wa-float:hover { transform: scale(1.05); color: #fff; }
.sn-wa-float i { font-size: 20px; }
@media(max-width:480px) { .sn-wa-float span { display: none; } .sn-wa-float { padding: 14px; border-radius: 50%; } }

/* ── Modal ────────────────────────────────────────── */
.sn-modal-overlay {
    position: fixed; inset: 0; z-index: 1000;
    background: rgba(15,23,42,.6);
    backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
    opacity: 0; pointer-events: none;
    transition: opacity .3s;
}
.sn-modal-overlay.show { opacity: 1; pointer-events: auto; }
.sn-modal {
    background: #fff; border-radius: var(--radius-lg);
    padding: 40px 32px;
    max-width: 400px; width: 100%;
    text-align: center;
    box-shadow: var(--shadow-lg);
    transform: scale(.9);
    transition: transform .3s cubic-bezier(.34,1.56,.64,1);
}
.sn-modal-overlay.show .sn-modal { transform: scale(1); }
.sn-modal-icon {
    width: 72px; height: 72px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; margin: 0 auto 20px;
}
.sn-modal-icon.success { background: var(--primary-light); color: var(--primary); }
.sn-modal-h2 { font-size: 22px; font-weight: 800; color: var(--text-main); margin: 0 0 8px; }
.sn-modal-p  { font-size: 14px; color: var(--text-muted); margin: 0 0 24px; line-height: 1.6; }

.sn-pin-box {
    background: var(--surface-2);
    border-radius: var(--radius-md);
    padding: 20px;
    margin-bottom: 24px;
}
.sn-pin-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-light); margin-bottom: 8px; }
.sn-pin-code  { font-size: 36px; font-weight: 900; letter-spacing: .4em; color: var(--primary); padding-left: .4em; }
.sn-pin-copy  {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: 12px; padding: 6px 14px;
    background: #fff; border: 1.5px solid var(--border);
    border-radius: 8px; font-size: 12px; font-weight: 700; color: var(--primary);
    cursor: pointer; font-family: inherit; transition: all .15s;
}
.sn-pin-copy:hover { background: var(--primary-light); border-color: var(--primary); }

.sn-modal-btn {
    display: block; width: 100%;
    padding: 14px;
    border-radius: var(--radius-md);
    font-size: 15px; font-weight: 800;
    text-decoration: none; margin-bottom: 10px;
    transition: all .2s;
}
.sn-modal-btn.primary { background: var(--text-main); color: #fff; }
.sn-modal-btn.primary:hover { background: #1e293b; color: #fff; }
.sn-modal-btn.ghost { color: var(--text-muted); font-size: 13px; background: none; }

/* ── Animations ───────────────────────────────────── */
@keyframes snSlideIn {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
.sn-anim { animation: snSlideIn .35s cubic-bezier(.4,0,.2,1) both; }

/* ── Responsive ───────────────────────────────────── */
@media(max-width:640px) {
    .sn-hero { padding: 70px 0 36px; }
    .sn-form-grid { grid-template-columns: 1fr; }
    .sn-field.col-span-2 { grid-column: span 1; }
    .sn-modal { padding: 32px 20px; }
    .sn-pin-code { font-size: 28px; }
}
</style>
@endpush

{{-- ═══════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════ --}}
@push('scripts')
<script>
// ── State ──────────────────────────────────────────────────
const LAYANAN_ID  = {{ $layanan->id }};
const TOTAL_STEPS = 5; // estimasi kedalaman max
let history = []; // [{id, name}] stack navigasi
let currentNodeId = null;

// ── Render root nodes saat init ────────────────────────────
const rootNodes = @json($rootNodes->map(fn($n) => [
    'id'=> $n->id, 'name'=>$n->name,
    'description'=>$n->description,
    'ikon'=>$n->ikon ?? 'fa-folder',
    'is_leaf'=>$n->is_leaf
]));

updateProgress(0);

// ── Pilih node ────────────────────────────────────────────
async function selectNode(nodeId, nodeName, isLeaf) {
    history.push({ id: nodeId, name: nodeName });
    updateBreadcrumb();

    if (isLeaf) {
        currentNodeId = nodeId;
        await loadLeafForm(nodeId, nodeName);
    } else {
        await loadChildren(nodeId);
    }
}

// ── Load children via AJAX ────────────────────────────────
async function loadChildren(nodeId) {
    showLoading();
    const res  = await fetch(`/api/layanan/nodes/${nodeId}/children`);
    const data = await res.json();

    updateProgress(history.length / TOTAL_STEPS * 60);

    const grid = document.getElementById('snNodeGrid');
    grid.innerHTML = data.children.map(n => `
        <button class="sn-node-card sn-anim"
                onclick="selectNode(${n.id}, '${escape(n.name)}', ${n.is_leaf})">
            <div class="sn-node-card-icon">
                <i class="fas ${n.ikon || 'fa-folder'}"></i>
            </div>
            <div class="sn-node-card-text">
                <span class="sn-node-card-label">${unescape(n.name)}</span>
                ${n.description ? `<span class="sn-node-card-desc">${n.description}</span>` : ''}
            </div>
            <div class="sn-node-card-arrow">
                ${n.is_leaf
                    ? '<span class="sn-leaf-badge">Ajukan</span>'
                    : '<i class="fas fa-chevron-right"></i>'}
            </div>
        </button>
    `).join('');

    document.getElementById('snPanelHint').textContent =
        'Pilih jenis yang sesuai:';
    showNodePanel();
    hideLoading();
}

// ── Load leaf form ────────────────────────────────────────
async function loadLeafForm(nodeId, nodeName) {
    showLoading();
    const res  = await fetch(`/api/layanan/nodes/${nodeId}/requirements`);
    const data = await res.json();
    const reqs = data.requirements || [];

    document.getElementById('snNodeId').value = nodeId;
    document.getElementById('snJenisLayanan').value = nodeName;

    // Requirement summary card
    const summaryEl = document.getElementById('snReqSummary');
    summaryEl.innerHTML = `
        <h3>Berkas yang Diperlukan</h3>
        <ul class="sn-req-list">
            ${reqs.map(r => `
                <li>
                    <div class="sn-req-dot ${!r.is_required ? 'opt' : ''}">
                        <i class="fas ${r.type==='file_upload' ? 'fa-paperclip' : r.type==='checkbox' ? 'fa-check' : 'fa-info'}"></i>
                    </div>
                    <div>
                        <div style="font-weight:700">${r.label}
                            ${r.is_required
                                ? '<span class="sn-required-tag">WAJIB</span>'
                                : '<span class="sn-optional-tag">Opsional</span>'}
                        </div>
                        ${r.description ? `<div style="font-size:11px;color:#64748b;margin-top:2px">${r.description}</div>` : ''}
                    </div>
                </li>
            `).join('')}
        </ul>
    `;

    // Upload slots (hanya untuk tipe file_upload)
    const slotsEl = document.getElementById('snUploadSlots');
    const fileReqs = reqs.filter(r => r.type === 'file_upload');

    if (fileReqs.length === 0) {
        slotsEl.innerHTML = `<p style="font-size:13px;color:#64748b">Tidak ada berkas yang perlu diunggah untuk layanan ini.</p>`;
    } else {
        slotsEl.innerHTML = fileReqs.map((r, i) => `
            <div class="sn-upload-slot" id="slot_${i}">
                <div class="sn-upload-slot-header">
                    <div class="sn-upload-slot-icon">
                        <i class="fas fa-file-upload" style="color:#0f766e"></i>
                    </div>
                    <div class="sn-upload-slot-info">
                        <div class="sn-upload-slot-label">
                            ${r.label}
                            ${r.is_required
                                ? '<span class="sn-required-tag">WAJIB</span>'
                                : '<span class="sn-optional-tag">Opsional</span>'}
                        </div>
                        <div class="sn-upload-slot-hint">.${(r.accepted_types||'jpg,png,pdf').replace(/,/g,', .')} — Maks ${r.max_size_mb||5}MB</div>
                    </div>
                </div>
                <label class="sn-file-btn">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Pilih File</span>
                    <input type="file"
                           name="attachments[]"
                           accept=".${(r.accepted_types||'jpg,png,pdf').split(',').join(',.')}"
                           ${r.is_required ? 'required' : ''}
                           data-req-id="${r.id}"
                           data-label="${r.label}"
                           onchange="handleFile(this, ${i})">
                </label>
                <input type="hidden" name="attachment_labels[]" value="${r.label}">
                <input type="hidden" name="attachment_req_ids[]" value="${r.id}">
                <div class="sn-file-chosen" id="chosen_${i}"></div>
            </div>
        `).join('');
    }

    updateProgress(75);
    showLeafPanel();
    hideLoading();
}

function handleFile(input, idx) {
    const slot    = document.getElementById(`slot_${idx}`);
    const chosen  = document.getElementById(`chosen_${idx}`);
    const file    = input.files[0];
    if (!file) return;
    slot.classList.add('has-file');
    chosen.style.display = 'block';
    chosen.innerHTML = `<i class="fas fa-check-circle" style="color:#0f766e"></i> ${file.name} (${(file.size/1024/1024).toFixed(2)} MB)`;
}

// ── Breadcrumb ────────────────────────────────────────────
function updateBreadcrumb() {
    const el = document.getElementById('snBreadcrumb');
    const items = [{ id: null, name: 'Pilih Jenis' }, ...history];
    el.innerHTML = items.map((item, i) => {
        const isLast = i === items.length - 1;
        return `
            ${i > 0 ? '<span class="sn-bc-sep">›</span>' : ''}
            <span class="sn-bc-item ${isLast ? 'active' : ''}"
                  ${!isLast ? `onclick="goBack(${i})"` : ''}>${item.name}</span>
        `;
    }).join('');
}

function goBack(toIndex) {
    history = history.slice(0, toIndex);
    updateBreadcrumb();

    if (toIndex === 0) {
        // Kembali ke root
        const grid = document.getElementById('snNodeGrid');
        grid.innerHTML = rootNodes.map(n => `
            <button class="sn-node-card sn-anim"
                    onclick="selectNode(${n.id}, '${escape(n.name)}', ${n.is_leaf})">
                <div class="sn-node-card-icon"><i class="fas ${n.ikon}"></i></div>
                <div class="sn-node-card-text">
                    <span class="sn-node-card-label">${unescape(n.name)}</span>
                    ${n.description ? `<span class="sn-node-card-desc">${n.description}</span>` : ''}
                </div>
                <div class="sn-node-card-arrow">
                    ${n.is_leaf ? '<span class="sn-leaf-badge">Ajukan</span>' : '<i class="fas fa-chevron-right"></i>'}
                </div>
            </button>
        `).join('');
        showNodePanel(); updateProgress(0);
    } else {
        const parentNode = history[toIndex - 1];
        loadChildren(parentNode.id);
    }
}

// ── Panel Toggle ──────────────────────────────────────────
function showNodePanel() {
    document.getElementById('snNodePanel').classList.remove('d-none');
    document.getElementById('snLeafPanel').classList.add('d-none');
}
function showLeafPanel() {
    document.getElementById('snNodePanel').classList.add('d-none');
    document.getElementById('snLeafPanel').classList.remove('d-none');
    document.getElementById('snLeafPanel').classList.add('sn-anim');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
function updateProgress(pct) {
    document.getElementById('snProgressFill').style.width = pct + '%';
}

// ── Loading ───────────────────────────────────────────────
function showLoading() {
    document.getElementById('snNodeGrid').innerHTML = `
        <div style="text-align:center;padding:40px">
            <div style="display:inline-block;width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:#0f766e;border-radius:50%;animation:spin .8s linear infinite"></div>
        </div>`;
}
function hideLoading() {}

// ── Form Submit ───────────────────────────────────────────
document.getElementById('snForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('snSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = `<span style="display:inline-block;width:18px;height:18px;border:2.5px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite"></span> <span>Mengirim...</span>`;

    try {
        const fd = new FormData(this);
        const res = await fetch('{{ route("apply.node.store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: fd
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('snPinDisplay').textContent = data.tracking_code;
            updateProgress(100);
            const modal = document.getElementById('snSuccessModal');
            modal.classList.add('show');
        } else {
            alert(data.message || 'Gagal mengirim. Silakan coba lagi.');
        }
    } catch(err) {
        console.error(err);
        alert('Terjadi kesalahan sistem.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="fas fa-paper-plane"></i> <span>Kirim Permohonan</span>`;
    }
});

function copyPin() {
    const pin = document.getElementById('snPinDisplay').textContent;
    navigator.clipboard.writeText(pin).then(() => {
        const btn = document.getElementById('snPinCopy');
        btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
        setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i> Salin PIN', 1500);
    });
}

// ── Global keyframe spin ───────────────────────────────────
const style = document.createElement('style');
style.textContent = `@keyframes spin { to { transform: rotate(360deg) } }`;
document.head.appendChild(style);
</script>
@endpush
@endsection
