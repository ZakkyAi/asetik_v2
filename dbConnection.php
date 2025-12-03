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
        
        // Try common pooler regions (starting with Singapore as it matches the IPv6 range seen)
        $poolerRegions = [
            'aws-0-ap-southeast-1.pooler.supabase.com', // Singapore
            'aws-0-us-east-1.pooler.supabase.com',      // N. Virginia
            'aws-0-eu-central-1.pooler.supabase.com',   // Frankfurt
            'aws-0-us-west-1.pooler.supabase.com'       // N. California
        ];
        
        foreach ($poolerRegions as $poolerHost) {
            $poolerIP = getIPv4($poolerHost);
            if ($poolerIP) {
                $hostIPv4 = $poolerIP; // Use the pooler's IPv4
                
                // Fix username for pooler (must be user.project)
                if (strpos($user, '.') === false) {
                    $user = "$user.$projectId";
                }
                
                // Use port 5432 (Session mode) or 6543 (Transaction mode)
                // Keep 5432 for compatibility
                break; 
            }
        }
    }
}

// If still no IPv4, we are stuck
if (!$hostIPv4) {
    die("Fatal Error: Could not resolve database host '$host' to an IPv4 address, and fallback to pooler failed. Please check your database configuration.");
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
