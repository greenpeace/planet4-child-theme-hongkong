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
 * Takes the collections returned by the AJAX call and filters out collections already existing.
 * Elements in both arrays must have a property "source"
 *
 * @param {Array} newCollections An array containing the new posts as returned by the call
 * @param {Array} existingCollections An array of existing posts in the carousel
 */
export const filterDuplicatesCollections = function(newCollections, existingCollections) {
	return newCollections.filter(
		newCollection =>
		existingCollections.find(existingCollection => existingCollection.slug == newCollection.slug) ===
		undefined
	);
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


export const sortByRecentUnixtime = function(posts) {
  return posts.sort(byRecentDateUnixtime);
};

function byRecentDateUnixtime(a, b) {
   if (a[1] < b[1]) return -1;
   if (a[1] > b[1]) return 1;
   return 0;
 }

const latestFollower = function() {
  if (!$('body').hasClass('js-latest-earth')) return;

  const $featuredSwiper = $('.section-featured .swiper-container').first();
  const $existingPosts = $featuredSwiper.find('.swiper-slide');  

  const existingPosts = $existingPosts
    .toArray()
    .filter(el => 'postid' in el.dataset)
    .map(el => ({
      ID: el.dataset.postid,
	}));
	
  const $trendingCollections = $('.section-article-row');

  const existingCollections = $trendingCollections
    .toArray()
    .filter(el => 'source' in el.dataset)
    .map(el => ({
      slug: el.dataset.source,
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
		  // console.log(data);
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
			followingResultsPosts = postsTag.posts;
			posts = posts.concat(followingResultsPosts);
		  });

		  // Remove existing posts from the returned posts, take first 5, sort by recent first
		  // console.log(posts.length + ' posts returned');
		  posts = filterDuplicates(posts, existingPosts);
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
		  swiper.addSlide(6, newPostsSlides);
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


		  // handle here also trending collections, same ajax call
		  // Create the HTML element for each new post
		  
		  followingResults = filterDuplicatesCollections(followingResults, existingCollections);
		  sortByRecentUnixtime(followingResults);
		  followingResults = followingResults.slice(0, 3);

		  const articleRowContainer = $('#template-article-row-container');
		  const buildArticleRowContainer = template(articleRowContainer[0].innerHTML);
		  const articleRowPost = $('#template-article-row');
		  const buildArticleRowPost = template(articleRowPost[0].innerHTML);

		  const trendingCollectionsDynamic = followingResults.map(collection => {        
			let rowPosts = (collection.posts).map(post => {
				return buildArticleRowPost(post);            
			  })
			  collection.row_posts = rowPosts.join('');
			  let trendingCollectionsContainer = buildArticleRowContainer(collection);
			  return trendingCollectionsContainer;
		  });


		  // Add the new slides in 6th position (1 first slide + 5 regular slides)
		  // swiper.addSlide(6, newPostsSlides);
		  // console.log(
		  //   newPostsSlides.length + ' slides created and added',
		  //   newPostsSlides
		  // );

		  // da sistemare per mostrare anche 2 trending attuali
		  $trendingCollections.first().prepend(trendingCollectionsDynamic);

		  let resizeEvent = new Event('resize');
		  window.dispatchEvent(resizeEvent);

		  // brutality take first 5...
		  $( ".section-article-row" ).hide().slice( 0, 5 ).show();


		},
		error: function(errorThrown) {
		  $featuredSwiper.removeClass('is-loading');
		  console.error(errorThrown);
		},
	  });
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

export default latestFollower;
