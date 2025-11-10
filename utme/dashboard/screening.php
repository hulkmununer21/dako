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

// Fetch personal info from DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$personal = [];
if ($conn->connect_error) {
    $personal = [];
} else {
    $stmt = $conn->prepare("SELECT * FROM utme_personal_info WHERE utme_id=? LIMIT 1");
    $stmt->bind_param("s", $utme);
    $stmt->execute();
    $result = $stmt->get_result();
    $personal = $result->fetch_assoc() ?: [];
    $stmt->close();
    $conn->close();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Online Screening — Start Application</title>
  <link rel="stylesheet" href="../asset/css/dashboard.css" />
  <style>
    .tabs { display: flex; margin-bottom: 16px; }
    .tabs li { list-style: none; padding: 10px 20px; cursor: pointer; background: #e6eef6; margin-right: 2px; border-radius: 8px 8px 0 0; }
    .tabs li.active { background: var(--accent); color: #fff; }
    .tab-content { display: none; border: 1px solid #e6eef6; border-radius: 0 0 10px 10px; padding: 20px; background: #fff; }
    .tab-content.active { display: block; }
    .msg { margin-top: 10px; color: green; }
    .msg.error { color: red; }
    .actions { display: flex; gap: 10px; margin-top: 12px; }
    .btn { padding: 10px 14px; border-radius: 8px; cursor: pointer; border: none; background: var(--accent); color: #fff; }
    .btn.secondary { background: #6b7280; }
  </style>
  <script>
    function showTab(tab) {
      document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
      document.getElementById('tab-' + tab).classList.add('active');
      document.querySelectorAll('.tabs li').forEach(el => el.classList.remove('active'));
      document.getElementById('tabbtn-' + tab).classList.add('active');
    }

    function saveSection(section) {
      let form = document.getElementById('form-' + section);
      let msg = document.getElementById('msg-' + section);
      msg.textContent = '';
      let formData = new FormData(form);

      fetch('ajax/save_' + section + '.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if(data.status === 'success') {
          msg.textContent = 'Saved!';
          msg.className = 'msg';
        } else {
          msg.textContent = data.message || 'Error saving data.';
          msg.className = 'msg error';
        }
      })
      .catch(() => {
        msg.textContent = 'Network error.';
        msg.className = 'msg error';
      });
    }
  </script>
</head>
<body>
  <?php include_once __DIR__ . '/../include/header.php'; ?>
  <div class="dashboard-root">
    <?php include_once __DIR__ . '/../include/sidebar.php'; ?>

    <main class="dashboard-main">
      <header class="main-header">
        <button id="toggleSidebar" class="toggle-btn">☰</button>
        <div class="user-info">
          <?php
            $candidateRow = $personal;
            $displayName = $candidateRow['surname'] ?? $candidateRow['first_name'] ?? $utme;
          ?>
          <div class="name"><?php echo htmlspecialchars($displayName); ?></div>
          <div class="meta">UTME No: <?php echo htmlspecialchars($utme); ?></div>
        </div>
      </header>

      <section class="content">
        <h1>Online Screening & Admission Application</h1>

        <!-- Multi-tab navigation -->
        <ul class="tabs">
          <li id="tabbtn-personal" class="active" onclick="showTab('personal')">Personal Info</li>
          <!-- Add other tabs as needed -->
        </ul>

        <!-- Personal Info Tab -->
        <div id="tab-personal" class="tab-content active">
          <form id="form-personal" class="form-section">
            <div class="row">
              <div class="col">
                <label for="dob">Date of birth</label>
                <input id="dob" name="dob" type="date" required value="<?php echo htmlspecialchars($personal['dob'] ?? ''); ?>" />
              </div>
              <div class="col">
                <label for="phone">Phone</label>
                <input id="phone" name="phone" type="text" required value="<?php echo htmlspecialchars($personal['phone'] ?? ''); ?>" />
              </div>
              <div class="col">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                  <option value="">--</option>
                  <option value="Male" <?php if(($personal['gender'] ?? '')=='Male') echo 'selected'; ?>>Male</option>
                  <option value="Female" <?php if(($personal['gender'] ?? '')=='Female') echo 'selected'; ?>>Female</option>
                </select>
              </div>
            </div>
            <div class="row" style="margin-top:8px">
              <div class="col">
                <label for="present_address">Present address</label>
                <textarea id="present_address" name="present_address" rows="2"><?php echo htmlspecialchars($personal['present_address'] ?? ''); ?></textarea>
              </div>
              <div class="col">
                <label for="permanent_address">Permanent address</label>
                <textarea id="permanent_address" name="permanent_address" rows="2"><?php echo htmlspecialchars($personal['permanent_address'] ?? ''); ?></textarea>
              </div>
            </div>
            <div class="row" style="margin-top:8px">
              <div class="col">
                <label for="state">State</label>
                <input id="state" name="state" type="text" value="<?php echo htmlspecialchars($personal['state'] ?? ''); ?>" />
              </div>
              <div class="col">
                <label for="lga">LGA</label>
                <input id="lga" name="lga" type="text" value="<?php echo htmlspecialchars($personal['lga'] ?? ''); ?>" />
              </div>
              <div class="col">
                <label for="blood_group">Blood group</label>
                <input id="blood_group" name="blood_group" type="text" value="<?php echo htmlspecialchars($personal['blood_group'] ?? ''); ?>" />
              </div>
            </div>
            <div class="actions">
              <button type="button" class="btn" onclick="saveSection('personal')">Save</button>
            </div>
            <span id="msg-personal" class="msg"></span>
          </form>
        </div>

      </section>
      <?php include_once __DIR__ . '/../include/footer.php'; ?>
    </main>
  </div>
</body>
</html>