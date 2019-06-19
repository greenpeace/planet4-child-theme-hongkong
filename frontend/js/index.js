import {
  Swiper,
  Navigation,
  Pagination,
  Scrollbar,
  Controller,
} from 'swiper/dist/js/swiper.esm.js';
import LazyLoad from 'vanilla-lazyload';
import * as Cookies from 'js-cookie';

import petitionThankyou from './petition-thankyou';
import donation from './donation';

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

Swiper.use([Navigation, Pagination, Scrollbar]);

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

new Swiper('.cards-swiper', {
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

new Swiper('.label-swiper', {
  slidesPerView: 'auto',
  centeredSlides: true,
});

/**
 * Activates tabs on donation forms.
 *
 * When you click a tab, the hidden field "frequency" gets updated with
 * the clicked tab's text content.
 *
 */
function connectDonationTabs() {
  document.querySelectorAll('.donation-form .tab-item').forEach(tab => {
    tab.addEventListener('click', e => {
      const tabBar = e.target.parentElement;
      const siblings = tabBar.children;
      const form = tabBar.parentElement.querySelector('form');

      Array.from(siblings).forEach(sibling => {
        sibling.classList.remove('active');
      });
      e.target.classList.add('active');
      form.frequency.value = e.target.textContent.trim();
    });
  });
}
connectDonationTabs();

/**
 * Functionality for the "dollar handle" donation form.
 *
 */
function connectDollarHandles() {
  // Clear free amount value when I select a dollar handle
  document.querySelectorAll('.dollar-handle input').forEach(radio => {
    radio.addEventListener('change', e => {
      const form = e.target.form;
      const freeAmount = form['free-amount'];
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
connectFollowingTags();

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

document.querySelectorAll('#search-expander-open').forEach(element => {
  element.addEventListener('click', e => {
    e.preventDefault();
    const searchExpander = document.getElementsByClassName('search-expander');
    if (searchExpander && searchExpander.length)
      searchExpander[0].classList.add('show');
  });
});

const searchExpanderClose = document.getElementById('search-expander-close');
if (searchExpanderClose)
  searchExpanderClose.addEventListener('click', e => {
    e.preventDefault();
    const searchExpander = document.getElementsByClassName('search-expander');
    if (searchExpander && searchExpander.length)
      searchExpander[0].classList.remove('show');
  });

function connectENForm() {
  const form = document.querySelector('#enform');
  if (!form) return;

  const cta = document.querySelector('#p4en_form_save_button');
  if (!cta) return;

  const petitionSource = document.querySelector('#petition-source').getAttribute('data-petition');

  const stats = document.createElement('div');
  stats.classList.add('signatures');
  stats.innerHTML =
    '	<div class="progress-bar"> \
			<div class="percent" style="width: 90%"></div> \
		</div> \
		<div class="stats">已有2,347 名簽署者</div>';

  const close = document.createElement('div');
  close.classList.add('close');
  close.innerHTML = '×';

  const ctaFacebook = document.createElement('button');
  ctaFacebook.classList.add('js-sign-facebook');
  ctaFacebook.classList.add('button__facebook');
  ctaFacebook.classList.add('btn');
  ctaFacebook.innerHTML = 'Facebook';

  ctaFacebook.addEventListener('click', e => {
    alert("fb connection in progress..");
  });

  const submitArea = document.querySelector('.submit');

  form.insertBefore(stats, form.firstChild);
  form.insertBefore(close, form.firstChild);

  cta.addEventListener('click', e => {
    if (!form.classList.contains('is-open')) {
      e.preventDefault();
      form.classList.add('is-open');
      // submitArea.append(ctaFacebook);
    } else {
      // e.preventDefault();
      let thankyouUrl = form.getAttribute('data-redirect-url');
      let fn = document.getElementsByName("supporter.firstName")[0].value;
      thankyouUrl += '?fn='+fn+'&pet=' + petitionSource;
      form.setAttribute('data-redirect-url', thankyouUrl);
      // form.querySelector('form').submit()
    }
  });

  close.addEventListener('click', e => {
    form.classList.remove('is-open');
  });
}
connectENForm();

/* Page specific functionality */
petitionThankyou();
donation(Swiper);
