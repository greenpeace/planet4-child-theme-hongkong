/**
 * Functionality for showing/hiding elements animating their heights, accordion style.
 *
 */
export default function() {
  document.querySelectorAll('.toggle').forEach(toggle => {
    toggle.addEventListener('click', e => {
      toggle.blur();
      const togglee = document.querySelector(toggle.dataset.toggleeSelector);

      if (!togglee || !togglee.children || !togglee.children.length === 1) {
        console.warn('"togglee" must exist have exactly 1 child');
        return;
      }

      if (togglee.classList.contains('is-animating')) return;

      const delay = togglee.classList.contains('accordion') ? 300 : 800;
      togglee.classList.add('is-animating');
      setTimeout(() => {
        togglee.classList.remove('is-animating');
        togglee.style.height = null;
      }, delay); // from _toggle.scss: transition: height 800ms ease;

      const toggleeHeight = togglee.children[0].getBoundingClientRect().height;

      if (toggle.classList.contains('has-opened')) {
        toggle.classList.remove('has-opened');
        togglee.classList.remove('is-open');
        togglee.style.height = toggleeHeight + 'px';
        setTimeout(() => {
          togglee.style.height = '0px';
        }, 10);
      } else {
        toggle.classList.add('has-opened');
        togglee.classList.add('is-open');
        togglee.style.height = toggleeHeight + 'px';
      }
    });
  });
}
