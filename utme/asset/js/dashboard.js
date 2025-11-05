document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('toggleSidebar');
  const navItems = document.querySelectorAll('.nav .nav-item');
  const sections = document.querySelectorAll('.section');

  // mobile toggle
  if (toggle) {
    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  }

  // sidebar navigation
  navItems.forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.nav .nav-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');

      const target = item.getAttribute('data-section');
      // show target section, hide others
      sections.forEach(s => {
        if (s.id === target) s.classList.remove('hidden');
        else s.classList.add('hidden');
      });

      // close sidebar on mobile
      if (sidebar.classList.contains('open')) sidebar.classList.remove('open');
    });
  });

  // Quick actions (placeholder)
  document.querySelectorAll('.quick-actions .qa').forEach(a => {
    a.addEventListener('click', (e) => {
      e.preventDefault();
      alert('This feature will be available soon.');
    });
  });

  // Example: expose candidate info
  if (window.__CANDIDATE) {
    // can fetch live updates or notifications later
    // console.log('Candidate', window.__CANDIDATE);
  }
});