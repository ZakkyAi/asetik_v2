<?php
// Debug script to trace connection logic
header('Content-Type: text/plain');

echo "=== Debug Connection ===\n";

// Load .env manually
$env = [];
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($k, $v) = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
}

$host = $env['DB_HOST'] ?? 'db.jadwfkeagcceroypuqer.supabase.co';
$db = $env['DB_NAME'] ?? 'postgres';
$user = $env['DB_USER'] ?? 'postgres';
$pass = $env['DB_PASSWORD'] ?? '';
$port = $env['DB_PORT'] ?? '5432';

echo "Config:\n";
echo "Host: $host\n";
echo "User: $user\n";
echo "Port: $port\n";
echo "DB: $db\n";
echo "Pass: " . (empty($pass) ? "EMPTY" : "SET") . "\n\n";

// 1. Check IPv4 resolution
echo "1. Checking IPv4 resolution for $host...\n";
$ips = gethostbynamel($host);
if ($ips) {
    echo "  Resolved to: " . implode(', ', $ips) . "\n";
} else {
    echo "  Failed to resolve using gethostbynamel\n";
}

// 2. Fallback logic
echo "\n2. Testing Fallback Logic...\n";
$projectId = '';
if (preg_match('/^db\.([a-z0-9]+)\.supabase\.co$/', $host, $matches)) {
    $projectId = $matches[1];
    echo "  Project ID extracted: $projectId\n";
} else {
    echo "  Could not extract project ID\n";
}

$poolerRegions = [
    'aws-0-ap-southeast-1.pooler.supabase.com', // Singapore
    'aws-0-us-east-1.pooler.supabase.com',      // N. Virginia
    'aws-0-eu-central-1.pooler.supabase.com',   // Frankfurt
    'aws-0-us-west-1.pooler.supabase.com',      // N. California
    'aws-0-sa-east-1.pooler.supabase.com',      // São Paulo
    'aws-0-ap-northeast-1.pooler.supabase.com', // Tokyo
    'aws-0-ap-northeast-2.pooler.supabase.com', // Seoul
    'aws-0-ca-central-1.pooler.supabase.com',   // Canada
    'aws-0-ap-south-1.pooler.supabase.com',     // Mumbai
    'aws-0-eu-west-1.pooler.supabase.com',      // Ireland
    'aws-0-eu-west-2.pooler.supabase.com',      // London
    'aws-0-eu-west-3.pooler.supabase.com',      // Paris
];

$testUser = (strpos($user, '.') === false) ? "$user.$projectId" : $user;
echo "  Testing with user: $testUser\n";

foreach ($poolerRegions as $region) {
    echo "  Checking region: $region... ";
    $ip = gethostbyname($region);
    if ($ip == $region) {
        echo "Could not resolve IP.\n";
        continue;
    }
    echo "IP: $ip... ";
    
    $dsn = "pgsql:host=$ip;port=5432;dbname=$db;sslmode=require";
    try {
        $pdo = new PDO($dsn, $testUser, $pass, [PDO::ATTR_TIMEOUT => 2, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "SUCCESS! Connected.\n";
        break;
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (strpos($msg, 'Tenant or user not found') !== false) {
            echo "Tenant not found.\n";
        } else {
            echo "Error: $msg\n";
        }
    }
}

echo "\nDone.\n";
