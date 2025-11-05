<?php
// utme/index.php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DAKO College of Nursing - Admission Portal</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(to right, #0f4c75, #3282b8); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #333; }
    .container { background: #fff; border-radius: 12px; padding: 40px; width: 400px; box-shadow: 0 0 25px rgba(0,0,0,0.2); text-align: center; }
    h2, h3 { color: #0f4c75; margin-bottom: 10px; }
    input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; margin-top: 15px; font-size: 16px; }
    button { width: 100%; padding: 12px; margin-top: 20px; background-color: #0f4c75; color: #fff; font-size: 16px; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; }
    button:hover { background-color: #3282b8; }
    .step { display: none; }
    .portal ul { text-align: left; }
  </style>
</head>
<body>
<div class="container">

  <!-- STEP 1: ELIGIBILITY CHECK -->
  <div id="step1" class="step" style="display:block;">
    <h2>DAKO College of Nursing</h2>
    <p><strong>Admission Eligibility Checker</strong></p>
    <input type="text" id="jambNo" placeholder="Enter JAMB Number (e.g. JAMB2025123)" />
    <button onclick="checkEligibility()">Check Eligibility</button>
  </div>

  <!-- STEP 2: ELIGIBILITY RESULT -->
  <div id="step2" class="step">
    <h3>Eligibility Details</h3>
    <p><strong>Full Name:</strong> <span id="name"></span></p>
    <p><strong>JAMB No:</strong> <span id="enteredJambNo"></span></p>
    <p><strong>Score:</strong> <span id="score"></span></p>
    <button onclick="goToLogin()">Proceed to Login</button>
  </div>

  <!-- STEP 3: LOGIN -->
  <div id="step3" class="step">
    <h3>Admission Portal Login</h3>
    <input type="text" id="loginJamb" readonly />
    <input type="password" id="loginPass" placeholder="Enter Password (Surname)" />
    <button onclick="loginPortal()">Login</button>
  </div>

  <!-- STEP 4: DASHBOARD -->
  <div id="step4" class="step portal">
    <h3>Welcome to Your Admission Portal</h3>
    <p><strong>Candidate:</strong> <span id="portalName"></span></p>
    <ul>
      <li>✅ View Admission Status</li>
      <li>📤 Upload Required Documents</li>
      <li>💳 Pay Acceptance Fee</li>
      <li>📑 Print Admission Letter</li>
    </ul>
    <button onclick="logout()">Logout</button>
  </div>

</div>

<script>
function checkEligibility() {
  const jambNo = document.getElementById('jambNo').value.trim();
  if (!jambNo) { alert("Please enter your JAMB Number."); return; }

  // AJAX request
  const xhr = new XMLHttpRequest();
  xhr.open('POST', './ajax/fetch_candidate.php', true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    if (this.status === 200) {
      const res = JSON.parse(this.responseText);
      if (res.status === 'success') {
        document.getElementById('name').textContent = res.full_name;
        document.getElementById('enteredJambNo').textContent = jambNo.toUpperCase();
        document.getElementById('score').textContent = res.score;
        showStep(2);
      } else {
        alert(res.message);
      }
    } else { alert('Server error.'); }
  };
  xhr.send('jambNo=' + encodeURIComponent(jambNo));
}

function goToLogin() {
  document.getElementById('loginJamb').value = document.getElementById('enteredJambNo').textContent;
  showStep(3);
}

function loginPortal() {
  const jambNo = document.getElementById('loginJamb').value.trim();
  const pass = document.getElementById('loginPass').value.trim();

  const xhr = new XMLHttpRequest();
  xhr.open('POST', './ajax/login_candidate.php', true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    if (this.status === 200) {
      const res = JSON.parse(this.responseText);
      if (res.status === 'success') {
        document.getElementById('portalName').textContent = res.full_name;
        showStep(4);
      } else {
        alert(res.message);
      }
    }
  };
  xhr.send('jambNo=' + encodeURIComponent(jambNo) + '&password=' + encodeURIComponent(pass));
}

function logout() {
  showStep(1);
  document.getElementById('jambNo').value = "";
  document.getElementById('loginPass').value = "";
}

function showStep(num) {
  for (let i = 1; i <= 4; i++) document.getElementById('step' + i).style.display = 'none';
  document.getElementById('step' + num).style.display = 'block';
}
</script>
</body>
</html>
