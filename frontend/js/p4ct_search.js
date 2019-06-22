// Custom code
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

      searchRequest = $.post(
        ajaxurl,
        {
          action: 'p4ct_search_site',
          search: term,
        },
        function(res) {
          $('.nothing-found').remove();
          // Frontend TODO: integrate
          res = JSON.parse(res);
          // window.res = res;
          // console.log(res);
          const posts = res.posts;
          const terms = res.terms;
          const issues = terms.filter(term => term.taxonomy === 'category');
          const topics = terms.filter(term => term.taxonomy === 'post_tag');

          if (posts.length) resultsPosts.show();
          else resultsPosts.hide();

          if (issues.length) resultsIssues.show();
          else resultsIssues.hide();

          if (topics.length) resultsTopics.show();
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

export default p4ct_search;

// /* global localizations */

// $ = jQuery; // eslint-disable-line no-global-assign.

// // Search page.
// $(function() {
//   var $search_form      = $( '#search_form' );
//   var $load_more_button = $( '.btn-load-more-click-scroll' );
//   var load_more_count   = 0;
//   var loaded_more       = false;

//   $( '#search-type button' ).click(function() {
//     $( '#search-type button' ).removeClass( 'active' );
//     $( this ).addClass( 'active' );
//   });

//   $( '.btn-filter:not( .disabled )' ).click(function() {
//     $( '#filtermodal' ).modal( 'show' );
//   });

//   // Submit form on Sort change event.
//   $( '#select_order' ).off( 'change' ).on( 'change', function() {
//     $( '#orderby', $search_form ).val( $( this ).val() ).parent().submit();
//     return false;
//   });

//   // Submit form on Filter click event or on Apply button click event.
//   $( 'input[name^="f["]:not(.modal-checkbox), .applybtn' ).off( 'click' ).on( 'click', function() {
//     $search_form.submit();
//   });

//   // Add all selected filters to the form submit.
//   $search_form.on( 'submit', function() {
//     if ( 0 === $('.filter-modal.show').length ) {
//       $( 'input[name^="f["]:not(.modal-checkbox):checked' ).each( function() {
//         var $checkbox = $( this ).clone( true );
//         $checkbox.css('display', 'none');
//         $search_form.append( $checkbox );
//       } );
//     } else {
//       $( 'input[name^="f["].modal-checkbox:checked').each( function() {
//         var $checkbox = $( this ).clone( true );
//         $checkbox.css('display', 'none');
//         $search_form.append( $checkbox );
//       } );
//     }
//   });

//   let $search_results = $( '.multiple-search-result' );
//   // Add filter by clicking on the page type label inside a result item.
//   // Delegate event handler to the dynamically created descendant elements.
//   $search_results.off( 'click', '.search-result-item-head' ).on( 'click', '.search-result-item-head', function() {
//     window.location.href = $( this ).data( 'href' );
//   });

//   // Navigate to the page of the search result item when clicking on it's thumbnail image.
//   // Delegate event handler to the dynamically created descendant elements.
//   $search_results.off( 'click', '.search-result-item-image').on( 'click', '.search-result-item-image', function() {
//     window.location.href = $( '.search-result-item-headline', $( this ).parent() ).attr( 'href' );
//   });

//   // Underline headline on thumbnail hover.
//   $('.search-result-item-image').hover(
//     function() {
//       $('.search-result-item-headline', $(this).parent()).addClass('search-hover');
//     }, function() {
//       $('.search-result-item-headline', $(this).parent()).removeClass('search-hover');
//     }
//   );

//   // Clear single selected filter.
//   $( '.activefilter-tag' ).off( 'click' ).on( 'click', function() {
//     $( '.p4-custom-control-input[value=' + $( this ).data( 'id' ) + ']' ).prop('checked', false );
//     $search_form.submit();
//   });

//   // Clear all selected filters.
//   $( '.clearall' ).off( 'click' ).on( 'click', function() {
//     $( 'input[name^="f["]' ).prop( 'checked', false );
//     $search_form.submit();
//   });

//   // Add click event for load more button in blocks.
//   $load_more_button.off( 'click' ).on( 'click', function() {
//     // If this button has this class then Lazy-loading is enabled.
//     if ( $(this).hasClass( 'btn-load-more-async' ) ) {
//       var total_posts    = $(this).data('total_posts');
//       var posts_per_load = $(this).data('posts_per_load');
//       var next_page      = $(this).data( 'current_page' ) + 1;
//       $(this).data( 'current_page', next_page );

//       $.ajax({
//         url: localizations.ajaxurl,
//         type: 'GET',
//         data: {
//           action:          'get_paged_posts',
//           'search-action': 'get_paged_posts',
//           'search_query':  $( '#search_input' ).val().trim(),
//           'paged':         next_page,
//           'query-string':  decodeURIComponent( location.search ).substr( 1 )		// Ignore the ? in the search url (first char).
//         },
//         dataType: 'html'
//       }).done(function ( response ) {
//         // Append the response at the bottom of the results and then show it.
//         $( '.multiple-search-result .list-unstyled' ).append( response );
//         $( '.row-hidden:last' ).removeClass( 'row-hidden' ).show( 'fast' );
//         if (posts_per_load * next_page > total_posts) {
//           $load_more_button.hide();
//         }
//       }).fail(function ( jqXHR, textStatus, errorThrown ) {
//         console.log(errorThrown); //eslint-disable-line no-console
//       });
//     } else {
//       var $row = $( '.row-hidden', $load_more_button.closest( '.container' ) );

//       if ( 1 === $row.length ) {
//         $load_more_button.closest( '.load-more-button-div' ).hide( 'fast' );
//       }
//       $row.first().show( 'fast' ).removeClass( 'row-hidden' );
//     }
//   });

//   // Reveal more results just by scrolling down the first 'show_scroll_times' times.
//   $( window ).scroll(function() {
//     if ($load_more_button.length > 0) {
//       let element_top       = $load_more_button.offset().top,
//         element_height      = $load_more_button.outerHeight(),
//         window_height       = $(window).height(),
//         window_scroll       = $(this).scrollTop(),
//         load_earlier_offset = 250;

//       if ( load_more_count < localizations.show_scroll_times ) {
//         // If next page has not loaded then load next page as soon as scrolling
//         // reaches 'load_earlier_offset' pixels before the Load more button.
//         if ( ! loaded_more
//             && window_scroll > ( element_top + element_height - window_height - load_earlier_offset )
//             && ( ( load_more_count + 1 ) * $load_more_button.data('posts_per_load') ) < $load_more_button.data('total_posts' ) ) {

//           load_more_count++;
//           $load_more_button.click();
//           loaded_more = true;

//           // Add a throttle to avoid multiple scroll events from firing together.
//           setTimeout(function () {
//             loaded_more = false;
//           }, 500);
//         }
//         if (window_scroll > (element_top + element_height - window_height)) {
//           $('.row-hidden').removeClass('row-hidden').show('fast');
//         }
//       }
//       return false;
//     }
//   });
// });
