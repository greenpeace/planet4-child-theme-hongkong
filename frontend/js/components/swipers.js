import { debounce } from 'throttle-debounce';

const $ = jQuery;

export default function (Swiper, LazyLoadImages) {

  $('.section-hero-set .arrow').on('click', function (e) {
    const $this = $(this);
    const $next_section = $this.closest('section').nextAll('section:visible').eq(0);
    const $adminbar = $('#wpadminbar');
    const $header = $('header:visible');
    const adminbar_height = $adminbar.length > 0 ? $adminbar.height() : 0;
    const header_height = $header.length > 0 ? $header.height() : 0;
    if($next_section.length > 0) {
      $('html, body').animate({scrollTop: $next_section.offset().top - adminbar_height - header_height + 1 }, 'slow');
    }
  });
  if($('.hero-swiper .video').length > 0) {
    var YTdeferred = jQuery.Deferred();
    window.onYouTubeIframeAPIReady = function() {
      YTdeferred.resolve(window.YT);
    };
    YTdeferred.done(function(YT) {
      init_hero_swiper(YT);
    });
  }
  else {
    init_hero_swiper();
  }
  function init_hero_swiper(YT) {
    $('.hero-swiper').each(function (index) {
      if($(this).is(':visible')) {
        deep_init_hero_swiper(this, YT);
        return;
      }
      var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if(mutation.target?.style?.display == 'block') {
            deep_init_hero_swiper(mutation.target.querySelector('.hero-swiper'), YT);
            observer.disconnect();
          }
        });
      });
      observer.observe($(this).closest('.section-hero-set').get(0), {
        attributes: true,
        attributeFilter: ['style'],
      });
    });
  }
  function deep_init_hero_swiper(_this, YT) {
    const $slides = $('.swiper-slide', _this);
      if($slides.length <= 1) {
        playHeroSetVideo($slides.get(0));
        return true;
      }
      new Swiper(_this, {
        slidesPerView: 1,
        simulateTouch: false,
        pagination: false,
        autoplay: true,
        loop: true,
        on: {
          init: function() {
            LazyLoadImages.update();
          },
          slideChangeTransitionEnd: function() {
            pauseHeroSetVideo(this.slides[this.previousIndex], YT);
            playHeroSetVideo(this.slides[this.activeIndex], YT);
          },
        },
      });
  }
  function playHeroSetVideo(slide, YT) {
    const mq = window.matchMedia("(max-width: 1279px)");
    if(mq.matches) { return; }
    const $video = $(slide).find('.video');
    if($video.length == 0) { return; }
    const $player = $video.find('.youtube-embed');
    if($player.is('iframe')) {
      $player.get(0).contentWindow.postMessage('{"event":"command","func":"seekTo","args":"0"}', '*');
      $player.get(0).contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
      return;
    }
    const ytId = $video.data('youtube-id');
    const playerId = $player.attr('id');
    const player = new YT.Player($player.get(0), {
      width: '100%',
      videoId: ytId,
      host: 'https://www.youtube-nocookie.com',
      playerVars: {
        'modestbranding': 1,
        'autohide': 1,
        'autoplay': 1,
        'showinfo': 0,
        'loop': 1,
        'mute': 1,
        'controls': 0,
        'rel': 0,
      },
      events: {
        'onStateChange': function(event) {
          if (event.data === YT.PlayerState.ENDED) {
            player.playVideo();
          }
        },
      },
    });
  }
  function pauseHeroSetVideo(slide, YT) {
    const mq = window.matchMedia("(max-width: 1279px)");
    if(mq.matches) { return; }
    const $video = $(slide).find('.video');
    if($video.length == 0) { return; }
    const $player = $video.find('.youtube-embed');
    if(!$player.is('iframe')) { return; }
    $player.get(0).contentWindow.postMessage('{"event":"command","func":"stopVideo","args":""}', '*');
  }

  // Get Involved Cards (Homepage B Version 4th Screen, Bottom Part)
  $('.get-involved-cards-swiper').each(function (index) {
    const $this = $(this);
    const pagination = $(this)
      .closest('.grid__outer2')
      .find('.custom-pagination')
      .first()[0];
    const prevBtn = $(this)
      .closest('.grid__outer2')
      .find('.custom-button-prev')
      .first()[0];
    const nextBtn = $(this)
      .closest('.grid__outer2')
      .find('.custom-button-next')
      .first()[0];
    if($('.swiper-slide', this).length < 3) {
      $this.addClass('few-slides');
    }
    new Swiper(this, {
      slidesPerView: 3,
      slidesPerGroup: 3,
      spaceBetween: 30,
      simulateTouch: false,
      pagination: {
        el: pagination,
        type: 'bullets',
        clickable: true,
        renderBullet: function(index, className) {
          return '<span class="' + className + '">' + (index + 1) + '</span>';
        },
      },
      navigation: {
        prevEl: prevBtn,
        nextEl: nextBtn,
      },
      autoplay: false,
      loop: false,
      observer: true,
      observeParents: true,
      breakpoints: {
        1279: {
          slidesPerView: 3,
          slidesPerGroup: 3,
          spaceBetween: 24,
        },
        1023: {
          slidesPerView: 1,
          slidesPerGroup: 1,
        },
      },
    });
  });

  // Testimony (Homepage B Version 6th Screen)
  $('.testimony-swiper').each(function (index) {
    const $this = $(this);
    const pagination = $(this)
      .closest('section')
      .find('.swiper-pagination')
      .first()[0];
    $('.slide-item--short', $this).each(function() {
      const $slide = $(this);
      let $subSlides = $('.slide-item__inner', this);
      if($subSlides.length > 1) {
        let subSlideHtml = $subSlides[1].outerHTML;
        $slide.after('<div class="swiper-slide slide-item slide-item--short slide-item__inner--mobile">' + subSlideHtml + '</div>');
      }
    });
    new Swiper(this, {
      slidesPerView: 'auto',
      simulateTouch: false,
      navigation: {
        nextEl: $this.find('.custom-button-next'),
        prevEl: $this.find('.custom-button-prev'),
      },
      pagination: {
        el: pagination,
        type: 'bullets',
        clickable: true,
      },
      autoplay: false,
      loop: false,
    });
  });

  $('.featured-swiper, .projects-swiper, .tips-swiper').each(function (index) {
    const $this = $(this);
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
          nextEl: $this.find('.swiper-button-next'),
          prevEl: $this.find('.swiper-button-prev'),
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

  $('.cards-swiper:not(.controlled-swiper)').each(function (index) {
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
    $('.mini-swiper').each(function () {
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
  window.addEventListener('resize', debounce(200, miniSwipers));

  $('.section-text-images-swiper').each(function () {
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

  $('.label-swiper').each(function () {
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

  $('.launcher-card-swiper').each(function () {
    const $this = $(this);
    const numOfSlides = $this.find('.swiper-slide').length;

    // We only active the swiper when
    // 1. The cards is more than three
    // 2. It's in mobile version
    if (numOfSlides > 3 || window.innerWidth < 1023) {
      new Swiper(this, {
        slidesPerView: 'auto',
        centeredSlides: true,
        simulateTouch: false,
        loop: false,
        initialSlide: window.innerWidth < 1023 ? 0 : 1,
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
    }
  });
}
