import Mailcheck from 'mailcheck';

export default function () {
  const $ = jQuery;

  let $section,
    $firstnameField,
    $lastnameField,
    $emailField;

  let requiredMsg,
    emailFormatErrMsg;

  /**
   * Collect the form values.
   * @return Object {firstname:string, lastname:string, email:string}
   */
  const formValues = function () {
    let values = {
      firstname: $firstnameField.find('input').val(),
      lastname: $lastnameField.find('input').val(),
      email: $emailField.find('input').val(),
      campaignId: $section.find('[name="sf_campaign_id"]').val(),
      optIn: $section.find('[name="supporter.emailok"]').is(":checked") ? 1 : 0,
    }
    return values;
  }

  /**
   * Only do validation after touched
   */
  const touchedFields = function () {
    return {
      firstname: $firstnameField.find('input').attr('data-touched'),
      lastname: $lastnameField.find('input').attr('data-touched'),
      email: $emailField.find('input').attr('data-touched'),
      campaignId: $section.find('[name="sf_campaign_id"]').attr('data-touched')
    }
  }

  const markTouched = function (e) {
    if (e==='ALL') {
        $('input').attr('data-touched', "1");
    } else {
        $(e.target).attr('data-touched', "1");
    }
  }

  const doValidation = function () {
    let values = formValues($section),
      touched = touchedFields(),
      allpass = true

    // validate the names
    if (touched.firstname) {
      toggleFieldRequiredMsg($firstnameField, !values.firstname);
    }
    if (touched.lastname) {
      toggleFieldRequiredMsg($lastnameField, !values.lastname);
    }

    allpass = allpass && values.firstname && values.lastname;

    // validate emails
    let emailPass
    if (touched.email) {
      emailPass = doEmailValidation()
    }
    allpass = allpass && emailPass

    return allpass
  }

  const doEmailValidation = function () {
    let values = formValues($section)

    // should follow the email format
    const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    let emailFormatPass = re.test(values.email)
    toggleEmailFormatErrMsg($emailField, !emailFormatPass)

    // suggest the right format
    Mailcheck.run({
      email: values.email,
      topLevelDomains: [
        'tw', 'com.tw',
        'hk', 'com.hk',
        'kr', 'com.kr',
        'jp', 'com.jp', 'co.jp',
        'com', 'org', 'net', 'edu', 'gov'
      ],
      secondLevelDomains: [
        'gmail', 'yahoo', 'hotmail', 'greenpeace', 'icloud', 'ymail',
        'mail', 'live', 'outlook', 'me', 'msn', 'cloud'
      ],
      suggested: function(suggestion) {
        // fetch the msg template from server side
        let tmpl = $('[name="email_do_you_mean"]').val()

        // show that element
        $emailField.find('.do-you-mean')
          .text(tmpl.replace('%s', suggestion.full))
          .show()
          .off('click')
          .on('click', function () {
            $emailField.find('input')
              .val(suggestion.full)
              .blur()
          })
      },
      empty: function() {
        $emailField.find('.do-you-mean').hide()
      }
    });

    return emailFormatPass;
  }

  /**
   * Toggle the required error messages
   * @param $field jQuery. The form element.
   * @param isErr Boolean. True is to show the required message.
   */
  const toggleFieldRequiredMsg = function ($field, isErr) {
    if ( !requiredMsg) {
        requiredMsg = $('[name="input_required_err_message"]').val()
    }

    // toggle the error class on the input element
    $field.find('input')
      .toggleClass("is-invalid", isErr, 400);

    // toggle the required error message
    $field.find('.invalid-feedback.is-required').remove()
    if (isErr) {
        $field.append('<div class="invalid-feedback is-required">'+requiredMsg+'</div>')
    }
  }

  const toggleEmailFormatErrMsg = function ($field, isErr) {
    if ( !emailFormatErrMsg) {
        emailFormatErrMsg = $('[name="email_format_err_message"]').val()
    }

    // toggle the error class on the input element
    $field.find('input').toggleClass("is-invalid", isErr);

    // toggle the required error message
    $field.find('.invalid-feedback.email-format-invalid').remove()
    if (isErr) {
        $field.append('<div class="invalid-feedback email-format-invalid">'+emailFormatErrMsg+'</div>').show()
    }
  }

  const doSubmit = function (e) {
    markTouched('ALL')

    // check all the inputs
    let allpass = doValidation();
    if ( !allpass) {
      return false;
    }

    // prepare the submit data
    let values = formValues($section)
    let postData = {
      FirstName: values.firstname,
      LastName: values.lastname,
      Email: values.email,
      CampaignId: values.campaignId,
      OptIn: values.optIn
    }

    // prepare the utm values
    const urlParams = new URLSearchParams(window.location.search);
    postData = Object.assign(postData, {
      UtmMedium: urlParams.get("utm_medium"),
      UtmSource: urlParams.get("utm_source"),
      UtmCampaign: urlParams.get("utm_campaign"),
      UtmContent: urlParams.get("utm_content"),
      UtmTerm: urlParams.get("utm_term"),
    })

    // also put in the current URL
    postData = Object.assign(postData, {
      CampaignData1__c: "URL: "+window.location.href,
      CampaignData2__c: "",
      CampaignData3__c: "",
      CampaignData4__c: "",
      CampaignData5__c: "",
    })

    let endpoint = $section.find('form').attr('action');

    // post it
    toggleSubmitLoading(true)
    toggleServerError(false)

    $.ajax({
      url: endpoint,
      method: 'POST',
      data: postData,
      dataType: 'json',
      success: function (data, status) {
        toggleSubmitLoading(false)
        $section
          .find('.ct-container.subscribe').addClass('hide')
          .parent()
          .find('.ct-container.thankyou').removeClass('hide')

        // trigger ga subscription event
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          'event' : 'gaEvent',
          'eventCategory': 'mc-subscription',
          'eventAction': 'subscribe',
          'eventLabel': '',
          'eventValue': ''
        });
      },
      error: function (jqXHR, textStatus) {
        console.log('error', jqXHR, textStatus)
        toggleSubmitLoading(false)
        toggleServerError(true, '('+jqXHR.status+') '+jqXHR.statusText)
      }
    })

    return false;
  }

  const toggleSubmitLoading = (isLoading) => {
    let $submit = $section.find('button.submit').toggleClass('loading', isLoading);
  }

  const toggleServerError = (isErr, errMsg) => {
    let $notice = $section.find('.enform-notice')

    if (isErr) {
      $notice
        .text($section.find('[name="server_error_message"]').val()+' '+errMsg)
        .show()
    } else {
      $notice.hide()
    }
  }

  // init functions
  $(function () {
    // assign the init values
    $section = $(".section-mc-subscription");
    $firstnameField = $section.find('[name="supporter.firstname"]').closest('.en__field__element');
    $lastnameField = $section.find('[name="supporter.lastname"]').closest('.en__field__element');
    $emailField = $section.find('[name="supporter.email"]').closest('.en__field__element');

    $("input[required]").blur(doValidation)
    $("input[required]").blur(markTouched)

    $section.find('button.submit').on('click', doSubmit)
  });
}
