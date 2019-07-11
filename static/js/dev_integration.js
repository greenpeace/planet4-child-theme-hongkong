$(document).ready(function() {
  // JS COOKIE TODO REMOVE
  !(function(e) {
    var n = !1;
    if (
      ('function' == typeof define && define.amd && (define(e), (n = !0)),
      'object' == typeof exports && ((module.exports = e()), (n = !0)),
      !n)
    ) {
      var o = window.Cookies,
        t = (window.Cookies = e());
      t.noConflict = function() {
        return (window.Cookies = o), t;
      };
    }
  })(function() {
    function g() {
      for (var e = 0, n = {}; e < arguments.length; e++) {
        var o = arguments[e];
        for (var t in o) {
          n[t] = o[t];
        }
      }
      return n;
    }
    return (function e(l) {
      function C(e, n, o) {
        var t;
        if ('undefined' != typeof document) {
          if (1 < arguments.length) {
            if (
              'number' == typeof (o = g({ path: '/' }, C.defaults, o)).expires
            ) {
              var r = new Date();
              r.setMilliseconds(r.getMilliseconds() + 864e5 * o.expires),
                (o.expires = r);
            }
            o.expires = o.expires ? o.expires.toUTCString() : '';
            try {
              (t = JSON.stringify(n)), /^[\{\[]/.test(t) && (n = t);
            } catch (e) {}
            (n = l.write
              ? l.write(n, e)
              : encodeURIComponent(String(n)).replace(
                  /%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,
                  decodeURIComponent
                )),
              (e = (e = (e = encodeURIComponent(String(e))).replace(
                /%(23|24|26|2B|5E|60|7C)/g,
                decodeURIComponent
              )).replace(/[\(\)]/g, escape));
            var i = '';
            for (var c in o) {
              o[c] && ((i += '; ' + c), !0 !== o[c] && (i += '=' + o[c]));
            }
            return (document.cookie = e + '=' + n + i);
          }
          e || (t = {});
          for (
            var a = document.cookie ? document.cookie.split('; ') : [],
              s = /(%[0-9A-Z]{2})+/g,
              f = 0;
            f < a.length;
            f++
          ) {
            var p = a[f].split('='),
              d = p.slice(1).join('=');
            this.json || '"' !== d.charAt(0) || (d = d.slice(1, -1));
            try {
              var u = p[0].replace(s, decodeURIComponent);
              if (
                ((d = l.read
                  ? l.read(d, u)
                  : l(d, u) || d.replace(s, decodeURIComponent)),
                this.json)
              ) {
                try {
                  d = JSON.parse(d);
                } catch (e) {}
              }
              if (e === u) {
                t = d;
                break;
              }
              e || (t[u] = d);
            } catch (e) {}
          }
          return t;
        }
      }
      return (
        ((C.set = C).get = function(e) {
          return C.call(C, e);
        }),
        (C.getJSON = function() {
          return C.apply({ json: !0 }, [].slice.call(arguments));
        }),
        (C.defaults = {}),
        (C.remove = function(e, n) {
          C(e, '', g(n, { expires: -1 }));
        }),
        (C.withConverter = e),
        C
      );
    })(function() {});
  });
  // END JS COOKIE TODO REMOVE
  // ARTICLES LIST.
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
  // END ARTICLES LIST.
});
