// define requirements
var gulp = require('gulp'),                         // gulp...
    autoprefixer = require('gulp-autoprefixer'),    // using CanIUse data, removes old and adds needed prefixes
    cache = require('gulp-cache'),                  // caches optimized images, so they are not re-optimized
    concat = require('gulp-concat'),                // concatenates files
    critical = require('critical'),                 // creates critical CSS
    imagemin = require('gulp-imagemin'),            // imnifies/optimizes images
    minifycss = require('gulp-minify-css'),         // minifies CSS
    rename = require('gulp-rename'),                // allows for easy renaming of files
    svgstore = require('gulp-svgstore'),            // concatenates SVG files
    svgmin = require('gulp-svgmin'),                // minifies SVG files
    uglify = require('gulp-uglify'),                // minifies JS
    webp = require('gulp-webp');                    // creates WEBP-versions of images
    
// run SVG-related tasks
/* maybe use these?
    gulp-svg-sprite
    gulp-svg2png
    gulp-svgo
*/
/*gulp.task('icons', function() {
    return gulp.src('src/icons/*.svg')              // grab all src css files
        .pipe(...); // save files into dist directory
});*/

// run theme CSS-related tasks
gulp.task('styles-theme', function() {
    return gulp.src('src/styles/*.css')             // grab all src css files
        .pipe(autoprefixer('last 2 version'))       // remove unneeded and add needed prefixes
        .pipe(concat('style-min.css'))              // concatenate all css files into a single files
        .pipe(minifycss())                          // minify concatenated css files
        .pipe(gulp.dest('dist/wp-content/themes/atg')); // save files into dist directory
});

// run theme JS-related tasks
gulp.task('scripts-theme', function() {
    return gulp.src('src/scripts/*.js')             // grab all src js files
        .pipe(concat('scripts-min.js'))             // concatenate all js files into a single files
        .pipe(uglify())                             // minify concatenated js files
        .pipe(gulp.dest('dist/wp-content/themes/atg')); // save files into dist directory
});

// run plugins CSS-related tasks
gulp.task('styles-plugins', function() {
    return gulp.src('dist/wp-content/plugins/**/*.css') // grab all dist css files that are NOT already minified
        .pipe(autoprefixer('last 2 version'))       // remove unneeded and add needed prefixes
        .pipe(gulp.dest('dist/wp-content/plugins/')) // save files into dist directory
        .pipe(minifycss())                          // minify concatenated css files
        .pipe(gulp.dest('dist/wp-content/plugins/')); // save files into dist directory
});

// run plugins JS-related tasks
gulp.task('scripts-plugins', function() {
    return gulp.src('dist/wp-content/plugins/**/*.js') // grab all src js files that are NOT already minified
        .pipe(uglify())                             // minify concatenated js files
        .pipe(gulp.dest('dist/wp-content/plugins/')); // save files into dist directory;
});

// run image-related tasks
gulp.task('images', function() {
    return gulp.src('dist/wp-content/uploads/**/**') // grab all dist image files
        .pipe(webp())
        .pipe(cache(imagemin({                      // optimize & cache images in the uploads directory
            optimizationLevel: 3, progressive: true, interlaced: true
        })))
        .pipe(gulp.dest('dist/wp-content/uploads/')); // save files into dist directory
});

// let's get this party started!
/*gulp.task('default', function(){
    console.log('starting...');
});*/
gulp.task('default', [ /*'icons', */'styles-theme', 'scripts-theme', 'styles-plugins', 'scripts-plugins', 'images' ]);