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

// fetch post-UTME JSON rows for this candidate
$rows = $db->select(
    "SELECT id, results, session, created_at FROM post_utme_json WHERE utme_id = :uid ORDER BY created_at DESC",
    [':uid' => $utme]
);

// decode and aggregate
$flat = [];
$totalScore = 0;
foreach ($rows as &$r) {
    $r['results'] = json_decode($r['results'], true) ?: [];
    foreach ($r['results'] as $res) {
        $course = $res['course'] ?? ($res['course_name'] ?? 'N/A');
        $score = isset($res['score']) ? intval($res['score']) : 0;
        $exam_date = $res['exam_date'] ?? null;
        $flat[] = [
            'course' => $course,
            'score' => $score,
            'exam_date' => $exam_date,
            'session' => $r['session'],
            'row_created' => $r['created_at']
        ];
        $totalScore += $score;
    }
}

// derive display values
$utmeNumberRow = $db->select("SELECT utme_number, surname, first_name FROM utme_candidates WHERE utme_id = :id LIMIT 1", [':id' => $utme]);
$utmeNumber = $utmeNumberRow[0]['utme_number'] ?? $utme;
$fullName = trim(($utmeNumberRow[0]['surname'] ?? '') . ' ' . ($utmeNumberRow[0]['first_name'] ?? ''));

$passMark = 40;
$passed = ($totalScore >= $passMark);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Post UTME Results — <?php echo htmlspecialchars($fullName ?: $utme); ?></title>
  <link rel="stylesheet" href="../asset/css/dashboard.css" />
  <style>
    /* small page-specific tweaks */
    .results-header { display:flex; gap:16px; align-items:center; margin-bottom:18px; flex-wrap:wrap; }
    .results-card { padding:16px; border-radius:10px; background:#fff; box-shadow:0 6px 18px rgba(15,76,117,0.04); }
    .results-table { width:100%; border-collapse:collapse; margin-top:12px; }
    .results-table th, .results-table td { padding:10px 12px; text-align:left; border-bottom:1px solid #eef2f6; }
    .badge-pass { background: #198754; color:#fff; padding:6px 10px; border-radius:8px; font-weight:600; }
    .badge-fail { background: #dc3545; color:#fff; padding:6px 10px; border-radius:8px; font-weight:600; }
    .empty { color: #6b7280; padding:12px 0; }
  </style>
</head>
<body>
  <?php include_once __DIR__ . '/../include/header.php'; ?>

  <div class="dashboard-root">
    <?php include_once __DIR__ . '/../include/sidebar.php'; ?>
    <main class="dashboard-main" style="min-height:700px;">
      <header class="main-header">
        <button id="toggleSidebar" class="toggle-btn">☰</button>
        <div class="user-info">
          <div class="name"><?php echo htmlspecialchars($fullName ?: 'Candidate'); ?></div>
          <div class="meta">UTME No: <?php echo htmlspecialchars($utmeNumber); ?></div>
        </div>
      </header>

      <section class="content" id="content">
        <div class="section" id="post-utme">
          <div class="results-header">
            <div class="results-card" style="min-width:220px;">
              <h4 style="margin:0 0 8px 0">UTME Number</h4>
              <div style="font-weight:700"><?php echo htmlspecialchars($utmeNumber); ?></div>
              <?php if ($fullName): ?><div style="color:var(--muted);font-size:13px"><?php echo htmlspecialchars($fullName); ?></div><?php endif; ?>
            </div>

            <div class="results-card" style="min-width:220px;">
              <h4 style="margin:0 0 8px 0">Total Score</h4>
              <div style="font-weight:700;font-size:20px"><?php echo htmlspecialchars($totalScore); ?></div>
              <div style="color:var(--muted);font-size:13px">Pass mark: <?php echo $passMark; ?></div>
            </div>

            <div class="results-card" style="flex:1;display:flex;flex-direction:column;justify-content:center;">
              <?php if ($totalScore === 0): ?>
                <div class="empty">No post-UTME results recorded yet.</div>
              <?php else: ?>
                <?php if ($passed): ?>
                  <div><span class="badge-pass">Congratulations</span></div>
                  <div style="margin-top:8px;color:var(--muted)">You scored above the pass mark of <?php echo $passMark; ?>.</div>
                <?php else: ?>
                  <div><span class="badge-fail">Below Pass Mark</span></div>
                  <div style="margin-top:8px;color:var(--muted)">Total score is below the pass mark of <?php echo $passMark; ?>.</div>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>

          <div class="card">
            <h3 style="margin-top:0">Courses & Scores</h3>

            <?php if (empty($flat)): ?>
              <div class="empty">No course results to display.</div>
            <?php else: ?>
              <table class="results-table" aria-describedby="results-desc">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Course</th>
                    <th>Score</th>
                    <th>Exam Date</th>
                    <th>Session</th>
                    <th>Recorded At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($flat as $i => $r): ?>
                    <tr>
                      <td><?php echo $i + 1; ?></td>
                      <td><?php echo htmlspecialchars($r['course']); ?></td>
                      <td><?php echo htmlspecialchars($r['score']); ?></td>
                      <td><?php echo htmlspecialchars($r['exam_date'] ?? '-'); ?></td>
                      <td><?php echo htmlspecialchars($r['session'] ?? '-'); ?></td>
                      <td><?php echo htmlspecialchars($r['row_created'] ?? '-'); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <?php include_once __DIR__ . '/../include/footer.php'; ?>
    </main>
  </div>

  <script src="../asset/js/dashboard.js"></script>
</body>
</html>