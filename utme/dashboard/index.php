<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Require session
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch candidate info
$sql = "SELECT c.utme_id, c.surname, c.first_name, c.middle_name, c.utme_number, c.utme_score, c.email, c.eligibility_status,
               pc.course_name, p.dob, p.phone, p.state, p.lga
        FROM utme_candidates c
        LEFT JOIN utme_personal_info p ON c.utme_id = p.utme_id
        LEFT JOIN utme_courses pc ON c.preferred_course_id = pc.course_id
        WHERE c.utme_id = :id
        LIMIT 1";

$rows = $db->select($sql, [':id' => $userId]);
$candidate = $rows[0] ?? null;

if (!$candidate) {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit(); 
}

$fullName = trim(($candidate['surname'] ?? '') . ' ' . ($candidate['first_name'] ?? '') . ' ' . ($candidate['middle_name'] ?? ''));
$course = $candidate['course_name'] ?? 'Not selected';
$eligibility = $candidate['eligibility_status'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard — <?php echo htmlspecialchars($fullName); ?></title>
  <link rel="stylesheet" href="../asset/css/dashboard.css" />
</head>
<body>
  <?php include_once __DIR__ . '/../include/header.php'; ?>

  <div class="dashboard-root">
    <aside class="dashboard-sidebar" id="sidebar">
      <div class="brand">
        <h2>DAKO Portal</h2>
        <small>UTME Dashboard</small>
      </div>

      <nav class="nav">
        <ul>
          <li><a class="nav-item active" href="index.php">Overview</a></li>
          <li><a class="nav-item" href="postutme.php">Post UTME Results</a></li>
          <li><a class="nav-item" href="screening.php">Online Screening</a></li>
          <li><a class="nav-item" href="notifications.php">Notification / Chat</a></li>
          <li><a class="nav-item" href="documents.php">Documents</a></li>
          <li><a class="nav-item" href="admission-status.php">Admission Status</a></li>
          <li><a class="nav-item" href="receipts.php">Receipts / Invoices</a></li>
        </ul>
      </nav>

      <div class="sidebar-footer">
        <a href="../logout.php" class="btn-logout">Logout</a>
      </div>
    </aside>

    <main class="dashboard-main">
      <header class="main-header">
        <button id="toggleSidebar" class="toggle-btn">☰</button>
        <div class="user-info">
          <div class="name"><?php echo htmlspecialchars($fullName); ?></div>
          <div class="meta">UTME No: <?php echo htmlspecialchars($candidate['utme_number'] ?? ''); ?></div>
        </div>
      </header>

      <section class="content" id="content">
        <div class="section" id="overview">
          <h1>Welcome, <?php echo htmlspecialchars(explode(' ', $fullName)[0] ?: 'Candidate'); ?></h1>

          <div class="cards">
            <div class="card">
              <h3>Program</h3>
              <p><?php echo htmlspecialchars($course); ?></p>
            </div>

            <div class="card">
              <h3>UTME Score</h3>
              <p><?php echo htmlspecialchars($candidate['utme_score'] ?? 'N/A'); ?></p>
            </div>

            <div class="card">
              <h3>Eligibility</h3>
              <p class="status <?php echo htmlspecialchars($eligibility); ?>"><?php echo ucfirst($eligibility); ?></p>
            </div>

            <div class="card">
              <h3>Contact</h3>
              <p><?php echo htmlspecialchars($candidate['phone'] ?? $candidate['email'] ?? 'N/A'); ?></p>
            </div>
          </div>

          <div class="info-grid">
            <div>
              <h4>Personal Details</h4>
              <table class="info-table">
                <tr><th>Full name</th><td><?php echo htmlspecialchars($fullName); ?></td></tr>
                <tr><th>UTME No</th><td><?php echo htmlspecialchars($candidate['utme_number'] ?? ''); ?></td></tr>
                <tr><th>Date of birth</th><td><?php echo htmlspecialchars($candidate['dob'] ?? ''); ?></td></tr>
                <tr><th>State / LGA</th><td><?php echo htmlspecialchars(($candidate['state'] ?? '') . ' / ' . ($candidate['lga'] ?? '')); ?></td></tr>
              </table>
            </div>

            <div>
              <h4>Quick Actions</h4>
              <div class="quick-actions">
                <a href="postutme.php" class="qa">View Post UTME Results</a>
                <a href="screening.php" class="qa">Online Screening</a>
                <a href="documents.php" class="qa">Manage Documents</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <?php include_once __DIR__ . '/../include/footer.php'; ?>
    </main>
  </div>

  <script>
    window.__CANDIDATE = {
      utme_id: "<?php echo htmlspecialchars($candidate['utme_id']); ?>",
      utme_number: "<?php echo htmlspecialchars($candidate['utme_number'] ?? ''); ?>",
      name: "<?php echo htmlspecialchars($fullName); ?>"
    };
  </script>
  <script src="../asset/js/dashboard.js"></script>
</body>
</html>