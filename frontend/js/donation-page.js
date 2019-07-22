import {
  Swiper,
  Navigation,
  Pagination,
  Scrollbar,
  Controller,
  Autoplay,
} from 'swiper/dist/js/swiper.esm.js';
import SmoothScroll from 'smooth-scroll';

import polyfills from './components/polyfills';

import trapFocus from './components/trap-focus';
import donationTabs from './Donation/donation-tabs';

import setLexicon from './Donation/lexicon';

import geoSelect from './Donation/geo-select';

import countriesMenu from './components/countries-menu';

import swiper from './Donation/swiper';
import validation from './Donation/validation';
import prevNext from './Donation/prevNext';
import mask from './Donation/masks';

Swiper.use([Navigation, Pagination, Scrollbar, Controller, Autoplay]);

polyfills();

function initDonation(Swiper, Scroll) {
  // Quit if we aren't in a donation page
  if (!document.querySelector('.page-template-donation')) return;

  // Activate radio-to-tabs
  donationTabs();

  // lexiconise.
  const donationLexicon = setLexicon();

  // populate select with country ecc.
  geoSelect(donationLexicon);

  // Activate swiper
  const donationSwiper = swiper(Swiper);

  // Init mask/validation logic
  const form = document.querySelector('#enform form');
  mask(form);
  let validateBlock = validation(form, donationLexicon);

  // add question mark for cvv label
  const labelCCVV = document.querySelectorAll(
    '.en__field--ccvv .en__field__label'
  );
  const ccvvOpener = document.createElement('span');
  const ccvvInfoBox = document.querySelectorAll('.payment-questions');
  const ccvvCloseButton = document.querySelectorAll(
    '.payment-questions .cvv-close'
  );
  ccvvOpener.classList.add('label-question-icon', 'js-open-ccvv-info');

  //labelCCVV[0].insertAdjacentHTML('beforeend', ccvvOpener);
  labelCCVV[0].append(ccvvOpener);

  if (ccvvInfoBox) {
    ccvvOpener.addEventListener('click', e => {
      // ccvvInfoBox[0].style.display = 'block';
      ccvvInfoBox[0].classList.add('is-visible');
    });

    if (ccvvCloseButton) {
      ccvvCloseButton[0].addEventListener('click', e => {
        // ccvvInfoBox[0].style.display = 'none';
        ccvvInfoBox[0].classList.remove('is-visible');
      });
    }
  }

  // Trap focus
  trapFocus(
    donationSwiper.slides[0],
    donationSwiper.slides[0].querySelectorAll('input')[0],
    donationSwiper.slides[0].querySelector('.js-amount-next'),
    false,
    true
  );
  trapFocus(
    donationSwiper.slides[1],
    donationSwiper.slides[1].querySelectorAll('input, select')[0],
    donationSwiper.slides[1].querySelector('.js-to-payment'),
    true,
    true
  );
  trapFocus(
    donationSwiper.slides[0],
    donationSwiper.slides[2].querySelectorAll('input, select')[0],
    donationSwiper.slides[2].querySelector('button.primary'),
    true,
    false
  );

  // Activate prev/next buttons
  prevNext(form, donationSwiper, validateBlock, Scroll);

  checkUrlParameters(donationSwiper);

  // Submit safeguard
  form.addEventListener('submit', e => {
    // Prevent enForm.js from messing with our validation
    e.stopImmediatePropagation();

    const block = donationSwiper.slides[donationSwiper.activeIndex];

    // Are we on step 1 or 2? Click on the "NEXT" button instead of submitting the form
    if (donationSwiper.activeIndex === 0) {
      e.preventDefault();

      form.querySelector('.js-amount-next').click();
      return false;
    } else if (donationSwiper.activeIndex === 1) {
      e.preventDefault();
      form.querySelector('.js-to-payment').click();
      return false;
    }

    // We are on the final step. Validate the payment slide and proceed.
    // first assign the credit card type based upon number
    let ccNumber = form.querySelector("input[name='transaction.ccnumber']");
    if (ccNumber) {
      // form.querySelector(
      //   "input[name='transaction.ccnumber']"
      // ).value = ccNumber.value.replace(/\s/g, '');
      let ccType = form.querySelector("input[name='transaction.paymenttype']");
      if (ccType)
        ccType.value = detectCardType(ccNumber.value.replace(/\s/g, ''));
    }

    if (!validateBlock(block, 'payment')) {
      e.preventDefault();
      Scroll.animateScroll(block.querySelector('.is-invalid'));
      // block.querySelector('.is-invalid').focus();
      return false;
    }

    e.preventDefault();
    jQuery('button.primary').prop('disabled', true);
    jQuery('button.primary').addClass('loading');

    // add GA event
    dataLayer.push({
      'event' : 'donationEvent',
      'eventCategory' : 'donations',
      'eventAction' : 'form_interaction',
      'eventLabel' : 'completed:credit_cards',
    });

    // let data = jQuery(form).serialize();
    let data = jQuery(form).serializeArray();
    
    // modify birth date and credit card number before submitting
    for (var item in data)
    {
      if (data[item].name == 'supporter.NOT_TAGGED_6') {
        let birthDate = data[item].value;
        birthDate = birthDate.split('/');
        birthDate = birthDate[1] + '/' + birthDate[0] + '/' + birthDate[2];
        data[item].value = birthDate;
      } else if (data[item].name == 'transaction.ccnumber') {
        data[item].value = data[item].value.replace(/\s/g, '');
      }
    }

    let dataForm = jQuery.param(data);
    
    let amountSent = jQuery("input[name='transaction.donationAmt']").val();
    let recurringSent = ( jQuery("input[name='supporter.NOT_TAGGED_31']").val() == 'N' ) ? 'single' : 'recurring';

    if(typeof fbq !== 'undefined') {
      fbq('track', 'AddPaymentInfo', {
        content_name: campaignName
      });      
    }

    jQuery
      .ajax({
        url: '2',
        method: 'POST',
        data: dataForm,
        dataType: 'html',
      })
      .done(function(t) {        
        jQuery('button').prop('disabled', false);
        jQuery('button').removeClass('loading');

        // CODE CHANGED TO REFLECT CURRENT FLOW 

        // console.log(t);
        var i = jQuery.parseHTML(t, !0);
        var s = jQuery(i).find('.en__errorHeader');
        var a = jQuery(i).find('.en__errorList');
        var n = jQuery(i).find('#thankyou-copy');

        // prepare the new pageJsonReplicated

        // var pageJsonReplicated = pageJson;
        pageJsonReplicated.amount = amountSent;
        pageJsonReplicated.country = jQuery("input[name='supporter.country']").val();
        pageJsonReplicated.currency = window.NRO_PROPERTIES['HK'].currency;
        pageJsonReplicated.pageNumber = 2;
        pageJsonReplicated.recurring = ( jQuery("input[name='supporter.NOT_TAGGED_31']").val() == 'N' ) ? 'false' : 'true';       
        pageJsonReplicated.paymentType = jQuery("input[name='transaction.paymenttype']").val();                

        if (s.length > 0) {
          pageJsonReplicated.giftProcess = false;
          // console.log(s);
          // console.log(a);
          jQuery('.en__errorHeader').remove();
          jQuery('.en__errorList').remove();
          jQuery('.credit-card__row').prepend(s[0].outerHTML + a[0].outerHTML);
          jQuery('#enform').css('padding-bottom', '100px');
          dataLayer.push({
            'event' : 'donationEvent',
            'eventCategory' : 'donations',
            'eventAction' : 'fail',
            'eventLabel' : recurringSent,
            'eventValue' : amountSent
          });
          // e.submitErrorHtml=s[0].outerHTML+a[0].outerHTML, e.submitError=!0, e.pageFn.retrySubmissionCount+=1, ""!=e.pageProps.ga_tracking_id&&ga("send", "event", "donations", "fail", e.isRecurring?"recurring": "single");
        } else {

          pageJsonReplicated.giftProcess = true;

          dataLayer.push({
            'event' : 'donationEvent',
            'eventCategory' : 'donations',
            'eventAction' : 'succeed',
            'eventLabel' : recurringSent,
            'eventValue' : amountSent
          });
          
          if(typeof fbq !== 'undefined') {
            fbq('track', 'Purchase', {
              value: amountSent, 
              content_name: campaignName, 
              currency: pageJsonReplicated.currency, 
              content_category: 'donations' });            
          }

          jQuery('.js-step-payment').removeClass('is-current');
          jQuery('.js-step-payment').removeClass('is-todo');
          jQuery('.js-step-payment').addClass('is-done');
          jQuery('.en__component--column').html(n);          

          // e.submitError=!1, e.submitErrorHtml="", n.appendTo(y()(e.$refs.page3)), console.log(thankyouPageIsRecurring, thankyouPageDonationAmount), thankyouPageIsRecurring="Y"==thankyouPageIsRecurring?"recurring":"single", thankyouPageDonationAmount=parseInt(/\$(\d+)\.00/.exec(thankyouPageDonationAmount)[1]), ""!=e.pageProps.ga_tracking_id&&ga("send", "event", "donations", "succeed", thankyouPageIsRecurring, thankyouPageDonationAmount), ""!=e.pageProps.fb_pixel_id&&fbq("track", "Purchase", {
          //     value: thankyouPageDonationAmount, currency: "HK"==e.nro?"HKD": "EA"==e.nro?"HKD": "TWD", content_category: "donations", content_type: thankyouPageIsRecurring, content_name: e.pageProps.campaign
          // }
          // ), e.currentPage+=1, e.pageFn.isPaymentSuccess=!0;
          // for(var r in e.fields)delete e.fields[r];
          // delete e.temp.card_number, delete e.temp.card_expiration_date
        }

        // if(e.pageFn.btnState="next", e.pageFn.isPaymentProceeding=!1, s.length>0)e.submitErrorHtml=s[0].outerHTML+a[0].outerHTML, e.submitError=!0, e.pageFn.retrySubmissionCount+=1, ""!=e.pageProps.ga_tracking_id&&ga("send", "event", "donations", "fail", e.isRecurring?"recurring": "single");
        // else {
        //     e.submitError=!1, e.submitErrorHtml="", n.appendTo(y()(e.$refs.page3)), console.log(thankyouPageIsRecurring, thankyouPageDonationAmount), thankyouPageIsRecurring="Y"==thankyouPageIsRecurring?"recurring":"single", thankyouPageDonationAmount=parseInt(/\$(\d+)\.00/.exec(thankyouPageDonationAmount)[1]), ""!=e.pageProps.ga_tracking_id&&ga("send", "event", "donations", "succeed", thankyouPageIsRecurring, thankyouPageDonationAmount), ""!=e.pageProps.fb_pixel_id&&fbq("track", "Purchase", {
        //         value: thankyouPageDonationAmount, currency: "HK"==e.nro?"HKD": "EA"==e.nro?"HKD": "TWD", content_category: "donations", content_type: thankyouPageIsRecurring, content_name: e.pageProps.campaign
        //     }
        //     ), e.currentPage+=1, e.pageFn.isPaymentSuccess=!0;
        //     for(var r in e.fields)delete e.fields[r];
        //     delete e.temp.card_number, delete e.temp.card_expiration_date
        // }

        //console.log(data);

        //   if(this.validateCreditCard(!0)) {
        //     this.pageFn.isPaymentPageError=!1, this.pageFn.btnState="loading", this.pageFn.isPaymentProceeding=!0, ""!=this.pageProps.ga_tracking_id&&ga("send", "event", "donations", "form_interaction", "submit:"+this.pageFn.retrySubmissionCount);
        //     var e=this;
        //     y.a.ajax( {
        //         url: "2", method: "POST", data: y()(this.$refs.enform).serialize(), dataType: "html"
        //     }
        //     ).done(function(t) {
        //         var i=y.a.parseHTML(t, !0), s=y()(i).find(".en__errorHeader"), a=y()(i).find(".en__errorList"), n=y()(i).find("#thankyou-copy");
        //         if(e.pageFn.btnState="next", e.pageFn.isPaymentProceeding=!1, s.length>0)e.submitErrorHtml=s[0].outerHTML+a[0].outerHTML, e.submitError=!0, e.pageFn.retrySubmissionCount+=1, ""!=e.pageProps.ga_tracking_id&&ga("send", "event", "donations", "fail", e.isRecurring?"recurring": "single");
        //         else {
        //             e.submitError=!1, e.submitErrorHtml="", n.appendTo(y()(e.$refs.page3)), console.log(thankyouPageIsRecurring, thankyouPageDonationAmount), thankyouPageIsRecurring="Y"==thankyouPageIsRecurring?"recurring":"single", thankyouPageDonationAmount=parseInt(/\$(\d+)\.00/.exec(thankyouPageDonationAmount)[1]), ""!=e.pageProps.ga_tracking_id&&ga("send", "event", "donations", "succeed", thankyouPageIsRecurring, thankyouPageDonationAmount), ""!=e.pageProps.fb_pixel_id&&fbq("track", "Purchase", {
        //                 value: thankyouPageDonationAmount, currency: "HK"==e.nro?"HKD": "EA"==e.nro?"HKD": "TWD", content_category: "donations", content_type: thankyouPageIsRecurring, content_name: e.pageProps.campaign
        //             }
        //             ), e.currentPage+=1, e.pageFn.isPaymentSuccess=!0;
        //             for(var r in e.fields)delete e.fields[r];
        //             delete e.temp.card_number, delete e.temp.card_expiration_date
        //         }
        //     }
        //     )
        // }
        // else this.pageFn.isPaymentPageError=!0
      })
      .fail(function(errorThrown) {
        console.log(errorThrown);
      });

    return false;
  });
}

