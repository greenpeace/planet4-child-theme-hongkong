/**
 * Activates tabs on donation forms.
 *
 * When you click a tab, the hidden field "frequency" gets updated with
 * the clicked tab's text content.
 *
 */
import validate from 'validate.js'; // lightweight validation library: http://validatejs.org/

export default function() {
  document.querySelectorAll('.js-tab-item').forEach(tab => {
    tab.addEventListener('click', e => {
      const tabBar = e.target.parentElement;
      const siblings = tabBar.children;
      const form = tabBar.parentElement.querySelector('form');      
      const handles = tabBar.parentElement.querySelectorAll('.dollar-handles');
      const tabRecurring = tabBar.parentElement.querySelector('.tab-item__recurring');
      const handlesOnce = tabBar.parentElement.querySelectorAll(
        '.dollar-handles__once .dollar-handle'
      );
      const handlesRecurring = tabBar.parentElement.querySelectorAll(
        '.dollar-handles__recurring .dollar-handle'
      );
      const paragraphs = tabBar.parentElement.querySelectorAll(
        "[class^='paragraph-handles__'], [class*=' paragraph-handles__']"
      );

      /* reminder box */
      const reminderBox = document.querySelector('.donation-reminder-box');
      // if reminder box present, and we are inside dollar handles, and oneoff is clicked -> we prompt for user confirmation
      if ( handles.length && reminderBox && e.target.classList.contains('tab-item__once') ) {
        reminderBox.style.display = 'flex';
        const buttonConfirmOneOff = document.querySelector('.js-donation-confirm-oneoff');
        const buttonConfirmRegular = document.querySelector('.js-donation-confirm-regular');

        buttonConfirmOneOff.addEventListener('click', eventClick => {
          reminderBox.style.display = 'none';
          // if one off confirmed, just hide the alert
        });

        buttonConfirmRegular.addEventListener('click', eventClick => {
          reminderBox.style.display = 'none';
          tabRecurring.click();
          // if regular confirmed, trigger click over it
        });
      }    

      Array.from(siblings).forEach(sibling => {
        sibling.classList.remove('is-active');
      });
      e.target.classList.add('is-active');      

      let invalidFeedback = document.querySelectorAll('.invalid-feedback');      

      // handle donation suggested for basic form
      if ( form.classList.contains('js-donation-basic') ) {
        if ( ( e.target.classList.contains('tab-item__once') ) && form.getAttribute('data-suggested-oneoff') ) {
          form.amount.value = form.getAttribute('data-suggested-oneoff');          
        } else if ( ( e.target.classList.contains('tab-item__recurring') ) && form.getAttribute('data-suggested-regular') ) {
          form.amount.value = form.getAttribute('data-suggested-regular');
        }
      }

      // set error messages for minimum amount
      if ( form.classList.contains('js-donation-launcher-form') ) {
        if ( e.target.classList.contains('tab-item__once') ) {
          // set corresponding error message
          if (invalidFeedback.length) invalidFeedback[0].innerHTML = form.getAttribute('data-minimum-single-error');          
        } else if ( e.target.classList.contains('tab-item__recurring') ) {
          // set corresponding error message
          if (invalidFeedback.length) invalidFeedback[0].innerHTML = form.getAttribute('data-minimum-recurring-error');
        }
      }      

      if (handles.length) {
        const changeEvent = document.createEvent('HTMLEvents');
        changeEvent.initEvent('change', false, true);        

        Array.from(handles).forEach(handle => {
          handle.classList.remove('is-active');
        });
        Array.from(paragraphs).forEach(paragraph => {
          paragraph.classList.remove('is-active');
        });
        if (e.target.classList.contains('tab-item__once')) {          
          form
            .querySelector('.dollar-handles__once')
            .classList.add('is-active');
          form
            .querySelector('.paragraph-handles__once')
            .classList.add('is-active');
          handlesOnce[1].querySelector('input').checked = true;
          handlesOnce[1].querySelector('input').dispatchEvent(changeEvent); 
        } else if (e.target.classList.contains('tab-item__recurring')) {
          form
            .querySelector('.dollar-handles__recurring')
            .classList.add('is-active');
          form
            .querySelector('.paragraph-handles__recurring')
            .classList.add('is-active');
          handlesRecurring[1].querySelector('input').checked = true;
          handlesRecurring[1].querySelector('input').dispatchEvent(changeEvent);          
        }
      }
      // form.frequency.value = e.target.textContent.trim();
      form.frequency.value = e.target.getAttribute('data-recurring');
    });
  });
  // on form submit do redirect to donation pages
  const form = document.querySelector('.js-donation-launcher-form');
  if (!form) return;
  form.addEventListener('submit', e => {
    e.preventDefault();
    let donationUrl = new URL(form.action);
    let amountValue = '';
    let frequencyValue = '';
    let amountInput;

    if (form.amount) {
      amountValue = form.amount.value;
      amountInput = form['amount'];
    } else if (form['free-amount'] && form['free-amount'].value) {
      amountInput = form['free-amount'];
      amountValue = form['free-amount'].value;
    } else if (form['dollar-handle'] && form['dollar-handle'].value) {
      amountInput = form['free-amount'];
      amountValue = form['dollar-handle'].value;
    }

    let minimumValue;
    if ( form.frequency.value == 'Y' ) minimumValue = form.getAttribute('data-minimum-regular');
    else minimumValue = form.getAttribute('data-minimum-oneoff');

    if ( parseInt(amountValue) < parseInt(minimumValue) ) {
      amountInput.classList.add('is-invalid');
      return false;
    }
    
    if (form['en_recurring_question'] && form['en_recurring_question'].value) {
      frequencyValue = form.frequency.value;
    }

    if ('mrm' == form.en_recurring_question.value) {
      if ('N' == frequencyValue) frequencyValue = 's';
      else frequencyValue = 'm';
      
      // donationUrl.searchParams.append(
      //   'donate_amt',
      //   frequencyValue + ':' + amountValue
      // );

      donationUrl = donationUrl + '?donate_amt=' + frequencyValue + ':' + amountValue;

    } else {
      donationUrl.searchParams.append('transaction.donationAmt', amountValue);
      donationUrl.searchParams.append(
        form.en_recurring_question.value,
        frequencyValue
      );
    }

    window.location.href = donationUrl;
  });
  // prepare field for error
  const feedback = document.createElement('p');
  feedback.classList.add('invalid-feedback');
  feedback.innerHTML = form.getAttribute('data-minimum-recurring-error');
  if (form.amount) form.amount.parentNode.insertBefore(feedback, form.amount.nextSibling);
  else if (form['free-amount']) form['free-amount'].parentNode.insertBefore(feedback, form['free-amount'].nextSibling);

  // remove option to set suggested if amount has been changed + check minimum
  // form.addEventListener('input', e => {
  //   let minimumValue = 0;
  //   // if user change suggested input we stop suggesting...
  //   if ( form.classList.contains('js-donation-basic') ) form.classList.remove('js-donation-basic');

  //   // on input change we check the minium
  //   if ( form.classList.contains('js-donation-launcher-form') ) {
  //     if ( form.frequency.value == 'Y' ) minimumValue = form.getAttribute('data-minimum-regular');
  //     else minimumValue = form.getAttribute('data-minimum-oneoff');
      
  //     // console.log(minimumValue);

  //     const invalid = validate.single(
  //       e.target.value,
  //       {
  //         numericality: {
  //           onlyInteger: true,
  //           greaterThanOrEqualTo: parseInt(minimumValue),
  //         }
  //       }
  //     );

  //     if (invalid) {
  //       e.target.classList.add('is-invalid');
  //     } else {
  //       e.target.classList.remove('is-invalid');
  //     }

  //   }
  // });


}
