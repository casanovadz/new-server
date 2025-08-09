<?php
// إعدادات الاتصال بقاعدة البيانات
$servername = "127.0.0.1";
$username = "root";
$password = "123456"; // كلمة مرور MySQL التي وضعتها عند تشغيل الحاوية
$dbname = "bls_liveness";

// الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// استقبال القيم من الرابط
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$selfie_url = isset($_GET['selfie_url']) ? $_GET['selfie_url'] : '';

if ($user_id && $selfie_url) {
    // إدخال البيانات في الجدول
    $stmt = $conn->prepare("INSERT INTO liveness_data (user_id, selfie_url, status) VALUES (?, ?, 'completed')");
    $stmt->bind_param("ss", $user_id, $selfie_url);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Data saved successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database insert failed"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
}

$conn->close();
?>
