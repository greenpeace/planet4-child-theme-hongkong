function prevNext(form, donationSwiper, validateBlock) {
  // All steps: PREV
  const prevs = form.querySelectorAll('.js-donate-prev');
  Array.from(prevs).forEach(prev => {
    prev.addEventListener('click', e => {
      e.preventDefault();
      donationSwiper.slidePrev();
    });
  });

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
      donationSwiper.slideNext();
    } else {
      block.querySelector('.is-invalid').focus();
    }
  });

  // Step 1: TO PAYMENT
  form.querySelector('.js-to-payment').addEventListener('click', e => {
    e.preventDefault();
    const block = e.target.closest('.en__component.en__component--formblock');
    const valid = validateBlock(block);
    if (valid) {
      donationSwiper.slideNext();
    } else {
      block.querySelector('.is-invalid').focus();
    }
  });
}

export default prevNext;
