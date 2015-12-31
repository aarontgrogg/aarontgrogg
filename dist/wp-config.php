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

/**
* WordPress Localized Language, defaults to English.
*
* Change this to localize WordPress. A corresponding MO file for the chosen
* language must be installed to wp-content/languages. For example, install
* de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
* language support.
*/
define( 'WPLANG', '' );


$_HOST = $_SERVER['HTTP_HOST'];

// ** MySQL settings - You can get this info from your web host ** //

/** Test if on local dev server **/
if ( $_HOST === 'aarontgrogg.com' ) {
	$_DOMAIN_CURRENT_SITE = 'aarontgrogg.com';
	$_SITE_DIRECTORY = '/home/aarontgrogg/aarontgrogg.com';
	$_DB_HOST = 'mysql.aarontgrogg.com';
	$_DB_NAME = 'aar1125205222323';
	$_DB_USER = 'aar1125205222323';
	$_FORCE_SSL_ADMIN = true;
	$_WP_DEBUG = false;

} else if ( $_HOST === 'aarontgrogg.dreamhosters.com' ) {
	$_DOMAIN_CURRENT_SITE = 'aarontgrogg.dreamhosters.com';
	$_SITE_DIRECTORY = '/home/aarontgrogg/aarontgrogg.dreamhosters.com';
	$_DB_HOST = 'mysql.aarontgrogg.dreamhosters.com';
	$_DB_NAME = 'aarontgrogg_dreamhosters';
	$_DB_USER = 'aarontgrogg';
	$_FORCE_SSL_ADMIN = true;
	$_WP_DEBUG = false;

} else {
	$_DOMAIN_CURRENT_SITE = 'aarontgrogg.dev';
	$_SITE_DIRECTORY = '';
	$_DB_HOST = 'localhost';
	$_DB_NAME = 'aar1125205222323';
	$_DB_USER = 'aar1125205222323';
	$_FORCE_SSL_ADMIN = true;
	$_WP_DEBUG = false;
}

$_PROTOCOL = ( $_FORCE_SSL_ADMIN ) ? 'https' : 'http';

// MySQL settings - all
define( 'DB_HOST', $_DB_HOST );
define( 'DB_NAME', $_DB_NAME );
define( 'DB_USER', $_DB_USER );
define( 'DB_PASSWORD', 'Try2Enter!' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

//
/**
* WordPress Database Table prefix.
*
* You can have multiple installations in one database if you give each a unique
* prefix. Only numbers, letters, and underscores please!
*/
$table_prefix  = 'wp_';


/**#@+
* Authentication Unique Keys and Salts.
*
* Change these to different unique phrases!
* You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
* You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
*
* @since 2.6.0
*/
define('AUTH_KEY',         'qsNX{Z[3T+$)!xD,#`G0^.om>IN-J,EsztpZB+||?Tp;$>wTbW-0}^;&yVOlF-9a');
define('SECURE_AUTH_KEY',  '+T-h?bp9 e(.77{9:I6lX>-]yx>w-mGDP3>]^k~^48B]N()yV4,K/75pf{7FfG)>');
define('LOGGED_IN_KEY',    'CR{#d[N%qZv^-%E*ks(,)oQ0oKM/?(TE2APL~c-Q%4,9xkJ]+m-W$&2$gEmv!<CR');
define('NONCE_KEY',        '~pXdgv0b>yP&?7=a$Cth^4jJE75K<,]rsqNidSe=|)?#fsil6n[4o|-U2_q)WHjM');
define('AUTH_SALT',        '!s)!#vd{hr#8XHZs#H,8%z@Ti{V[{F`sXit?|[RP{Z^V]#@Cejqrjk*G2r>(/[>h');
define('SECURE_AUTH_SALT', '[}]/v>#rC@3)|MB|/<&93gA4?!KKT ;%PN#9SKVDW|[+e>Z<x!QHbNr5 ,@#z{]y');
define('LOGGED_IN_SALT',   'eJjUgN=gH{|$@61loMhUlTizS-7bF tEsZw3A~4!G9eX^fefh5)|P^-<(ONb#^a-');
define('NONCE_SALT',       '|Y-%.EB51R]U$uf!nXlI546P`@26L(+.#c3w`Dv~7x>8-X(=;|#=ij][GX@zoUi6');

/**#@-*/


define( 'FORCE_SSL_ADMIN', $_FORCE_SSL_ADMIN );

define( 'WP_DEBUG', $_WP_DEBUG );
define( 'WP_HOME', $_PROTOCOL.'://'.$_HOST );
define( 'WP_SITEURL', $_PROTOCOL.'://'.$_HOST );
define( 'DOMAIN_CURRENT_SITE', $_DOMAIN_CURRENT_SITE );
//define( 'WPCACHEHOME', $_SITE_DIRECTORY . '/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define( 'TEMPLATEPATH', $_SITE_DIRECTORY . '/wp-content/themes/boilerplate' );
define( 'STYLESHEETPATH', $_SITE_DIRECTORY . '/wp-content/themes/atg' );
define( 'WP_PLUGIN_URL', $_SITE_DIRECTORY . '/wp-content/plugins' );
define( 'COOKIEPATH', preg_replace('|'.$_PROTOCOL.'?://[^/]+|i', '', WP_HOME.'/') );
define( 'SITECOOKIEPATH', preg_replace('|'.$_PROTOCOL.'?://[^/]+|i', '', WP_SITEURL.'/') );
define( 'PLUGINS_COOKIE_PATH', preg_replace('|'.$_PROTOCOL.'?://[^/]+|i', '', WP_PLUGIN_URL) );
define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH.'wp-admin' );


/**
* For developers: WordPress debugging mode.
*
* Change this to true to enable the display of notices during development.
* It is strongly recommended that plugin and theme developers use WP_DEBUG
* in their development environments.
*/
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );


/**
* Added based on:
* https://digwp.com/2009/06/wordpress-configuration-tricks/
*/
define( 'ENABLE_CACHE', false );
define( 'CACHE_EXPIRATION_TIME', 3600 );
define( 'COOKIE_DOMAIN', '.'.$_HOST ); // don't omit the leading '.'
define( 'WP_POST_REVISIONS', 3 );
define( 'WP_MEMORY_LIMIT', '128M' );
define(' SAVEQUERIES', false );


// Added by WP-Cache Manager
//define( 'WP_CACHE', true );


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
define('ABSPATH', dirname(__FILE__) . '/');

/** Attempt to fix missing JS files... http://wordpress.org/support/topic/failed-to-load-resources-error-after-update-to-35-admin-back-end */
define('CONCATENATE_SCRIPTS', false);

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
