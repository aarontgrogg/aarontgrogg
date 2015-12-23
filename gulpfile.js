// define requirements
var gulp = require('gulp'),
    plugins = require('gulp-load-plugins')(),
    AUTOPREFIXER_MATRIX = 'last 2 version',
    THEME_DIST_DIR = 'dist/wp-content/themes/atg',
    PLUGIN_DIST_DIR = 'dist/wp-content/plugins/',
    UPLOADS_DIST_DIR = 'dist/wp-content/uploads/';

// run SVG-related tasks
/* maybe use these?
    gulp-svg-sprite
    gulp-svg2png
    gulp-svgo
*/
/*gulp.task('icons', function() {
    return gulp.src('src/icons/*.svg')                  // grab all src css files
        .pipe(...); // save files into dist directory
});*/

// run theme CSS-related tasks
gulp.task( 'styles-theme', function() {
    return gulp.src( 'src/styles/*.css' )                   // grab all src css files
        .pipe( plugins.changedInPlace() )                   // check if source has changed since last build
        .pipe( plugins.autoprefixer( AUTOPREFIXER_MATRIX ) ) // remove unneeded and add needed prefixes
        .pipe( plugins.concat( 'style-min.css' ) )          // concatenate all css files into a single files
        .pipe( plugins.minifyCss() )                        // minify concatenated css files
        .pipe( gulp.dest( THEME_DIST_DIR ) );               // save files into dist directory
});

// run theme JS-related tasks
gulp.task( 'scripts-theme', function() {
    return gulp.src( 'src/scripts/*.js' )                   // grab all src js files
        .pipe( plugins.changedInPlace() )                   // check if source has changed since last build
        .pipe( plugins.concat( 'scripts-min.js' ) )         // concatenate all js files into a single files
        .pipe( plugins.uglify() )                           // minify concatenated js files
        .pipe( gulp.dest( THEME_DIST_DIR ) );               // save files into dist directory
});

// run plugins CSS-related tasks
gulp.task( 'styles-plugins', function() {
    return gulp.src( PLUGIN_DIST_DIR + '**/*.css' )         // grab all dist css files
        .pipe( plugins.changedInPlace() )                   // check if source has changed since last build
        .pipe( plugins.autoprefixer( AUTOPREFIXER_MATRIX ) ) // remove unneeded and add needed prefixes
        .pipe( gulp.dest( PLUGIN_DIST_DIR) )                // save files into dist directory
        .pipe( plugins.minifyCss() )                        // minify concatenated css files
        .pipe( gulp.dest( PLUGIN_DIST_DIR) );               // save files into dist directory
});

// run plugins JS-related tasks
gulp.task( 'scripts-plugins', function() {
    return gulp.src( PLUGIN_DIST_DIR + '**/*.js' )          // grab all src js files
        .pipe( plugins.changedInPlace() )                   // check if source has changed since last build
        .pipe( plugins.uglify() )                           // minify concatenated js files
        .pipe( gulp.dest( PLUGIN_DIST_DIR ) );              // save files into dist directory;
});

// run image-related tasks
gulp.task( 'images', function() {
    return gulp.src( UPLOADS_DIST_DIR + '**/**' )           // grab all dist image files
        .pipe( plugins.changedInPlace() )                   // check if source has changed since last build
        .pipe( plugins.webp() )                             // create WEBP versions of images
        .pipe( plugins.imagemin(                            // optimize & cache images in the uploads directory
            {
                optimizationLevel: 3, 
                progressive: true, 
                interlaced: true
            }
        ))
        .pipe( gulp.dest( UPLOADS_DIST_DIR ) );             // save files into dist directory
});

// let's get this party started!
gulp.task('default', [ /*'icons', */'styles-theme', 'scripts-theme', 'styles-plugins', 'scripts-plugins', 'images' ]);