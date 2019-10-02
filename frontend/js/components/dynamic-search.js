const $ = jQuery;
window.over_sb = null; // fix issue with jQuery autoComplete v1.0.7

export default function() {
  const $dynamicSearch = $('.dynamic-search');
  const $dynamicSearchTrigger = $('.js-dynamic-search');
  const $dynamicSearchClose = $('.js-close-dynamic-search');

  $dynamicSearchTrigger.on('click', function(e) {
    if ($dynamicSearch.length) {
      $dynamicSearch.toggleClass('is-visible');
      $(document.body).toggleClass('has-open-dynamic-search');
    }

    setTimeout(() => {
      $('#search_input').focus();
    }, 100);

    e.preventDefault();
  });

  $dynamicSearchClose.on('click', function(e) {
    $('#search_input').blur();
    $dynamicSearch.removeClass('is-visible');
    $(document.body).removeClass('has-open-dynamic-search');
    e.preventDefault();
  });
}
