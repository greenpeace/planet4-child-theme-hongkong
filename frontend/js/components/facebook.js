/* export default (function() {
  function _onClick(e) {
    e.preventDefault();
    // $('.form-petition').addClass('active');
    // launch event to track who click on facebook
    // dataLayer.push({'event':'event_facebook_click','petition': 'savethearctic_' + Petition.petitionTrack + ''});

    const $form = $(e.target).closest('form');

    FB.login(
      function(response) {
        if (response.status === 'connected') {
          _setUserInfo($form);
        } else {
          console.error('User cancelled login or did not fully authorize.');
          // social login interrupted
        }
        // }, { scope: 'email,user_birthday' });
      },
      { scope: 'email' }
    );
  }

  function _setUserInfo($form) {
    // dataLayer.push({'event':'event_facebook_complete','petition': 'savethearctic_' + Petition.petitionTrack + ''});

    FB.api('/me?fields=first_name,last_name,email,birthday', function(
      response
    ) {
      Array.prototype.forEach.call(_byName('supporter.emailAddress'), function(
        el
      ) {
        el.value = response.email;
        _triggerInput(el);
        // $(el).parent('.field-group').addClass('is-active');
      });

      Array.prototype.forEach.call(_byName('supporter.firstName'), function(
        el
      ) {
        el.value = response.first_name;
        _triggerInput(el);
        // $(el).parent('.field-group').addClass('is-active');
      });

      // Array.prototype.forEach.call(_byName('last_name'), function(el) {
      // 	el.value = response.last_name;
      // 	$(el).parent('.field-group').addClass('is-active');
      // 	_triggerInput(el);
      // });

      // if (response.birthday) {
      // 	const parts = response.birthday.split('/');
      // 	const birthDate = `${parts[1]}-${parts[0]}-${parts[2]}`;
      // 	const birthYear = parts[2];

      // 	Array.prototype.forEach.call(_byName('birth_date'),
      // 		function(el) {
      // 			el.value = birthDate;
      // 			_triggerInput(el);
      // 			$(el).parent('.field-group').addClass('is-active');
      // 		}
      // 	);

      // 	Array.prototype.forEach.call(_byName('birth_year'),
      // 		function(el) {
      // 			el.value = birthYear;
      // 			_triggerInput(el);
      // 			$(el).parent('.field-group').addClass('is-active');
      // 		}
      // 	);
      // }

      // Array.prototype.forEach.call(_byName('facebook_login'),
      // 	function(el) {
      // 		el.value = 1;
      // 		_triggerInput(el);
      // 	}
      // );

      // social login completed
    });
  }

  function _byName(name) {
    return document.getElementsByName(name);
  }

  function _triggerInput(el) {
    const event = document.createEvent('HTMLEvents');
    event.initEvent('input', true, false);
    el.dispatchEvent(event);
  }

  function init($el) {
    $el.on('click', _onClick);
  }

  return { init };
})(); */

export const setFacebookUserInfo = function() {
  // dataLayer.push({'event':'event_facebook_complete','petition': 'savethearctic_' + Petition.petitionTrack + ''});

  // FB.api('/me?fields=first_name,last_name,email,birthday', function(response) {
  FB.api('/me?fields=first_name,last_name,email', function(response) {
    Array.prototype.forEach.call(_byName('supporter.emailAddress'), function(
      el
    ) {
      el.value = response.email;
      _triggerInput(el);
      // $(el).parent('.field-group').addClass('is-active');
    });

    Array.prototype.forEach.call(_byName('supporter.firstName'), function(el) {
      el.value = response.first_name;
      _triggerInput(el);
      // $(el).parent('.field-group').addClass('is-active');
    });

    // Array.prototype.forEach.call(_byName('last_name'), function(el) {
    // 	el.value = response.last_name;
    // 	$(el).parent('.field-group').addClass('is-active');
    // 	_triggerInput(el);
    // });

    // if (response.birthday) {
    // 	const parts = response.birthday.split('/');
    // 	const birthDate = `${parts[1]}-${parts[0]}-${parts[2]}`;
    // 	const birthYear = parts[2];

    // 	Array.prototype.forEach.call(_byName('birth_date'),
    // 		function(el) {
    // 			el.value = birthDate;
    // 			_triggerInput(el);
    // 			$(el).parent('.field-group').addClass('is-active');
    // 		}
    // 	);

    // 	Array.prototype.forEach.call(_byName('birth_year'),
    // 		function(el) {
    // 			el.value = birthYear;
    // 			_triggerInput(el);
    // 			$(el).parent('.field-group').addClass('is-active');
    // 		}
    // 	);
    // }

    // Array.prototype.forEach.call(_byName('facebook_login'),
    // 	function(el) {
    // 		el.value = 1;
    // 		_triggerInput(el);
    // 	}
    // );

    // social login completed
  });
};

function _byName(name) {
  return document.getElementsByName(name);
}

function _triggerInput(el) {
  const event = document.createEvent('HTMLEvents');
  event.initEvent('input', true, false);
  el.dispatchEvent(event);
}
