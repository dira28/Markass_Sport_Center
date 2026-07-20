document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('.admin-sidebar');
  const btn = document.querySelector('[data-admin-sidebar-toggle]');

  if (!sidebar || !btn) return;

  btn.addEventListener('click', () => {
    const isOpen = sidebar.classList.contains('is-open');
    const nextOpen = !isOpen;
    sidebar.classList.toggle('is-open', nextOpen);
    document.body.classList.toggle('sidebar-open', nextOpen);

    // Close immediately if quickly toggled
    if (!nextOpen) {
      return;
    }
  });



  // Close sidebar on link click (mobile)
  sidebar.querySelectorAll('a').forEach((a) => {
    a.addEventListener('click', () => {
      if (window.matchMedia('(max-width: 575px)').matches) {
        sidebar.classList.remove('is-open');
      }
    });
  });
});

