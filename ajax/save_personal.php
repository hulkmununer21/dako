<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Not authenticated']);
    exit;
}
$utme_id = $_SESSION['user_id'];

// Collect and sanitize input
$dob = $_POST['dob'] ?? '';
$phone = $_POST['phone'] ?? '';
$gender = $_POST['gender'] ?? '';
$present_address = $_POST['present_address'] ?? '';
$permanent_address = $_POST['permanent_address'] ?? '';
$state = $_POST['state'] ?? '';
$lga = $_POST['lga'] ?? '';
$blood_group = $_POST['blood_group'] ?? '';

// Connect to DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message'=>'DB error']);
    exit;
}

// Check if record exists
$stmt = $conn->prepare("SELECT info_id FROM utme_personal_info WHERE utme_id=? LIMIT 1");
$stmt->bind_param("s", $utme_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update
    $stmt->close();
    $stmt2 = $conn->prepare("UPDATE utme_personal_info SET dob=?, phone=?, gender=?, present_address=?, permanent_address=?, state=?, lga=?, blood_group=? WHERE utme_id=?");
    $stmt2->bind_param("sssssssss", $dob, $phone, $gender, $present_address, $permanent_address, $state, $lga, $blood_group, $utme_id);
    $ok = $stmt2->execute();
    $stmt2->close();
} else {
    // Insert
    $stmt->close();
    $stmt2 = $conn->prepare("INSERT INTO utme_personal_info (utme_id, dob, phone, gender, present_address, permanent_address, state, lga, blood_group) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssssssss", $utme_id, $dob, $phone, $gender, $present_address, $permanent_address, $state, $lga, $blood_group);
    $ok = $stmt2->execute();
    $stmt2->close();
}

$conn->close();
echo json_encode(['status'=>$ok ? 'success' : 'error']);
?>