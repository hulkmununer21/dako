<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Not authenticated']);
    exit;
}
$utme_id = $_SESSION['user_id'];

$doc_types = $_POST['doc_type'] ?? [];
$files = $_FILES['documents'] ?? null;

$upload_dir = '../../../uploads/utme_documents/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message'=>'DB error']);
    exit;
}

$ok = true;
$msg = '';
for ($i=0; $i<count($doc_types); $i++) {
    $type = $doc_types[$i];
    if (!empty($type) && isset($files['name'][$i]) && $files['error'][$i] == UPLOAD_ERR_OK) {
        $filename = basename($files['name'][$i]);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','pdf'])) {
            $msg .= "Invalid file type for $type. ";
            $ok = false;
            continue;
        }
        if ($files['size'][$i] > 2*1024*1024) {
            $msg .= "File too large for $type. ";
            $ok = false;
            continue;
        }
        $newname = $utme_id . '_' . $type . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target = $upload_dir . $newname;
        if (move_uploaded_file($files['tmp_name'][$i], $target)) {
            // Save to DB
            $stmt = $conn->prepare("INSERT INTO utme_documents (utme_id, doc_type, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $utme_id, $type, $newname);
            $stmt->execute();
            $stmt->close();
        } else {
            $msg .= "Upload failed for $type. ";
            $ok = false;
        }
    }
}
$conn->close();
echo json_encode(['status'=>$ok ? 'success' : 'error', 'message'=>$msg]);
?>