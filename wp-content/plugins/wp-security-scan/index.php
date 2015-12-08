<?php
/**
Plugin Name: Acunetix WP Security
Plugin URI: http://www.acunetix.com/websitesecurity/wordpress-security-plugin/
Description: The Acunetix WP Security plugin is the ultimate must-have tool when it comes to WordPress security. The plugin is free and monitors your website for security weaknesses that hackers might exploit and tells you how to easily fix them.
Version: 4.0.1
Author: Acunetix
Author URI: http://www.acunetix.com/
License: GPLv2 or later
Text Domain: WSDWP_SECURITY
Domain Path: /languages
 */
define('WSS_PLUGIN_PREFIX', 'wss_');
define('WSS_PLUGIN_NAME', 'Acunetix WP Security');
define('WSS_PLUGIN_URL', trailingslashit(plugins_url('', __FILE__)));
define('WSS_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WSS_PLUGIN_BASE_NAME', basename(__DIR__));


require('wss-settings.php');
require('res/inc/alerts.php');
require('res/inc/WsdUtil.php');
require('res/inc/WsdPlugin.php');
require('res/inc/WsdInfo.php');
require('res/inc/WsdSecurity.php');
require('res/inc/WsdCheck.php');
require('res/inc/WsdScheduler.php');
require('res/inc/WsdWatch.php');
require('res/inc/WsdLiveTraffic.php');
require('res/inc/wss-functions.php');


//#!--
add_action('admin_init', array('WsdUtil','loadPluggable'));
register_activation_hook( __FILE__, array('WsdPlugin', 'activate') );
register_deactivation_hook( __FILE__, array('WsdPlugin', 'deactivate') );
register_uninstall_hook( __FILE__, array('WsdPlugin', 'uninstall') );
//#++


//#! register tasks
if(false !== get_option('WSD-PLUGIN-CAN-RUN-TASKS',false))
{
    WsdScheduler::registerTask(array('WsdPlugin','loadResources'), 'init');
    WsdScheduler::registerTask(array('WsdPlugin','createWpMenu'), 'admin_menu');
    WsdScheduler::registerTask(array('WsdLiveTraffic','registerHit'), 'init');
    WsdScheduler::registerTask(array('WsdLiveTraffic','ajaxGetTrafficData'), 'wp_ajax_ajaxGetTrafficData');
    WsdScheduler::registerTask(array('WsdLiveTraffic','ajaxGetTrafficData'), 'wp_ajax_nopriv_ajaxGetTrafficData');
    WsdScheduler::registerTask(array('WsdUtil','addDashboardWidget'), 'wp_dashboard_setup');

// override - scheduled task
    WsdScheduler::registerCronTask('wsd_check_user_admin', array('WsdCheck','adminUsername'), '8h');

// scheduled task - hourly cleanup of events in live traffic
    WsdScheduler::registerCronTask('wsd_cleanup_live_traffic', array('WsdLiveTraffic','clearEvents'), 'hourly');

// stacked
    WsdScheduler::registerTask(array('WsdWatch','userPasswordUpdate'));

// #! run fixes. Only those checked by the user will run (@see: settings page)
    WsdScheduler::registerClassTasks('WsdSecurity','fix_');

//#! run checks.
    WsdScheduler::registerClassTasks('WsdCheck','check_');
}
