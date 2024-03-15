import template from 'lodash.template';
import './vendor/jquery.auto-complete.min.js';

$ = jQuery; // eslint-disable-line no-global-assign

const p4ct_search = function() {
  const $search_form = $('#search_form_inner');
  if (!$search_form.length){ return; }

  var search_request;
  const ajaxurl = window.localizations.ajaxurl; // eslint-disable-line no-undef

  const $live_result_posts = $('#ajax-search-posts');

  const $post_template = $('#template-posts');
  const build_posts = template($post_template[0].innerHTML);

  const is_result_page = $('body.search').length > 0;
  const $result_page_result_title = $('.results-no');
  const $result_page_no_results = $('.nothing-found');
  const $result_page_result_posts = $('.multiple-search-result .results-list');
  const $load_more_button = $('.btn-load-more-click-scroll');

  $('.search-autocomplete').autoComplete({
    minChars: is_result_page ? 0 : 2,
    source: function(term, suggest) {
      try {
        search_request.abort();
      } catch (e) {} // eslint-disable-line no-empty

      if(is_result_page) {
        $search_form.trigger('submit');
        return;
      }

      $search_form.addClass('is-loading');
      $live_result_posts.addClass('fade-out');

      search_request = $.post(
        ajaxurl,
        {
          action: 'p4ct_search_site',
          search: term,
        },
        function(res) {
          $('.nothing-found').remove();
          $search_form.removeClass('is-loading');
          // Frontend TODO: integrate
          res = JSON.parse(res);
          // window.res = res;
          // console.log(res);
          const posts = res.posts;

          if (posts.length) { $live_result_posts.show().removeClass('fade-out'); }
          else { $live_result_posts.hide(); }

          // posts = posts.map(post => post.post_title);
          // posts = posts.join('<br>');
          // posts = posts || 'No posts found';
          $live_result_posts.find('.results-list')[0].innerHTML = build_posts({
            posts: posts,
          });
          // console.log(posts);
          // suggest(res.data);
        }
      );
    },
  });

  $search_form.on('submit', function(e) {
    if(is_result_page) {
      e.preventDefault();
      next_page = 1;
      load_next_page();
    }
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
    const search_query = $('#search_input').val().trim();
    const current_params = new URLSearchParams(location.search);
    $.ajax({
      url: window.localizations.ajaxurl,
      type: 'GET',
      data: {
        action: 'get_paged_posts',
        'search-action': 'get_paged_posts',
        search_query: search_query,
        paged: next_page,
        'query-string': 's=' + search_query, // Ignore the ? in the search url (first char).
      },
      dataType: 'html',
    })
      .done(function(response) {
        // console.log(response);
        // Append the response at the bottom of the results and then show it.
        current_page = next_page;
        $load_more_button.removeClass('loading');
        $(document.body).removeClass('is-loading');
        if(next_page == 1) {
          $result_page_result_posts.empty();
        }
        $result_page_result_posts.append(response);
        // $result_page_result_title = '';
        current_params.set('s', search_query);
        history.replaceState(null, '', '?' + current_params.toString());
        if (posts_per_load * next_page > total_posts || total_posts == 0) {
          $load_more_button.hide();
        }
        else {
          $load_more_button.show();
        }
        if (total_posts == 0) {
          $result_page_no_results.show();
          $result_page_result_posts.hide();
        }
        else {
          $result_page_no_results.hide();
          $result_page_result_posts.show();
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        $load_more_button.removeClass('loading');
        $(document.body).removeClass('is-loading');
        console.log(errorThrown); //eslint-disable-line no-console
      });
  }
};

/* Run */
p4ct_search();
