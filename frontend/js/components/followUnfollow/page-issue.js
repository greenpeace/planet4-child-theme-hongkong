import * as Cookies from 'js-cookie';

const $ = jQuery;

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
      gpea_issues_followed.push(current_issue_id);
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

export default pageIssue;
