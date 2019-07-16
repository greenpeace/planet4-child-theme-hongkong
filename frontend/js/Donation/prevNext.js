function prevNext(form, donationSwiper, validateBlock, Scroll) {
  // Focus and up
  donationSwiper.on('slideChangeTransitionEnd', function() {
    // console.log('slide changed');
    Scroll.animateScroll(
      donationSwiper.slides[donationSwiper.activeIndex].querySelectorAll(
        'input, select'
      )[0]
    );
  });

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
      // scroll.animateScroll(document.querySelector('.form-caption'));
    });
  });

  const stepAmount = document.querySelector('.js-step-amount');
  const stepDetails = document.querySelector('.js-step-details');
  const stepPayment = document.querySelector('.js-step-payment');

  // Step 1: NEXT
  form.querySelector('.js-amount-next').addEventListener('click', e => {
    e.preventDefault();
	let recurring = document.querySelector('input[name="supporter.NOT_TAGGED_31"]:checked').value;
	let amountType = '';
	amountType = ('Y' == recurring) ? 'amountRecurring' : 'amountSingle';
		
    const block = e.target.closest('.en__component.en__component--formblock');
    const amount = document.getElementById('en__field_transaction_donationAmt');
    const reminders = form.querySelectorAll('.js-reminder-amount');
    const remindersRecurring = form.querySelectorAll('.js-reminder-recurring');
    const valid = validateBlock(block, amountType);
    if (valid) {
      Array.from(reminders).forEach(reminder => {
        reminder.textContent = window.NRO_PROPERTIES[NRO].currency + ' ' + amount.value;
      });
      Array.from(remindersRecurring).forEach(reminderRecurring => {
        if (amountType == 'amountRecurring') {
          reminderRecurring.textContent = window.NRO_PROPERTIES[NRO].gift.type.recurring;
        }
        else {
          reminderRecurring.textContent = window.NRO_PROPERTIES[NRO].gift.type.single;
        }
      });
      stepAmount.classList.remove('is-current');
      stepAmount.classList.add('is-done');
      stepDetails.classList.remove('is-todo');
      stepDetails.classList.add('is-current');
      donationSwiper.slideNext();
      // Scroll.animateScroll(document.querySelector('.form-caption'));
      // donationSwiper.slides[1].querySelectorAll('input, select')[0].focus();
    } else {
      Scroll.animateScroll(block.querySelector('.is-invalid'));
      block.querySelector('.is-invalid').focus();
    }
  });

  // Step 2: TO PAYMENT
  form.querySelector('.js-to-payment').addEventListener('click', e => {
    e.preventDefault();
    const block = e.target.closest('.en__component.en__component--formblock');
    const valid = validateBlock(block, 'data');
    if (valid) {
      stepDetails.classList.remove('is-current');
      stepDetails.classList.add('is-done');
      stepPayment.classList.remove('is-todo');
      stepPayment.classList.add('is-current');
      donationSwiper.slideNext();
      // donationSwiper.slides[2].querySelectorAll('input, select')[0].focus();
      // Scroll.animateScroll(document.querySelector('.form-caption'));
    } else {
      Scroll.animateScroll(block.querySelector('.is-invalid'));
      block.querySelector('.is-invalid').focus();
    }
  });
}

export default prevNext;
