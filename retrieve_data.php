<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// إعدادات اتصال قاعدة البيانات
$servername = "mysql-container"; // أو "localhost" إذا لم تكن تستخدم Docker
$username = "bls_user";
$password = "SecurePass123!";
$dbname = "bls_liveness";

// الاتصال بقاعدة البيانات
try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// الحصول على البيانات المرسلة
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

if ($action === 'save') {
    if (empty($input['user_id']) || empty($input['selfie_url'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "user_id and selfie_url are required"]);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO liveness_data 
            (user_id, selfie_url, status) 
            VALUES (:user_id, :selfie_url, 'completed')
            ON DUPLICATE KEY UPDATE
            selfie_url = VALUES(selfie_url),
            status = VALUES(status),
            updated_at = NOW()
        ");

        $stmt->execute([
            ':user_id' => $input['user_id'],
            ':selfie_url' => $input['selfie_url']
        ]);

        echo json_encode(["status" => "success", "message" => "Data saved successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
?>