<?php
$host = 'db';
$port = '3306';
$db = 'dashboard_kecamatan';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_TIMEOUT => 5,
];

echo "Testing connection to $host:$port...\n";
$start = microtime(true);
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $end = microtime(true);
    echo "Connected successfully in " . ($end - $start) . " seconds.\n";

    $stmt = $pdo->query("SELECT VERSION()");
    $row = $stmt->fetch();
    echo "MySQL Version: " . $row['VERSION()'] . "\n";
} catch (\PDOException $e) {
    $end = microtime(true);
    echo "Connection failed after " . ($end - $start) . " seconds: " . $e->getMessage() . "\n";
}
