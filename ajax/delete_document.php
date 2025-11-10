<?php
<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Not authenticated']);
    exit;
}
$utme_id = $_SESSION['user_id'];
$file_path = $_POST['file_path'] ?? '';

if (!$file_path) {
    echo json_encode(['status'=>'error','message'=>'No file specified']);
    exit;
}

// Find and delete from DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message'=>'DB error']);
    exit;
}

// Check if file belongs to user
$stmt = $conn->prepare("SELECT doc_id FROM utme_documents WHERE utme_id=? AND file_path=? LIMIT 1");
$stmt->bind_param("ss", $utme_id, $file_path);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    $conn->close();
    echo json_encode(['status'=>'error','message'=>'File not found']);
    exit;
}
$stmt->close();

// Delete DB record
$stmt2 = $conn->prepare("DELETE FROM utme_documents WHERE utme_id=? AND file_path=?");
$stmt2->bind_param("ss", $utme_id, $file_path);
$stmt2->execute();
$stmt2->close();
$conn->close();

// Delete file from filesystem
$full_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/utme_documents/' . $file_path;
if (file_exists($full_path)) {
    unlink($full_path);
}

echo json_encode(['status'=>'success']);
?>