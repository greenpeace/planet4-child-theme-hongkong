import {
  Swiper,
  Navigation,
  Pagination,
  Scrollbar,
  Controller,
} from 'swiper/dist/js/swiper.esm.js';
import LazyLoad from 'vanilla-lazyload';
import SmoothScroll from 'smooth-scroll';

import petitionThankyou from './petition-thankyou';
import donation from './donation';
import followUnfollow from './follow-unfollow';

// matches polyfill
window.Element &&
  (function(ElementPrototype) {
    ElementPrototype.matches =
      ElementPrototype.matches ||
      ElementPrototype.matchesSelector ||
      ElementPrototype.webkitMatchesSelector ||
      ElementPrototype.msMatchesSelector ||
      function(selector) {
        var node = this,
          nodes = (node.parentNode || node.document).querySelectorAll(selector),
          i = -1;
        while (nodes[++i] && nodes[i] != node);
        return !!nodes[i];
      };
  })(Element.prototype);

// closest polyfill
window.Element &&
  (function(ElementPrototype) {
    ElementPrototype.closest =
      ElementPrototype.closest ||
      function(selector) {
        var el = this;
        while (el.matches && !el.matches(selector)) el = el.parentNode;
        return el.matches ? el : null;
      };
  })(Element.prototype);

// NodeList.forEach polyfill
if (window.NodeList && !NodeList.prototype.forEach) {
  NodeList.prototype.forEach = function(callback, thisArg) {
    thisArg = thisArg || window;
    for (var i = 0; i < this.length; i++) {
      callback.call(thisArg, this[i], i, this);
    }
  };
}

Swiper.use([Navigation, Pagination, Scrollbar, Controller]);

new LazyLoad({
  elements_selector: '.lazy',
});

