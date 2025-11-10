<?php
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

// Fetch all data for prefilling
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$personal = [];
$parent = [];
$education = [];
$documents = [];

if (!$conn->connect_error) {
    // Personal Info
    $stmt = $conn->prepare("SELECT * FROM utme_personal_info WHERE utme_id=? LIMIT 1");
    $stmt->bind_param("s", $utme);
    $stmt->execute();
    $result = $stmt->get_result();
    $personal = $result->fetch_assoc() ?: [];
    $stmt->close();

    // Parent Info
    $stmt = $conn->prepare("SELECT * FROM utme_parent_info WHERE utme_id=? LIMIT 1");
    $stmt->bind_param("s", $utme);
    $stmt->execute();
    $result = $stmt->get_result();
    $parent = $result->fetch_assoc() ?: [];
    $stmt->close();

    // Education
    $stmt = $conn->prepare("SELECT * FROM utme_education_background WHERE utme_id=? LIMIT 1");
    $stmt->bind_param("s", $utme);
    $stmt->execute();
    $result = $stmt->get_result();
    $education = $result->fetch_assoc() ?: [];
    $stmt->close();

    // Documents
    $stmt = $conn->prepare("SELECT doc_type, file_path FROM utme_documents WHERE utme_id=?");
    $stmt->bind_param("s", $utme);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
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
    .form-section { background:#fff; padding:16px; border-radius:10px; box-shadow:0 6px 18px rgba(15,76,117,0.04); margin-bottom:16px; }
    .row { display:flex; gap:12px; flex-wrap:wrap; }
    .col { flex:1; min-width:200px; }
    label { display:block; font-weight:600; margin-bottom:6px; color:#334155; }
    input, select, textarea { width:100%; padding:10px; border-radius:6px; border:1px solid #e6eef6; }
    .doc-row { display:flex; gap:8px; align-items:center; margin-bottom:8px; }
    .doc-row select, .doc-row input[type="file"] { flex:1; }
    .doc-row .remove-btn { background:#ef4444; color:#fff; border:none; padding:8px 10px; border-radius:6px; cursor:pointer; }
    .doc-actions { margin-top:8px; }
    .uploaded-doc { margin-bottom: 6px; font-size: 14px; }
    @media (max-width: 900px) {
      .dashboard-root { flex-direction: column; }
      .dashboard-main { padding: 8px; }
      .main-header { flex-direction: column; align-items: flex-start; gap: 8px; }
      .tabs { flex-wrap: wrap; font-size: 15px; padding-left: 0; }
      .tabs li { padding: 8px 12px; font-size: 15px; margin-bottom: 2px; border-radius: 8px; }
      .tab-content { padding: 10px; }
      .form-section { padding: 10px; }
      .row { flex-direction: column; gap: 8px; }
      .col { min-width: 100%; }
      label { margin-bottom: 4px; font-size: 15px; }
      input, select, textarea { font-size: 15px; padding: 8px; }
      .actions { flex-direction: column; gap: 8px; }
      .btn, .btn.secondary { width: 100%; font-size: 16px; padding: 10px 0; }
      .doc-row { flex-direction: column; gap: 6px; }
      .uploaded-doc { font-size: 13px; }
    }
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

      fetch('/ajax/save_' + section + '.php', {
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

    function addDocumentRow() {
      const wrap = document.getElementById('docsWrap');
      const row = document.createElement('div');
      row.className = 'doc-row';
      row.innerHTML = `
        <select name="doc_type[]" required>
          <option value="">-- Select document type --</option>
          <option value="birth_certificate">Birth Certificate</option>
          <option value="indigene_certificate">Indigene Certificate</option>
          <option value="ssce_result">SSCE Result</option>
          <option value="scratch_card_image">Scratch Card Image</option>
          <option value="primary_school_testimonial">Primary School Testimonial</option>
          <option value="secondary_school_testimonial">Secondary School Testimonial</option>
          <option value="marriage_certificate">Marriage Certificate</option>
        </select>
        <input type="file" name="documents[]" accept=".jpg,.jpeg,.png,.pdf" required />
        <button type="button" class="remove-btn" onclick="removeDocumentRow(this)">Remove</button>
      `;
      wrap.appendChild(row);
    }
    function removeDocumentRow(btn) {
      const row = btn.closest('.doc-row');
      if (row) row.remove();
    }
    document.addEventListener('DOMContentLoaded', function(){
      if (!document.getElementById('docsWrap').children.length) addDocumentRow();
    });
    function addSubject() {
      const wrap = document.getElementById('subjectsWrap');
      const row = document.createElement('div');
      row.className = 'row subject-row';
      row.innerHTML = `<div class="col"><input name="subject[]" placeholder="Subject" /></div>
                       <div class="col"><input name="grade[]" placeholder="Grade" /></div>
                       <div class="col" style="max-width:90px"><button type="button" class="btn secondary" onclick="removeSubject(this)">Remove</button></div>`;
      wrap.appendChild(row);
    }
    function removeSubject(btn) {
      const row = btn.closest('.subject-row');
      if (row) row.remove();
    }

    function deleteDocument(filePath, btn) {
      if (!confirm('Are you sure you want to delete this document?')) return;
      fetch('/ajax/delete_document.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'file_path=' + encodeURIComponent(filePath)
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          const docDiv = btn.closest('.uploaded-doc');
          if (docDiv) docDiv.remove();
        } else {
          alert(data.message || 'Error deleting document.');
        }
      })
      .catch(() => {
        alert('Network error.');
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
            $displayName = $personal['surname'] ?? $personal['first_name'] ?? $utme;
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
          <li id="tabbtn-parent" onclick="showTab('parent')">Parent / Guardian</li>
          <li id="tabbtn-education" onclick="showTab('education')">Educational Background</li>
          <li id="tabbtn-documents" onclick="showTab('documents')">Documents</li>
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

        <!-- Parent / Guardian Tab -->
        <div id="tab-parent" class="tab-content">
          <form id="form-parent" class="form-section">
            <div class="row">
              <div class="col">
                <label for="guardian_name">Guardian name</label>
                <input id="guardian_name" name="guardian_name" type="text" value="<?php echo htmlspecialchars($parent['guardian_name'] ?? ''); ?>" />
              </div>
              <div class="col">
                <label for="guardian_occupation">Guardian occupation</label>
                <input id="guardian_occupation" name="guardian_occupation" type="text" value="<?php echo htmlspecialchars($parent['occupation'] ?? ''); ?>" />
              </div>
            </div>
            <div class="row" style="margin-top:8px">
              <div class="col">
                <label for="mother_name">Mother name</label>
                <input id="mother_name" name="mother_name" type="text" value="<?php echo htmlspecialchars($parent['mother_name'] ?? ''); ?>" />
              </div>
              <div class="col">
                <label for="mother_occupation">Mother occupation</label>
                <input id="mother_occupation" name="mother_occupation" type="text" value="<?php echo htmlspecialchars($parent['mother_occupation'] ?? ''); ?>" />
              </div>
            </div>
            <div class="row" style="margin-top:8px">
              <div class="col">
                <label for="guardian_address">Guardian address</label>
                <input id="guardian_address" name="guardian_address" type="text" value="<?php echo htmlspecialchars($parent['guardian_address'] ?? ''); ?>" />
              </div>
              <div class="col">
                <label for="parent_phone">Parent / Guardian phone</label>
                <input id="parent_phone" name="parent_phone" type="text" value="<?php echo htmlspecialchars($parent['phone'] ?? ''); ?>" />
              </div>
            </div>
            <div class="actions">
              <button type="button" class="btn" onclick="saveSection('parent')">Save</button>
            </div>
            <span id="msg-parent" class="msg"></span>
          </form>
        </div>

        <!-- Educational Background Tab -->
        <div id="tab-education" class="tab-content">
          <form id="form-education" class="form-section">
            <div class="row">
              <div class="col">
                <label for="sitting">Sitting</label>
                <select id="sitting" name="sitting">
                  <option value="">-- Select --</option>
                  <option value="1" <?php if(($education['sitting'] ?? '')=='1') echo 'selected'; ?>>1 sitting</option>
                  <option value="2" <?php if(($education['sitting'] ?? '')=='2') echo 'selected'; ?>>2 sittings</option>
                </select>
              </div>
              <div class="col">
                <label for="exam_type">Exam Type</label>
                <select id="exam_type" name="exam_type">
                  <option value="">-- Select --</option>
                  <option value="WAEC" <?php if(($education['exam_type'] ?? '')=='WAEC') echo 'selected'; ?>>WAEC</option>
                  <option value="NECO" <?php if(($education['exam_type'] ?? '')=='NECO') echo 'selected'; ?>>NECO</option>
                  <option value="NABTEB" <?php if(($education['exam_type'] ?? '')=='NABTEB') echo 'selected'; ?>>NABTEB</option>
                </select>
              </div>
              <div class="col">
                <label for="exam_year">Year</label>
                <input id="exam_year" name="exam_year" type="text" value="<?php echo htmlspecialchars($education['exam_year'] ?? ''); ?>" />
              </div>
            </div>
            <div class="row" style="margin-top:8px">
              <div class="col"><label for="exam_no">Exam Number</label><input id="exam_no" name="exam_no" type="text" value="<?php echo htmlspecialchars($education['exam_no'] ?? ''); ?>" /></div>
              <div class="col"><label for="exam_date">Exam Date</label><input id="exam_date" name="exam_date" type="date" value="<?php echo htmlspecialchars($education['exam_date'] ?? ''); ?>" /></div>
            </div>
            <h4 style="margin-top:12px">Subjects & Grades</h4>
            <div id="subjectsWrap">
              <?php
                $subjects = [];
                if (!empty($education['subjects_json'])) {
                  $subjects = json_decode($education['subjects_json'], true);
                }
                if ($subjects) {
                  foreach ($subjects as $subj) {
                    echo '<div class="row subject-row">';
                    echo '<div class="col"><input name="subject[]" placeholder="Subject" value="'.htmlspecialchars($subj['subject']).'" /></div>';
                    echo '<div class="col"><input name="grade[]" placeholder="Grade" value="'.htmlspecialchars($subj['grade']).'" /></div>';
                    echo '<div class="col" style="max-width:90px"><button type="button" class="btn secondary" onclick="removeSubject(this)">Remove</button></div>';
                    echo '</div>';
                  }
                } else {
                  // Show one empty row
                  echo '<div class="row subject-row">';
                  echo '<div class="col"><input name="subject[]" placeholder="Subject" /></div>';
                  echo '<div class="col"><input name="grade[]" placeholder="Grade" /></div>';
                  echo '<div class="col" style="max-width:90px"><button type="button" class="btn secondary" onclick="removeSubject(this)">Remove</button></div>';
                  echo '</div>';
                }
              ?>
            </div>
            <div style="margin-top:8px"><button type="button" class="btn" onclick="addSubject()">Add subject</button></div>
            <div class="actions">
              <button type="button" class="btn" onclick="saveSection('education')">Save</button>
            </div>
            <span id="msg-education" class="msg"></span>
          </form>
        </div>

        <!-- Documents Tab -->
        <div id="tab-documents" class="tab-content">
          <form id="form-documents" class="form-section" enctype="multipart/form-data">
            <div>
              <?php
                if ($documents) {
                  foreach ($documents as $doc) {
                    echo '<div class="uploaded-doc" id="doc-'.md5($doc['file_path']).'">';
                    echo '<strong>'.ucwords(str_replace('_',' ', $doc['doc_type'])).':</strong> ';
                    echo '<a href="/uploads/utme_documents/'.htmlspecialchars($doc['file_path']).'" target="_blank">View</a> ';
                    echo '<button type="button" class="btn secondary" style="margin-left:8px" onclick="deleteDocument(\''.addslashes($doc['file_path']).'\', this)">Delete</button>';
                    echo '</div>';
                  }
                }
              ?>
            </div>
            <div id="docsWrap"></div>
            <div class="doc-actions">
              <button type="button" class="btn" onclick="addDocumentRow()">Add document</button>
              <div style="margin-top:8px;color:var(--muted);font-size:13px">Allowed types: jpg, png, pdf. Max size per file: 2MB.</div>
            </div>
            <div class="actions">
              <button type="button" class="btn" onclick="saveSection('documents')">Save</button>
            </div>
            <span id="msg-documents" class="msg"></span>
          </form>
        </div>

      </section>
      <?php include_once __DIR__ . '/../include/footer.php'; ?>
    </main>
  </div>
</body>
</html>