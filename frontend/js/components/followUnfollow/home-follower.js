import * as Cookies from 'js-cookie';
import template from 'lodash.template';

const $ = jQuery;

const homeFollower = function() {
  if (!$('body').hasClass('js-home-follower')) return;

  const $sectionProjects = $('.section-projects').first();

  // handle project section here
  $sectionProjects.removeClass('is-loading');
  $sectionProjects.addClass('is-loaded');
  // $.ajax({
  //   url: p4_vars.ajaxurl,
  //   type: 'post',
  //   data: {
  //     action: 'projectsFollowing',
  //   },
  //   success: function(data) {
  //     // This outputs the result of the ajax request
  //     console.log(data);
  //   },
  //   error: function(errorThrown) {
  //     //alert(errorThrown);
  //     console.log(errorThrown);
  //   },
  // });
};

export default homeFollower;
