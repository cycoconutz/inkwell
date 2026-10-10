// Progressive enhancement for small interactions.
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('[data-theme-toggle]');
  if (toggle) {
    toggle.addEventListener('click', () => {
      document.documentElement.classList.toggle('theme--light');
    });
  }
});
