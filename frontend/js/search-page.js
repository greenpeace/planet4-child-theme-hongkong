import template from 'lodash.template';
import './vendor/jquery.auto-complete.min.js';

$ = jQuery; // eslint-disable-line no-global-assign

const p4ct_search = function() {
  var $search_form = $('#search_form_inner');
  if (!$search_form.length) return;
  var searchRequest;
  var ajaxurl = window.localizations.ajaxurl; // eslint-disable-line no-undef

  var resultsPosts = $('#ajax-search-posts');
  var resultsIssues = $('#ajax-search-issues');
  var resultsTopics = $('#ajax-search-topics');

  var reset_filters = $('#btn_filter_reset');

  const postsTemplate = $('#template-posts');
  const buildPosts = template(postsTemplate[0].innerHTML);

  const issuesTemplate = $('#template-issues');
  const buildIssues = template(issuesTemplate[0].innerHTML);

  const topicsTemplate = $('#template-topics');
  const buildTopics = template(topicsTemplate[0].innerHTML);

  $('.search-autocomplete').autoComplete({
    minChars: 2,
    source: function(term, suggest) {
      try {
        searchRequest.abort();
      } catch (e) {} // eslint-disable-line no-empty

      $search_form.addClass('is-loading');
      resultsPosts.addClass('fade-out');
      resultsIssues.addClass('fade-out');
      resultsTopics.addClass('fade-out');

      searchRequest = $.post(
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
          const terms = res.terms;
          const issues = terms.filter(term => term.taxonomy === 'category');
          const topics = terms.filter(term => term.taxonomy === 'post_tag');

          if (posts.length) resultsPosts.show().removeClass('fade-out');
          else resultsPosts.hide();

          if (issues.length) resultsIssues.show().removeClass('fade-out');
          else resultsIssues.hide();

          if (topics.length) resultsTopics.show().removeClass('fade-out');
          else resultsTopics.hide();

          // posts = posts.map(post => post.post_title);
          // posts = posts.join('<br>');
          // posts = posts || 'No posts found';
          resultsPosts.find('.results-list')[0].innerHTML = buildPosts({
            posts: posts,
          });
          resultsIssues.find('.results-list')[0].innerHTML = buildIssues({
            issues: issues,
          });
          resultsTopics.find('.results-list')[0].innerHTML = buildTopics({
            topics: topics,
          });
          // terms = terms.map(term => term.name);
          // terms = terms.join('<br>');
          // terms = terms || 'No terms found';
          // results_terms.html(terms);
          // console.log(posts);
          // suggest(res.data);
        }
      );
    },
  });

  var filters_search = $('.filter-search');

  $search_form.on('submit', function() {
    $(document.body).addClass('is-loading');
  });

  filters_search.change(function(ev) {
    $search_form.submit();
  });

  reset_filters.click(function(ev) {
    filters_search.map(function() {
      $(this).val('');
    });
    $search_form.submit();
  });

  var $load_more_button = $('.btn-load-more-click-scroll');
  var load_more_count = 0;
  var loaded_more = false;
  // Add click event for load more button in blocks.
  $load_more_button.off('click').on('click', function() {
    // If this button has this class then Lazy-loading is enabled.
    if ($(this).hasClass('btn-load-more-async')) {
      var total_posts = $(this).data('total_posts');
      var posts_per_load = $(this).data('posts_per_load');
      var next_page = $(this).data('current_page') + 1;
      $(this).data('current_page', next_page);

      var $load_more_button = $('.btn-load-more-click-scroll');
      var load_more_count = 0;
      var loaded_more = false;
      $.ajax({
        url: window.localizations.ajaxurl,
        type: 'GET',
        data: {
          action: 'get_paged_posts',
          'search-action': 'get_paged_posts',
          search_query: $('#search_input')
            .val()
            .trim(),
          paged: next_page,
          'query-string': decodeURIComponent(location.search).substr(1), // Ignore the ? in the search url (first char).
        },
        dataType: 'html',
      })
        .done(function(response) {
          console.log(response);
          // Append the response at the bottom of the results and then show it.
          $('.multiple-search-result .results-list').append(response);
          $('.row-hidden:last')
            .removeClass('row-hidden')
            .show('fast');
          if (posts_per_load * next_page > total_posts) {
            $load_more_button.hide();
          }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
          console.log(errorThrown); //eslint-disable-line no-console
        });
    } else {
      var $row = $('.row-hidden', $load_more_button.closest('.container'));

      if (1 === $row.length) {
        $load_more_button.closest('.load-more-button-div').hide('fast');
      }
      $row
        .first()
        .show('fast')
        .removeClass('row-hidden');
    }
  });
};

/* Run */
p4ct_search();
