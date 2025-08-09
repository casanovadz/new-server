<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require 'db_connect.php'; // نستخدم نفس ملف الاتصال

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !is_array($input)) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid input data']));
}

try {
    $pdo->beginTransaction();
    
    foreach ($input as $entry) {
        $stmt = $pdo->prepare("
            INSERT INTO liveness_data 
            (user_id, transaction_id, spoof_ip, liveness_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            transaction_id = VALUES(transaction_id),
            spoof_ip = VALUES(spoof_ip),
            liveness_id = VALUES(liveness_id),
            updated_at = NOW()
        ");
        
        $stmt->execute([
            $entry['user_id'] ?? null,
            $entry['transaction_id'] ?? null,
            $entry['spoof_ip'] ?? null,
            $entry['liveness_id'] ?? null
        ]);
    }
    
    $pdo->commit();
    echo json_encode(['message' => 'Data saved successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'details' => $e->getMessage()
    ]);
}
?>