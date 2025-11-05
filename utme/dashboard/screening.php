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
$error = '';
$success = '';

/**
 * Generate unique application id like DAKO-UTME-1234567
 */
function generateAppId($db) {
    do {
        $id = 'DAKO-UTME-' . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        $exists = $db->select("SELECT app_id FROM utme_applications WHERE app_id = :id LIMIT 1", [':id' => $id]);
    } while (!empty($exists));
    return $id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_application'])) {
    // collect personal info with safe defaults to avoid trim(null) deprecation
    $dob = trim((string)($_POST['dob'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $gender = trim((string)($_POST['gender'] ?? ''));
    $marital_status = trim((string)($_POST['marital_status'] ?? ''));
    $blood_group = trim((string)($_POST['blood_group'] ?? ''));
    $present_address = trim((string)($_POST['present_address'] ?? ''));
    $permanent_address = trim((string)($_POST['permanent_address'] ?? ''));
    $state = trim((string)($_POST['state'] ?? ''));
    $lga = trim((string)($_POST['lga'] ?? ''));

    // parent info
    $guardian_name = trim((string)($_POST['guardian_name'] ?? ''));
    $guardian_occupation = trim((string)($_POST['guardian_occupation'] ?? ''));
    $mother_name = trim((string)($_POST['mother_name'] ?? ''));
    $mother_occupation = trim((string)($_POST['mother_occupation'] ?? ''));
    $guardian_address = trim((string)($_POST['guardian_address'] ?? ''));
    $parent_phone = trim((string)($_POST['parent_phone'] ?? ''));

    // education
    $sitting = trim((string)($_POST['sitting'] ?? ''));
    $exam_type = trim((string)($_POST['exam_type'] ?? ''));
    $exam_year = trim((string)($_POST['exam_year'] ?? ''));
    $exam_no = trim((string)($_POST['exam_no'] ?? ''));
    $exam_date = trim((string)($_POST['exam_date'] ?? ''));
    $scratch_pin = trim((string)($_POST['scratch_pin'] ?? ''));
    $scratch_serial = trim((string)($_POST['scratch_serial'] ?? ''));

    // subjects and grades arrays (normalize)
    $subjects = [];
    if (!empty($_POST['subject']) && is_array($_POST['subject'])) {
        foreach ($_POST['subject'] as $i => $sub) {
            $s = trim((string)($sub ?? ''));
            $g = trim((string)($_POST['grade'][$i] ?? ''));
            if ($s !== '') $subjects[] = ['subject' => $s, 'grade' => $g];
        }
    }

    // simple validation: require dob and phone
    if ($dob === '' || $phone === '') {
        $error = 'Please provide date of birth and phone number.';
    } else {
        try {
            // personal info insert/update
            $exists = $db->select("SELECT info_id FROM utme_personal_info WHERE utme_id = :uid LIMIT 1", [':uid' => $utme]);
            if (!empty($exists)) {
                $db->execute(
                    "UPDATE utme_personal_info SET dob = :dob, phone = :phone, gender = :gender, marital_status = :marital, blood_group = :bg,
                     present_address = :present, permanent_address = :permanent, state = :state, lga = :lga, created_at = NOW()
                     WHERE utme_id = :uid",
                    [
                        ':dob' => $dob, ':phone' => $phone, ':gender' => $gender, ':marital' => $marital_status,
                        ':bg' => $blood_group, ':present' => $present_address, ':permanent' => $permanent_address,
                        ':state' => $state, ':lga' => $lga, ':uid' => $utme
                    ]
                );
            } else {
                $db->execute(
                    "INSERT INTO utme_personal_info (utme_id, dob, phone, gender, marital_status, blood_group, present_address, permanent_address, state, lga, created_at)
                     VALUES (:uid, :dob, :phone, :gender, :marital, :bg, :present, :permanent, :state, :lga, NOW())",
                    [
                        ':uid' => $utme, ':dob' => $dob, ':phone' => $phone, ':gender' => $gender, ':marital' => $marital_status,
                        ':bg' => $blood_group, ':present' => $present_address, ':permanent' => $permanent_address,
                        ':state' => $state, ':lga' => $lga
                    ]
                );
            }

            // parent info insert/update
            $pexists = $db->select("SELECT parent_id FROM utme_parent_info WHERE utme_id = :uid LIMIT 1", [':uid' => $utme]);
            if (!empty($pexists)) {
                $db->execute(
                    "UPDATE utme_parent_info SET guardian_name = :gn, occupation = :occ, mother_name = :mn, mother_occupation = :mo, guardian_address = :addr, phone = :ph, created_at = NOW()
                     WHERE utme_id = :uid",
                    [':gn' => $guardian_name, ':occ' => $guardian_occupation, ':mn' => $mother_name, ':mo' => $mother_occupation, ':addr' => $guardian_address, ':ph' => $parent_phone, ':uid' => $utme]
                );
            } else {
                $db->execute(
                    "INSERT INTO utme_parent_info (utme_id, guardian_name, occupation, mother_name, mother_occupation, guardian_address, phone, created_at)
                     VALUES (:uid, :gn, :occ, :mn, :mo, :addr, :ph, NOW())",
                    [':uid' => $utme, ':gn' => $guardian_name, ':occ' => $guardian_occupation, ':mn' => $mother_name, ':mo' => $mother_occupation, ':addr' => $guardian_address, ':ph' => $parent_phone]
                );
            }

            // educational background insert/update
            $subjects_json = json_encode(array_values($subjects), JSON_UNESCAPED_UNICODE);
            $eexists = $db->select("SELECT edu_id FROM utme_educational_background WHERE utme_id = :uid LIMIT 1", [':uid' => $utme]);
            if (!empty($eexists)) {
                $db->execute(
                    "UPDATE utme_educational_background SET sitting = :sitting, exam_type = :etype, exam_year = :eyear, exam_no = :eno, exam_date = :edate,
                     scratch_pin = :pin, scratch_serial = :serial, subjects_json = :subs, created_at = NOW() WHERE utme_id = :uid",
                    [':sitting' => $sitting, ':etype' => $exam_type, ':eyear' => $exam_year, ':eno' => $exam_no, ':edate' => $exam_date, ':pin' => $scratch_pin, ':serial' => $scratch_serial, ':subs' => $subjects_json, ':uid' => $utme]
                );
            } else {
                $db->execute(
                    "INSERT INTO utme_educational_background (utme_id, sitting, exam_type, exam_year, exam_no, exam_date, scratch_pin, scratch_serial, subjects_json, created_at)
                     VALUES (:uid, :sitting, :etype, :eyear, :eno, :edate, :pin, :serial, :subs, NOW())",
                    [':uid' => $utme, ':sitting' => $sitting, ':etype' => $exam_type, ':eyear' => $exam_year, ':eno' => $exam_no, ':edate' => $exam_date, ':pin' => $scratch_pin, ':serial' => $scratch_serial, ':subs' => $subjects_json]
                );
            }

            // handle documents upload to uploads/utme/
            $uploadBase = __DIR__ . '/../../uploads/utme/';
            if (!file_exists($uploadBase)) mkdir($uploadBase, 0777, true);

            $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
            $maxBytes = 2 * 1024 * 1024; // 2MB

            if (!empty($_FILES['documents']['name']) && is_array($_FILES['documents']['name'])) {
                foreach ($_FILES['documents']['name'] as $i => $name) {
                    if (empty($name)) continue;
                    $tmp = $_FILES['documents']['tmp_name'][$i] ?? null;
                    $size = $_FILES['documents']['size'][$i] ?? 0;
                    $docType = trim((string)($_POST['doc_type'][$i] ?? 'document'));
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExt, true)) continue;
                    if ($size > $maxBytes) continue;
                    $safe = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($name, PATHINFO_FILENAME));
                    $destName = $utme . '_' . $docType . '_' . time() . '_' . $i . '.' . $ext;
                    $dest = $uploadBase . $destName;
                    if ($tmp && is_uploaded_file($tmp) && move_uploaded_file($tmp, $dest)) {
                        $db->execute(
                            "INSERT INTO utme_documents (utme_id, doc_type, file_path, uploaded_at) VALUES (:uid, :dtype, :path, NOW())",
                            [':uid' => $utme, ':dtype' => $docType, ':path' => 'uploads/utme/' . $destName]
                        );
                    }
                }
            }

            // create application record (no course selection — store NULL)
            $app_id = generateAppId($db);
            $payment_status = 'unpaid';
            $admission_status = 'pending';
            $acceptance_status = 'unpaid';

            $db->execute(
                "INSERT INTO utme_applications (app_id, utme_id, course_id, payment_status, admission_status, acceptance_status, session, created_at)
                 VALUES (:app_id, :uid, :course_id, :pay, :adm, :acc, :session, NOW())",
                [':app_id' => $app_id, ':uid' => $utme, ':course_id' => null, ':pay' => $payment_status, ':adm' => $admission_status, ':acc' => $acceptance_status, ':session' => null]
            );

            $success = 'Application started successfully. Your Application ID: ' . $app_id . '.';
            header("Refresh:2; url=admission-status.php");
            exit();
        } catch (Exception $e) {
            $error = 'Failed to start application. Try again later.';
            // optionally log $e->getMessage()
        }
    }
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
    .form-section { background:#fff; padding:16px; border-radius:10px; box-shadow:0 6px 18px rgba(15,76,117,0.04); margin-bottom:16px; }
    .row { display:flex; gap:12px; flex-wrap:wrap; }
    .col { flex:1; min-width:200px; }
    label { display:block; font-weight:600; margin-bottom:6px; color:#334155; }
    input, select, textarea { width:100%; padding:10px; border-radius:6px; border:1px solid #e6eef6; }
    .actions { display:flex; gap:10px; margin-top:12px; }
    .btn { padding:10px 14px; border-radius:8px; cursor:pointer; border:none; background:var(--accent); color:#fff; }
    .btn.secondary { background:#6b7280; }
    .message { padding:12px; border-radius:8px; margin-bottom:12px; }
    .message.success { background:#ecfdf5; color:#065f46; }
    .message.error { background:#fff1f2; color:#7f1d1d; }
    .doc-row { display:flex; gap:8px; align-items:center; margin-bottom:8px; }
    .doc-row select, .doc-row input[type="file"] { flex:1; }
    .doc-row .remove-btn { background:#ef4444; color:#fff; border:none; padding:8px 10px; border-radius:6px; cursor:pointer; }
    .doc-actions { margin-top:8px; }
  </style>
  <script defer>
    function addDocumentRow() {
      const wrap = document.getElementById('docsWrap');
      const idx = wrap.children.length;
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
            // try fetch name for display
            $candidateRow = $db->select("SELECT surname, first_name FROM utme_candidates WHERE utme_id = :id LIMIT 1", [':id' => $utme]);
            $displayName = $candidateRow[0]['surname'] ?? $candidateRow[0]['first_name'] ?? $utme;
          ?>
          <div class="name"><?php echo htmlspecialchars($displayName); ?></div>
          <div class="meta">UTME No: <?php echo htmlspecialchars($utme); ?></div>
        </div>
      </header>

      <section class="content">
        <h1>Online Screening & Admission Application</h1>

        <?php if ($error): ?>
          <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" id="screeningForm">
          <div class="form-section">
            <h3>Application Options</h3>
            <div class="row">
              <div class="col">
                <p style="color:var(--muted)">You will select your preferred course later in the application flow. For now complete your personal, education and document details.</p>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>Personal Information</h3>
            <div class="row">
              <div class="col">
                <label for="dob">Date of birth</label>
                <input id="dob" name="dob" type="date" required />
              </div>
              <div class="col">
                <label for="phone">Phone</label>
                <input id="phone" name="phone" type="text" required />
              </div>
              <div class="col">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                  <option value="">--</option>
                  <option>Male</option>
                  <option>Female</option>
                </select>
              </div>
            </div>

            <div class="row" style="margin-top:8px">
              <div class="col">
                <label for="present_address">Present address</label>
                <textarea id="present_address" name="present_address" rows="2"></textarea>
              </div>
              <div class="col">
                <label for="permanent_address">Permanent address</label>
                <textarea id="permanent_address" name="permanent_address" rows="2"></textarea>
              </div>
            </div>

            <div class="row" style="margin-top:8px">
              <div class="col">
                <label for="state">State</label>
                <input id="state" name="state" type="text" />
              </div>
              <div class="col">
                <label for="lga">LGA</label>
                <input id="lga" name="lga" type="text" />
              </div>
              <div class="col">
                <label for="blood_group">Blood group</label>
                <input id="blood_group" name="blood_group" type="text" />
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>Parent / Guardian</h3>
            <div class="row">
              <div class="col">
                <label for="guardian_name">Guardian name</label>
                <input id="guardian_name" name="guardian_name" type="text" />
              </div>
              <div class="col">
                <label for="guardian_occupation">Guardian occupation</label>
                <input id="guardian_occupation" name="guardian_occupation" type="text" />
              </div>
            </div>
            <div class="row" style="margin-top:8px">
              <div class="col">
                <label for="mother_name">Mother name</label>
                <input id="mother_name" name="mother_name" type="text" />
              </div>
              <div class="col">
                <label for="mother_occupation">Mother occupation</label>
                <input id="mother_occupation" name="mother_occupation" type="text" />
              </div>
            </div>
            <div class="row" style="margin-top:8px">
              <div class="col">
                <label for="guardian_address">Guardian address</label>
                <input id="guardian_address" name="guardian_address" type="text" />
              </div>
              <div class="col">
                <label for="parent_phone">Parent / Guardian phone</label>
                <input id="parent_phone" name="parent_phone" type="text" />
              </div>
            </div>
          </div>

          <div class="form-section" id="educationSection">
            <h3>Educational Background</h3>
            <div class="row">
              <div class="col">
                <label for="sitting">Sitting</label>
                <select id="sitting" name="sitting">
                  <option value="">-- Select --</option>
                  <option value="1 sitting">1 sitting</option>
                  <option value="2 sittings">2 sittings</option>
                </select>
              </div>
              <div class="col">
                <label for="exam_type">Exam Type</label>
                <select id="exam_type" name="exam_type">
                  <option value="">-- Select --</option>
                  <option value="WAEC">WAEC</option>
                  <option value="NECO">NECO</option>
                  <option value="NABTEB">NABTEB</option>
                </select>
              </div>
              <div class="col">
                <label for="exam_year">Year</label>
                <input id="exam_year" name="exam_year" type="text" />
              </div>
            </div>
            <div class="row" style="margin-top:8px">
              <div class="col"><label for="exam_no">Exam Number</label><input id="exam_no" name="exam_no" type="text" /></div>
              <div class="col"><label for="exam_date">Exam Date</label><input id="exam_date" name="exam_date" type="date" /></div>
            </div>

            <h4 style="margin-top:12px">Subjects & Grades</h4>
            <div id="subjectsWrap">
              <div class="row subject-row">
                <div class="col"><input name="subject[]" placeholder="Subject" /></div>
                <div class="col"><input name="grade[]" placeholder="Grade" /></div>
                <div class="col" style="max-width:90px"><button type="button" class="btn secondary" onclick="removeSubject(this)">Remove</button></div>
              </div>
            </div>
            <div style="margin-top:8px"><button type="button" class="btn" onclick="addSubject()">Add subject</button></div>
          </div>

          <div class="form-section">
            <h3>Documents</h3>
            <div id="docsWrap"></div>
            <div class="doc-actions">
              <button type="button" class="btn" onclick="addDocumentRow()">Add document</button>
              <div style="margin-top:8px;color:var(--muted);font-size:13px">Allowed types: jpg, png, pdf. Max size per file: 2MB.</div>
            </div>
          </div>

          <div class="actions">
            <button type="submit" name="start_application" class="btn">Start Admission Application</button>
            <button type="reset" class="btn secondary">Reset</button>
          </div>
        </form>

      </section>

      <?php include_once __DIR__ . '/../include/footer.php'; ?>
    </main>
  </div>

</body>
</html>