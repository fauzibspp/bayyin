<?php

$host = $argv[1] ?? 'localhost';
$port = $argv[2] ?? '8080';

$publicPath = realpath(__DIR__ . '/../public');

if ($publicPath === false) {
    exit("Public folder not found.\n");
}

$command = sprintf(
    'php -S %s:%s -t %s',
    escapeshellarg($host),
    escapeshellarg($port),
    escapeshellarg($publicPath)
);

echo "Starting BayyinFramework  on http://{$host}:{$port}\n";
passthru($command);