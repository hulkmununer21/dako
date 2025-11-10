<?php
// utme/login_candidate.php
session_start();
require_once './includes/config.php';
require_once './includes/database.php';
require_once './includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jambNo = trim($_POST['jambNo']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM utme_candidates WHERE utme_id = :jambNo LIMIT 1";
    $candidate = $db->select($sql, [':jambNo' => $jambNo]);

    if ($candidate) {
        $c = $candidate[0];
        // Password stored as hashed surname
        if (verifyPassword($password, $c['password'])) {
            $_SESSION['utme_id'] = $c['utme_id'];
            $_SESSION['utme_name'] = $c['first_name'].' '.$c['surname'];
            echo json_encode(['status'=>'success','full_name'=>$_SESSION['utme_name']]);
        } else {
            echo json_encode(['status'=>'error','message'=>'Invalid password. Use your surname.']);
        }
    } else {
        echo json_encode(['status'=>'error','message'=>'UTME number not found.']);
    }
}
?>
