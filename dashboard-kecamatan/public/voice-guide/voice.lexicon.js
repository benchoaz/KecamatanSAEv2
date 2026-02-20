/**
 * voice.lexicon.js
 * Kamus Kata (Thesaurus) & Daftar Kata Abaian (Stopwords)
 * Updated with granular structure provided by User.
 */

window.VoiceLexicon = (function () {

    // RAW DATA FROM USER (Structured)
    const RAW_LEXICON = {
        verbs: {
            // Base Concept : [Variations]
            'read': [
                'baca', 'bacakan', 'bacain', 'membaca', 'dibaca',
                'denger', 'dengar', 'dengerin', 'dengarkan',
                'lihat', 'liatin', 'bukakan', 'buka', 'tampilkan', 'tunjukkan'
            ],
            'stop': [
                'stop', 'setop', 'berhenti', 'diem', 'diam',
                'cukup', 'sudah', 'jangan', 'matikan', 'nonaktifkan',
                'batal', 'shh', 'tutup', 'keluar'
            ],
            'back': [
                'kembali', 'balik', 'pulang', 'ke belakang',
                'ke awal', 'ke beranda', 'ke halaman utama', 'back'
            ],
            'next': [
                'lanjut', 'lanjutin', 'selanjutnya', 'berikutnya',
                'terus', 'sambung', 'next'
            ],
            'contact': [
                'hubungi', 'telepon', 'wa', 'whatsapp', 'email', 'alamat', 'kantor', 'lokasi',
                'tanya', 'bertanya', 'minta tolong', 'syarat', 'persyaratan', 'bagaimana', 'cara', 'prosedur'
            ],
            'login': [
                'masuk', 'login', 'log in', 'sign in', 'daftar', 'akun', 'admin', 'operator', 'petugas'
            ]
        },

        nouns: {
            'news': [
                'berita', 'kabar', 'informasi', 'info',
                'artikel', 'pengumuman', 'warta', 'bacaan', 'informasi terbaru'
            ],
            'audio': [
                'suara', 'audio', 'panduan suara', 'voice', 'panduan'
            ],
            'menu': [
                'menu', 'halaman', 'tampilan'
            ],
            'service': [
                'layanan', 'servis', 'jasa', 'pelayanan', 'bantuan', 'service',
                'administrasi', 'urus', 'pembuatan', 'surat', 'dokumen', 'berkas',
                'ktp', 'kk', 'akta', 'pengaduan', 'lapor', 'aduan', 'aspirasi'
            ],
            'region': [
                'wilayah', 'daerah', 'desa', 'lokasi', 'tempat', 'peta', 'geografi',
                'wisata', 'piknik', 'jalan-jalan', 'tamasya', 'rekreasi'
            ],
            'umkm': [
                'umkm', 'jualan', 'dagangan', 'bisnis', 'usaha', 'pedagang', 'warung', 'toko'
            ],
            'loker': [
                'loker', 'lowongan', 'kerja', 'kerjaan', 'karir', 'rekrutmen'
            ],
            'profile': [
                'profil', 'tentang', 'about', 'kami', 'kita', 'siapa', 'visimisi', 'sejarah',
                'struktur', 'organisasi', 'pegawai', 'perangkat', 'pejabat', 'camat'
            ],
            'hours': [
                'jam', 'pukul', 'waktu', 'jadwal', 'kapan',
                'buka', 'operasi', 'operasional', 'kerja'
            ]
        },

        references: {
            'position': [
                'pertama', 'kedua', 'ketiga', 'keempat',
                'terakhir', 'paling atas', 'paling bawah',
                'sebelumnya', 'sesudahnya'
            ],
            'contextual': [
                'ini', 'itu', 'yang itu', 'yang tadi',
                'tadi', 'sebelumnya', 'barusan', 'tersebut'
            ]
        },

        fillers: [
            'tolong', 'dong', 'ya', 'deh', 'sih', 'lah', 'eh',
            'anu', 'nih', 'aja', 'coba', 'boleh', 'mohon',
            'saya', 'aku', 'kami', 'ingin', 'pengen', 'mau',
            'bisa', 'harap', 'hendak', 'akan', 'sedang', 'lagi',
            'kok', 'kan', 'yuk', 'mari', 'halo', 'hai', 'selamat',
            'nggak', 'tidak', 'bukan', 'emang', 'apa', 'gimana',
            'tuh', 'nah', 'wah', 'oh', 'hmm', 'ehm', 'yang', 'di', 'ke', 'dari'
        ],

        corrections: {
            'bacain': 'bacakan',
            'denger': 'dengar',
            'dengerin': 'dengar',
            'setop': 'stop',
            'balik': 'kembali',
            'lanjutin': 'lanjut',
            'beritanya': 'berita',
            'camad': 'camat',
            'hom': 'home',
            'omkm': 'umkm',
            'bup': 'buka',
            'wes': 'wisata'
        }
    };

    // Flatten logic for Normalizer consumption
    const SYNONYMS = {
        ...RAW_LEXICON.verbs,
        ...RAW_LEXICON.nouns,
        ...RAW_LEXICON.references
    };

    const STOPWORDS = new Set(RAW_LEXICON.fillers);
    const TYPO_MAP = RAW_LEXICON.corrections;

    return {
        synonyms: SYNONYMS,
        stopwords: STOPWORDS,
        typoMap: TYPO_MAP,
        // Expose raw structure if needed by other modules
        _raw: RAW_LEXICON
    };
})();
