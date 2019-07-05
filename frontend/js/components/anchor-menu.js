/**
 * Functionality for the dynamic submenu (eg. on page Reports)
 */
export default function() {
  const container = document.querySelector('.hero-submenu-container');
  if (!container) return;

  const anchors = document.querySelectorAll('.js-anchor-menu');

  Array.from(anchors).forEach(anchor => {
    const anchorEl = document.createElement('li');
    anchorEl.classList.add('hero-submenu-item');
    anchorEl.innerHTML =
      '<li class="hero-submenu-item"><a href="#' +
      anchor.id +
      '">' +
      anchor.dataset.labelmenu +
      '</a></li>';
    container.appendChild(anchorEl);
  });
}
