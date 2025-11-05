<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

$jambNo = 'UTM1234567';

$sql = "SELECT * FROM utme_candidates WHERE utme_id = :jambNo LIMIT 1";
$candidate = $db->select($sql, [':jambNo' => $jambNo]);

if ($candidate) {
    print_r($candidate);
} else {
    echo "UTME number not found!";
}
?>
