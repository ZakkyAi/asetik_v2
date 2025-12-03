<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'db.jadwfkeagcceroypuqer.supabase.co';
$project = 'jadwfkeagcceroypuqer';

echo "<!DOCTYPE html><html><head><title>DNS Diagnostics</title></head><body>";
echo "<h1>DNS Diagnostics</h1>";

function check($h) {
    echo "<h3>Checking: $h</h3>";
    $a = dns_get_record($h, DNS_A);
    $aaaa = dns_get_record($h, DNS_AAAA);
    $cname = dns_get_record($h, DNS_CNAME);
    
    echo "<b>A (IPv4):</b> " . (empty($a) ? "NONE" : json_encode($a)) . "<br>";
    echo "<b>AAAA (IPv6):</b> " . (empty($aaaa) ? "NONE" : json_encode($aaaa)) . "<br>";
    echo "<b>CNAME:</b> " . (empty($cname) ? "NONE" : json_encode($cname)) . "<br>";
    
    $ip = gethostbyname($h);
    echo "<b>gethostbyname:</b> $ip<br>";
}

check($host);
check("$project.supabase.co");
check("aws-0-ap-southeast-1.pooler.supabase.com");

// Try to find the pooler host
$regions = ['ap-southeast-1', 'us-east-1', 'eu-central-1', 'us-west-1'];
foreach ($regions as $r) {
    $ph = "aws-0-$r.pooler.supabase.com";
    $ip = gethostbyname($ph);
    if ($ip !== $ph) {
        echo "Found pooler for region $r: $ph -> $ip<br>";
    }
}

echo "</body></html>";
?>
