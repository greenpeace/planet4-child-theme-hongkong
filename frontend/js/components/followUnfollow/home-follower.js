import template from 'lodash.template';

const $ = jQuery;

/**
 * Takes the posts returned by the AJAX call and filters out posts already existing.
 * Elements in both arrays must have a property "ID"
 *
 * @param {Array} newPosts An array containing the new posts as returned by the call
 * @param {Array} existingPosts An array of existing posts in the carousel
 */
export const filterDuplicates = function(newPosts, existingPosts) {
  return newPosts.filter(
    newPost =>
      existingPosts.find(existingPost => existingPost.ID == newPost.ID) ===
      undefined
  );
};

/**
 * create array with univoque post id
 *
 * @param {Array} posts An array containing the new posts as returned by the call
 */
export const getUnique = function(posts) {
	const final = [ ];
	posts.map((e,i)=> 
		!final.find(final => final.ID == e.ID) && final.push(e) 
	)
	return final;
  };

/**
 * Sorts an array of posts, most recent first
 *
 * @param {Array} posts The array of posts to sort (by reference)
 */
export const sortByRecentFirst = function(posts) {
  return posts.sort(byRecentDate);
};

function byRecentDate(a, b) {
  const aDate = new Date(a.date.replace(/ /g, ''));
  const bDate = new Date(b.date.replace(/ /g, ''));

  // If both dates are null or invalid, don't change the order
  if (isNaN(aDate.getTime()) && isNaN(bDate.getTime())) return 0;

  if (isNaN(bDate.getTime()) || aDate > bDate) return -1;
  if (isNaN(aDate.getTime()) || bDate > aDate) return 1;

  return 0;
}

const homeFollower = function() {
  if (!$('body').hasClass('js-home-follower')) return;

  const $featuredSwiper = $('.section-featured .swiper-container').first();
  const $existingPosts = $featuredSwiper.find('.swiper-slide');

  const $sectionProjects = $('.section-projects').first();

  const existingPosts = $existingPosts
    .toArray()
    .filter(el => 'postid' in el.dataset)
    .map(el => ({
      ID: el.dataset.postid,
    }));

  let gpea_topics_followed = Cookies.get('gpea_topics');
  if (typeof gpea_topics_followed !== 'undefined' && gpea_topics_followed.length > 0) {

	  $featuredSwiper.addClass('is-loading');
	  $.ajax({
		url: p4_vars.ajaxurl,
		type: 'post',
		data: {
		  action: 'topicsFollowing',
		},
		success: function(data) {
		  const swiper = $featuredSwiper[0].swiper;
		  let followingResults;
		  let followingResultsPosts;
		  let posts = [];

		  try {
			followingResults = JSON.parse(data);
		  } catch (error) {
			console.error(error);
			$featuredSwiper.removeClass('is-loading');
			return;
		  }

		  followingResults.map(postsTag => {
			let followingResultsPosts = postsTag.posts;
			posts = posts.concat(followingResultsPosts);
		  });

		  // Remove existing posts from the returned posts, take first 5, sort by recent first
		  // console.log(posts.length + ' posts returned');
		  posts = filterDuplicates(posts, existingPosts);
		  posts = getUnique(posts);
		  sortByRecentFirst(posts);
		  posts = posts.slice(0, 5);
		  // console.log(existingPosts.length + ' existing posts');
		  // console.log(posts.length + ' posts returned');

		  // Create the HTML element for each new post

		  const petitionTemplate = $('#template-card-petition-big');
		  const buildPetition = template(petitionTemplate[0].innerHTML);
		  const updateTemplate = $('#template-card-update-big');
		  const buildUpdate = template(updateTemplate[0].innerHTML);

		  const newPostsSlides = posts.map(post => {
			if (post.engaging_pageid !== undefined) {
			  return buildPetition(post);
			} else {
			  return buildUpdate(post);
			}
		  });

		  // Add the new slides in 6th position (1 first slide + 5 regular slides)
		  swiper.addSlide(0, newPostsSlides);

		  // move to the first slide
		  swiper.slideTo(0);

		  // console.log(
		  //   newPostsSlides.length + ' slides created and added',
		  //   newPostsSlides
		  // );

		  // Remove slides beyond 11 (1 first slide + 10 regular slides)
		  const $slides = $featuredSwiper.find('.swiper-slide');
		  // console.log($slides.length + ' new number of total slides');
		  const totalSlides = $slides.length;
		  if (totalSlides > 11) {
			const slidesToRemove = [];
			for (let i = 11; i < totalSlides; i++) {
			  slidesToRemove.push(i);
			}
			swiper.removeSlide(slidesToRemove);
			// console.log(slidesToRemove, 'slides removed');
		  }

		  // All done
		  $featuredSwiper.removeClass('is-loading');
		},
		error: function(errorThrown) {
		  $featuredSwiper.removeClass('is-loading');
		  console.error(errorThrown);
		},
	  });
	}
  // handle project section here
  let gpea_projects_followed = Cookies.get('gpea_projects');
  if (typeof gpea_projects_followed !== 'undefined' && gpea_projects_followed.length > 0) {
	  $sectionProjects.addClass('is-loading');
	  $.ajax({
		url: p4_vars.ajaxurl,
		type: 'post',
		data: {
		  action: 'projectsFollowing',
		},
		success: function(data) {
		  let projects;

		  try {
			projects = JSON.parse(data);
		  } catch (error) {
			console.error(error);
			$sectionProjects.removeClass('is-loading');
			$sectionProjects.addClass('is-loaded');
			return;
		  }
		  // Create the HTML element for each new post

		  const projectContainer = $('#template-section-projects-following');
		  const buildContainer = template(projectContainer[0].innerHTML);
		  const projectUpdate = $('#template-project-post-update');
		  const buildUpdate = template(projectUpdate[0].innerHTML);
		  const projectUpdateMobile = $('#template-project-post-update-small');
		  const buildUpdateMobile = template(projectUpdateMobile[0].innerHTML);

		  // const newPostsSlides = posts.map(post => {
		  //   if (post.engaging_pageid !== undefined) {
		  //     return buildPetition(post);
		  //   } else {
		  //     return buildUpdate(post);
		  //   }
		  // });

		  if (projects.length) {
			const projectFollowing = projects.map(project => {
			  let relatedPosts = project.related.map(post => {
				return buildUpdate(post);
			  });
			  project.related_posts = relatedPosts.join('');

			  let relatedPostsMobile = project.related.map(post => {
				return buildUpdateMobile(post);
			  });
			  project.related_posts_mobile = relatedPostsMobile.join('');

			  let sectionProject = buildContainer(project);
			  return sectionProject;
			});

			const $oldContent = $sectionProjects.find('> .ct-container');
			$oldContent.remove();
			$sectionProjects.prepend(projectFollowing);

			let resizeEvent = new Event('resize');
			window.dispatchEvent(resizeEvent);

			$sectionProjects.removeClass('dark');
		  }

		  // All done
		  $sectionProjects.removeClass('is-loading');
		  $sectionProjects.addClass('is-loaded');
		},
		error: function(errorThrown) {
		  $sectionProjects.removeClass('is-loading');
		  $sectionProjects.addClass('is-loaded');
		  console.error(errorThrown);
		},
	  });
  } else {
	$sectionProjects.removeClass('is-loading');
	$sectionProjects.addClass('is-loaded');
  }
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
