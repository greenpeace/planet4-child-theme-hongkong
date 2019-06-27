const homeFollower = require('./followUnfollow/home-follower');

test('filterDuplicates filters out alright', () => {
  const newPosts = [
    {
      ID: 20,
    },
    {
      ID: 30,
    },
    {
      ID: 40,
    },
  ];

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

  const result = homeFollower.filterDuplicates(newPosts, existingPosts);

  expect(result).toHaveLength(1);
  expect(result[0].ID).toBe(30);
});
