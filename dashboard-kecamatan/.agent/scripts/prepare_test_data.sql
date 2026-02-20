-- ============================================
-- Script: Prepare Test Data for Tracking Berkas
-- Purpose: Create sample data for testing tracking feature
-- ============================================

-- 1. Create test berkas with DIGITAL COMPLETION
INSERT INTO public_services (
    uuid,
    nama_pemohon,
    nik,
    desa_id,
    jenis_layanan,
    uraian,
    whatsapp,
    status,
    completion_type,
    result_file_path,
    public_response,
    is_agreed,
    ip_address,
    created_at,
    updated_at
) VALUES (
    '550e8400-e29b-41d4-a716-446655440001',
    'Test User Digital',
    '3501010101010001',
    1,
    'Surat Keterangan Usaha',
    'Test pengajuan untuk digital completion',
    '628111111111',
    'Selesai',
    'digital',
    'public_services/sample_result.pdf',
    'Berkas Anda telah selesai diproses. Silakan download hasil dokumen.',
    true,
    '127.0.0.1',
    NOW(),
    NOW()
);

-- 2. Create test berkas with PHYSICAL COMPLETION
INSERT INTO public_services (
    uuid,
    nama_pemohon,
    nik,
    desa_id,
    jenis_layanan,
    uraian,
    whatsapp,
    status,
    completion_type,
    ready_at,
    pickup_person,
    pickup_notes,
    public_response,
    is_agreed,
    ip_address,
    created_at,
    updated_at
) VALUES (
    '550e8400-e29b-41d4-a716-446655440002',
    'Test User Physical',
    '3501010101010002',
    1,
    'Surat Keterangan Domisili',
    'Test pengajuan untuk physical completion',
    '628222222222',
    'Selesai',
    'physical',
    NOW() + INTERVAL '1 day',
    'Bapak Ahmad (Loket 1)',
    'Mohon bawa KTP asli dan fotokopi saat pengambilan. Jam pelayanan: 08.00-14.00 WIB',
    'Berkas Anda sudah siap diambil di kantor kecamatan.',
    true,
    '127.0.0.1',
    NOW(),
    NOW()
);

-- 3. Create test berkas with STATUS: Sedang Diproses
INSERT INTO public_services (
    uuid,
    nama_pemohon,
    nik,
    desa_id,
    jenis_layanan,
    uraian,
    whatsapp,
    status,
    public_response,
    is_agreed,
    ip_address,
    created_at,
    updated_at
) VALUES (
    '550e8400-e29b-41d4-a716-446655440003',
    'Test User Proses',
    '3501010101010003',
    1,
    'Surat Pengantar',
    'Test pengajuan untuk status sedang diproses',
    '628333333333',
    'Sedang Diproses',
    'Berkas Anda sedang dalam proses verifikasi. Estimasi selesai 2-3 hari kerja.',
    true,
    '127.0.0.1',
    NOW(),
    NOW()
);

-- 4. Create test berkas with STATUS: Menunggu Klarifikasi
INSERT INTO public_services (
    uuid,
    nama_pemohon,
    nik,
    desa_id,
    jenis_layanan,
    uraian,
    whatsapp,
    status,
    public_response,
    is_agreed,
    ip_address,
    created_at,
    updated_at
) VALUES (
    '550e8400-e29b-41d4-a716-446655440004',
    'Test User Pending',
    '3501010101010004',
    1,
    'Surat Keterangan Tidak Mampu',
    'Test pengajuan untuk status menunggu klarifikasi',
    '628444444444',
    'Menunggu Klarifikasi',
    'Mohon melengkapi dokumen pendukung. Hubungi loket pelayanan untuk informasi lebih lanjut.',
    true,
    '127.0.0.1',
    NOW(),
    NOW()
);

-- 5. Create test berkas with STATUS: Dikoordinasikan ke Desa
INSERT INTO public_services (
    uuid,
    nama_pemohon,
    nik,
    desa_id,
    jenis_layanan,
    uraian,
    whatsapp,
    status,
    public_response,
    is_agreed,
    ip_address,
    created_at,
    updated_at
) VALUES (
    '550e8400-e29b-41d4-a716-446655440005',
    'Test User Koordinasi',
    '3501010101010005',
    1,
    'Surat Keterangan Belum Menikah',
    'Test pengajuan untuk status dikoordinasikan',
    '628555555555',
    'Dikoordinasikan ke Desa',
    'Berkas Anda sedang dikoordinasikan dengan perangkat desa untuk verifikasi data.',
    true,
    '127.0.0.1',
    NOW(),
    NOW()
);

-- ============================================
-- QUERY HELPER untuk Testing
-- ============================================

-- Check all test data
SELECT 
    id,
    uuid,
    nama_pemohon,
    whatsapp,
    jenis_layanan,
    status,
    completion_type,
    created_at
FROM public_services
WHERE whatsapp LIKE '6281%'
ORDER BY created_at DESC;

-- Get specific test data for manual testing
SELECT 
    'Digital Completion' as test_type,
    uuid,
    whatsapp,
    status
FROM public_services
WHERE whatsapp = '628111111111'

UNION ALL

SELECT 
    'Physical Completion' as test_type,
    uuid,
    whatsapp,
    status
FROM public_services
WHERE whatsapp = '628222222222'

UNION ALL

SELECT 
    'Sedang Diproses' as test_type,
    uuid,
    whatsapp,
    status
FROM public_services
WHERE whatsapp = '628333333333'

UNION ALL

SELECT 
    'Menunggu Klarifikasi' as test_type,
    uuid,
    whatsapp,
    status
FROM public_services
WHERE whatsapp = '628444444444'

UNION ALL

SELECT 
    'Dikoordinasikan' as test_type,
    uuid,
    whatsapp,
    status
FROM public_services
WHERE whatsapp = '628555555555';

-- ============================================
-- CLEANUP (Run this after testing)
-- ============================================

-- Delete all test data
DELETE FROM public_services 
WHERE whatsapp IN (
    '628111111111',
    '628222222222',
    '628333333333',
    '628444444444',
    '628555555555'
);

-- Verify cleanup
SELECT COUNT(*) as remaining_test_data
FROM public_services
WHERE whatsapp LIKE '6281%';
