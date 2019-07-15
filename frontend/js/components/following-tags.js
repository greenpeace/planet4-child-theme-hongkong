/**
 * Functionality for the tag cloud. TODO
 *
 * On submit it saves the selected tags in localStorage and then... wat?
 */
export default function() {
  let following = window.localStorage.getItem('following');

  document.querySelectorAll('.topic-button').forEach(element => {
    if (following) {
      following.split(',').forEach(item => {
        if (item === element.getAttribute('data'))
          element.classList.add('active');
      });
    }

    element.addEventListener('click', e => {
      const data = e.target.getAttribute('data');
      if (following) {
        if (!following.split(',').includes(data)) {
          e.target.classList.add('active');
          following = `${following},${data}`;
        } else {
          e.target.classList.remove('active');
          following = following
            .split(',')
            .filter(item => item !== data)
            .reduce(
              (string, item) => (string ? `${string},${item}` : item),
              ''
            );
        }
      } else {
        e.target.classList.add('active');
        following = data;
      }
    });
  });

  const topicSubmitButton = document.getElementById('topic-submit-button');
  if (topicSubmitButton) {
    topicSubmitButton.onclick = () => {
      window.localStorage.setItem('following', following);
    };
  }
}
