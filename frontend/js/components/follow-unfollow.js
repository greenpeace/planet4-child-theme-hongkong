import pageProject from './followUnfollow/page-project';
import pageIssue from './followUnfollow/page-issue';
import tagCloud from './followUnfollow/tag-cloud';
import homeFollower from './followUnfollow/home-follower';
import latestFollower from './followUnfollow/latest-follower';
import makechangeFollower from './followUnfollow/makechange-follower';

const followUnfollow = function() {
  pageProject();
  pageIssue();
  tagCloud();
  homeFollower();
  latestFollower();
  makechangeFollower();
};

export default followUnfollow;
