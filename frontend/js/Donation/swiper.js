// Wrap wrapper around nodes
// Just pass a collection of nodes, and a wrapper element
function wrapAll(nodes, wrapper) {
  // Cache the current parent and previous sibling of the first node.
  var parent = nodes[0].parentNode;
  var previousSibling = nodes[0].previousSibling;

  // Place each node in wrapper.
  //  - If nodes is an array, we must increment the index we grab from
  //    after each loop.
  //  - If nodes is a NodeList, each node is automatically removed from
  //    the NodeList when it is removed from its parent with appendChild.
  for (var i = 0; nodes.length - i; wrapper.firstChild === nodes[0] && i++) {
    wrapper.appendChild(nodes[i]);
  }

  // Place the wrapper just after the cached previousSibling,
  // or if that is null, just before the first child.
  var nextSibling = previousSibling
    ? previousSibling.nextSibling
    : parent.firstChild;
  parent.insertBefore(wrapper, nextSibling);

  return wrapper;
}

function swiper(Swiper) {
  // 1. Wrap form in Swiper-friendly HTML
  const swiperContainer = document.createElement('div');
  swiperContainer.classList.add('swiper-container', 'form-swiper');

  const swiperWrapper = document.createElement('div');
  swiperWrapper.classList.add('swiper-wrapper');

  const formblocks = document.querySelectorAll(
    '.en__component.en__component--formblock'
  );

  Array.from(formblocks).forEach(block => {
    block.classList.add('swiper-slide');
  });

  wrapAll(formblocks, swiperWrapper);
  wrapAll([swiperWrapper], swiperContainer);

  // 2. Add prev/next button to each slide
  formblocks[0].innerHTML +=
    '<button type="button" class="button primary fluid js-amount-next">Donate</button>';

  formblocks[1].innerHTML +=
    '<div class="donate-navigation"> \
			<div class="reminder-amount">每月捐款<br /><span class="js-reminder-amount"></span></div> \
			<div> \
				<button type="button" class="button small js-donate-prev">Back</button> \
				<button type="button" class="button primary js-to-payment">Next</button> \
			</div> \
    </div>';

  const submitButton = formblocks[2].querySelector('.en__submit button');
  submitButton.classList.add('primary');
  formblocks[2].innerHTML +=
    '<div class="donate-navigation"> \
			<div class="reminder-amount">每月捐款<br /><span class="js-reminder-amount"></span></div> \
			<div> \
				<button type="button" class="button small js-donate-prev">Back</button> \
			</div> \
		</div>';
  formblocks[2]
    .querySelector('.donate-navigation > div:last-child')
    .appendChild(submitButton);

  // 3. Activate swiper
  return new Swiper('.form-swiper', {
    slidesPerView: 1,
    spaceBetween: 60,
    pagination: false,
    navigation: false,
    autoHeight: true,
    allowTouchMove: false,
  });
}

export default swiper;
