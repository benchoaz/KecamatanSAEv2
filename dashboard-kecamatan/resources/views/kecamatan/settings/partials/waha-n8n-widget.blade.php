@php
    $wahaSettings = \App\Models\WahaN8nSetting::getSettings();
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fab fa-whatsapp text-green-500"></i>
                <h6 class="mb-0 fw-bold text-slate-900">Status WhatsApp Bot</h6>
            </div>
            <a href="{{ route('kecamatan.settings.waha-n8n.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-cog me-1"></i> Kelola
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- WAHA Status -->
            <div class="col-md-4">
                <div
                    class="p-3 rounded-3 {{ $wahaSettings?->is_waha_connected ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div
                            class="avatar-sm {{ $wahaSettings?->is_waha_connected ? 'bg-success text-white' : 'bg-danger text-white' }} rounded-circle d-flex align-items-center justify-content-center">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div>
                            <small class="text-slate-500 d-block">WAHA</small>
                            <span
                                class="fw-bold {{ $wahaSettings?->is_waha_connected ? 'text-success' : 'text-danger' }}">
                                {{ $wahaSettings?->is_waha_connected ? 'Terhubung' : 'Terputus' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- n8n Status -->
            <div class="col-md-4">
                <div
                    class="p-3 rounded-3 {{ $wahaSettings?->is_n8n_connected ? 'bg-warning bg-opacity-10' : 'bg-secondary bg-opacity-10' }}">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div
                            class="avatar-sm {{ $wahaSettings?->is_n8n_connected ? 'bg-warning text-white' : 'bg-secondary text-white' }} rounded-circle d-flex align-items-center justify-content-center">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div>
                            <small class="text-slate-500 d-block">n8n</small>
                            <span
                                class="fw-bold {{ $wahaSettings?->is_n8n_connected ? 'text-warning' : 'text-secondary' }}">
                                {{ $wahaSettings?->is_n8n_connected ? 'Terhubung' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bot Status -->
            <div class="col-md-4">
                <div
                    class="p-3 rounded-3 {{ $wahaSettings?->bot_status === 'connected' ? 'bg-primary bg-opacity-10' : 'bg-slate-100' }}">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div
                            class="avatar-sm {{ $wahaSettings?->bot_status === 'connected' ? 'bg-primary text-white' : 'bg-slate-400 text-white' }} rounded-circle d-flex align-items-center justify-content-center">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <small class="text-slate-500 d-block">Bot Status</small>
                            <span
                                class="fw-bold {{ $wahaSettings?->bot_status === 'connected' ? 'text-primary' : 'text-slate-500' }}">
                                @if($wahaSettings?->bot_status === 'connected')
                                    Aktif
                                @elseif($wahaSettings?->bot_status === 'qr_required')
                                    QR Required
                                @else
                                    Tidak Aktif
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($wahaSettings?->bot_number)
            <div class="mt-3 pt-3 border-top border-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-slate-500">Nomor Bot:</small>
                        <span class="fw-semibold text-slate-700 ms-2">{{ $wahaSettings->bot_number }}</span>
                    </div>
                    <a href="{{ $wahaSettings->getWhatsappLink() }}" target="_blank" class="btn btn-sm btn-success">
                        <i class="fab fa-whatsapp me-1"></i> Chat Bot
                    </a>
                </div>
            </div>
        @endif

        @if($wahaSettings?->last_connection_check)
            <small class="text-slate-400 d-block mt-2">
                <i class="fas fa-clock me-1"></i>
                Terakhir dicek: {{ $wahaSettings->last_connection_check->diffForHumans() }}
            </small>
        @endif
    </div>
</div>