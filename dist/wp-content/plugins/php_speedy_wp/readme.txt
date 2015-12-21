-------------------------------------------------
Plugin Name: PHP Speedy WP
Plugin URI: http://aciddrop.com
Description: Speeds up the display of your blog
Version: 0.4.7
Author: Leon Chevalier
Author URI: http://aciddrop.com/
Copyright (c) 2008 Leon Chevalier
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt
-------------------------------------------------

Installation
-----------

It's a standard Wordpress plugin install - just copy the entire php_speedy_wp folder into your Wordpress plugins folder. Activate the plugin via the "Plugins" menu, then go to Options -> PHP Speedy.

You should then:-

    * Configure PHP Speedy as you see fit, and click the "Set Options" button at the bottom on the configure screen
    * Test the configuration
    * Finally, activate PHP Speedy on its own activation screen. This is separate from the WP Plugin activation.


Known Issues
-----------

Whilst it tries to be a plug-and-play solution PHP Speedy can't (yet) do everything. The following are some issues you should take into account if you are having problems:

    * The config.php file and the cache directory must be writable by the server
    * PHP Speedy doesn't compress external JavaScript or CSS files. This means your widgets, tracking codes etc will not be compressed
    * PHP Speedy ignores JavaScript of CSS served with any extension other than .js or .css. This is because if the extension is .php or something else, the file is probably dynamic and therefore probably shouldn't be cached
    * If you have a JavaScript that loads other JavaScripts via document.write this may cause problems. The new scripts will be loaded after the entire block of all your other scripts, thereby changing the load order. In this case, you should manually link to each script individually.
    * PHP Speedy ignores querystrings in the links to your JS and CSS files
    * PHP Speedy only sets the expiration for your CSS and JS files. It doesn't (yet) handle the images, which is probably why you won't score an A in Y-Slow for far future expires.
    * Enabling page gzip compression just enables the option in Wordpress (in Options | Reading). PHP Speedy itself does no page gzipping.
    * In Wordpress MU, you cannot configure your MU sites for PHP Speedy individually; the same config affects all the sites have enabled PHP Speedy on. You should turn off file cleanup. This is a feature (not a bug)
    * PHP Speedy doesn't support the @import syntax for including CSS files
    * Don't forget to activate PHP Speedy once you have tested the configuration!


