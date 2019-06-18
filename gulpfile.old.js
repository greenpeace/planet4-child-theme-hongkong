const browserSync = require('browser-sync').create();
const del = require('del');
const gulp = require('gulp');
const plumber = require('gulp-plumber');
const postcss = require('gulp-postcss');
const rucksack = require('rucksack-css');
const lost = require('lost');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const rename = require('gulp-rename');
const notify = require('gulp-notify');
const sass = require('gulp-sass');
const webpack = require('webpack-stream');

const path = './344_gp_frontend/src';

function clean() {
  return del('static');
}

const scss = isProduction => {
  var postCssPlugin = [
    rucksack(),
    lost(),
    autoprefixer({
      browsers: ['> 1%', 'last 3 versions', 'Firefox >= 20', 'iOS >=7'],
    }),
  ];

  if (isProduction) postCssPlugin.push(cssnano());

  return () =>
    gulp
      .src(path + '/scss/main.scss')
      .pipe(
        plumber({ errorHandler: notify.onError('Error: <%= error.message %>') })
      )
      .pipe(sass().on('error', sass.logError))
      .pipe(postcss(postCssPlugin))
      .pipe(rename('style.css'))
      .pipe(gulp.dest(path + '/assets/css/'))
      .pipe(browserSync.stream());
};

const js = isProduction => {
  return () =>
    gulp
      .src(path + '/js/index.js')
      .pipe(
        webpack({
          mode: isProduction ? 'production' : 'development',
        })
      )
      .pipe(rename('script.js'))
      .pipe(gulp.dest(path + '/assets/js/'))
      .pipe(browserSync.stream());
};

const assets = () => {
  return gulp.src(path + '/assets/**/*').pipe(gulp.dest('static'));
};

const fonts = () => {
  return gulp.src('static/fonts/**/*').pipe(gulp.dest('static/css/fonts/'));
};

function cleanfonts() {
  return del('static/fonts');
}

function cleanjs() {
  return del('static/script.js');
}

function cleancss() {
  return del('static/style.css');
}

const watch = () => {
  browserSync.init({
    server: path,
    notify: false,
    port: 8080,
  });

  gulp.watch(path + '/scss/**', scss());
  gulp.watch(path + '/js/*.js', js());
  gulp.watch(path + '/*.html').on('change', browserSync.reload);
};

exports.build = gulp.series(
  clean,
  gulp.parallel(scss(true), js(true)),
  assets,
  /*fonts, cleanfonts,*/ cleanjs,
  cleancss
);
exports.sass = gulp.series(
  clean,
  gulp.parallel(scss(true)),
  assets,
  /*fonts, cleanfonts,*/ cleanjs,
  cleancss
);
exports.default = gulp.series(gulp.parallel(scss(true), js(true)), watch);
