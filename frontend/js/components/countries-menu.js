export default function() {
  const $ = jQuery;

  // Get Countries List from <script> data block.
  let $countries_script = $('#countries_script');
  if (!$countries_script.length) return;

  let countries_json;
  try {
    countries_json = JSON.parse($countries_script.text());
  } catch (error) {
    console.error('Could not build countries menu');
    console.error(error);
    return;
  }

  // Build html for countries drop down list.
  let countries_html = $(
    '<div id="country-list" class="country-list open">' +
      '<a class="international" href=""></a>' +
      '<ul class="countries_list"></ul>' +
      '</div>'
  );
  $.each(countries_json, function(index, element) {
    if ('0' === index) {
      $('.international', countries_html)
        .attr('href', element[0].url)
        .text(element[0].name);
    } else {
      let countries_sublist = $(
        '<li>' +
          '<h3 class="country-group-letter">' +
          index +
          '</h3>' +
          '<ul class="countries_sublist"></ul>' +
          '</li>'
      );
      $('.countries_list', countries_html).append(countries_sublist);
      $.each(element, function(index, country) {
        const url = country.url ? country.url : country.lang[0].url;
        $('.countries_sublist', countries_sublist).append(
          '<li>' +
            '<a href="' +
            country.url +
            '">' +
            country.name +
            '</a>' +
            '</li>'
        );
        // $.each(country, function(index, lang) {
        //   console.log(lang);
        //   $('.countries_sublist', countries_sublist).append(
        //     '<li>' +
        //       '<a href="' +
        //       lang.url +
        //       '">' +
        //       lang.name +
        //       '</a>' +
        //       '</li>'
        //   );
        // });
      });
    }
  });
  $('.js-country-select').append(countries_html);
}
