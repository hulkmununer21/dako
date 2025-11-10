<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Not authenticated']);
    exit;
}
$utme_id = $_SESSION['user_id'];

$guardian_name = $_POST['guardian_name'] ?? '';
$guardian_occupation = $_POST['guardian_occupation'] ?? '';
$mother_name = $_POST['mother_name'] ?? '';
$mother_occupation = $_POST['mother_occupation'] ?? '';
$guardian_address = $_POST['guardian_address'] ?? '';
$phone = $_POST['parent_phone'] ?? '';

// Connect to DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message'=>'DB error']);
    exit;
}

// Check if record exists
$stmt = $conn->prepare("SELECT parent_id FROM utme_parent_info WHERE utme_id=? LIMIT 1");
$stmt->bind_param("s", $utme_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update
    $stmt->close();
    $stmt2 = $conn->prepare("UPDATE utme_parent_info SET guardian_name=?, occupation=?, mother_name=?, mother_occupation=?, guardian_address=?, phone=? WHERE utme_id=?");
    $stmt2->bind_param("sssssss", $guardian_name, $guardian_occupation, $mother_name, $mother_occupation, $guardian_address, $phone, $utme_id);
    $ok = $stmt2->execute();
    $stmt2->close();
} else {
    // Insert
    $stmt->close();
    $stmt2 = $conn->prepare("INSERT INTO utme_parent_info (utme_id, guardian_name, occupation, mother_name, mother_occupation, guardian_address, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssssss", $utme_id, $guardian_name, $guardian_occupation, $mother_name, $mother_occupation, $guardian_address, $phone);
    $ok = $stmt2->execute();
    $stmt2->close();
}

$conn->close();
echo json_encode(['status'=>$ok ? 'success' : 'error']);
?>