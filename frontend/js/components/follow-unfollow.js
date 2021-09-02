import pageProject from './followUnfollow/page-project';
import pageIssue from './followUnfollow/page-issue';
import tagCloud from './followUnfollow/tag-cloud';

const followUnfollow = function() {
  pageProject();
  pageIssue();
  tagCloud();
};

export default followUnfollow;
