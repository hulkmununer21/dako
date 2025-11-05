<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$utme = $_SESSION['user_id'];

// Fetch notifications sent to this UTME candidate or broadcast to all
$sql = "
    SELECT n.notif_id, n.sender_id, n.receiver_type, n.receiver_id, n.subject, n.message, n.delivery_mode, n.created_at, a.full_name AS sender_name
    FROM notifications n
    LEFT JOIN admins a ON n.sender_id = a.admin_id
    WHERE
      n.receiver_type = 'all'
      OR (n.receiver_type = 'utme' AND n.receiver_id = :uid)
    ORDER BY n.created_at DESC
";
$notes = $db->select($sql, [':uid' => $utme]);

function timeAgo($ts) {
    $t = strtotime($ts);
    $diff = time() - $t;
    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff/60) . 'm ago';
    if ($diff < 86400) return floor($diff/3600) . 'h ago';
    return floor($diff/86400) . 'd ago';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Notifications — Dashboard</title>
  <link rel="stylesheet" href="../asset/css/dashboard.css" />
  <style>
    .note-list { display:flex; flex-direction:column; gap:12px; }
    .note { background:#fff; padding:14px; border-radius:10px; box-shadow:0 6px 18px rgba(15,76,117,0.04); display:flex; justify-content:space-between; gap:12px; align-items:flex-start; }
    .note .meta { color:var(--muted); font-size:13px; }
    .note .subject { font-weight:700; margin-bottom:6px; }
    .note .msg { color:#374151; }
    .note .delivery { font-size:12px; padding:6px 8px; border-radius:8px; background:#f3f4f6; color:#111827; }
    .empty { color:var(--muted); padding:18px; background:#fff; border-radius:10px; box-shadow:0 6px 18px rgba(15,76,117,0.03); }
  </style>
</head>
<body>
  <?php include_once __DIR__ . '/../include/header.php'; ?>
  <div class="dashboard-root">
    <?php include_once __DIR__ . '/../include/sidebar.php'; ?>

    <main class="dashboard-main">
      <header class="main-header">
        <button id="toggleSidebar" class="toggle-btn">☰</button>
        <div class="user-info">
          <div class="name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_id']); ?></div>
          <div class="meta">UTME No: <?php echo htmlspecialchars($utme); ?></div>
        </div>
      </header>

      <section class="content">
        <h1>Notifications & Messages</h1>

        <?php if (empty($notes)): ?>
          <div class="empty">You have no notifications at the moment.</div>
        <?php else: ?>
          <div class="note-list" role="list">
            <?php foreach ($notes as $n): ?>
              <article class="note" role="listitem" aria-labelledby="subject-<?php echo $n['notif_id']; ?>">
                <div style="flex:1">
                  <div id="subject-<?php echo $n['notif_id']; ?>" class="subject">
                    <?php echo htmlspecialchars($n['subject'] ?: 'No subject'); ?>
                  </div>
                  <div class="meta">From: <?php echo htmlspecialchars($n['sender_name'] ?: 'System'); ?> • <?php echo htmlspecialchars($n['receiver_type'] === 'all' ? 'Broadcast' : 'Direct'); ?> • <?php echo htmlspecialchars(timeAgo($n['created_at'])); ?></div>
                  <div class="msg" style="margin-top:8px;"><?php echo nl2br(htmlspecialchars($n['message'])); ?></div>
                </div>

                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px">
                  <div class="delivery"><?php echo htmlspecialchars(ucfirst($n['delivery_mode'] ?? 'dashboard')); ?></div>
                  <div style="font-size:12px;color:var(--muted)"><?php echo htmlspecialchars($n['created_at']); ?></div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </section>

      <?php include_once __DIR__ . '/../include/footer.php'; ?>
    </main>
  </div>

  <script src="../asset/js/dashboard.js"></script>
</body>
</html>