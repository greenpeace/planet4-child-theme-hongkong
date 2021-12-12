import * as Cookies from 'js-cookie';

const $ = jQuery;

export default function() {
  const COOKIE_ID = 'gpea_closed_notification_ids';
  let $container = $('.gpea-notification');
  let last_modified = $container.data('time');
  let closed_notification = Cookies.getJSON(COOKIE_ID) || [];
  if($container.length == 0) {
    return;
  }
  if(!closed_notification || !closed_notification.time || !closed_notification.ids || closed_notification.time != last_modified) {
    closed_notification = {
      time: last_modified,
      ids: [],
    };
    Cookies.remove(COOKIE_ID);
  }
  $.each(closed_notification.ids, function(k, v) {
    $container.find('[data-id="' + v + '"]').remove();
  });
  $('.gpea-notification__close-button').on('click', function() {
    let $target = $(this).closest('.gpea-notification__item');
    closed_notification.time = last_modified;
    closed_notification.ids.push($target.data('id'));
    $target.fadeOut();
    Cookies.set(COOKIE_ID, closed_notification, { expires: 365, path: '/' });
  });
  
  /*let submitted_tips_ids = Cookies.getJSON(COOKIE_ID) || [];
  $('.tip-engage').each(function() {
    let form = $(this);
    let pid = form.find('input[name=pid]');
    if (pid.length && submitted_tips_ids.includes(pid.val())) {
      // form.find(':submit').attr('disabled', 'disabled');
      form.closest('.tip-action-buttons').addClass('has-committed');
    }
  });
  $('.tip-engage').on('submit', function(ev) {
    ev.preventDefault();
    let counter = false;
    let form = $(this);
    let query = form
      .children()
      .serializeArray()
      .reduce(function(acc, el) {
        return Object.assign(acc, { [el.name]: el.value });
      }, {});
    form.find(':submit').addClass('loading');
    if (query.pid) {
      counter = $(
        '#tip_commitments_post_' + query.pid + ' .js-tip-commitments'
      );
      submitted_tips_ids.push(query.pid);
      Cookies.set(COOKIE_ID, submitted_tips_ids, { expires: 365, path: '/' + gpeaSiteHome + '/' });
    }
    $.ajax({
      url: window.localizations.ajaxurl,
      type: 'POST',
      data: {
        action: 'gpea_tips_pledge',
        query: query,
      },
      dataType: 'html',
    })
      .done(function(response) {
        if (counter) {
          let currentCount = parseInt(counter.text(), 10);
          if (isNaN(currentCount)) currentCount = 0;
          counter.text(currentCount + 1);
        }
        form.find(':submit').removeClass('loading');
        form.find(':submit').attr('disabled', 'disabled');
        form.closest('.tip-action-buttons').addClass('has-committed');
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        form.find(':submit').removeClass('loading');
        console.error(errorThrown); // eslint-disable-line no-console
      });
    return false;
  });*/
}
