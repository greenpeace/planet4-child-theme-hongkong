import * as Cookies from 'js-cookie';

const pageProject = function() {
  // check project following and update cookies
  if ($('body').hasClass('page-template-project')) {
    var gpea_projects_followed = Cookies.get('gpea_projects')
      ? Cookies.getJSON('gpea_projects')
      : new Array();
    var current_project_id = $('.js-project-follow').data('project');

    // if already following
    if (gpea_projects_followed.includes(current_project_id)) {
      $('.js-project-unfollow').show();
    } else {
      $('.js-project-follow').show();
    }

    $('.js-project-follow').on('click', function(e) {
      e.preventDefault();
      gpea_projects_followed.push(current_project_id);
      Cookies.set('gpea_projects', gpea_projects_followed);
      $('.js-project-follow').hide();
      $('.js-project-unfollow').fadeIn();
    });

    $('.js-project-unfollow').on('click', function(e) {
      e.preventDefault();
      var index = gpea_projects_followed.indexOf(current_project_id);
      if (index > -1) {
        gpea_projects_followed.splice(index, 1);
      }
      Cookies.set('gpea_projects', gpea_projects_followed);
      $('.js-project-follow').fadeIn();
      $('.js-project-unfollow').hide();
    });
  }
};

const pageIssue = function() {
  // check main issue following and update cookies
  if ($('body').hasClass('page-template-main-issue')) {
    var gpea_issues_followed = Cookies.get('gpea_issues')
      ? Cookies.getJSON('gpea_issues')
      : new Array();
    var current_issue_id = $('.js-issue-follow').data('topic');

    // if already following
    if (gpea_issues_followed.includes(current_issue_id)) {
      $('.js-issue-unfollow').show();
    } else {
      $('.js-issue-follow').show();
    }

    $('.js-issue-follow').on('click', function(e) {
      e.preventDefault();
      gpea_issues_followed.push(gpea_issues_followed);
      Cookies.set('gpea_issues', gpea_issues_followed);
      $('.js-issue-follow').hide();
      $('.js-issue-unfollow').fadeIn();
    });

    $('.js-issue-unfollow').on('click', function(e) {
      e.preventDefault();
      var index = gpea_issues_followed.indexOf(current_issue_id);
      if (index > -1) {
        gpea_issues_followed.splice(index, 1);
      }
      Cookies.set('gpea_issues', gpea_issues_followed);
      $('.js-issue-follow').fadeIn();
      $('.js-issue-unfollow').hide();
    });
  }
};

const tagCloud = function() {
  // For each tag, mark it as "active" if followed by the user; and make sure it's shown.
  if ($('.js-tag-cloud').length) {
    // Issues followed
    var cookie_name_issues = 'gpea_issues';
    var gpea_cookie_issues = Cookies.get(cookie_name_issues)
      ? Cookies.getJSON(cookie_name_issues)
      : new Array();
    $.each(gpea_cookie_issues, function(key, value) {
      $(`.topic-button[data-topic='${value}']`).addClass('active');
      $(`.topic-button[data-topic='${value}']`).show();
    });

    // Topics followed
    var cookie_name_topics = 'gpea_topics';
    var gpea_cookie_topics = Cookies.get(cookie_name_topics)
      ? Cookies.getJSON(cookie_name_topics)
      : new Array();
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
      if ($(this).hasClass('active')) {
        // if ( gpea_cookie_issues.includes(gpea_selected_topic_id) ) return;
        // else gpea_cookie_issues.push(gpea_selected_topic_id);
        if (!gpea_cookie_issues.includes(gpea_selected_topic_id))
          gpea_cookie_issues.push(gpea_selected_topic_id);
        $(`#en__field_supporter_questions_${gpea_selected_topic_id}`).val('Y');
      } else {
        var index = gpea_cookie_issues.indexOf(gpea_selected_topic_id);
        if (index > -1) {
          gpea_cookie_issues.splice(index, 1);
        }
        $(`#en__field_supporter_questions_${gpea_selected_topic_id}`).val('');
      }
    });
    Cookies.set(cookie_name_issues, gpea_cookie_issues);

    var cookie_name_topics = 'gpea_topics';
    var gpea_cookie_topics = Cookies.get(cookie_name_topics)
      ? Cookies.getJSON(cookie_name_topics)
      : new Array();

    $('.topic-button[data-taxonomy="post_tag"]').each(function(index) {
      var gpea_selected_topic_id = $(this).data('topic');
      if ($(this).hasClass('active')) {
        // if ( gpea_cookie_topics.includes(gpea_selected_topic_id) ) return;
        // else gpea_cookie_topics.push(gpea_selected_topic_id);
        if (!gpea_cookie_topics.includes(gpea_selected_topic_id))
          gpea_cookie_topics.push(gpea_selected_topic_id);
        $(`#en__field_supporter_questions_${gpea_selected_topic_id}`).val('Y');
      } else {
        var index = gpea_cookie_topics.indexOf(gpea_selected_topic_id);
        if (index > -1) {
          gpea_cookie_topics.splice(index, 1);
        }
        $(`#en__field_supporter_questions_${gpea_selected_topic_id}`).val('');
      }
    });
    Cookies.set(cookie_name_topics, gpea_cookie_topics);

    $(this).hide();
    $('.tag_cloud_form').fadeIn();
    // $('.tag_cloud_form .signatures').addClass('is-open');
  });
};

const followUnfollow = function() {
  pageProject();
  pageIssue();
  tagCloud();
};

export default followUnfollow;