function detectCardType(number) {
  var re = {
    electron: /^(4026|417500|4405|4508|4844|4913|4917)\d+$/,
    maestro: /^(5018|5020|5038|5612|5893|6304|6759|6761|6762|6763|0604|6390)\d+$/,
    dankort: /^(5019)\d+$/,
    interpayment: /^(636)\d+$/,
    unionpay: /^(62|88)\d+$/,
    visa: /^4[0-9]{12}(?:[0-9]{3})?$/,
    mastercard: /^5[1-5][0-9]{14}$/,
    amex: /^3[47][0-9]{13}$/,
    diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/,
    discover: /^6(?:011|5[0-9]{2})[0-9]{12}$/,
    jcb: /^(?:2131|1800|35\d{3})\d{11}$/,
  };
  if (re.electron.test(number)) {
    return 'ELECTRON';
  } else if (re.maestro.test(number)) {
    return 'MAESTRO';
  } else if (re.dankort.test(number)) {
    return 'DANKORT';
  } else if (re.interpayment.test(number)) {
    return 'INTERPAYMENT';
  } else if (re.unionpay.test(number)) {
    return 'UNIONPAY';
  } else if (re.visa.test(number)) {
    return 'VISA';
  } else if (re.mastercard.test(number)) {
    return 'MASTERCARD';
  } else if (re.amex.test(number)) {
    return 'AMEX';
  } else if (re.diners.test(number)) {
    return 'DINERS';
  } else if (re.discover.test(number)) {
    return 'DISCOVER';
  } else if (re.jcb.test(number)) {
    return 'JCB';
  } else {
    return undefined;
  }
}

