/**
 * Everything you need to start your frontend project with gulp, now.
 *
 * This setup will output a single app.js and a single app.css file, which is
 * what you will want to do in most websites (less files = less requests =
 * faster loading times). It will:
 * 	- convert your JSX (react) into proper JS
 * 	- convert your ES2015 files to ES5, concatenate and minify them
 * 	- convert your SCSS files to CSS, autoprefix, concatenate and minify them
 * 	- watch your JS and reload the browser on change
 * 	- watch your CSS and inject the new rules on change
 * 	- watch your HTML and PHP and reload the browser on change
 * 	- provide a server at localhost:3000 and 192.168.my.ip:3000
 * 	- make sure all browsers find the polyfills for Promise and fetch
 *
 * Moreover it can (simply uncomment the corresponding lines further down
 * the code below, they start with #MASONRY, #GSAP, #FOUNDATION, #JQUERY):
 * 	- make sure '$' and 'jQuery' variables are available to plugins and modules
 * 	- make Foundation (JS and SCSS) work
 * 	- make sure GSAP plugins find their parents (TweenLite and TweenMax)
 * 	- make sure Masonry, Isotope and imagesloaded work as they are supposed to
 *
 * COMMANDS
 * $ gulp          Start watching and fire up a browser tab at localhost:3000
 * $ gulp watch    Start watching but do not open a new tab
 * $ gulp build    Build and compress all files production ready mode
 *
 */

/* eslint-disable no-multi-spaces */
const fs = require('fs');
const path = require('path');
const gulp = require('gulp');
const log = require('fancy-log');
const colors = require('ansi-colors');
const PluginError = require('plugin-error');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const notify = require('gulp-notify');
const webpack = require('webpack');
const browserSync = require('browser-sync');
/* eslint-enable no-multi-spaces */

/**
 * Edit the following to suit your project
 * (or, better, edit gulpconfig.js)
 * @type {Object}
 */
const settings = {
  // this is the folder where your scss files reside (you can use subfolders)
  styleSrc: './frontend/scss/**/*.{sass,scss}',

  // this is a helper for the soucemaps: make sure it matches the above,
  // it's relative to the position of the final css
  styleMapRoot: '../../frontend/scss/',

  // here is where app.css will end up
  styleDest: './static/css/',

  // this is the entry js file(s) (the array keys define the output filenames),
  // it's used only by the 'webpack' task
  jsEntry: {
    script: ['./frontend/js/index.js'],
    search: ['./frontend/js/search-page.js'],
    donation: ['./frontend/js/donation-page.js'],
  },

  // this is a helper for the soucemaps: make sure it matches the above,
  // it's relative to the position of the final css
  jsMapRoot: '../../frontend/js/',

  // this is where the files defined in jsEntry will end up
  jsDest: path.join(__dirname, '/static/js/'),

  // here you can tell browserSync which static (PHP, HTML, etc…) files to watch
  watch: [
    '../../plugins/planet4-gpea-plugin-blocks/**/*.twig',
    './templates/**/*.twig',
    './static/demo/**/*.html',
  ],

  // now you have two choices: either you indicate a folder which will be
  // considered the document root by the server [docroot], or you can
  // specify which virtual host domain to proxy in gulpconfig.js (comment this
  // line and uncomment below to include gulpconfig.js)
  //docroot: './dist',
};

// Merge settings with local config
const localConfig = require('./gulpconfig');

for (const attrName in localConfig) {
  // eslint-disable-line
  if (localConfig.hasOwnProperty(attrName)) {
    // eslint-disable-line
    settings[attrName] = localConfig[attrName];
  }
}

// You can stop editing here, the rest will just work, unless you need
// Masonry, GSAP, jQuery or Foundation, then keep looking down --v

/**
 * Notify the error and let gulp go on
 */
function handleErrors(errorObject, callback) {
  // eslint-disable-line
  // eslint-disable-next-line
  notify
    .onError(
      errorObject
        .toString()
        .split(': ')
        .join(':\n')
    )
    .apply(this, arguments);
  if (typeof this.emit === 'function') this.emit('end'); // Keep gulp from hanging on this task
}

/**
 * Format milliseconds to 999ms or 1.23s
 */
function prettifyTime(milliseconds) {
  if (milliseconds > 999) {
    return `${(milliseconds / 1000).toFixed(2)} ms`;
  }

  return `${milliseconds} ms`;
}

/**
 * Log a webpack error to console
 */
