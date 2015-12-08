<?php if(! defined('WSS_PLUGIN_PREFIX')) return;

/**
 * Common function to add custom time intervals to wp cron.
 * This function should not be called directly.
 *
 * Usage: add_filter( 'cron_schedules', 'wsdplugin_addCronIntervals' );
 *
 * @param $schedules
 * @return mixed
 */
function wsdplugin_addCronIntervals( $schedules )
{
    $schedules['8h'] = array( // The name to be used in code
        'interval' => 28800, // Intervals: in seconds
        'display' => __('Every 8 hours') // display name
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'wsdplugin_addCronIntervals' );

if(WsdUtil::canLoad() && WsdUtil::isAdministrator())
{
    //#!++
    add_action('admin_notices', 'wsdPluginInstallErrorNotice');
    function wsdPluginInstallErrorNotice() {
        if ($notices = get_option('wsd_plugin_install_error')) {
            if(! empty($notices)){
                foreach ($notices as $notice) {
                    echo "<div class='updated'><p>$notice</p></div>";
                }
            }
        }
    }
    //#--
}
