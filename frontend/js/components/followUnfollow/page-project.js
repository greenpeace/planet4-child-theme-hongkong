import * as Cookies from 'js-cookie';

const $ = jQuery;

const pageProject = function() {
  // check project following and update cookies
  if ($('body').hasClass('page-template-project')) {
    var gpea_projects_followed = Cookies.get('gpea_projects')
      ? Cookies.getJSON('gpea_projects')
      : new Array();
    var current_project_id = $('.js-project-follow').data('project');

    // in case of malformed array, reset it
    if (!Array.isArray(gpea_projects_followed)) gpea_projects_followed = new Array();

    // if already following
    if (gpea_projects_followed.includes(current_project_id)) {
      $('.js-project-unfollow').show();
    } else {
      $('.js-project-follow').show();
    }

    $('.js-project-follow').on('click', function(e) {
      e.preventDefault();
      gpea_projects_followed.push(current_project_id);
      Cookies.set('gpea_projects', gpea_projects_followed, { expires: 3650, path: gpeaSiteHome });
      $('.js-project-follow').hide();
      $('.js-project-unfollow').fadeIn();
    });

    $('.js-project-unfollow').on('click', function(e) {
      e.preventDefault();
      var index = gpea_projects_followed.indexOf(current_project_id);
      if (index > -1) {
        gpea_projects_followed.splice(index, 1);
      }
      Cookies.set('gpea_projects', gpea_projects_followed, { expires: 3650, path: gpeaSiteHome });
      $('.js-project-follow').fadeIn();
      $('.js-project-unfollow').hide();
    });
  }
};

export default pageProject;
