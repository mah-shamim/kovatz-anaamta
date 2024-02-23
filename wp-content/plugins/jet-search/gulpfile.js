'use strict';

var gulp            = require( 'gulp' ),
	rename          = require( 'gulp-rename' ),
	notify          = require( 'gulp-notify' ),
	autoprefixer    = require( 'gulp-autoprefixer' ),
	sass            = require( 'gulp-sass' ),
	plumber         = require( 'gulp-plumber' ),
	livereload      = require( 'gulp-livereload' ),
	checktextdomain = require( 'gulp-checktextdomain' );

//css
gulp.task( 'css', function() {
	return gulp.src( './assets/scss/jet-search.scss' )
		.pipe(
			plumber( {
				errorHandler: function( error ) {
					console.log( '=================ERROR=================' );
					console.log( error.message );
					this.emit( 'end' );
				}
			} )
		)
		.pipe( sass( { outputStyle: 'compressed' } ) )
		.pipe( autoprefixer( {
			browsers: ['last 10 versions'],
			cascade:  false
		} ) )

		.pipe( rename( 'jet-search.css' ) )
		.pipe( gulp.dest( './assets/css/' ) )
		.pipe( livereload() )
		.pipe( notify( 'Compile Sass Done!' ) );
} );

//css-suggestions
gulp.task( 'css-suggestions', function() {
	return gulp.src( './assets/scss/admin/suggestions.scss' )
		.pipe(
			plumber( {
				errorHandler: function( error ) {
					console.log( '=================ERROR=================' );
					console.log( error.message );
					this.emit( 'end' );
				}
			} )
		)
		.pipe( sass( { outputStyle: 'compressed' } ) )
		.pipe( autoprefixer( {
			browsers: ['last 10 versions'],
			cascade:  false
		} ) )

		.pipe( rename( 'jet-search-suggestions.css' ) )
		.pipe( gulp.dest( './assets/css/admin' ) )
		.pipe( livereload() )
		.pipe( notify( 'Compile Sass Done!' ) );
} );

//icons
gulp.task( 'jet-search-icons', () => {
	return gulp.src( './assets/scss/jet-search-icons.scss' )
		.pipe(
			plumber( {
				errorHandler: function( error ) {
					console.log( '=================ERROR=================' );
					console.log( error.message );
					this.emit( 'end' );
				}
			} )
		)
		.pipe( sass( { outputStyle: 'compressed' } ) )
		.pipe( autoprefixer( {
			browsers: ['last 10 versions'],
			cascade:  false
		} ) )

		.pipe( rename( 'jet-search-icons.css' ) )
		.pipe( gulp.dest( './assets/css/' ) )
		.pipe( notify( 'Compile Sass Done!' ) );
} );

//css-editor
gulp.task( 'css-editor', () => {
	return gulp.src( './assets/scss/jet-search-editor.scss' )
		.pipe(
			plumber( {
				errorHandler: function( error ) {
					console.log( '=================ERROR=================' );
					console.log( error.message );
					this.emit( 'end' );
				}
			} )
		)
		.pipe( sass( { outputStyle: 'compressed' } ) )
		.pipe( autoprefixer( {
			browsers: ['last 10 versions'],
			cascade:  false
		} ) )

		.pipe( rename( 'jet-search-editor.css' ) )
		.pipe( gulp.dest( './assets/css/' ) )
		.pipe( livereload() )
		.pipe( notify( 'Compile Sass Done!' ) );
} );

//watch
gulp.task( 'watch', function() {
	//livereload.listen();
	gulp.watch( './assets/scss/**', gulp.series( ...['css', 'css-editor', 'css-suggestions', 'jet-search-icons'] ) );
} );

//checktextdomain
gulp.task( 'checktextdomain', function() {
	return gulp.src( ['**/*.php'] )
		.pipe( checktextdomain( {
			text_domain: 'jet-search',
			keywords:    [
				'__:1,2d',
				'_e:1,2d',
				'_x:1,2c,3d',
				'esc_html__:1,2d',
				'esc_html_e:1,2d',
				'esc_html_x:1,2c,3d',
				'esc_attr__:1,2d',
				'esc_attr_e:1,2d',
				'esc_attr_x:1,2c,3d',
				'_ex:1,2c,3d',
				'_n:1,2,4d',
				'_nx:1,2,4c,5d',
				'_n_noop:1,2,3d',
				'_nx_noop:1,2,3c,4d',
				'translate_nooped_plural:1,2c,3d'
			]
		} ) );
} );

