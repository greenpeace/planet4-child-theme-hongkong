export default function() {
  const share = document.querySelector('.floating-share');
  if (!share) return;

  const fixFromTop = 80;
  let unfixWithScroll = 50 + window.innerHeight * 0.7 + 15;
  let left = share.getBoundingClientRect().left;

  function checkScroll() {
    if (
      !share.classList.contains('is-fixed') &&
      share.getBoundingClientRect().top <= fixFromTop
    ) {
      // if (element.getBoundingClientRect().top < top) {
      share.classList.add('is-fixed');
      share.style.left = left + 'px';
      share.style.marginLeft = '0px';
    } else if (
      share.classList.contains('is-fixed') &&
      window.scrollY <= unfixWithScroll - fixFromTop
    ) {
      share.classList.remove('is-fixed');
      share.style.left = null;
      share.style.marginLeft = null;
    }
  }

  function windowResize(event) {
    share.classList.remove('is-fixed');
    share.style.left = null;
    unfixWithScroll = 50 + window.innerHeight * 0.7 + 15;
    left = share.getBoundingClientRect().left;
    windowScroll();
  }

  function windowScroll(event) {
    !!window.requestAnimationFrame
      ? requestAnimationFrame(checkScroll)
      : checkScroll();
  }

  // Reset the target element offsets when the window is resized, then
  // check to see if we need to fix or unfix the target element.
  window.addEventListener('resize', windowResize);

  // When the window scrolls, check to see if we need to fix or unfix
  // the target element.
  window.addEventListener('scroll', windowScroll);

  // For touch devices, call checkScroll directlly rather than
  // rAF wrapped windowScroll to animate the element
  if ('ontouchmove' in window) {
    window.addEventListener('touchmove', checkScroll);
  }
}
