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
  const validateBlock = validation(form, donationLexicon);

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
    form.querySelector("input[name='transaction.ccnumber']").value = ccNumber.value.replace(/\s/g, '');
		let ccType = form.querySelector("input[name='transaction.paymenttype']");
		if (ccType) ccType.value = detectCardType(ccNumber.value.replace(/\s/g, ''));
	}
	
    if (!validateBlock(block, 'payment')) {
      e.preventDefault();
      Scroll.animateScroll(block.querySelector('.is-invalid'));
      // block.querySelector('.is-invalid').focus();
      return false;
    } 
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
        jcb: /^(?:2131|1800|35\d{3})\d{11}$/
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

function getUrlParameters(parameter, staticURL, decode){
  /*
   Function: getUrlParameters
   Description: Get the value of URL parameters either from 
                current URL or static URL
   Author: Tirumal
   URL: www.code-tricks.com
  */
  const currLocation = (staticURL.length)? staticURL : window.location.search;
  if (currLocation.indexOf('?') == -1) return false;
  
  let parArr = currLocation.split("?")[1].split("&"),
      returnBool = true;
  
  for(var i = 0; i < parArr.length; i++){
       let parr = parArr[i].split("=");
       if(parr[0] == parameter){
           return (decode) ? decodeURIComponent(parr[1]) : parr[1];
           returnBool = true;
       }else{
           returnBool = false;            
       }
  }
  
  if(!returnBool) return false;  
}

function checkUrlParameters() {
	
  // let campaignActive = getUrlParameters("campaign", "", true);
  // if (window.NRO_PROPERTIES[NRO].backgroundStyle.campaignActive.image.backgroundImage) ;
	
  let amountValue = getUrlParameters("transaction.donationAmt", "", true);
	if (amountValue) {
    console.log('sss');
    document.querySelector('button.js-amount-next').click();
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

  checkUrlParameters();

  countriesMenu();
});
