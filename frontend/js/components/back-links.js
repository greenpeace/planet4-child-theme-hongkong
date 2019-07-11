// Returns TRUE if the previous (referrer) page is from a different website
const isFirstSessionPage = function() {
  const referrerHost = document.referrer
    .replace(/^https?:\/\//, '')
    .split('/')[0];
  return referrerHost !== window.location.host;
};

// Returns TRUE if it's the Home, Make a Change or Latest from the Earth page
const isMainPage = function() {
  const regex = /\/(make-change|make-a-change|from-the-earth)\//;
  return (
    document.body.classList.contains('home') ||
    window.location.href.match(regex) !== null
  );
};

// Returns TRUE if it's one of the About pages
const isAbout = function() {
  const regex = /\/about\//;
  return window.location.href.match(regex) !== null;
};

const backLink = function() {
  const link = document.querySelector('.js-back-button');
  if (!link) return;

  const latestMainPage = window.localStorage.getItem('latest-main-page');
  if (latestMainPage) link.href = latestMainPage;

  /* keep it commented in case we want to re-add it later
  link.addEventListener('mouseenter', function() {
    document.body.style.willChange = 'opacity, transform';
  });

  link.addEventListener('mouseleave', function() {
    document.body.style.willChange = null;
  });

  link.addEventListener('click', function() {
    document.body.classList.add('is-backing');
  }); */
};

const closeLink = function() {
  const link = document.querySelector('.js-close-button');
  if (!link) return;

  const latestPage = window.localStorage.getItem('latest-page');
  if (latestPage) link.href = latestPage;

  /* keep it commented in case we want to re-add it later
  link.addEventListener('mouseenter', function() {
    document.body.style.willChange = 'opacity, transform';
  });

  link.addEventListener('mouseleave', function() {
    document.body.style.willChange = null;
  });

  link.addEventListener('click', function() {
    document.body.classList.add('is-exiting');
  }); */
};

/**
 * Functionality to set dynamic back link (aka "navigation of Francesco")
 */
export default function() {
  // If it's the first page visited on GP reset the saved items
  if (isFirstSessionPage()) {
    window.localStorage.setItem('latest-main-page', '');
    window.localStorage.setItem('latest-page', '');
  }

  // Attach back link functionality (the one in Main Issue, Project, Report, Post pages)
  backLink();

  // Attach close link functionality (the one in About pages)
  closeLink();

  // If we are on a main page, save it
  if (isMainPage()) {
    window.localStorage.setItem('latest-main-page', window.location.href);
  }

  // If we are NOT in an About page, save it
  if (!isAbout()) {
    window.localStorage.setItem('latest-page', window.location.href);
  }
}
