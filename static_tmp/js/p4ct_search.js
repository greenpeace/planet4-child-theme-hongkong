// Custom code

$ = jQuery; // eslint-disable-line no-global-assign

jQuery(function($){

  var $search_form = $( '#search_form_inner' );
  var searchRequest;
  var ajaxurl = localizations.ajaxurl; // eslint-disable-line no-undef
  var results_posts = $('#ajax-search-posts');
  var results_terms = $('#ajax-search-terms');

  $('.search-autocomplete').autoComplete({
    minChars: 2,
    source: function(term, suggest) {
      try {
        searchRequest.abort();
      } catch(e) {} // eslint-disable-line no-empty

      searchRequest = $.post(
        ajaxurl,
        {
          action: 'p4ct_search_site',
          search: term,
        },
        function(res) {
          // Frontend TODO: integrate
          res = JSON.parse(res);
          window.res = res;
          var posts = res.posts,
              terms = res.terms;
          posts = posts.map(post => post.post_title);
          posts = posts.join('<br>');
          posts = posts || 'No posts found';
          results_posts.html(posts);
          terms = terms.map(term => term.name);
          terms = terms.join('<br>');
          terms = terms || 'No terms found';
          results_terms.html(terms);
            console.log(posts);
         // suggest(res.data);
        }
      );
    }
  });

  var $load_more_button = $( '.btn-load-more-click-scroll' );
  var load_more_count   = 0;
  var loaded_more       = false;
  // Add click event for load more button in blocks.
  $load_more_button.off( 'click' ).on( 'click', function() {
    // If this button has this class then Lazy-loading is enabled.
    if ( $(this).hasClass( 'btn-load-more-async' ) ) {
      var total_posts    = $(this).data('total_posts');
      var posts_per_load = $(this).data('posts_per_load');
      var next_page      = $(this).data( 'current_page' ) + 1;
      $(this).data( 'current_page', next_page );

      var $load_more_button = $( '.btn-load-more-click-scroll' );
      var load_more_count   = 0;
      var loaded_more       = false;
      $.ajax({
        url: localizations.ajaxurl,
        type: 'GET',
        data: {
          action:          'get_paged_posts',
          'search-action': 'get_paged_posts',
          'search_query':  $( '#search_input' ).val().trim(),
          'paged':         next_page,
          'query-string':  decodeURIComponent( location.search ).substr( 1 )		// Ignore the ? in the search url (first char).
        },
        dataType: 'html'
      }).done(function ( response ) {
        // Append the response at the bottom of the results and then show it.
        $( '.multiple-search-result .list-unstyled' ).append( response );
        $( '.row-hidden:last' ).removeClass( 'row-hidden' ).show( 'fast' );
        if (posts_per_load * next_page > total_posts) {
          $load_more_button.hide();
        }
      }).fail(function ( jqXHR, textStatus, errorThrown ) {
        console.log(errorThrown); //eslint-disable-line no-console
      });
    } else {
      var $row = $( '.row-hidden', $load_more_button.closest( '.container' ) );

      if ( 1 === $row.length ) {
        $load_more_button.closest( '.load-more-button-div' ).hide( 'fast' );
      }
      $row.first().show( 'fast' ).removeClass( 'row-hidden' );
    }
  });


});
