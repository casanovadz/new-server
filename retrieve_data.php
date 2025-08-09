<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// إعدادات محسنة للاتصال
define('DB_HOST', 'mysql-container'); // اسم حاوية MySQL إذا كنت تستخدم Docker
define('DB_USER', 'bls_user');
define('DB_PASS', 'SecurePass123!');
define('DB_NAME', 'bls_liveness');

// اتصال PDO محسن
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode([
        'status' => 'error',
        'message' => 'Database connection failed',
        'error' => $e->getMessage()
    ]));
}

// معالجة الطلب
$user_id = $_GET['user_id'] ?? null;
$action = $_GET['action'] ?? 'get';

try {
    switch ($action) {
        case 'get':
            if (!$user_id) {
                throw new Exception('user_id parameter is required');
            }
            
            $stmt = $pdo->prepare("SELECT * FROM liveness_data WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            
            echo json_encode($result ?: ['status' => 'not_found']);
            break;
            
        case 'save':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['user_id']) || empty($data['selfie_url'])) {
                throw new Exception('Missing required fields');
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO liveness_data 
                (user_id, selfie_url, status, created_at) 
                VALUES (?, ?, 'completed', NOW())
                ON DUPLICATE KEY UPDATE
                selfie_url = VALUES(selfie_url),
                status = VALUES(status),
                updated_at = NOW()
            ");
            
            $stmt->execute([$data['user_id'], $data['selfie_url']]);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Data saved successfully'
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>