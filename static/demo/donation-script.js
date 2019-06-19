// Force wide blocks outside the container
var $ = jQuery;

$(document).ready(function() {
  // Wrap form elements to sections

  $('.en__component.en__component--formblock').wrapAll('<div class="swiper-container maxi-swiper maxi-swiper-centered cards-swiper"><div class="swiper-wrapper"></div></div>');
  
  $('label').addClass('en__field__top__label');
  $('.en__field__element').addClass('form-group');

  // $('.en__field--paycurrency, .en__field--donationAmt').wrapAll('<div class="swiper-slide card-explainer"></div>');
  // $('.en__field--firstName, .en__field--lastName, .en__field--address1, .en__field--city, .en__field--postcode, .en__field--country, .en__field--emailAddress').wrapAll('<div class="swiper-slide card-explainer"></div>');
  // $('.en__field--paymenttype, .en__field--ccnumber, .en__field--ccexpire, .en__field--ccvv').wrapAll('<div class="swiper-slide card-explainer"></div>');

  /*
  // Add headings on sections
  $('<div class="formsection-heading formsection-heading-amount"><span class="badge badge-light">1</span>Your Gift Amount</div>').insertBefore('.donations-formsection-amount');
  $('<div class="formsection-heading formsection-heading-info"><span class="badge badge-light">2</span>Your Name &amp; Address</div>').insertBefore('.donations-formsection-info');
  $('<div class="formsection-heading formsection-heading-payment"><span class="badge badge-light">3</span>Your Gift Payment Method</div>').insertBefore('.donations-formsection-payment');

  // Add next buttons on sections
  $('.donations-formsection-amount').append('<button class="btn btn-primary btn-block formsection-next" type="button">Next</button>');
  $('.donations-formsection-info').append('<button class="btn btn-primary btn-block formsection-next" type="button">Next</button>');
  $('.donations-formsection-payment').append('<button class="btn btn-primary btn-block formsection-next" type="button">Next</button>');

  // Add informative text
  $('.en__field--checkbox').prepend('<div class="tax-text">Please note that donations to Greenpeace International are not tax-deductable.</div>');
  $('.en__field--checkbox').prepend('<div class="secure-text">This is a secure webpage. We do not store your credit card information, and your personal data is subject to Greenpeace\'s privacy policy.</div>');

  // Show the first section
  $('.donations-formsection-amount').show();

  function sectionIsComplete($formSection) {
    var isComplete = true;
    $formSection.find('.en__mandatory input, .en__mandatory select').each(function() {
      if ($(this).val() == '') {
        isComplete = false;
      }
    });
    return isComplete;
  }

  // Next buttons visibility trigger
  $('.formsection-next').click(function() {
    var $formSection = $(this).parent();

    if (!sectionIsComplete($formSection)) {
      $('.en__submit button').click();
    } else {
      $formSection.slideUp();
      $formSection.next().next().slideDown();
    }
  });

  // Expand section on heading click
  $('.formsection-heading').click(function() {
    $('div[class*="donations-formsection-"]').slideUp();
    $(this).next('div').slideDown();
  });

  // Expand section on error
  $('body').on('DOMNodeInserted', '.en__field__error', function () {
    window.setTimeout(function() {
      var $firstError = $('.en__field__error').first();
      if (!$firstError.is(':visible')) {
        $('div[class*="donations-formsection-"]').slideUp();
        $firstError.parent().parent().slideDown();
      }
      $('.en__field--validationFailed input').first().focus();
      window.scrollTo(0, $('.en__field--validationFailed').first().offset().top - 100);
    }, 100);
  });
  */
});