import * as Cookies from 'js-cookie';

const $ = jQuery;

const tagCloud = function() {
  // For each tag, mark it as "active" if followed by the user; and make sure it's shown.
  if ($('.js-tag-cloud').length) {
    // Issues followed
    var cookie_name_issues = 'gpea_issues';
    var gpea_cookie_issues = Cookies.get(cookie_name_issues)
      ? Cookies.getJSON(cookie_name_issues)
      : new Array();

    // in case of malformed array, reset it
    if (!Array.isArray(gpea_cookie_issues)) gpea_cookie_issues = new Array();
    
    $.each(gpea_cookie_issues, function(key, value) {
      $(`.topic-button[data-topic='${value}']`).addClass('active');
      $(`.topic-button[data-topic='${value}']`).show();
    });

    // Topics followed
    var cookie_name_topics = 'gpea_topics';
    var gpea_cookie_topics = Cookies.get(cookie_name_topics)
      ? Cookies.getJSON(cookie_name_topics)
      : new Array();

    // in case of malformed array, reset it
    if (!Array.isArray(gpea_cookie_topics)) gpea_cookie_topics = new Array();
    
    $.each(gpea_cookie_topics, function(key, value) {
      $(`.topic-button[data-topic='${value}']`).addClass('active');
    });
  }

  $('.topic-button').each(function() {
    $(this).on('click', function(e) {
      e.preventDefault();
      $(this).toggleClass('active');
    });
  });

  // Set cookies on topic click
  $('#topic-submit-button').on('click', function(e) {
    e.preventDefault();
    var cookie_name_issues = 'gpea_issues';
    var gpea_cookie_issues = Cookies.get(cookie_name_issues)
      ? Cookies.getJSON(cookie_name_issues)
      : new Array();

    $('.topic-button[data-taxonomy="category"]').each(function(index) {
      var gpea_selected_topic_id = $(this).data('topic');
      var gpea_selected_question_engaging_id = $(this).data('engaging');
      if ($(this).hasClass('active')) {
        // if ( gpea_cookie_issues.includes(gpea_selected_topic_id) ) return;
        // else gpea_cookie_issues.push(gpea_selected_topic_id);
        if (!gpea_cookie_issues.includes(gpea_selected_topic_id)) {
          gpea_cookie_issues.push(gpea_selected_topic_id);
        }
        $(`#en__field_supporter_questions_${gpea_selected_question_engaging_id}`).val('Y');
      } else {
        var index = gpea_cookie_issues.indexOf(gpea_selected_topic_id);
        if (index > -1) {
          gpea_cookie_issues.splice(index, 1);
        }
        $(`#en__field_supporter_questions_${gpea_selected_question_engaging_id}`).val('');
      }
    });
    Cookies.set(cookie_name_issues, gpea_cookie_issues, { expires: 3650 });

    var cookie_name_topics = 'gpea_topics';
    var gpea_cookie_topics = Cookies.get(cookie_name_topics)
      ? Cookies.getJSON(cookie_name_topics)
      : new Array();

    $('.topic-button[data-taxonomy="post_tag"]').each(function(index) {
      var gpea_selected_topic_id = $(this).data('topic');
      var gpea_selected_question_engaging_id = $(this).data('engaging');
      if ($(this).hasClass('active')) {
        // if ( gpea_cookie_topics.includes(gpea_selected_topic_id) ) return;
        // else gpea_cookie_topics.push(gpea_selected_topic_id);
        if (!gpea_cookie_topics.includes(gpea_selected_topic_id)) {
          gpea_cookie_topics.push(gpea_selected_topic_id);
        }          
        $(`#en__field_supporter_questions_${gpea_selected_question_engaging_id}`).val('Y');
      } else {
        var index = gpea_cookie_topics.indexOf(gpea_selected_topic_id);
        if (index > -1) {
          gpea_cookie_topics.splice(index, 1);
        }
        $(`#en__field_supporter_questions_${gpea_selected_question_engaging_id}`).val('');
      }
    });
    Cookies.set(cookie_name_topics, gpea_cookie_topics, { expires: 3650 });

    $(this).hide();
    $('.tag_cloud_form').fadeIn();
    // $('.tag_cloud_form .signatures').addClass('is-open');
  });

  
  const p4en_form_tag_cloud = document.querySelector('.section-choose-topics #p4en_form');
  // general, for all donation we check all checkbox... yes sir, this probably can be avoided in a couple of weeks, using new feature of official plugin
  // specific for korea: assign a value to checkbox, otherwise "required" is never satisfied, and also assign required "programmatically", since plugin won't allow to set required to an input
  if (p4en_form_tag_cloud && ( 'ko-KR' != document.documentElement.lang ) ) {
    let privacyOptions = document.querySelectorAll('#p4en_form [type=checkbox]');
    for (var i = 0, elementOption; (elementOption = privacyOptions[i++]); ) {
      elementOption.checked = true;
    }
  } else if (p4en_form_tag_cloud && ( 'ko-KR' == document.documentElement.lang ) ) {
    let privacyOptions = document.querySelectorAll('#p4en_form [type=checkbox]');
    for (var i = 0, elementOption; (elementOption = privacyOptions[i++]); ) {
      if ( 'en__field_supporter_all_check' != elementOption.id ) elementOption.required = true;
      elementOption.addEventListener( 'change', (e) => {
        if(e.target.checked) {
          e.target.value = 'Y';
        } else {
          e.target.value = '';
        }
      });
    }      
  }  

};

export default tagCloud;
