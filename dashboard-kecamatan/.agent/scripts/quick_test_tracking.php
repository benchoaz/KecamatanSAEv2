#!/usr/bin/env php
<?php

/**
 * Quick Test Script for Tracking Berkas Feature
 * 
 * Usage: php .agent/scripts/quick_test_tracking.php
 */

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       QUICK TEST: Tracking Berkas Feature                   ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Configuration
$baseUrl = 'http://localhost:8000';
$trackingUrl = $baseUrl . '/lacak-berkas/cek';

// Test data
$testCases = [
    [
        'name' => 'Digital Completion',
        'identifier' => '628111111111',
        'expected_status' => 'Selesai',
        'expected_type' => 'digital'
    ],
    [
        'name' => 'Physical Completion',
        'identifier' => '628222222222',
        'expected_status' => 'Selesai',
        'expected_type' => 'physical'
    ],
    [
        'name' => 'Sedang Diproses',
        'identifier' => '628333333333',
        'expected_status' => 'Sedang Diproses',
        'expected_type' => null
    ],
    [
        'name' => 'Menunggu Klarifikasi',
        'identifier' => '628444444444',
        'expected_status' => 'Menunggu Klarifikasi',
        'expected_type' => null
    ],
    [
        'name' => 'Data Not Found',
        'identifier' => '621111111111',
        'expected_status' => null,
        'expected_type' => null,
        'should_fail' => true
    ]
];

// Get CSRF token
echo "🔐 Getting CSRF token...\n";
$ch = curl_init($baseUrl . '/lacak-berkas');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
curl_close($ch);

preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches);
$csrfToken = $matches[1] ?? null;

if (!$csrfToken) {
    echo "❌ Failed to get CSRF token. Make sure Laravel server is running.\n";
    echo "   Run: php artisan serve\n\n";
    exit(1);
}

echo "✅ CSRF token obtained\n\n";

// Run tests
$passed = 0;
$failed = 0;

foreach ($testCases as $index => $test) {
    $testNumber = $index + 1;
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Test #{$testNumber}: {$test['name']}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Identifier: {$test['identifier']}\n";

    // Make request
    $ch = curl_init($trackingUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-CSRF-TOKEN: ' . $csrfToken,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'identifier' => $test['identifier']
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    // Validate response
    $testPassed = true;
    $errors = [];

    if (isset($test['should_fail']) && $test['should_fail']) {
        // Should return 404 or found=false
        if ($httpCode === 404 || (isset($data['found']) && !$data['found'])) {
            echo "✅ Correctly returned 'not found'\n";
        } else {
            $testPassed = false;
            $errors[] = "Expected 'not found' but got different response";
        }
    } else {
        // Should return success
        if ($httpCode !== 200) {
            $testPassed = false;
            $errors[] = "HTTP Code: {$httpCode} (expected 200)";
        }

        if (!isset($data['found']) || !$data['found']) {
            $testPassed = false;
            $errors[] = "Data not found in database";
        }

        if (isset($data['status']) && $data['status'] !== $test['expected_status']) {
            $testPassed = false;
            $errors[] = "Status mismatch: got '{$data['status']}', expected '{$test['expected_status']}'";
        }

        if ($test['expected_type'] && isset($data['completion_type']) && $data['completion_type'] !== $test['expected_type']) {
            $testPassed = false;
            $errors[] = "Completion type mismatch: got '{$data['completion_type']}', expected '{$test['expected_type']}'";
        }

        if ($testPassed) {
            echo "✅ Status: {$data['status']}\n";
            echo "✅ UUID: {$data['uuid']}\n";
            echo "✅ Jenis Layanan: {$data['jenis_layanan']}\n";

            if (isset($data['completion_type'])) {
                echo "✅ Completion Type: {$data['completion_type']}\n";
            }

            if (isset($data['download_url'])) {
                echo "✅ Download URL: {$data['download_url']}\n";
            }

            if (isset($data['pickup_info'])) {
                echo "✅ Pickup Info: Available\n";
            }
        }
    }

    if ($testPassed) {
        echo "\n🎉 TEST PASSED\n";
        $passed++;
    } else {
        echo "\n❌ TEST FAILED\n";
        foreach ($errors as $error) {
            echo "   - {$error}\n";
        }
        echo "\nResponse:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
        $failed++;
    }

    echo "\n";
}

// Summary
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                            ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "Total Tests: " . count($testCases) . "\n";
echo "✅ Passed: {$passed}\n";
echo "❌ Failed: {$failed}\n";
echo "\n";

if ($failed === 0) {
    echo "🎊 ALL TESTS PASSED! 🎊\n";
    exit(0);
} else {
    echo "⚠️  SOME TESTS FAILED. Please check the errors above.\n";
    exit(1);
}
