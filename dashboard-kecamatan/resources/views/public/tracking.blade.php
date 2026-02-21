@extends('layouts.public')

@section('title', 'Lacak Status Berkas')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-blue-50 py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                {{-- Header --}}
                <div class="text-center mb-8">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full shadow-lg mb-4">
                        <i class="fas fa-search-location text-white text-3xl"></i>
                    </div>
                    <h1 class="text-4xl font-black text-slate-800 mb-3">Lacak Status Berkas</h1>
                    <p class="text-slate-600 text-lg">Cek status pengajuan layanan Anda secara real-time</p>
                </div>

                {{-- Search Form --}}
                <div class="card bg-white shadow-2xl rounded-3xl border-0 overflow-hidden mb-6">
                    <div class="card-body p-8">
                        <form id="trackingForm">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-700 mb-3">
                                    <i class="fas fa-fingerprint text-teal-500 me-2"></i>
                                    Masukkan Nomor WhatsApp atau ID Berkas
                                </label>
                                <input type="text" id="identifier" name="identifier"
                                    class="form-control form-control-lg bg-slate-50 border-slate-200 rounded-3 shadow-sm"
                                    placeholder="Contoh: 628123456789 atau UUID berkas" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Gunakan nomor WA yang Anda daftarkan saat mengajukan layanan
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold shadow-lg py-3">
                                <i class="fas fa-search me-2"></i> Cek Status Sekarang
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Result Container --}}
                <div id="resultContainer" class="d-none">
                    <div class="card bg-white shadow-2xl rounded-3xl border-0 overflow-hidden">
                        <div class="card-header bg-gradient-to-r from-teal-600 to-teal-700 text-white py-4 px-5">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-file-alt me-2"></i> Detail Status Berkas
                            </h5>
                        </div>
                        <div class="card-body p-5">
                            <div id="resultContent"></div>
                        </div>
                    </div>
                </div>

                {{-- Error Container --}}
                <div id="errorContainer" class="d-none">
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 p-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-exclamation-circle text-danger fs-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold">Berkas Tidak Ditemukan</h6>
                                <p class="mb-0 small" id="errorMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .status-emerald {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .status-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .status-amber {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .status-rose {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            color: white;
        }

        .status-slate {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: white;
        }

        .info-row {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            flex: 0 0 180px;
            font-weight: 600;
            color: #64748b;
            font-size: 0.9rem;
        }

        .info-value {
            flex: 1;
            color: #1e293b;
            font-weight: 500;
        }

        .download-btn {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.3);
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.4);
            color: white;
        }

        .pickup-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
    </style>

    <script>
        document.getElementById('trackingForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const identifier = document.getElementById('identifier').value;
            const resultContainer = document.getElementById('resultContainer');
            const errorContainer = document.getElementById('errorContainer');
            const resultContent = document.getElementById('resultContent');

            // Hide previous results
            resultContainer.classList.add('d-none');
            errorContainer.classList.add('d-none');

            try {
                const response = await fetch('{{ route('public.tracking.check') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ identifier })
                });

                const data = await response.json();

                if (data.found) {
                    // Build result HTML
                    const statusIcon = data.status_color === 'emerald' ? 'fa-check-double' :
                        (data.status_color === 'rose' ? 'fa-times-circle' : 'fa-circle-notch fa-spin');

                    let html = `
                            <div class="mb-4">
                                <span class="status-badge status-${data.status_color}">
                                    <i class="fas ${statusIcon}"></i> ${data.status_label}
                                </span>
                            </div>

                            <div class="info-row">
                                <div class="info-label">ID Berkas</div>
                                <div class="info-value"><code class="bg-slate-100 px-2 py-1 rounded">${data.uuid}</code></div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Jenis Layanan</div>
                                <div class="info-value">${data.jenis_layanan}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Tanggal Pengajuan</div>
                                <div class="info-value">${data.created_at}</div>
                            </div>
                        `;

                    if (data.public_response) {
                        html += `
                                <div class="info-row">
                                    <div class="info-label">Tanggapan</div>
                                    <div class="info-value">${data.public_response}</div>
                                </div>
                            `;
                    }

                    // Digital completion
                    if (data.completion_type === 'digital' && data.download_url) {
                        html += `
                                <div class="mt-4 pt-4 border-top text-center">
                                    <h6 class="fw-bold text-success mb-3">
                                        <i class="fas fa-check-circle me-2"></i>Berkas Sudah Selesai!
                                    </h6>
                                    <a href="${data.download_url}" target="_blank" class="download-btn">
                                        <i class="fas fa-file-pdf fs-5"></i>
                                        <span>Download Hasil (PDF)</span>
                                    </a>
                                    <p class="text-muted small mt-3 mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        File akan terbuka di tab baru
                                    </p>
                                </div>
                            `;
                    }

                    // Physical completion
                    if (data.completion_type === 'physical' && data.pickup_info) {
                        html += `
                                <div class="pickup-card">
                                    <h6 class="fw-bold text-amber-900 mb-3">
                                        <i class="fas fa-building me-2"></i>Ambil di Kantor Kecamatan
                                    </h6>
                                    <div class="mb-2">
                                        <strong class="text-amber-800">Siap Diambil:</strong>
                                        <span class="text-amber-900">${data.pickup_info.ready_at || 'Segera'}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong class="text-amber-800">Hubungi:</strong>
                                        <span class="text-amber-900">${data.pickup_info.pickup_person || '-'}</span>
                                    </div>
                                    ${data.pickup_info.pickup_notes ? `
                                        <div class="mt-3 p-3 bg-white rounded-3">
                                            <small class="text-slate-700">
                                                <i class="fas fa-sticky-note text-amber-600 me-2"></i>
                                                ${data.pickup_info.pickup_notes}
                                            </small>
                                        </div>
                                    ` : ''}
                                </div>
                            `;
                    }

                    resultContent.innerHTML = html;
                    resultContainer.classList.remove('d-none');

                } else {
                    document.getElementById('errorMessage').textContent = data.message;
                    errorContainer.classList.remove('d-none');
                }

            } catch (error) {
                document.getElementById('errorMessage').textContent = 'Terjadi kesalahan sistem. Silakan coba lagi.';
                errorContainer.classList.remove('d-none');
            }
        });

    </script>
@endsection