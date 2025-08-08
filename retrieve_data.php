<?php
// تعيين نوع المحتوى للرد JSON
header('Content-Type: application/json');

// الحصول على قيمة user_id من الرابط
$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    // إذا وجد user_id، أرسل استجابة نجاح مع القيمة
    echo json_encode(['status' => 'success', 'user_id' => $user_id]);
} else {
    // إذا لم يُرسل user_id، أرسل رسالة خطأ
    echo json_encode(['status' => 'error', 'message' => 'user_id missing']);
}
?>
