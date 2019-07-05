/**
 * Functionality for the "dollar handle" donation form.
 *
 */
export default function() {
  // Update paragraph content, and
  // Clear free amount value when I select a dollar handle
  document.querySelectorAll('.dollar-handle input').forEach(radio => {
    radio.addEventListener('change', e => {
      const form = e.target.form;
      const group = e.target.closest('.dollar-handles');
      const paragraph = group.classList.contains('dollar-handles__once')
        ? form.querySelector('.paragraph-handles__once')
        : form.querySelector('.paragraph-handles__recurring');
      const paragraphOther = group.classList.contains(
        'dollar-handles__recurring'
      )
        ? form.querySelector('.paragraph-handles__once')
        : form.querySelector('.paragraph-handles__recurring');
      const freeAmount = form['free-amount'];
      paragraph.textContent = radio.dataset.paragraph;
      paragraphOther.textContent = '';
      freeAmount.value = '';
    });
  });

  // Clear checked dollar handle if I insert a free amount
  document.querySelectorAll('.donation-form .free-amount').forEach(input => {
    input.addEventListener('change', e => {
      const form = e.target.form;
      const checkedHandles = form['dollar-handle'];
      Array.from(checkedHandles).forEach(radio => {
        radio.checked = false;
      });
    });
  });
}
