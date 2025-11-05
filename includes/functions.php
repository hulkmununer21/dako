<?php
// functions.php

// Generate random 10-character ID
function generateRandomID($length = 10) {
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// Hash password using bcrypt
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit;
}

// Example: send notification to candidate
function sendNotification($db, $sender_id, $receiver_type, $receiver_id, $subject, $message, $delivery_mode='dashboard') {
    $sql = "INSERT INTO notifications (sender_id, receiver_type, receiver_id, subject, message, delivery_mode) 
            VALUES (:sender_id, :receiver_type, :receiver_id, :subject, :message, :delivery_mode)";
    $params = [
        ':sender_id' => $sender_id,
        ':receiver_type' => $receiver_type,
        ':receiver_id' => $receiver_id,
        ':subject' => $subject,
        ':message' => $message,
        ':delivery_mode' => $delivery_mode
    ];
    return $db->execute($sql, $params);
}

?>
