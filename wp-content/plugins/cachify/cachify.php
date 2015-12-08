<?php
/*
Plugin Name: Cachify
Description: Smarter Cache für WordPress. Reduziert die Ladezeit der Blogseiten, indem Inhalte in statischer Form abgelegt und ausgeliefert werden.
Author: Sergej M&uuml;ller
Author URI: http://wpcoder.de
Plugin URI: http://cachify.de
Version: 2.0.6
*/


/* Sicherheitsabfrage */
if ( !class_exists('WP') ) {
	die();
}


/* Konstanten */
define('CACHIFY_FILE', __FILE__);
define('CACHIFY_BASE', plugin_basename(__FILE__));
define('CACHIFY_CACHE_DIR', WP_CONTENT_DIR. '/cache/cachify');


/* Hooks */
add_action(
	'init',
	array(
		'Cachify',
		'instance'
	),
	99
);
register_activation_hook(
	__FILE__,
	array(
		'Cachify',
		'install'
	)
);
register_uninstall_hook(
	__FILE__,
	array(
		'Cachify',
		'uninstall'
	)
);


/* Autoload Init */
spl_autoload_register('cachify_autoload');

/* Autoload Funktion */
function cachify_autoload($class) {
	if ( in_array($class, array('Cachify', 'Cachify_APC', 'Cachify_DB', 'Cachify_HDD')) ) {
		require_once(
			sprintf(
				'%s/inc/%s.class.php',
				dirname(__FILE__),
				strtolower($class)
			)
		);
	}
}