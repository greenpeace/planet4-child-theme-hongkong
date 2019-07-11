const delta = 100;

export default function() {
  const els = document.querySelectorAll('.js-parallax-me');

  Array.from(els).forEach(el => {
    if (el.hasAttribute('data-from-top')) {
      // el.style.transform = 'translateY(0px)';
      el.style.top = '0px';
      el.style.bottom = '-' + delta + 'px';
    } else {
      // el.style.transform = 'translateY(' + delta / 2 + 'px)';
      el.style.top = '-' + delta / 2 + 'px';
      el.style.bottom = '-' + delta / 2 + 'px';
    }
  });

  function translate(e) {
    const innerHeight = window.innerHeight;
    Array.from(els).forEach(el => {
      const elementTop = el.getBoundingClientRect().top;
      const parentHeight = el.parentElement.getBoundingClientRect().height;
      if (elementTop + parentHeight < 0 || elementTop > innerHeight) return;
      if (el.hasAttribute('data-from-top')) {
        const inFor = 1 - (elementTop + parentHeight) / parentHeight;
        const deltaVariation = inFor * delta;
        el.style.transform = 'translateY(' + -deltaVariation + 'px)';
      } else {
        const inFor =
          1 - (elementTop + parentHeight) / (innerHeight + parentHeight);
        const deltaVariation = inFor * delta;
        el.style.transform =
          'translateY(' + (-deltaVariation + delta / 2) + 'px)';
      }
    });
  }

  window.addEventListener('scroll', translate);
  translate();
}
