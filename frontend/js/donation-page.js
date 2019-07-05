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
import donationTabs from './components/donation-tabs';
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

  // Activate swiper
  const donationSwiper = swiper(Swiper);

  // Init mask/validation logic
  const form = document.querySelector('#enform form');
  mask(form);
  const validateBlock = validation(form);

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
    if (!validateBlock(block, 'payment')) {
      e.preventDefault();
      Scroll.animateScroll(block.querySelector('.is-invalid'));
      // block.querySelector('.is-invalid').focus();
      return false;
    }
  });
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
