import 'remove-focus-outline';
import {
  Swiper,
  Navigation,
  Pagination,
  Scrollbar,
  Controller,
  Autoplay,
} from 'swiper/dist/js/swiper.esm.js';
import LazyLoad from 'vanilla-lazyload';
import SmoothScroll from 'smooth-scroll';
import AOS from 'aos';

import polyfills from './components/polyfills';

import donationTabs from './components/donation-tabs';
import dollarHandles from './components/dollar-handles';
// import followingTags from './components/following-tags';
import toggles from './components/toggles';
import ENPetitionForm from './components/en-petition-form';
import anchorMenu from './components/anchor-menu';
import countriesMenu from './components/countries-menu';
import swipers from './components/swipers';
import followUnfollow from './components/follow-unfollow';
import shareToFixed from './components/share-to-fixed';
import tips from './components/tips';
import backLinks from './components/back-links';
import parallax from './components/parallax';
import scrollWay from './components/scroll-way';
import articleList from './components/article-list';
import facebookVideo from './components/facebook-video';

import petitionThankyou from './petition-thankyou';

// import donationPage from './donation-page';

Swiper.use([Navigation, Pagination, Scrollbar, Controller, Autoplay]);

polyfills();

document.addEventListener('DOMContentLoaded', function(event) {
  new LazyLoad({
    elements_selector: '.lazy',
    threshold: 400,
  });

  const Scroll = new SmoothScroll('a[href*="#"]', {
    header: 'body > header',
    offset: 40,
    updateURL: false,
  });

  /* Components */

  donationTabs();

  dollarHandles();

  //followingTags();

  toggles();

  ENPetitionForm();

  anchorMenu();

  countriesMenu();

  swipers(Swiper);

  setTimeout(followUnfollow, 0);

  shareToFixed();

  tips();

  backLinks();

  parallax();

  scrollWay();

  articleList();

  facebookVideo();

  AOS.init({
    easing: 'ease-in-out-cubic',
    once: true,
    disable: 'phone',
  });

  /**
   * Open/close country offices megamenu (not even putting it in a separate module)
   */
  const $countryOfficesDropdown = $('header .dropdown');
  $countryOfficesDropdown.on('click', function(e) {
    e.stopPropagation();
  });
  $countryOfficesDropdown.children('button').on('click', function() {
    $countryOfficesDropdown.toggleClass('is-open');
  });
  $(window).click(function() {
    $countryOfficesDropdown.removeClass('is-open');
  });

  /* Page specific functionality */

  petitionThankyou(Scroll);
});
