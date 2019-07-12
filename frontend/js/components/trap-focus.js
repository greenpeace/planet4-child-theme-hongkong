export default function(
  block,
  firstFocusableEl,
  lastFocusableEl,
  preventBackExit,
  preventFrontExit
) {
  block.addEventListener('keydown', e => {
    // SHIFT + Tab
    if (event.keyCode === 9 && event.shiftKey) {
      if (preventBackExit && document.activeElement === firstFocusableEl) {
        console.log('focus back exit prevented');
        event.preventDefault();
        lastFocusableEl.focus();
      }
      // Tab
    } else if (event.keyCode === 9) {
      if (preventFrontExit && document.activeElement === lastFocusableEl) {
        console.log('focus front exit prevented');
        event.preventDefault();
        firstFocusableEl.focus();
      }
    } else if (event.keyCode === 13) {
      if (preventFrontExit) {
        console.log('focus front exit prevented');
        event.preventDefault();
        lastFocusableEl.click();
      }
    }
  });
}