function logger(err, stats) {
  let statColor;
  let compileTime;

  if (err) throw new PluginError('webpack', err);

  statColor = stats.compilation.warnings.length < 1 ? 'green' : 'yellow';

  if (stats.compilation.errors.length > 0) {
    stats.compilation.errors.forEach(error => {
      handleErrors(error);
      statColor = 'red';
    });
  } else {
    compileTime = prettifyTime(stats.endTime - stats.startTime);
    log(colors[statColor](stats));
    log(
      'Compiled with',
      colors.cyan('webpack:development'),
      'in',
      colors.magenta(compileTime)
    );
  }
}

const scss = isProduction => {
  const outputStyle = isProduction ? 'compressed' : 'compact';
  const config = {
    sass: {
      outputStyle,
    },
  };

  const scss = () =>
    gulp
      .src(settings.styleSrc)
      .pipe(sourcemaps.init())
      .pipe(sass(config.sass))
      .on('error', handleErrors)
      .pipe(autoprefixer(/*config.autoprefixer*/))
      .pipe(
        sourcemaps.write('./', {
          includeContent: false,
          sourceRoot: settings.styleMapRoot,
        })
      )
      .pipe(gulp.dest(settings.styleDest))
      .pipe(browserSync.stream({ match: '**/*.css' }))
      .pipe(gulp.dest(settings.styleDest));

  return scss;
};

const jsPack = (isProduction, doWatch) => {
  let built = false;
  const config = {
    entry: settings.jsEntry,
    output: {
      path: settings.jsDest,
      filename: '[name].js',
    },
    module: {
      rules: [
        // #MASONRY - Uncomment here
        // (https://github.com/desandro/masonry/issues/679)
        /* {
					test: /(masonry-layout|isotope-layout|imagesloaded)/,
					loader: 'imports?define=>false&this=>window'
				}, */
        // #JQUERY - Uncomment this
        // (make '$' and 'jQuery' globals)
        /* {
					test: /\/jquery\.js$/,
					loader: 'expose-loader?$!expose-loader?jQuery'
				}, */
        {
          test: /\.jsx?$/,
          exclude: /node_modules\/(?!foundation)/,
          loader: 'babel-loader',
          query: { presets: ['@babel/preset-env', '@babel/preset-react'] },
        },
        // {
        //   enforce: 'pre',
        //   test: /\.jsx?$/, // include .js files
        //   exclude: /node_modules/, // exclude any and all files in the node_modules folder
        //   loader: 'eslint-loader',
        // },
      ],
    },
    resolve: {
      extensions: ['.js', '.jsx'],
      alias: {
        // #GSAP - Uncomment here
        // (needed to have GSAP plugins satisfy their "requires")
        /* 'TweenLite': 'gsap/src/uncompressed/TweenLite',
				'TweenMax': 'gsap/src/uncompressed/TweenMax', */
      },
    },
    plugins: [
      new webpack.ProvidePlugin({
        Promise: 'promise-polyfill',
        fetch: 'exports-loader?self.fetch!whatwg-fetch',
      }),
    ],
  };

  if (isProduction) {
    config.mode = 'production';
    config.optimization = {
      noEmitOnErrors: true,
    };
  } else {
    config.mode = 'development';
  }

  let jsPack;

  if (doWatch) {
    jsPack = () =>
      new Promise(resolve =>
        webpack(config).watch(200, (err, stats) => {
          logger(err, stats);
          browserSync.reload();
          // On the initial compile, let gulp know the task is done
          if (!built) {
            built = true;
            resolve();
          }
        })
      );
  } else {
    jsPack = () =>
      new Promise(resolve =>
        webpack(config, (err, stats) => {
          logger(err, stats);
          resolve();
        })
      );
  }

  return jsPack;
};

const watcher = doOpen => {
  const config = {
    open: doOpen || false,
    files: settings.watch,
  };

  try {
    if (fs.existsSync('./certs/planet4.test+2.pem')) {
      config.https = {
        key: './certs/planet4.test+2-key.pem',
        cert: './certs/planet4.test+2.pem',
      };
    }
  } catch (err) {
    // do nothing
  }

  if (settings.proxy) {
    config.proxy = settings.proxy;
  } else {
    config.server = settings.docroot;
  }

  const watcher = () => {
    browserSync.init(config);
    gulp.watch(settings.styleSrc, scss());
  };

  return watcher;
};

exports.build = gulp.parallel(scss(true), jsPack(true));
exports.watch = gulp.series(
  gulp.parallel(scss(), jsPack(false, true)),
  watcher(false)
);
exports.default = gulp.series(
  gulp.parallel(scss(), jsPack(false, true)),
  watcher(true)
);
