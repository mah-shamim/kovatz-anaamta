'use strict';

let gulp            = require('gulp'),
	rename          = require('gulp-rename'),
	notify          = require('gulp-notify'),
	autoprefixer    = require('gulp-autoprefixer'),
	sass            = require( 'gulp-sass')(require('sass')),
	plumber         = require('gulp-plumber');

gulp.task('wp-admin-styles', () => {
	return gulp.src('./assets/scss/wp-admin-styles.scss')
		.pipe(
			plumber( {
				errorHandler: function ( error ) {
					console.log('=================ERROR=================');
					console.log(error.message);
					this.emit( 'end' );
				}
			})
		)
		.pipe(sass( { outputStyle: 'compressed' } ))
		.pipe(autoprefixer({
			browsers: ['last 10 versions'],
			cascade: false
		}))

		.pipe(rename('wp-admin-styles.css'))
		.pipe(gulp.dest('./assets/css/'))
		.pipe(notify('Compile Sass Done!'));
});

gulp.task('jet-dashboard-admin', () => {
	return gulp.src('./assets/scss/jet-dashboard-admin.scss')
		.pipe(
			plumber( {
				errorHandler: function ( error ) {
					console.log('=================ERROR=================');
					console.log(error.message);
					this.emit( 'end' );
				}
			})
		)
		.pipe(sass( { outputStyle: 'compressed' } ))
		.pipe(autoprefixer({
				browsers: ['last 10 versions'],
				cascade: false
		}))

		.pipe(rename('jet-dashboard-admin.css'))
		.pipe(gulp.dest('./assets/css/'))
		.pipe(notify('Compile Sass Done!'));
});

gulp.task('jet-dashboard-admin-rtl', () => {
	return gulp.src('./assets/scss/jet-dashboard-admin-rtl.scss')
		.pipe(
			plumber( {
				errorHandler: function ( error ) {
					console.log('=================ERROR=================');
					console.log(error.message);
					this.emit( 'end' );
				}
			})
		)
		.pipe(sass( { outputStyle: 'compressed' } ))
		.pipe(autoprefixer({
			browsers: ['last 10 versions'],
			cascade: false
		}))

		.pipe(rename('jet-dashboard-admin-rtl.css'))
		.pipe(gulp.dest('./assets/css/'))
		.pipe(notify('Compile Sass Done!'));
});

//watch
gulp.task('watch', () => {
	gulp.watch( './assets/scss/**', gulp.series( ...[ 'jet-dashboard-admin', 'jet-dashboard-admin-rtl', 'wp-admin-styles' ] ) );
});


