<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$prefill = '';
$error = '';

if (isset($_GET['jambNo'])) {
    $prefill = strtoupper(trim($_GET['jambNo']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prefill = strtoupper(trim($_POST['jambNo'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($prefill === '' || $password === '') {
        $error = 'Please provide your JAMB number and password.';
    } else {
        // Simple query using your Database helper
        $sql = "SELECT utme_id, password FROM utme_candidates WHERE utme_id = :jambNo LIMIT 1";
        $rows = $db->select($sql, [':jambNo' => $prefill]);
        $user = $rows[0] ?? null;

        if ($user) {
            $storedHash = $user['password'];
            $ok = false;

            // bcrypt check
            if (password_verify($password, $storedHash)) {
                $ok = true;
            } elseif (md5($password) === $storedHash) {
                // legacy md5 -> migrate to bcrypt
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $db->execute(
                    "UPDATE utme_candidates SET password = :hash WHERE utme_id = :id",
                    [':hash' => $newHash, ':id' => $user['utme_id']]
                );
                $ok = true;
            }

            if ($ok) {
                session_regenerate_id(true);
                // store only utme_id in session
                $_SESSION['user_id'] = $user['utme_id'];
                header('Location: dashboard/index.php');
                exit();
            }
        }

        $error = 'Invalid JAMB number or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>UTME Login — DAKO Admissions</title>
  <style>
    body{font-family:Segoe UI,Arial,Helvetica,sans-serif;background:linear-gradient(90deg,#0f4c75,#3282b8);display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
    .card{background:#fff;padding:28px;border-radius:10px;width:380px;box-shadow:0 6px 30px rgba(0,0,0,.15)}
    h2{margin:0 0 12px;color:#0f4c75;text-align:center}
    input{width:100%;padding:10px 12px;margin-top:10px;border-radius:6px;border:1px solid #ccc;font-size:15px}
    button{width:100%;padding:11px;margin-top:16px;background:#0f4c75;color:#fff;border:none;border-radius:6px;cursor:pointer}
    .error{color:#b00020;margin-top:10px;text-align:center}
    .small{font-size:13px;color:#555;margin-top:8px;display:block;text-align:center}
    a{color:#0f4c75;text-decoration:none}
  </style>
</head>
<body>
  <div class="card">
    <h2>UTME Candidate Login</h2>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <input id="jambNo" name="jambNo" type="text" value="<?php echo htmlspecialchars($prefill); ?>" placeholder="Enter JAMB Number" required autofocus />
      <input id="password" name="password" type="password" placeholder="Enter Password" required />
      <button type="submit">Login</button>
    </form>

    <div class="small">
      <a href="index.php">← Back to Eligibility Checker</a>
    </div>
  </div>
</body>
</html>
