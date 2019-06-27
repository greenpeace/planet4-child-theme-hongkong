const homeFollower = require('./followUnfollow/home-follower');

const posts = [
  {
    ID: 20,
    date: '2018 - 05 - 26',
  },
  {
    ID: 30,
    date: '2019 - 05 - 26',
  },
  {
    ID: 40,
    date: '2017 - 05 - 26',
  },
];

test('filterDuplicates filters out alright', () => {
  const existingPosts = [
    {
      ID: 20,
    },
    {
      ID: 25,
    },
    {
      ID: 40,
    },
  ];

  const expected = [
    {
      ID: 30,
    },
  ];

  const result = homeFollower.filterDuplicates(posts, existingPosts);

  expect(result).toHaveLength(1);
  expect(result[0].ID).toBe(30);
});

test('sortByRecentFirst sorts alright', () => {
  const clonedPosts = posts.slice(0);
  homeFollower.sortByRecentFirst(clonedPosts);

  expect(clonedPosts[0].date).toBe('2019 - 05 - 26');
  expect(clonedPosts[2].date).toBe('2017 - 05 - 26');
});
