<?php
/**
 * Railway-optimized database connection
 * Works with Railway environment variables WITHOUT dotenv
 */

// Get environment variables from Railway (or system)
$host = $_SERVER['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$db   = $_SERVER['DB_NAME'] ?? getenv('DB_NAME') ?? 'postgres';
$user = $_SERVER['DB_USER'] ?? getenv('DB_USER') ?? 'postgres';  
$pass = $_SERVER['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';
$port = $_SERVER['DB_PORT'] ?? getenv('DB_PORT') ?? '5432';

// For local development with .env file
if (empty($pass) && file_exists(__DIR__ . '/.env')) {
    $envFile = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if ($key === 'DB_HOST') $host = $value;
        if ($key === 'DB_NAME') $db = $value;
        if ($key === 'DB_USER') $user = $value;
        if ($key === 'DB_PASSWORD') $pass = $value;
        if ($key === 'DB_PORT') $port = $value;
    }
}

// Robust IPv4 resolution function
function getIPv4($hostname) {
    $hostname = trim($hostname);
    
    // Method 1: gethostbynamel (returns list of IPv4)
    $ips = gethostbynamel($hostname);
    if ($ips && is_array($ips) && !empty($ips)) {
        return $ips[0];
    }
    
    // Method 2: dns_get_record (DNS_A for IPv4)
    if (function_exists('dns_get_record')) {
        $records = dns_get_record($hostname, DNS_A);
        if ($records && !empty($records)) {
            foreach ($records as $r) {
                if (isset($r['ip'])) return $r['ip'];
            }
        }
    }
    
    // Method 3: shell_exec with dig (if available)
    if (function_exists('shell_exec')) {
        // Try using Google DNS explicitly
        $output = shell_exec("dig @8.8.8.8 +short A " . escapeshellarg($hostname));
        if ($output !== null) {
            $ip = trim($output);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ip;
            }
        }

        // Try standard dig
        $output = shell_exec("dig +short A " . escapeshellarg($hostname));
        if ($output !== null) {
            $ip = trim($output);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ip;
            }
        }
        
        // Method 4: getent
        $output = shell_exec("getent hosts " . escapeshellarg($hostname));
        if ($output !== null) {
            $parts = preg_split('/\s+/', trim($output));
            if (isset($parts[0]) && filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $parts[0];
            }
        }
    }
    
    // Fallback: gethostbyname (might return hostname on failure)
    $ip = gethostbyname($hostname);
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $ip;
    }

    return null; // Failed to resolve to IPv4
}

$hostIPv4 = getIPv4($host);

// Auto-fallback to Supavisor Pooler if direct IPv4 fails
if (!$hostIPv4) {
    // Extract project ID from host (e.g., db.abcdefg.supabase.co -> abcdefg)
    if (preg_match('/^db\.([a-z0-9]+)\.supabase\.co$/', $host, $matches)) {
        $projectId = $matches[1];
        
        // Try common pooler regions
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
        
        foreach ($poolerRegions as $poolerHost) {
            $poolerIP = getIPv4($poolerHost);
            if ($poolerIP) {
                // We found an IP for this region, but is it the RIGHT region?
                // We must test the connection to see if the Tenant exists there.
                
                $testUser = (strpos($user, '.') === false) ? "$user.$projectId" : $user;
                $testDsn = "pgsql:host=$poolerIP;port=5432;dbname=$db;sslmode=require";
                
                try {
                    // specific test connection with short timeout
                    $testPdo = new PDO($testDsn, $testUser, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_TIMEOUT => 3 // Increased slightly
                    ]);
                    
                    // If we got here, it worked!
                    $hostIPv4 = $poolerIP;
                    $user = $testUser;
                    break;
                } catch (PDOException $e) {
                    $msg = $e->getMessage();
                    
                    // If "password authentication failed", this IS the right region, just wrong pass.
                    if (strpos($msg, 'password authentication failed') !== false) {
                        $hostIPv4 = $poolerIP;
                        $user = $testUser;
                        break;
                    }
                    
                    // For any other error (Tenant not found, timeout, network unreachable), 
                    // we assume this is NOT the right region (or we can't reach it), so we continue.
                    continue;
                }
            }
        }
    }
}

// If still no IPv4, fallback to original host (might work via IPv6)
if (!$hostIPv4) {
    $hostIPv4 = $host;
}

$dsn = "pgsql:host=$hostIPv4;port=$port;dbname=$db;sslmode=require";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "<br>Host: $host<br>DB: $db<br>User: $user");
}
?>
