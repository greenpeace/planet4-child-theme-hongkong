import './vendor/jquery.auto-complete.min.js';

$ = jQuery; // eslint-disable-line no-global-assign

const p4ct_search = function() {
  const $search_form = $('#search_form_inner');
  if (!$search_form.length){ return; }

  var search_request;
  // Read the search words from the page address, not from the search box.
  // On a category or tag page the box shows the filter name.
  // That name is a label here, so Load More must not search for it.
  var search_query = (new URLSearchParams(window.location.search).get('s') || '').trim();

  const $result_page_result_posts = $('.multiple-search-result .results-list');
  const $load_more_button = $('.btn-load-more-click-scroll');

  $search_form.on('submit', function(e) {
    $(document.body).addClass('is-loading');
  });

  // GA4
  $('[role="search"]').on('submit', function() {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      'event': 'custom_event',
      'event_name': 'search',
      'event_category': 'blog',
      'event_action': 'search',
      'search_query': $(this).find('[name="s"]').val(),
    });
  });

  // Add click event for load more button in blocks.
  var total_posts = $load_more_button.data('total_posts');
  var posts_per_load = $load_more_button.data('posts_per_load');
  var current_page = $load_more_button.data('current_page');
  var next_page = current_page + 1;
  $load_more_button.off('click').on('click', function() {
    next_page = current_page + 1;
    $load_more_button.addClass('loading');
    load_next_page();
  });
  function load_next_page() {
    try {
      search_request.abort();
    } catch (e) {}
    search_request = $.ajax({
      url: window.localizations.ajaxurl,
      type: 'GET',
      data: {
        action: 'get_paged_posts',
        'search-action': 'get_paged_posts',
        search_query: search_query,
        paged: next_page,
        // Send the page's own query string. It carries the category and tag filters.
        // So Load More stays inside the same filter as the first page.
        // The server reads everything after "&query-string=". Keep this key last.
        'query-string': window.location.search.substring(1),
      },
      dataType: 'html',
    })
      .done(function(response) {
        // console.log(response);
        // Append the response at the bottom of the results and then show it.
        current_page = next_page;
        $load_more_button.removeClass('loading');
        $result_page_result_posts.append(response);
        if (posts_per_load * current_page > total_posts || total_posts == 0) {
          $load_more_button.hide();
        }
        else {
          $load_more_button.show();
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        $load_more_button.removeClass('loading');
        console.log(errorThrown); //eslint-disable-line no-console
      });
  }
};

/* Run */
p4ct_search();
