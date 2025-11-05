<?php
// Sidebar include — use in every dashboard page
// Expects optional $fullName (string). Falls back to session user id/name.

$script = basename($_SERVER['SCRIPT_NAME']);

function navActiveClass($file) {
    global $script;
    return $script === $file ? 'nav-link active' : 'nav-link';
}

$fullName = $fullName ?? ($_SESSION['user_name'] ?? null);
$displayName = $fullName ?: 'Candidate';
$uid = $_SESSION['user_id'] ?? '';

$initials = 'U';
if ($fullName) {
    $parts = preg_split('/\s+/', trim($fullName));
    $initials = strtoupper( ($parts[0][0] ?? 'U') . ($parts[1][0] ?? '') );
}
?>
<aside class="dashboard-sidebar" id="sidebar">
  <div class="brand">
    <h2>DAKO Portal</h2>
    <small>UTME Dashboard</small>
  </div>

  <div class="sidebar-profile">
    <div class="avatar"><?php echo htmlspecialchars($initials); ?></div>
    <div class="profile-info">
      <div class="profile-name"><?php echo htmlspecialchars($displayName); ?></div>
      <div class="profile-meta">UTME No: <?php echo htmlspecialchars($uid); ?></div>
    </div>
  </div>

  <nav class="nav">
    <ul>
      <li><a class="<?php echo navActiveClass('index.php'); ?>" href="index.php"><?php /* svg icon */ ?> <span class="nav-title">Overview</span></a></li>
      <li><a class="<?php echo navActiveClass('postutme.php'); ?>" href="postutme.php"> <span class="nav-title">Post UTME Results</span></a></li>
      <li><a class="<?php echo navActiveClass('screening.php'); ?>" href="screening.php"> <span class="nav-title">Online Screening</span></a></li>
      <li><a class="<?php echo navActiveClass('notifications.php'); ?>" href="notifications.php"> <span class="nav-title">Notification / Chat</span></a></li>
      <li><a class="<?php echo navActiveClass('documents.php'); ?>" href="documents.php"> <span class="nav-title">Documents</span></a></li>
      <li><a class="<?php echo navActiveClass('admission-status.php'); ?>" href="admission-status.php"> <span class="nav-title">Admission Status</span></a></li>
      <li><a class="<?php echo navActiveClass('receipts.php'); ?>" href="receipts.php"> <span class="nav-title">Receipts / Invoices</span></a></li>
    </ul>
  </nav>

  <div class="sidebar-footer">
    <a href="../logout.php" class="btn-logout">Logout</a>
  </div>
</aside>
