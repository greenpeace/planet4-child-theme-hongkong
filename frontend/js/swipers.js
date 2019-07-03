import { debounce } from 'debounce';

const $ = jQuery;

export default function(Swiper) {
  $('.featured-swiper, .projects-swiper, .tips-swiper').each(function(index) {
    const pagination = $(this)
      .closest('section')
      .find('.swiper-pagination')
      .first()[0];

    setTimeout(() => {
      new Swiper(this, {
        slidesPerView: 'auto',
        simulateTouch: false,
        loop: false,
        loopedSlides: 1,
        pagination: {
          el: pagination,
          type: 'bullets',
          clickable: true,
        },
        navigation: {
          nextEl: $(this).find('.swiper-button-next'),
          prevEl: $(this).find('.swiper-button-prev'),
        },
        breakpoints: {
          // when window width is <= 1023px
          1023: {
            pagination: false,
            // navigation: false,
          },
        },
      });
    }, 0);
  });

  new Swiper('.issues-swiper', {
    slidesPerView: 'auto',
    simulateTouch: false,
    paginationn: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true,
    },
    pagination: false,
    navigation: {
      nextEl: '.issues-swiper .swiper-button-next',
      prevEl: '.issues-swiper .swiper-button-prev',
    },
    breakpoints: {
      // when window width is <= 1023px
      1023: {
        pagination: false,
        // navigation: false,
      },
    },
  });

  $('.cards-swiper:not(.controlled-swiper)').each(function(index) {
    const $this = $(this);

    new Swiper(this, {
      slidesPerView: 'auto',
      centeredSlides: true,
      simulateTouch: false,
      pagination: {
        el: $this.find('.swiper-pagination'),
        type: 'bullets',
        clickable: true,
      },
      navigation: {
        nextEl: $this.find('.swiper-button-next'),
        prevEl: $this.find('.swiper-button-prev'),
      },
      breakpoints: {
        // when window width is <= 1023px
        1023: {
          pagination: false,
          navigation: false,
        },
      },
    });
  });

  function miniSwipers() {
    $('.mini-swiper').each(function() {
      const $this = $(this);
      if (this.swiper) this.swiper.destroy();

      const $slides = $this.find('.swiper-slide');

      if ($slides.length > 4 || window.innerWidth < 1024) {
        new Swiper(this, {
          slidesPerView: 'auto',
          simulateTouch: false,
          loop: false,
          loopedSlides: 1,
          pagination: false,
          navigation: {
            nextEl: $this.find('.swiper-button-next'),
            prevEl: $this.find('.swiper-button-prev'),
          },
          breakpoints: {
            // when window width is <= 1023px
            1023: {
              navigation: false,
            },
          },
        });
      }
    });
  }
  miniSwipers();
  window.addEventListener('resize', debounce(miniSwipers, 200));

  $('.section-text-images-swiper').each(function() {
    const $this = $(this);

    new Swiper(this, {
      slidesPerView: 1,
      simulateTouch: false,
      autoplay: true,
      pagination: {
        el: $this.find('.swiper-pagination'),
        type: 'bullets',
        clickable: true,
      },
      navigation: {
        nextEl: $this.find('.swiper-button-next'),
        prevEl: $this.find('.swiper-button-prev'),
      },
      breakpoints: {
        // when window width is <= 1023px
        1023: {
          navigation: false,
        },
      },
    });
  });

  $('.label-swiper').each(function() {
    const $this = $(this);
    const $controlled = $($this.data('controls'));

    const controlledSwiper = new Swiper($controlled[0], {
      slidesPerView: 'auto',
      centeredSlides: true,
      simulateTouch: false,
      pagination: false,
      navigation: {
        nextEl: $controlled.find('.swiper-button-next'),
        prevEl: $controlled.find('.swiper-button-prev'),
      },
      breakpoints: {
        // when window width is <= 1023px
        1023: {
          navigation: false,
        },
      },
    });

    const labelSwiper = new Swiper($this[0], {
      slidesPerView: 'auto',
      centeredSlides: true,
      slideToClickedSlide: true,
    });

    controlledSwiper.controller.control = labelSwiper;
    labelSwiper.controller.control = controlledSwiper;
  });
}
