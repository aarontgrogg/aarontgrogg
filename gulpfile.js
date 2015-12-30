// define requirements
var gulp = require('gulp'),
    path = require('path'),
    //critical = require('critical').stream,
    criticalcss = require('criticalcss'),
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
    return gulp.src( 'src/scripts/*.js' )                   // grab all src js files
        .pipe( plugins.changed( THEME_DIST_DIR ) )          // check if source has changed since last build
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
        .pipe( gulp.dest( PLUGIN_DIST_DIR ) );              // save files into dist directory
});

// Generate & Inline Critical-path CSS
/*gulp.task( 'critical', function () {
    return gulp.src( 'https://aarontgrogg.dreamhosters.com/' ) // grab home page
        .pipe( critical({
            minify: true                                    // minify results
        }))
        .pipe( gulp.dest( THEME_DIST_DIR + '/critical.css' ) ); // save files into dist directory

});*/
gulp.task('styles-critical', function() {
    var remoteURL = 'https://aarontgrogg.dreamhosters.com',
        request = require('request'),
        fs = require('fs'),
        tmpDir = require('os').tmpdir(),
        cssUrl = remoteURL + '/wp-content/themes/atg/style-min.css',
        cssPath = path.join( tmpDir, 'style.css' ),
        includePath = path.join( __dirname, 'src/styles/critical.css' );

    request( cssUrl )
        .pipe( fs.createWriteStream( cssPath ) ).on( 'close', function() {
            criticalcss.getRules( cssPath, function( err, output ) {
                if ( err ) {
                    throw new Error( err );
                } else {
                    criticalcss.findCritical( remoteURL, { 
                        rules: JSON.parse( output ) 
                    }, function( err, output ) {
                        if ( err ) {
                            throw new Error( err );
                        } else {
                            fs.writeFile( includePath, output, function( err ) {
                                if ( err ) {
                                    return console.log( err );
                                }
                                console.log( 'Critical written to include!' );
                            });
                        }
                    });
                }
            });
        });

    return gulp.src( 'src/styles/critical.css' )
        // minify it
        .pipe( plugins.minifyCss() )
        // wrap with style tags
        .pipe( plugins.concatUtil.header('<style>') )
        .pipe( plugins.concatUtil.footer('</style>') )
        // convert it to a php file
        .pipe( plugins.rename({
            basename: 'criticalcss',
            extname: '.php'
        }) )
        // insert it Wordpress theme folder
        .pipe( gulp.dest( THEME_DIST_DIR ) );
});



// let's get this party started!
gulp.task('default', [ 'icons', 'styles-theme', 'scripts-theme', 'styles-plugins', 'scripts-plugins', 'styles-critical' ]);