new Swiper('.featured-swiper, .projects-swiper, .issues-swiper', {
  slidesPerView: 'auto',
  pagination: {
    el: '.swiper-pagination',
    type: 'bullets',
    clickable: true,
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  breakpoints: {
    // when window width is <= 1023px
    1023: {
      pagination: false,
      navigation: false,
    },
  },
});

new Swiper('.cards-swiper:not(.controlled)', {
  slidesPerView: 'auto',
  centeredSlides: true,
  pagination: {
    el: '.swiper-pagination',
    type: 'bullets',
    clickable: true,
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  breakpoints: {
    // when window width is <= 1023px
    1023: {
      pagination: false,
      navigation: false,
    },
  },
});

new Swiper('.mini-swiper', {
  slidesPerView: 'auto',
  pagination: false,
  navigation: false,
});

new Swiper('.section-text-images-swiper', {
  slidesPerView: 1,
  pagination: {
    el: '.swiper-pagination',
    type: 'bullets',
    clickable: true,
  },
});

// new Swiper('.label-swiper', {
//   slidesPerView: 'auto',
//   centeredSlides: true,
// });

$('.label-swiper').each(function() {
  const $this = $(this);
  const $controlled = $($this.data('controls'));

  const controlledSwiper = new Swiper($controlled[0], {
    slidesPerView: 'auto',
    centeredSlides: true,
    pagination: false,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      // when window width is <= 1023px
      1023: {
        navigation: false,
      },
    },
  });

  const labelSwiper = new Swiper($this[0], {
    slidesPerView: 'auto',
    centeredSlides: true,
    slideToClickedSlide: true,
  });

  controlledSwiper.controller.control = labelSwiper;
  labelSwiper.controller.control = controlledSwiper;
});

/**
 * Activates tabs on donation forms.
 *
 * When you click a tab, the hidden field "frequency" gets updated with
 * the clicked tab's text content.
 *
 */
function connectDonationTabs() {
  document.querySelectorAll('.js-tab-item').forEach(tab => {
    tab.addEventListener('click', e => {
      const tabBar = e.target.parentElement;
      const siblings = tabBar.children;
      const form = tabBar.parentElement.querySelector('form');
      const handles = tabBar.parentElement.querySelectorAll('.dollar-handles');
      const paragraphs = tabBar.parentElement.querySelectorAll(
        "[class^='paragraph-handles__'], [class*=' paragraph-handles__']"
      );

      Array.from(siblings).forEach(sibling => {
        sibling.classList.remove('is-active');
      });
      e.target.classList.add('is-active');

      if (handles.length) {
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
        } else if (e.target.classList.contains('tab-item__recurring')) {
          form
            .querySelector('.dollar-handles__recurring')
            .classList.add('is-active');
          form
            .querySelector('.paragraph-handles__recurring')
            .classList.add('is-active');
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
    let donationUrl = new URL( form.action );
    let amountValue = '';
    let frequencyValue = '';

    if ( form.amount ) {
      amountValue = form.amount.value;      
    } else if ( form['free-amount'] && form['free-amount'].value ) {
      amountValue = form['free-amount'].value;      
    } else if ( form['dollar-handle'] && form['dollar-handle'].value ) {
      amountValue = form['dollar-handle'].value;
    }
    if ( form['en_recurring_question'] && form['en_recurring_question'].value ) {
      frequencyValue = form.frequency.value;      
    }

    if ( 'mrm' == form.en_recurring_question.value ) {
      if ( 'N' == frequencyValue ) frequencyValue = 'S';
      else frequencyValue = 'M';

      donationUrl.searchParams.append('donate_amt', frequencyValue + ':' + amountValue );
    } else {
      donationUrl.searchParams.append('transaction.donationAmt', amountValue);
      donationUrl.searchParams.append(form.en_recurring_question.value, frequencyValue);
    }

    window.location.href = donationUrl;
  });
}
connectDonationTabs();

/**
 * Functionality for the "dollar handle" donation form.
 *
 */
function connectDollarHandles() {
  // Update paragraph content, and
  // Clear free amount value when I select a dollar handle
  document.querySelectorAll('.dollar-handle input').forEach(radio => {
    radio.addEventListener('change', e => {
      const form = e.target.form;
      const group = e.target.closest('.dollar-handles');
      const paragraph = group.classList.contains('dollar-handles__once')
        ? form.querySelector('.paragraph-handles__once')
        : form.querySelector('.paragraph-handles__recurring');
      const paragraphOther = group.classList.contains(
        'dollar-handles__recurring'
      )
        ? form.querySelector('.paragraph-handles__once')
        : form.querySelector('.paragraph-handles__recurring');
      const freeAmount = form['free-amount'];
      paragraph.textContent = radio.dataset.paragraph;
      paragraphOther.textContent = '';
      freeAmount.value = '';
    });
  });

  // Clear checked dollar handle if I insert a free amount
  document.querySelectorAll('.donation-form .free-amount').forEach(input => {
    input.addEventListener('change', e => {
      const form = e.target.form;
      const checkedHandles = form['dollar-handle'];
      Array.from(checkedHandles).forEach(radio => {
        radio.checked = false;
      });
    });
  });
}
connectDollarHandles();

/**
 * Functionality for the tag cloud. TODO
 *
 * On submit it saves the selected tags in localStorage and then... wat?
 */
function connectFollowingTags() {
  let following = window.localStorage.getItem('following');

  document.querySelectorAll('.topic-button').forEach(element => {
    if (following) {
      following.split(',').forEach(item => {
        if (item === element.getAttribute('data'))
          element.classList.add('active');
      });
    }

    element.addEventListener('click', e => {
      const data = e.target.getAttribute('data');
      if (following) {
        if (!following.split(',').includes(data)) {
          e.target.classList.add('active');
          following = `${following},${data}`;
        } else {
          e.target.classList.remove('active');
          following = following
            .split(',')
            .filter(item => item !== data)
            .reduce(
              (string, item) => (string ? `${string},${item}` : item),
              ''
            );
        }
      } else {
        e.target.classList.add('active');
        following = data;
      }
    });
  });

  const topicSubmitButton = document.getElementById('topic-submit-button');
  if (topicSubmitButton) {
    topicSubmitButton.onclick = () => {
      window.localStorage.setItem('following', following);
    };
  }
}
// connectFollowingTags();

/**
 * Functionality for showing/hiding elements animating their heights, accordion style.
 *
 */
function connectToggles() {
  document.querySelectorAll('.toggle').forEach(toggle => {
    toggle.addEventListener('click', e => {
      toggle.blur();
      const togglee = document.querySelector(toggle.dataset.toggleeSelector);

      if (!togglee || !togglee.children || !togglee.children.length === 1) {
        console.warn('"togglee" must exist have exactly 1 child');
        return;
      }

      if (togglee.classList.contains('is-animating')) return;

      const delay = togglee.classList.contains('accordion') ? 300 : 800;
      togglee.classList.add('is-animating');
      setTimeout(() => {
        togglee.classList.remove('is-animating');
        togglee.style.height = null;
      }, delay); // from _toggle.scss: transition: height 800ms ease;

      const toggleeHeight = togglee.children[0].getBoundingClientRect().height;

      if (toggle.classList.contains('has-opened')) {
        toggle.classList.remove('has-opened');
        togglee.classList.remove('is-open');
        togglee.style.height = toggleeHeight + 'px';
        setTimeout(() => {
          togglee.style.height = '0px';
        }, 10);
      } else {
        toggle.classList.add('has-opened');
        togglee.classList.add('is-open');
        togglee.style.height = toggleeHeight + 'px';
      }
    });
  });
}
connectToggles();

// document.querySelectorAll('#search-expander-open').forEach(element => {
//   element.addEventListener('click', e => {
//     e.preventDefault();
//     const searchExpander = document.getElementsByClassName('search-expander');
//     if (searchExpander && searchExpander.length)
//       searchExpander[0].classList.add('show');
//   });
// });

// const searchExpanderClose = document.getElementById('search-expander-close');
// if (searchExpanderClose)
//   searchExpanderClose.addEventListener('click', e => {
//     e.preventDefault();
//     const searchExpander = document.getElementsByClassName('search-expander');
//     if (searchExpander && searchExpander.length)
//       searchExpander[0].classList.remove('show');
//   });

/**
 * Functionality for the EN form
 *
 * - add signatures bar
 * - open/close
 * - add FB button
 */
function connectENForm() {
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

  form.insertBefore(stats, form.firstChild);
  form.insertBefore(close, form.firstChild);
  cta.parentNode.insertBefore(ctaFacebook, cta);

  cta.addEventListener('click', e => {
    if (!form.classList.contains('is-open')) {
      e.preventDefault();
      form.classList.add('is-open');
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
    if (!form.classList.contains('is-open')) {
      e.preventDefault();
      form.classList.add('is-open');
    } else {
      // e.preventDefault();
      alert('fb connection in progress..');
      // form.querySelector('form').submit()
    }
  });

  close.addEventListener('click', e => {
    form.classList.remove('is-open');
  });
}
connectENForm();

/**
 * Functionality for the dynamic submenu (eg. on page Reports)
 */
function connectAnchorMenu() {
  const container = document.querySelector('.hero-submenu-container');
  if (!container) return;

  const anchors = document.querySelectorAll('.js-anchor-menu');

  Array.from(anchors).forEach(anchor => {
    const anchorEl = document.createElement('li');
    anchorEl.classList.add('hero-submenu-item');
    anchorEl.innerHTML =
      '<li class="hero-submenu-item"><a href="#' +
      anchor.id +
      '">' +
      anchor.dataset.labelmenu +
      '</a></li>';
    container.appendChild(anchorEl);
  });
  new SmoothScroll('a[href*="#"]');
}
connectAnchorMenu();

/* Page specific functionality */
petitionThankyou();
donation(Swiper);
followUnfollow();
