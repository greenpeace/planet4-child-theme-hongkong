import template from 'lodash.template';

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

const homeFollower = function() {
  if (!$('body').hasClass('js-home-follower')) return;

  const $featuredSwiper = $('.section-featured .swiper-container').first();
  const $existingPosts = $featuredSwiper.find('.swiper-slide');

  const existingPosts = $existingPosts
    .toArray()
    .filter(el => 'postid' in el.dataset)
    .map(el => ({
      ID: el.dataset.postid,
    }));

  $featuredSwiper.addClass('is-loading');
  $.ajax({
    url: p4_vars.ajaxurl,
    type: 'post',
    data: {
      action: 'topicsFollowing',
    },
    success: function(data) {
      $featuredSwiper.removeClass('is-loading');

      const swiper = $featuredSwiper[0];
      let posts;

      try {
        posts = JSON.parse(data);
      } catch (error) {
        console.error(errorThrown);
        return;
      }

      // Remove existing posts from the returned posts
      posts = filterDuplicates(posts, existingPosts);

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

      // Add the new slides at position 5
      swiper.addSlide(4, newPostsSlides);

      // Remove slides beyond 8
      const $slides = $featuredSwiper.find('.swiper-slide');
      const totalSlides = $slides.length;
      if (totalSlides > 8) {
        const slidesToRemove = [];
        for (let i = 8; i < totalSlides; i++) {
          slidesToRemove.push(i);
        }
        swiper.removeSlide(slidesToRemove);
      }
    },
    error: function(errorThrown) {
      $featuredSwiper.removeClass('is-loading');
      console.error(errorThrown);
    },
  });
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
