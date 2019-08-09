/**
 * Activates tabs on donation forms.
 *
 * When you click a tab, the hidden field "frequency" gets updated with
 * the clicked tab's text content.
 *
 */

export default function() {
  document.querySelectorAll('.js-tab-item').forEach(tab => {
    tab.addEventListener('click', e => {
		// console.log('clicked');
      const tabBar = e.target.parentElement;
      const siblings = tabBar.children;
      const form = tabBar.parentElement.querySelector('form');  

      Array.from(siblings).forEach(sibling => {
        sibling.classList.remove('is-active');
      });
      e.target.classList.add('is-active');

      // handle donation suggested for basic form
      
		if ( ( e.target.classList.contains('tab-item__once') ) && form.getAttribute('data-suggested-oneoff') ) {
		  form.amount.value = form.getAttribute('data-suggested-oneoff');
		} else if ( ( e.target.classList.contains('tab-item__recurring') ) && form.getAttribute('data-suggested-regular') ) {
		  form.amount.value = form.getAttribute('data-suggested-regular');
		}        
      

      // form.frequency.value = e.target.textContent.trim();
      form.frequency.value = e.target.getAttribute('data-recurring');
    });
  });
  // on form submit do redirect to donation pages

  // prepare field for error
  // const feedback = document.createElement('p');
  // feedback.classList.add('invalid-feedback');
  // feedback.innerHTML = form.getAttribute('data-minimum-error');
  // if (form.amount) form.amount.parentNode.insertBefore(feedback, form.amount.nextSibling);
  // else if (form['free-amount']) form['free-amount'].parentNode.insertBefore(feedback, form['free-amount'].nextSibling);

  // // remove option to set suggested if amount has been changed + check minimum
  // form.addEventListener('input', e => {
    // let minimumValue = 0;
    // // if user change suggested input we stop suggesting...
    // if ( form.classList.contains('js-donation-basic') ) form.classList.remove('js-donation-basic');

    // // on input change we check the minium
    // if ( form.classList.contains('js-donation-launcher-form') ) {
      // if ( form.frequency.value == 'Y' ) minimumValue = form.getAttribute('data-minimum-regular');
      // else minimumValue = form.getAttribute('data-minimum-oneoff');
      
      // console.log(minimumValue);

      // const invalid = validate.single(
        // e.target.value,
        // {
          // numericality: {
            // onlyInteger: true,
            // greaterThanOrEqualTo: parseInt(minimumValue),
          // }
        // }
      // );

      // if (invalid) {
        // e.target.classList.add('is-invalid');
      // } else {
        // e.target.classList.remove('is-invalid');
      // }

    // }
  // });


}
