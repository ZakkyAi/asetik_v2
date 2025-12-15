<?php
require_once(__DIR__ . "/src/helpers.php");

echo "Testing route() function:\n";
echo "route('/users/edit/{id}', ['id' => 18]) = " . route('/users/edit/{id}', ['id' => 18]) . "\n";
echo "\nTesting url() function:\n";
echo "url('/users/edit/18') = " . url('/users/edit/18') . "\n";
