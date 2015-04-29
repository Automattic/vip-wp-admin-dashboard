/**
 * Forked from Scott's Glug
 *
 * https://github.com/scottsweb/glug
 */


/**
 * Settings
 *
 * Setup your project paths and requirements here
 */
var settings = {

	// path to JS files
	scriptspath: 'src/js/*.js',
	scriptspath_base: 'src/js/',
	scriptspath_jquery: 'src/js/jquery-1.9.1.min.js',
	scriptspath_plugins: 'src/js/plugins.js',
	scriptspath_app: 'src/js/app.js',
	jspath: 'assets/js/',

	// path to main scss file
	scss: 'src/scss/style.scss',

	// path to output css file
	css: 'assets/css/style.css',

	// path to watch for changed scss files
	scsswatch: 'src/scss/**/*.scss',

	// path to output css folder
	csspath: 'assets/css/',

	// path to images
	imagespath: 'src/img/',
	imagesdistpath: 'assets/img/',

	// path to base
	basepath: './',
	
	// path to html
	htmlpath: './*.html',
	// partials: ['app/**/*.html', '!app/index.html'],

	// enable the static file server and browsersync
	// check for unused styles in static html? - seems buggy, requires html
	staticserver: true,
	checkunusedcss: false,

	// enable the proxied local server for browsersync
	// static above server must be disabled
	proxyserver: false,
	proxylocation: 'mysite.dev'

};

/**
 * Load node modules
 */
var	gulp = require('gulp'),
	
	// Plugins
	autoprefixer = require('gulp-autoprefixer'),
	browserify = require( 'browserify' ),
	browsersync = require('browser-sync'),
	checkcss = require( 'gulp-check-unused-css' ),
	concat = require('gulp-concat'),
	csscomb = require('gulp-csscomb'),
	filter = require('gulp-filter'),
	imagemin = require('gulp-imagemin'),
	minifycss = require('gulp-minify-css'),
	notify = require('gulp-notify'),
	parker = require('gulp-parker'),
	plumber = require('gulp-plumber'),
	sass = require('gulp-sass'),
	reactify = require( 'reactify' ),
	sourcemaps = require('gulp-sourcemaps'),
	sync = require('gulp-config-sync'),
	uglify = require('gulp-uglify'),
	watch = require('gulp-watch');

/**
 * Generic error handler used by plumber
 *
 * Display an OS notification and sound with error message
 */
var onError = function(err) {
	notify.onError({
		title:	"Gulp",
		message: "Error: <%= error.message %>\nLine: <%= error.lineNumber %>",
		sound:	"Sosumi"
	})(err);

	this.emit('end');
};

/**
 * Default Task
 *
 * Watch for changes and run tasks
 */
gulp.task('default', function() {
	
	// Compile Styles on start
	gulp.start('styles');
	
	// Compile Scripts on start
	gulp.start('scripts');
	
	// Process Images on start
	gulp.start('images');

	// Browsersync and local server
	// Options: http://www.browsersync.io/docs/options/
	if (settings.staticserver) {
		browsersync({
			server: settings.basepath
		});

		// Check to see if the CSS is being used
		if (settings.checkunusedcss) {
			gulp.watch(settings.css, ['checkcss']);
		}
	}

	if (settings.proxyserver) {
		browsersync({
			proxy: settings.proxylocation
		});
	}

	// Watch for SCSS changes
	gulp.watch(settings.scsswatch, ['styles']);

	// Watch for image changes
	gulp.watch(settings.imagespath, ['images']);
	
	// Watch for JS changes
	gulp.watch(settings.scriptspath, ['scripts']);
	
	// Watch for HTML changes
	gulp.watch(settings.htmlpath, ['markup']);

});

/**
 * Stylesheet Task
 *
 * SCSS -> CSS
 * Autoprefix
 * CSSComb
 * Sourcemaps
 * Minify
 * Report
 */
gulp.task('styles', function() {
	return gulp.src(settings.scss)
		.pipe(plumber({errorHandler: onError}))
		.pipe(sass({
			style: 'expanded',
			errLogToConsole: false
		}))
		.pipe(sourcemaps.init())
		.pipe(autoprefixer('last 2 versions', 'ie 8', 'ie 9'))
		.pipe(csscomb())
		.pipe(sourcemaps.write('./'))
		.pipe(minifycss())
		.pipe(gulp.dest(settings.csspath))
		.pipe(filter('**/*.css'))
		.pipe(browsersync.reload({stream: true}))
		.pipe(parker())
		.pipe(notify({ message: 'Styles task complete' }));
});

/**
 * Javascript Task
 *
 * Minify and contact all JS files into one
 */
gulp.task('scripts', function() {
	return gulp.src( [ settings.scriptspath_jquery, settings.scriptspath_plugins, settings.scriptspath_app ], { base: settings.scriptspath_base } )
	    .pipe(concat('app.js'))
	    .pipe(uglify())
	    .pipe(gulp.dest( settings.jspath ))
		.pipe(browsersync.reload({stream: true}))
		.pipe(notify({ message: 'Scripts task complete' }));
});

/**
 * Images Task
 *
 * Run independantly when you want to optimise image assets
 */
gulp.task('images', function() {
	return gulp.src(settings.imagespath + '**/*.{gif,jpg,png}')
		.pipe(plumber({errorHandler: onError}))
		.pipe(imagemin({
			progressive: true,
			interlaced: true,
			//svgoPlugins: [ {removeViewBox:false}, {removeUselessStrokeAndFill:false} ]
		}))
		.pipe(gulp.dest(settings.imagesdistpath))
		.pipe(browsersync.reload({stream: true}))
		.pipe(notify({ message: 'Images task complete' }));
});

/**
 * CheckCSS Task
 *
 * Are all our styles being used correctly?
 */
gulp.task('checkcss', function() {
	return gulp.src([ settings.css, settings.staticlocation + '*.html' ])
		.pipe(plumber({errorHandler: onError}))
		.pipe(checkcss());
});


/**
 * Reload HTML files
 *
 * If modified, refreshes HTML files
 */
gulp.task('markup', function() {
	return gulp.src(settings.htmlpath)
		.pipe(browsersync.reload({stream: true}))
		.pipe(notify({ message: 'Markup task complete' }));
});
