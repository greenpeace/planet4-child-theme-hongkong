import swiper from './Donation/swiper';
import validation from './Donation/validation';
import prevNext from './Donation/prevNext';
import mask from './Donation/masks';

function donation(Swiper) {
  // Quit if we aren't in a donation page
  if (!document.querySelector('.page-template-donation')) return;

  // Activate swiper
  const donationSwiper = swiper(Swiper);

  // Init mask/validation logic
  const form = document.querySelector('#enform form');
  mask(form);
  const validateBlock = validation(form);

  // Activate prev/next buttons
  prevNext(form, donationSwiper, validateBlock);

  // Submit safeguard
  form.addEventListener('submit', e => {
    if (
      !validateBlock(block, 'amount') ||
      !validateBlock(block, 'data') ||
      !validateBlock(block, 'payment')
    ) {
      scroll.animateScroll(block.querySelector('.is-invalid'));
      // block.querySelector('.is-invalid').focus();
      e.preventDefault();
      return false;
    }
  });
}

export default donation;
