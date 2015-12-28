<?php

/**
* The base configurations of the WordPress.
*
* This file has the following configurations: MySQL settings, Table Prefix,
* Secret Keys, WordPress Language, and ABSPATH. You can find more information
* by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
* wp-config.php} Codex page. You can get the MySQL settings from your web host.
*
* This file is used by the wp-config.php creation script during the
* installation. You don't have to use the web site, you can just copy this file
* to "wp-config.php" and fill in the values.
*
* @package WordPress
*/

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'aar1125205222323');

/** MySQL database username */
define('DB_USER', 'aar1125205222323');

/** MySQL database password */
define('DB_PASSWORD', 'Try2Enter!');

/** MySQL hostname */
/** Test if on local dev server **/
if ($_SERVER['HTTP_HOST'] === 'aarontgrogg.com') {
	// prod
	define('DB_HOST', 'mysql.aarontgrogg.com'); 

} else if ($_SERVER['HTTP_HOST'] === 'aarontgrogg.dreamhosters.com') {
	// temp dev on prod server
	define('DB_HOST', 'mysql.aarontgrogg.com');

} else if ($_SERVER['HTTP_HOST'] === 'aarontgrogg.dev') {
	// local dev
	define('DB_HOST', 'localhost');

} else {
	// localhost
	define('DB_HOST', 'localhost');
}

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
* Authentication Unique Keys and Salts.
*
* Change these to different unique phrases!
* You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
* You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
*
* @since 2.6.0
*/
define('AUTH_KEY',        '}k~+)`a^dUe3_K:n+-}4q_85$>1mx)&L*/7)0n./F)rV?h[oO-Vy|{afsXpx{(Xi');
define('SECURE_AUTH_KEY', 'e<H@BHQI=!>e{#C0(-^(Yn;E` |XT+#|`O#%&W2:Ds9IT;oS3ewnw{K)raGTBrM8');
define('LOGGED_IN_KEY',   '(R|pu|36@Yu4+a}bC2B.Y`WcLcpx@*c)Z?]Sd!S@4yBp(t87#ciVxV>N)k@~&D^S');
define('NONCE_KEY',       'kGust|R&.)vAPua(J]d/TBZqnH8[g_:8|ON/7wRhZs5& _FpbEwVtgUW1D.p +YN');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

/**#@-*/

/**
* WordPress Database Table prefix.
*
* You can have multiple installations in one database if you give each a unique
* prefix. Only numbers, letters, and underscores please!
*/
$table_prefix  = 'wp_';

/**
* WordPress Localized Language, defaults to English.
*
* Change this to localize WordPress. A corresponding MO file for the chosen
* language must be installed to wp-content/languages. For example, install
* de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
* language support.
*/
define( 'WPLANG', '' );


/**
* For developers: WordPress debugging mode.
*
* Change this to true to enable the display of notices during development.
* It is strongly recommended that plugin and theme developers use WP_DEBUG
* in their development environments.
*/
define( 'WP_DEBUG', true );
define( 'WP_AUTO_UPDATE_CORE', false );

define( 'WP_HOME', 'https://'.$_SERVER['HTTP_HOST'] );
define( 'WP_SITEURL', 'https://'.$_SERVER['HTTP_HOST'] );
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

// Added by WP-Cache Manager
define( 'WP_CACHE', true );

/**
* Added based on:
* https://digwp.com/2009/06/wordpress-configuration-tricks/
*/
define( 'ENABLE_CACHE', true );
define( 'CACHE_EXPIRATION_TIME', 3600 );
define( 'COOKIE_DOMAIN', '.'.$_SERVER['HTTP_HOST'] ); // don't omit the leading '.'
define( 'COOKIEPATH', preg_replace('|https?://[^/]+|i', '', WP_HOME.'/') );
define( 'SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', WP_SITEURL.'/') );
define( 'PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL) );
define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH.'wp-admin' );
define( 'WP_POST_REVISIONS', 3 );
define( 'WP_MEMORY_LIMIT', '128M' );
define(' SAVEQUERIES', true );


/*
 * SSL added 12/7/2015
 */
define('FORCE_SSL_ADMIN', true);


/*
 * Adjustments for domain
 */
if ($_SERVER['HTTP_HOST'] === 'aarontgrogg.com') {
	$_DOMAIN_CURRENT_SITE = 'aarontgrogg.com';
	$_SITE_DIRECTORY = '/home/aarontgrogg/aarontgrogg.com';
} else if ($_SERVER['HTTP_HOST'] === 'aarontgrogg.dreamhosters.com') {
	$_DOMAIN_CURRENT_SITE = 'aarontgrogg.dreamhosters.com';
	$_SITE_DIRECTORY = '/home/aarontgrogg/aarontgrogg.com';
} else if ($_SERVER['HTTP_HOST'] === 'aarontgrogg.dev') {
	$_DOMAIN_CURRENT_SITE = 'aarontgrogg.dev';
	$_SITE_DIRECTORY = '';
} else {
	$_DOMAIN_CURRENT_SITE = 'localhost';
	$_SITE_DIRECTORY = '';
}
define( 'DOMAIN_CURRENT_SITE', $_DOMAIN_CURRENT_SITE );
define( 'WPCACHEHOME', $_SITE_DIRECTORY . '/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define( 'TEMPLATEPATH', $_SITE_DIRECTORY . '/wp-content/themes/boilerplate' );
define( 'STYLESHEETPATH', $_SITE_DIRECTORY . '/wp-content/themes/atg' );


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
define('ABSPATH', dirname(__FILE__) . '/');

/** Attempt to fix missing JS files... http://wordpress.org/support/topic/failed-to-load-resources-error-after-update-to-35-admin-back-end */
define('CONCATENATE_SCRIPTS', false);

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
