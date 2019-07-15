const delta = 100;

export default function() {
  const els = document.querySelectorAll('.js-parallax-me');

  Array.from(els).forEach(el => {
    const speed = el.hasAttribute('data-prlx-speed')
      ? parseInt(el.dataset.prlxSpeed, 10)
      : 1;
    if (el.hasAttribute('data-prlx-from-top')) {
      // el.style.transform = 'translateY(0px)';
      el.style.top = '-' + (delta * speed) / 3 + 'px';
      el.style.bottom = '-' + delta * speed + 'px';
    } else {
      // el.style.transform = 'translateY(' + delta / 2 + 'px)';
      el.style.top = '-' + (delta * speed) / 2 + 'px';
      el.style.bottom = '-' + (delta * speed) / 2 + 'px';
    }
  });

  function translate(e) {
    const innerHeight = window.innerHeight;
    Array.from(els).forEach(el => {
      const speed = el.hasAttribute('data-prlx-speed')
        ? parseInt(el.dataset.prlxSpeed, 10)
        : 1;
      const elementTop = el.getBoundingClientRect().top;
      const parentHeight = el.parentElement.getBoundingClientRect().height;
      if (elementTop + parentHeight < 0 || elementTop > innerHeight) return;
      if (el.hasAttribute('data-prlx-from-top')) {
        const inFor = 1 - (elementTop + parentHeight) / parentHeight;
        const deltaVariation = inFor * (delta * speed);
        el.style.transform = 'translate3D(0, ' + -deltaVariation + 'px, 0)';
      } else {
        const inFor =
          1 - (elementTop + parentHeight) / (innerHeight + parentHeight);
        const deltaVariation = inFor * (delta * speed);
        el.style.transform =
          'translate3D(0, ' +
          (-deltaVariation + (delta * speed) / 2) +
          'px, 0)';
      }
    });
  }

  window.addEventListener('scroll', translate);
  translate();
}
