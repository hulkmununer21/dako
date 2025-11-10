<?php
// utme/fetch_candidate.php
require_once '../includes/config.php';
require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jambNo = trim($_POST['jambNo']);

    $sql = "SELECT utme_candidates.*, utme_courses.course_name 
            FROM utme_candidates 
            LEFT JOIN utme_courses ON utme_candidates.preferred_course_id = utme_courses.course_id 
            WHERE utme_id = :jambNo LIMIT 1";

    $candidate = $db->select($sql, [':jambNo' => $jambNo]);

    if ($candidate) {
        $c = $candidate[0];
        if ($c['eligibility_status'] === 'eligible') {
            echo json_encode([
                'status' => 'success',
                'full_name' => $c['first_name'] . ' ' . $c['surname'],
                'programme' => $c['course_name'] ?? 'N/A',
                'score' => $c['utme_score']
            ]);
        } else {
            echo json_encode(['status'=>'error','message'=>'Sorry, you are not eligible.']);
        }
    } else {
        echo json_encode(['status'=>'error','message'=>'UTME number not found.']);
    }
}
?>
