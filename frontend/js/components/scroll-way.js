import { throttle } from 'throttle-debounce';

export default function() {
  let lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;

  window.addEventListener(
    'scroll',
    throttle(100, function() {
      const scrollTop =
        window.pageYOffset || document.documentElement.scrollTop; // Credits: "https://github.com/qeremy/so/blob/master/so.dom.js#L426"
      if (scrollTop > lastScrollTop) {
        // downscroll code
        document.body.classList.add('is-scrolling-down');
      } else {
        // upscroll code
        document.body.classList.remove('is-scrolling-down');
      }
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
    }),
    false
  );
}
