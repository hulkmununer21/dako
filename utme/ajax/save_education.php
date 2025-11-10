<?php
session_start();
require_once '../../../includes/config.php';
require_once '../../../includes/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Not authenticated']);
    exit;
}
$utme_id = $_SESSION['user_id'];

$sitting = $_POST['sitting'] ?? '';
$exam_type = $_POST['exam_type'] ?? '';
$exam_year = $_POST['exam_year'] ?? '';
$exam_no = $_POST['exam_no'] ?? '';
$exam_date = $_POST['exam_date'] ?? '';
$subjects = $_POST['subject'] ?? [];
$grades = $_POST['grade'] ?? [];

// Combine subjects and grades into JSON
$subjects_json = [];
for ($i=0; $i<count($subjects); $i++) {
    if (!empty($subjects[$i])) {
        $subjects_json[] = [
            'subject' => $subjects[$i],
            'grade' => $grades[$i] ?? ''
        ];
    }
}
$subjects_json_str = json_encode($subjects_json);

// Connect to DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message'=>'DB error']);
    exit;
}

// Check if record exists
$stmt = $conn->prepare("SELECT edu_id FROM utme_education_background WHERE utme_id=? LIMIT 1");
$stmt->bind_param("s", $utme_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update
    $stmt->close();
    $stmt2 = $conn->prepare("UPDATE utme_education_background SET sitting=?, exam_type=?, exam_year=?, exam_no=?, exam_date=?, subjects_json=? WHERE utme_id=?");
    $stmt2->bind_param("sssssss", $sitting, $exam_type, $exam_year, $exam_no, $exam_date, $subjects_json_str, $utme_id);
    $ok = $stmt2->execute();
    $stmt2->close();
} else {
    // Insert
    $stmt->close();
    $stmt2 = $conn->prepare("INSERT INTO utme_education_background (utme_id, sitting, exam_type, exam_year, exam_no, exam_date, subjects_json) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssssss", $utme_id, $sitting, $exam_type, $exam_year, $exam_no, $exam_date, $subjects_json_str);
    $ok = $stmt2->execute();
    $stmt2->close();
}

$conn->close();
echo json_encode(['status'=>$ok ? 'success' : 'error']);
?>