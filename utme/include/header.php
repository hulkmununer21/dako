<?php
// Minimal header fragment for dashboard (expects $fullName or $_SESSION available)
?>
<div class="topbar">
  <div class="topbar-left">
    <h3 class="topbar-brand">DAKO Admissions</h3>
  </div>
  <div class="topbar-right">
    <div class="top-user">
      <span class="top-user-name"><?php echo htmlspecialchars($fullName ?? ($_SESSION['user_id'] ?? '')); ?></span>
    </div>
  </div>
</div>