// define requirements & local variables
var gulp = require('gulp'),
    path = require('path'),
    critical = require('critical'),
    plugins = require('gulp-load-plugins')(),
    AUTOPREFIXER_MATRIX = 'last 2 version',
    THEME_DIST_DIR = 'dist/wp-content/themes/atg',
    PLUGIN_DIST_DIR = 'dist/wp-content/plugins/';

// run SVG-related tasks
gulp.task( 'icons', function() {
    return gulp.src( 'src/icons/*.svg' )                    // grab all src svg files
        .pipe( plugins.svgmin( function ( file ) {          // minify all svg files
            var prefix = path.basename( file.relative, path.extname( file.relative ) ); // create unique prefix for each symbol's ID
            return {
                plugins: [{
                    cleanupIDs: {
                        minify: true
                    }
                }]
            }
        }))
        .pipe(plugins.svgstore({                            // concatenate all icon svgs into a single file
            inlineSvg: true                                 // exclude 
        }))
        .pipe(gulp.dest( THEME_DIST_DIR + '/icons'))        // save files into dist directory
});

// run theme CSS-related tasks
gulp.task( 'styles-theme', function() {
    return gulp.src( 'src/styles/*.css' )                   // grab all src css files
        .pipe( plugins.changed( THEME_DIST_DIR ) )          // check if source has changed since last build
        .pipe( plugins.autoprefixer( AUTOPREFIXER_MATRIX ) ) // remove unneeded and add needed prefixes
        .pipe( plugins.concat( 'style-min.css' ) )          // concatenate all css files into a single files
        .pipe( plugins.minifyCss() )                        // minify concatenated css files
        .pipe( gulp.dest( THEME_DIST_DIR ) );               // save files into dist directory
});

// run theme JS-related tasks
gulp.task( 'scripts-theme', function() {
    return gulp.src( 'src/scripts/scripts.js' )             // grab theme's js file
        .pipe( plugins.changed( THEME_DIST_DIR ) )          // check if source has changed since last build
        .pipe( plugins.concat( 'scripts-min.js' ) )         // concatenate all js files into a single files
        .pipe( plugins.uglify() )                           // minify concatenated js files
        .pipe( gulp.dest( THEME_DIST_DIR ) );               // save files into dist directory
});

// run serviceworker JS-related tasks
gulp.task( 'scripts-serviceworker', function() {
    return gulp.src( 'src/scripts/serviceworker*.js' )      // grab all serviceworker js files
        .pipe( plugins.changed( THEME_DIST_DIR ) )          // check if source has changed since last build
        .pipe( plugins.concat( 'serviceworker-min.js' ) )   // concatenate all js files into a single files
        .pipe( plugins.uglify() )                           // minify concatenated js files
        .pipe( gulp.dest( 'dist/' ) );                      // save files into root /dist directory
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
        .pipe( gulp.dest( PLUGIN_DIST_DIR ) );              // save files into dist directory
});

// generate critical CSS
gulp.task( 'styles-critical', function() {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;           // prevent Node from balking at self-signed ssl cert
    critical.generate({
        /* note: cannot use 'base:' or will break remote 'src:' */
        inline: false,                                      // we want css, not html
        css: 'src/styles/style.css',                        // css source file
        dest: THEME_DIST_DIR + '/critical-min.css',         // css destination file
        src: 'https://aarontgrogg.dreamhosters.com/',       // page to use for picking critical
        minify: true,                                       // make sure the output is minified
        dimensions: [{                                      // pick multiple dimensions for top nav
            height: 500,
            width: 300
        },{
            height: 600,
            width: 480
        },{
            height: 800,
            width: 600
        },{
            height: 940,
            width: 1280
        },{
            height: 1000,
            width: 1300
        },{
            height: 1200,
            width: 1800
        },{
            height: 1200,
            width: 2300
        }]
    });
});

// let's get this party started!
gulp.task('default', [ 'icons', 'styles-theme', 'scripts-theme', 'scripts-serviceworker', 'styles-plugins', 'scripts-plugins', 'styles-critical' ]);
