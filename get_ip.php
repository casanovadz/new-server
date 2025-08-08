<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

// سجل البيانات في قاعدة البيانات
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "bls_liveness";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

foreach($data as $entry) {
    $spoof_ip = $entry['spoof_ip'];
    $user_id = $entry['user_id'];
    $transaction_id = $entry['transaction_id'];
    $liveness_id = $entry['liveness_id'];

    $sql = "INSERT INTO liveness_data (user_id, transaction_id, spoof_ip, liveness_id) 
            VALUES ('$user_id', '$transaction_id', '$spoof_ip', '$liveness_id') 
            ON DUPLICATE KEY UPDATE 
            transaction_id='$transaction_id', spoof_ip='$spoof_ip', liveness_id='$liveness_id'";

    if (!$conn->query($sql)) {
        echo json_encode(["error" => "Error: " . $sql . "<br>" . $conn->error]);
        $conn->close();
        exit();
    }
}

echo json_encode(["message" => "Data saved successfully"]);
$conn->close();
?>