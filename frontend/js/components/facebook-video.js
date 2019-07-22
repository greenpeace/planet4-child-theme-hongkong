import { debounce } from 'throttle-debounce';

export default function() {
  const fbVideos = document.querySelectorAll(
    'iframe[src^="https://www.facebook.com/"]'
  );

  function fixFbVideoHeights() {
    Array.from(fbVideos).forEach(function(video) {
      video.style.height = video.offsetWidth + 'px';
    });
  }

  fixFbVideoHeights();
  window.addEventListener('load', fixFbVideoHeights);
  window.addEventListener('resize', debounce(500, fixFbVideoHeights));
}