function getUrlParameters(parameter, staticURL, decode) {
  /*
   Function: getUrlParameters
   Description: Get the value of URL parameters either from
                current URL or static URL
   Author: Tirumal
   URL: www.code-tricks.com
  */
  const currLocation = staticURL.length ? staticURL : window.location.search;
  if (currLocation.indexOf('?') == -1) return false;

  let parArr = currLocation.split('?')[1].split('&'),
    returnBool = true;

  for (var i = 0; i < parArr.length; i++) {
    let parr = parArr[i].split('=');
    if (parr[0] == parameter) {
      return decode ? decodeURIComponent(parr[1]) : parr[1];
      returnBool = true;
    } else {
      returnBool = false;
    }
  }

  if (!returnBool) return false;
}

function checkUrlParameters(donationSwiper) {
  // let campaignActive = getUrlParameters("campaign", "", true);
  // if (window.NRO_PROPERTIES[NRO].backgroundStyle.campaignActive.image.backgroundImage) ;

  let amountValue = getUrlParameters('transaction.donationAmt', '', true);
  if (amountValue) {
    document.querySelector('button.js-amount-next').click();
  }

  let alreadySubmitted = getUrlParameters('val', '', true);
  if (alreadySubmitted) {
    donationSwiper.slideTo(1);
    const stepAmount = document.querySelector('.js-step-amount');
    const stepDetails = document.querySelector('.js-step-details');
    const stepPayment = document.querySelector('.js-step-payment');
    stepAmount.classList.remove('is-current');
    stepAmount.classList.add('is-done');
    stepDetails.classList.remove('is-done');
    stepDetails.classList.add('is-current');
    // stepPayment.classList.add('is-current');
  }
}

document.addEventListener('DOMContentLoaded', function(event) {
  const Scroll = new SmoothScroll('a[href*="#"]', {
    header: 'body > header',
    offset: 40,
    updateURL: false,
    speed: 400,
    speedAsDuration: true,
  });

  initDonation(Swiper, Scroll);

  countriesMenu();
});
