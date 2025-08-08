<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "bls_liveness";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$user_id = $_GET['user_id'];
$sql = "SELECT * FROM liveness_data WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode(["error" => "No data found"]);
}
$conn->close();
?>