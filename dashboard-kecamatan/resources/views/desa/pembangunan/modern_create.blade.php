@extends('layouts.modern')

@section('title', 'Tambah Kegiatan Baru')

@section('breadcrumb')
    <li><a href="{{ route('desa.pembangunan.fisik.index') }}">Kegiatan</a></li>
    <li><span>Tambah Baru</span></li>
@endsection

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Form Section -->
        <div class="flex-1">
            <div class="card bg-base-100 shadow-xl border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-2xl font-bold mb-6 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-primary"></i> Form Kegiatan Desa
                    </h2>

                    <form id="activityForm" class="space-y-6">
                        @csrf
                        <!-- Nama Kegiatan -->
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-bold text-base-content/70">Pilih Jenis Kegiatan</span>
                            </label>
                            <select id="master_kegiatan_id" name="master_kegiatan_id"
                                class="select select-bordered w-full select-primary">
                                <option disabled selected>-- Pilih Referensi Kegiatan --</option>
                                @foreach(\App\Models\MasterKegiatan::active()->get() as $mk)
                                    <option value="{{ $mk->id }}" data-jenis="{{ $mk->jenis_kegiatan }}">
                                        [{{ $mk->kode_kegiatan }}] {{ $mk->nama_kegiatan }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="label">
                                <span class="label-text-alt italic">Gunakan referensi untuk mempermudah persiapan
                                    administrasi.</span>
                            </label>
                        </div>

                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-bold text-base-content/70">Nama Kegiatan Real</span>
                            </label>
                            <input type="text" id="nama_kegiatan_real" name="nama_kegiatan"
                                placeholder="Contoh: Pembangunan Jalan Beton RT 01" class="input input-bordered w-full" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-base-content/70">Pagu Anggaran (Rp)</span>
                                </label>
                                <input type="number" id="pagu_anggaran" name="pagu_anggaran" placeholder="0"
                                    class="input input-bordered w-full font-mono text-primary font-bold" />
                            </div>
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text font-bold text-base-content/70">Lokasi</span>
                                </label>
                                <input type="text" name="lokasi" placeholder="Mis: Dusun Krajan"
                                    class="input input-bordered w-full" />
                            </div>
                        </div>

                        <!-- Komponen Belanja (Multi-select simulation for demo) -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-bold text-base-content/70">Komponen Belanja</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach(\App\Models\MasterKomponenBelanja::active()->take(6)->get() as $komp)
                                    <label
                                        class="label cursor-pointer justify-start gap-3 bg-base-200/30 p-3 rounded-xl border border-transparent hover:border-primary/30 transition-all">
                                        <input type="checkbox" name="components[]" value="{{ $komp->id }}"
                                            class="checkbox checkbox-primary component-checkbox" />
                                        <span class="label-text">{{ $komp->nama_komponen }}</span>
                                        @if($komp->objek_pajak)
                                            <div class="badge badge-warning badge-xs">Pajak</div>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-8">
                            <button type="button" class="btn btn-ghost">Batal</button>
                            <button type="submit" class="btn btn-primary px-8 shadow-lg shadow-primary/20">Simpan
                                Draft</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assistant Sidebar -->
        <div class="w-full lg:w-96">
            <div class="sticky top-24 space-y-4">
                <!-- SPJ Assistant -->
                <div class="card bg-accent text-accent-content shadow-xl">
                    <div class="card-body p-6">
                        <h3 class="card-title text-lg flex items-center gap-2">
                            <i class="fas fa-magic"></i> SAE Assistant (SPJ)
                        </h3>
                        <p class="text-xs opacity-80 mb-4 font-medium italic">Berdasarkan pilihan Anda, berikut saran
                            dokumen yang perlu disiapkan:</p>

                        <div id="spjChecklist" class="space-y-2">
                            <div class="flex items-center gap-3 bg-white/20 p-2 rounded-lg">
                                <i class="fas fa-info-circle"></i>
                                <span class="text-sm">Pilih jenis kegiatan untuk melihat saran dokumen.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax Assistant -->
                <div id="taxCard" class="card bg-warning text-warning-content shadow-xl hidden">
                    <div class="card-body p-6">
                        <h3 class="card-title text-lg flex items-center gap-2">
                            <i class="fas fa-calculator"></i> Bantuan Pajak
                        </h3>
                        <div id="taxResult" class="space-y-3">
                            <!-- Tax estimation results here -->
                        </div>
                        <div class="divider divider-warning opacity-30 my-1"></div>
                        <p class="text-[10px] leading-tight flex items-start gap-2">
                            <i class="fas fa-lightbulb mt-0.5"></i>
                            <span>Silakan sesuaikan dengan ketentuan daerah (Perbup Probolinggo No. 5/2025).</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const kegiatanSelect = document.getElementById('master_kegiatan_id');
        const paguInput = document.getElementById('pagu_anggaran');
        const spjChecklist = document.getElementById('spjChecklist');
        const taxCard = document.getElementById('taxCard');
        const taxResult = document.getElementById('taxResult');
        const componentCheckboxes = document.querySelectorAll('.component-checkbox');

        function updateAssistant() {
            const masterId = kegiatanSelect.value;
            const option = kegiatanSelect.options[kegiatanSelect.selectedIndex];
            const jenis = option ? option.dataset.jenis : '';
            const pagu = paguInput.value || 0;

            const selectedComponents = Array.from(componentCheckboxes)
                .filter(i => i.checked)
                .map(i => i.value);

            if (!masterId) return;

            // Predict Documents
            fetch('/api/assistant/predict-docs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nama_kegiatan: option.text,
                    jenis_kegiatan: jenis,
                    pagu_anggaran: pagu,
                    components: selectedComponents
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        spjChecklist.innerHTML = data.documents.map(doc => `
                        <div class="flex items-center gap-3 bg-white/10 p-2 rounded-lg animate-in slide-in-from-right duration-300">
                            <i class="fas fa-file-alt text-xs"></i>
                            <span class="text-xs font-semibold">${doc.nama_dokumen}</span>
                        </div>
                    `).join('');
                    }
                });

            // Simple Tax Estimation for demo (checks items with pajak badge)
            // In real app, this would call /api/assistant/estimate-tax per component
            let totalTax = 0;
            let hasTax = false;

            taxResult.innerHTML = '';

            componentCheckboxes.forEach(cb => {
                if (cb.checked) {
                    // Mock logic: if it has tax, show 10%
                    const label = cb.parentElement.querySelector('.label-text').innerText;
                    const isPajak = cb.parentElement.querySelector('.badge-warning');

                    if (isPajak) {
                        hasTax = true;
                        const est = pagu * 0.1; // Simulated
                        taxResult.innerHTML += `
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold">${label}</span>
                                <span>Estimasi: 10%</span>
                            </div>
                        `;
                    }
                }
            });

            if (hasTax && pagu > 0) {
                taxCard.classList.remove('hidden');
            } else {
                taxCard.classList.add('hidden');
            }
        </div >

                kegiatanSelect.addEventListener('change', updateAssistant);
            paguInput.addEventListener('input', updateAssistant);
            componentCheckboxes.forEach(cb => cb.addEventListener('change', updateAssistant));

    </script>
@endpush