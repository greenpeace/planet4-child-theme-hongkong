import pageProject from './followUnfollow/page-project';
import pageIssue from './followUnfollow/page-issue';
import tagCloud from './followUnfollow/tag-cloud';
import homeFollower from './followUnfollow/home-follower';

const followUnfollow = function() {
  pageProject();
  pageIssue();
  tagCloud();
  homeFollower();
};

export default followUnfollow;
