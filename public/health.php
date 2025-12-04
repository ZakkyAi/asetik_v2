<?php
// Simple health check for Railway
header('Content-Type: application/json');

try {
    // Just check if PHP is running
    echo json_encode([
        'status' => 'ok',
        'message' => 'Application is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
