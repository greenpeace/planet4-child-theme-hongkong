import Globalicious from './Globalicious';

document.addEventListener('DOMContentLoaded', function(event) {
  const globe = document.getElementById('history-globe');
  if (!globe) return;

  const years = document.querySelectorAll(
    '.section-history .label-swiper .swiper-slide'
  );
  const latitudes = document.querySelectorAll(
    '.section-history .maxi-swiper .swiper-slide[data-latitude]'
  );
  const longitudes = document.querySelectorAll(
    '.section-history .maxi-swiper .swiper-slide[data-longitude]'
  );

  if (
    years.length !== latitudes.length ||
    latitudes.length !== longitudes.length
  ) {
    console.warn(
      'World Slideshow: Please make sure to provide latitudes and longitudes for ALL slides'
    );
    return;
  }

  /** Will be our swiper
   ******************************************************************************************/
  const swiperContainer = document.querySelector(
    '.section-history .maxi-swiper'
  );

  /** SVGs for our markers
   ******************************************************************************************/
  const pin =
    '<svg viewBox="0 0 24 24" width="40" height="40" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.41"><path fill="#000000" d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" /></svg>';

  /** Build the list of coordinates
   ******************************************************************************************/
  const markers = Array.from(latitudes).map(slide => {
    const lat = parseFloat(slide.dataset.latitude.trim());
    const long = parseFloat(slide.dataset.longitude.trim());
    return {
      lat,
      long,
    };
  });

  /** Initiate the spinning globe
   ******************************************************************************************/
  window.Earth = new Globalicious(globe, {
    speed: 0,
    incline: 10,
    startingRotation: -120, // East Asia
    borders: false,
    outline: false,
    sea: false,
    gridOver: false,
    gridUnder: false,
    land: '#fff',
    data: world_vars.templateUrl + '/static/js/world-110m.json',
    goToDuration: 500,
    goToEasing: 'easeOutQuart',
  });

  /** Let's do the rest after Load, to give the page time to breath
   ******************************************************************************************/
  window.addEventListener('load', function() {
    if (!swiperContainer.swiper) return;

    /** Place markers on the globe
     ******************************************************************************************/
    markers.forEach(marker => {
      window.Earth.mark(marker.lat, marker.long, pin);
    });

    /** Go to the first location
     ******************************************************************************************/
    window.Earth.goTo(markers[0].lat - 60, markers[0].long);

    /** Hook up to the slide change
     ******************************************************************************************/
    swiperContainer.swiper.on('slideChange', function() {
      window.Earth.goTo(
        markers[this.activeIndex].lat - 60,
        markers[this.activeIndex].long
      );
    });
  });
});
