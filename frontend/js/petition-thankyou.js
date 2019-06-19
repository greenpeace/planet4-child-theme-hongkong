import SmoothScroll from 'smooth-scroll';

function petitionThankyou() {
  if (!document.querySelector('.page-template-petition-thankyou')) return;

  const state = {
    sign: true,
    share: null,
    donate: null,
  };

  const scroll = new SmoothScroll();

  const stepShare = document.querySelector('.js-step-share');
  const stepDonate = document.querySelector('.js-step-donate');

  document.querySelector('.js-yes-share').addEventListener('click', e => {
    stepShare.classList.add('is-visible');
    scroll.animateScroll(stepShare);
  });

  document.querySelector('.js-no-share').addEventListener('click', e => {
    stepDonate.classList.add('is-visible');
    scroll.animateScroll(stepDonate);
  });

  document.querySelector('.js-skip-share').addEventListener('click', e => {
    stepDonate.classList.add('is-visible');
    scroll.animateScroll(stepDonate);
  });
}

export default petitionThankyou;
