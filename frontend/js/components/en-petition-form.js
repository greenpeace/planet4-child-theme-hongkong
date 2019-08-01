import { throttle } from 'throttle-debounce';

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

  // that's valid only for korea... to be added option in backend
  const checkboxCheckall = document.querySelector(
    '#en__field_supporter_questions_455891'
  );

  if (!form || !cta) return;

  // required by korea: if checkbox with "check_all" feature present
  if (checkboxCheckall) {
    checkboxCheckall.addEventListener('click', e => {
      let privacyOptions = document.querySelectorAll('[type=checkbox]');
      for (var i = 0, elementOption; (elementOption = privacyOptions[i++]); ) {
        elementOption.checked = checkboxCheckall.checked;
      }
    });
  }

  // general, for all donation we check all checkbox... yes sir, this probably can be avoided in a couple of weeks, using new feature of official plugin
  // specific for korea: assign a value to checkbox, otherwise "required" is never satisfied, and also assign required "programmatically", since plugin won't allow to set required to an input
  if ( 'ko-KR' != document.documentElement.lang ) {
    let privacyOptions = document.querySelectorAll('[type=checkbox]');
    for (var i = 0, elementOption; (elementOption = privacyOptions[i++]); ) {
      elementOption.checked = true;
    }
  } else if ( 'ko-KR' == document.documentElement.lang ) {
    let privacyOptions = document.querySelectorAll('[type=checkbox]');
    for (var i = 0, elementOption; (elementOption = privacyOptions[i++]); ) {
      if ( 'en__field_supporter_questions_455891' != elementOption.id ) elementOption.required = true;
      elementOption.addEventListener( 'change', (e) => {
        if(e.target.checked) {
          e.target.value = 'Y';
        } else {
          e.target.value = '';
        }
      });
    }
    // force translation...waiting for translated strings
    let allInputEnForm = document.querySelectorAll('input');
    for (var i = 0, elementOption; (elementOption = allInputEnForm[i++]); ) {
      if ( 'en__field_supporter_emailAddress' == elementOption.id ) {
        elementOption.setAttribute('data-errormessage', '이메일 형식이 유효하지 않습니다');
      } else {
        elementOption.setAttribute('data-errormessage', '개인정보 제공 필수 항목입니다');
      }
      
    }
  }

  if (!document.querySelector('#petition-source')) return;

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
      let fn = '';
      if (document.getElementsByName('supporter.firstName').lenght)
        fn = document.getElementsByName('supporter.firstName')[0].value;
      else if (document.getElementsByName('supporter.NOT_TAGGED_4').lenght) {
        // this is a special condition for korea... move to options?..
        fn = document.getElementsByName('supporter.NOT_TAGGED_4')[0].value;
      }
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

  // close form
  close.addEventListener('click', e => {
    form.classList.remove('is-open');
    document.body.classList.remove('has-open-form');
  });

  /* THIS HAS BEEN GENERALIZED IN ./scroll-way.js  --v

  // compact the form if I scroll down
  let lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
  // element should be replaced with the actual target element on which you have applied scroll, use window in case of no target element.
  window.addEventListener(
    'scroll',
    throttle(100, function() {
      const scrollTop =
        window.pageYOffset || document.documentElement.scrollTop; // Credits: "https://github.com/qeremy/so/blob/master/so.dom.js#L426"
      if (scrollTop > lastScrollTop) {
        // downscroll code
        form.classList.add('is-compact');
      } else {
        // upscroll code
        form.classList.remove('is-compact');
      }
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
    }),
    false
  );

  */ 

}
