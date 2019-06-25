import SmoothScroll from 'smooth-scroll';

function prevNext(form, donationSwiper, validateBlock) {
  const scroll = new SmoothScroll();

  // All steps: PREV
  const prevs = form.querySelectorAll('.js-donate-prev');
  Array.from(prevs).forEach(prev => {
    prev.addEventListener('click', e => {
      e.preventDefault();
      const stepCurrent = document.querySelector('.step.is-current');
      const stepPrev = stepCurrent.previousElementSibling;

      stepCurrent.classList.remove('is-current');
      stepCurrent.classList.add('is-todo');
      stepPrev.classList.remove('is-done');
      stepPrev.classList.add('is-current');

      donationSwiper.slidePrev();
      scroll.animateScroll(document.querySelector('.form-caption'));
    });
  });

  const stepAmount = document.querySelector('.js-step-amount');
  const stepDetails = document.querySelector('.js-step-details');
  const stepPayment = document.querySelector('.js-step-payment');

  // Step 1: NEXT
  form.querySelector('.js-amount-next').addEventListener('click', e => {
    e.preventDefault();
    const block = e.target.closest('.en__component.en__component--formblock');
    const amount = document.getElementById('en__field_transaction_donationAmt');
    const reminders = form.querySelectorAll('.js-reminder-amount');
    const valid = validateBlock(block);
    if (valid) {
      Array.from(reminders).forEach(reminder => {
        reminder.textContent = amount.value;
      });
      stepAmount.classList.remove('is-current');
      stepAmount.classList.add('is-done');
      stepDetails.classList.remove('is-todo');
      stepDetails.classList.add('is-current');
      donationSwiper.slideNext();
      scroll.animateScroll(document.querySelector('.form-caption'));
    } else {
      block.querySelector('.is-invalid').focus();
    }
  });

  // Step 2: TO PAYMENT
  form.querySelector('.js-to-payment').addEventListener('click', e => {
    e.preventDefault();
    const block = e.target.closest('.en__component.en__component--formblock');
    const valid = validateBlock(block);
    if (valid) {
      stepDetails.classList.remove('is-current');
      stepDetails.classList.add('is-done');
      stepPayment.classList.remove('is-todo');
      stepPayment.classList.add('is-current');
      donationSwiper.slideNext();
      scroll.animateScroll(document.querySelector('.form-caption'));
    } else {
      block.querySelector('.is-invalid').focus();
    }
  });
}

export default prevNext;
