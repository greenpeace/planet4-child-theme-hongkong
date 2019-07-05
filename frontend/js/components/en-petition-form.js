import { setFacebookUserInfo } from './facebook';

/**
 * Functionality for the EN form
 *
 * - add signatures bar
 * - open/close
 * - add FB button
 */
export default function() {
  const form = document.querySelector('#enform');

  const cta = document.querySelector('#p4en_form_save_button');

  if (!form || !cta || !document.querySelector('#petition-source')) return;

  const petitionSource = document
    .querySelector('#petition-source')
    .getAttribute('data-petition');

  // const stats = document.createElement('div');
  // stats.classList.add('signatures');
  // stats.innerHTML =
  //   '	<div class="progress-bar"> \
  // 		<div class="percent" style="width: 90%"></div> \
  // 	</div> \
  // 	<div class="stats">已有2,347 名簽署者</div>';

  const stats = document.createElement('div');
  stats.classList.add('signatures');
  stats.innerHTML = document.querySelector('#petition-source').innerHTML;

  const close = document.createElement('div');
  close.classList.add('close');
  close.innerHTML = '×';

  const ctaFacebook = document.createElement('button');
  ctaFacebook.classList.add('button', 'fb', 'js-sign-facebook');
  ctaFacebook.innerHTML = 'Facebook';

  const checkboxCheckall = document.querySelector(
    '#en__field_supporter_all_check'
  );

  form.insertBefore(stats, form.firstChild);
  form.insertBefore(close, form.firstChild);
  cta.parentNode.insertBefore(ctaFacebook, cta);

  cta.addEventListener('click', e => {
    if (!form.classList.contains('is-open')) {
      e.preventDefault();
      form.classList.add('is-open');
      document.body.classList.add('has-open-form');
      // submitArea.append(ctaFacebook);
    } else {
      // e.preventDefault();
      let thankyouUrl = form.getAttribute('data-redirect-url');
      let fn = document.getElementsByName('supporter.firstName')[0].value;
      thankyouUrl += '?fn=' + fn + '&pet=' + petitionSource;
      form.setAttribute('data-redirect-url', thankyouUrl);
      // form.querySelector('form').submit()
    }
  });

  ctaFacebook.addEventListener('click', e => {
    e.preventDefault();

    if (
      FB &&
      (!form.querySelector('[name="supporter.emailAddress"]').value ||
        form.classList.contains('is-open'))
    ) {
      FB.login(
        function(response) {
          if (response.status === 'connected') {
            setFacebookUserInfo(form);
          } else {
            console.error('User cancelled login or did not fully authorize.');
            // social login interrupted
          }
          // }, { scope: 'email,user_birthday' });
        },
        { scope: 'email' }
      );
    }

    if (!form.classList.contains('is-open')) {
      form.classList.add('is-open');
      document.body.classList.add('has-open-form');
    }
  });

  // move email field "above the fold" and open form on focus
  const emailField = form.querySelector('.en__field--emailAddress');
  const formBlock = form.querySelector('.formblock-flex');
  if (emailField) {
    formBlock.parentNode.insertBefore(emailField, formBlock);
    emailField.querySelector('input').addEventListener('focus', () => {
      form.classList.add('is-open');
      document.body.classList.add('has-open-form');
    });
  }

  close.addEventListener('click', e => {
    form.classList.remove('is-open');
    document.body.classList.remove('has-open-form');
  });

  // required by korea: if checkbox with "check_all" feature present
  if (checkboxCheckall) {
    checkboxCheckall.addEventListener('click', e => {
      let privacyOptions = document.querySelectorAll('[type=checkbox]');
      for (var i = 0, elementOption; (elementOption = privacyOptions[i++]); ) {
        elementOption.checked = checkboxCheckall.checked;
      }
    });
  }
}
