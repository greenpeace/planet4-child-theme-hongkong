const $ = jQuery;

export default function() {
  function fetchArticles(reset_container, ev) {
    ev.preventDefault();
    let query = $('#articles_list_load_more')
      .children()
      .serializeArray()
      .reduce(function(acc, el) {
        return Object.assign(acc, { [el.name]: el.value });
      }, {});
    let main_issue_id = $('#articles_list_load_more_issue').val();
    if (main_issue_id) {
      query.miid = main_issue_id;
    }
    let year = $('#articles_list_load_more_year').val();
    let container = $('#articles_list_post_container');
    let messages = $('#articles_list_messages');
    let btn = $('#articles_list_load_more_btn');
    if (year) {
      query.year = year;
    }
    query.paged = reset_container ? 1 : btn.data('paged');
    btn.data('paged', query.paged + 1);
    btn.addClass('loading');
    $.ajax({
      url: window.localizations.ajaxurl,
      type: 'POST',
      data: {
        action: 'gpea_blocks_articles_load_more',
        query: query,
      },
      dataType: 'html',
    })
      .done(function(response) {
        btn.removeClass('loading');
        response = JSON.parse(response);
        let html_data = response.html_data,
          posts_found = parseInt(response.posts_found),
          has_more_posts = response.has_more_posts;
        messages.html('');
        if (reset_container) {
          container.html('');
          btn.attr('disabled', false);
        }
        if (posts_found) {
          container.append(html_data);
        } else {
          messages.append(html_data);
          btn.attr('disabled', true);
        }
        if (!has_more_posts) {
          btn.attr('disabled', true);
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        btn.removeClass('loading');
        console.log(errorThrown); // eslint-disable-line no-console
      });
    return false;
  }

  $('#articles_list_load_more').on('submit', fetchArticles.bind(null, false));
  $('#articles_list_load_more_issue').on(
    'change',
    fetchArticles.bind(null, true)
  );
  $('#articles_list_load_more_year').on(
    'change',
    fetchArticles.bind(null, true)
  );
}
