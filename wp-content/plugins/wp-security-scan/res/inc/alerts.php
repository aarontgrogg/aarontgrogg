<?php /** Alerts & descriptions */
$wsdPluginAlertsArray = array
(
// WsdCheck::adminUsername
    'check_username_admin' => array('name' => 'check_username_admin ', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
// WsdCheck::tablePrefix
    'check_table_prefix' => array('name' => 'check_table_prefix', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
// WsdCheck::currentVersion
    'check_wp_current_version' => array('name' => 'check_wp_current_version', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
// WsdCheck::files
    'check_index_wp_content' => array('name' => 'check_index_wp_content', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdCheck::files
    'check_index_wp_content' => array('name' => 'check_index_wp_content', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdCheck::files
    'check_index_wp_plugins' => array('name' => 'check_index_wp_plugins', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdCheck::files
    'check_index_wp_themes' => array('name' => 'check_index_wp_themes', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdCheck::files - alert issued only if the wp-content/uploads directory exists
    'check_index_wp_uploads' => array('name' => 'check_index_wp_uploads', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdCheck::files
    'check_htaccess_wp_admin' => array('name' => 'check_htaccess_wp_admin', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdCheck::files
    'check_readme_wp_root' => array('name' => 'check_readme_wp_root', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),


//  WsdSecurity::fix_hideWpVersion
    'fix_wp_version_hidden' => array('name' => 'fix_wp_version_hidden', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_removeWpMetaGeneratorsFrontend
    'fix_wp_generators_frontend' => array('name' => 'fix_wp_generators_frontend', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_removeReallySimpleDiscovery
    'fix_wp_rsd_frontend' => array('name' => 'fix_wp_rsd_frontend', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_removeWindowsLiveWriter
    'fix_wp_wlw_frontend' => array('name' => 'fix_wp_wlw_frontend', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_disableErrorReporting
    'fix_wp_error_reporting' => array('name' => 'fix_wp_error_reporting', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_removeCoreUpdateNotification
    'fix_wp_core_update_notif' => array('name' => 'fix_wp_core_update_notif', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_removePluginUpdateNotifications
    'fix_wp_plugins_update_notif' => array('name' => 'fix_wp_plugins_update_notif', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_removeThemeUpdateNotifications
    'fix_wp_themes_update_notif' => array('name' => 'fix_wp_themes_update_notif', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_removeLoginErrorNotificationsFrontEnd
    'fix_wp_login_errors' => array('name' => 'fix_wp_login_errors', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_hideAdminNotifications
    'fix_wp_admin_notices' => array('name' => 'fix_wp_admin_notices', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_preventDirectoryListing
    'fix_wp_dir_listing' => array('name' => 'fix_wp_dir_listing', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_preventDirectoryListing
    'fix_wp_index_content' => array('name' => 'fix_wp_index_content', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_preventDirectoryListing
    'fix_wp_index_plugins' => array('name' => 'fix_wp_index_plugins', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_preventDirectoryListing
    'fix_wp_index_themes' => array('name' => 'fix_wp_index_themes', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_preventDirectoryListing - alert issued only if the wp-content/uploads directory exists
    'fix_wp_index_uploads' => array('name' => 'fix_wp_index_uploads', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
//  WsdSecurity::fix_removeWpVersionFromLinks
    'fix_remove_wp_version_links' => array('name' => 'fix_remove_wp_version_links', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),
// WsdSecurity::fix_emptyReadmeFileFromRoot
    'fix_empty_root_readme_file' => array('name' => 'fix_empty_root_readme_file', 'type' => WSS_PLUGIN_ALERT_TYPE_OVERWRITE ),


//  WsdWatch::userPasswordUpdate
    'watch_admin_password_update' => array('name' => 'watch_admin_password_update', 'type' => WSS_PLUGIN_ALERT_TYPE_STACK ),
);