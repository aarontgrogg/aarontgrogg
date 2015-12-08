<?php if(! defined('WSS_PLUGIN_PREFIX')) return;
/**
 * Class WsdPlugin
 * Static class
 */
class WsdPlugin
{
    public static function createWpMenu()
    {
        if (current_user_can('administrator') && function_exists('add_menu_page'))
        {
            $reqCap = 'activate_plugins';
            add_menu_page('WP Security', 'WP Security', $reqCap, WSS_PLUGIN_PREFIX, array(get_class(),'pageMain'), WsdUtil::imageUrl('logo-small.png'));
            add_submenu_page(WSS_PLUGIN_PREFIX, 'Dashboard', __('Dashboard'), $reqCap, WSS_PLUGIN_PREFIX, array(get_class(),'pageMain'));
            add_submenu_page(WSS_PLUGIN_PREFIX, 'Database', __('Database'), $reqCap, WSS_PLUGIN_PREFIX.'database', array(get_class(),'pageDatabase'));
            add_submenu_page(WSS_PLUGIN_PREFIX, 'Scanner', __('Scanner'), $reqCap, WSS_PLUGIN_PREFIX.'scanner', array(get_class(),'pageScanner'));
            add_submenu_page(WSS_PLUGIN_PREFIX, 'Live traffic', __('Live traffic'), $reqCap, WSS_PLUGIN_PREFIX.'live_traffic', array(get_class(),'pageLiveTraffic'));
            add_submenu_page(WSS_PLUGIN_PREFIX, 'Blog', __('Blog'), $reqCap, WSS_PLUGIN_PREFIX.'blog', array(get_class(),'pageBlog'));
            add_submenu_page(WSS_PLUGIN_PREFIX, 'Settings', __('Settings'), $reqCap, WSS_PLUGIN_PREFIX.'settings', array(get_class(),'pageSettings'));
            add_submenu_page(WSS_PLUGIN_PREFIX, 'About', __('About'), $reqCap, WSS_PLUGIN_PREFIX.'about', array(get_class(),'pageAbout'));
        }
    }

    public static function pageMain() { WsdUtil::includePage('dashboard.php'); }
    public static function pageDatabase() { WsdUtil::includePage('database.php'); }
    public static function pageScanner() { WsdUtil::includePage('scanner.php'); }
    public static function pageLiveTraffic() { WsdUtil::includePage('live_traffic.php'); }
    public static function pageBlog() { WsdUtil::includePage('blog.php'); }
    public static function pageSettings() { WsdUtil::includePage('settings.php'); }
    public static function pageAbout() { WsdUtil::includePage('about.php'); }

    public static function loadResources()
    {
        if(WsdUtil::canLoad()){
            wp_enqueue_style('wsd-styles-base', WsdUtil::cssUrl('styles.base.css'));
            wp_enqueue_style('wsd-styles-alerts', WsdUtil::cssUrl('styles.alerts.css'));
            wp_enqueue_style('wsd-styles-general', WsdUtil::cssUrl('styles.general.css'));
            wp_enqueue_style('wsd-styles-status', WsdUtil::cssUrl('styles.status.css'));
            wp_enqueue_script('wsdplugin-js-util', WsdUtil::jsUrl('wsd-util.js'), array('jquery'));
        }
    }


    /**
     * Common method to add an alert to database.
     * @static
     * @param string $actionName The name of the action of the alert
     * @param int $type Can only be one of the following: WSS_PLUGIN_ALERT_TYPE_OVERWRITE | WSS_PLUGIN_ALERT_TYPE_STACK. Defaults to WSS_PLUGIN_ALERT_TYPE_OVERWRITE
     * @param int $severity Can only have one of the following values: 0 1 2 3. Defaults to 0.
     * @param string $title
     * @param string $description
     * @param string $solution
     * @return bool
     */
    public static function alert($actionName, $type = 0, $severity = 0, $title = '', $description = '', $solution = '') {
        global $wpdb;

        $table = self::getTableName();

        if($type == WSS_PLUGIN_ALERT_TYPE_STACK)
        {
            //#! Check the max number of stacked alerts to keep and remove the exceeding ones
            $afsDate = $wpdb->get_var("SELECT alertFirstSeen FROM $table WHERE alertActionName = '$actionName' ORDER BY `alertDate`;");
            if(empty($afsDate)){ $afsDate = "CURRENT_TIMESTAMP()";}
            else { $afsDate = "'".$afsDate."'"; }
            $result = $wpdb->get_var("SELECT COUNT(alertId) FROM $table WHERE alertActionName = '$actionName';");
            if($result >= WSS_PLUGIN_ALERT_STACK_MAX_KEEP){
                // remove older entries to make room for the new ones
                $query = "DELETE FROM $table ORDER BY alertDate ASC LIMIT ".($result - (WSS_PLUGIN_ALERT_STACK_MAX_KEEP - 1));
                $wpdb->query($query);
            }

            //Add the new entry
            $query = $wpdb->prepare(
                "INSERT INTO $table
                (`alertType`,
                `alertSeverity`,
                `alertActionName`,
                `alertTitle`,
                `alertDescription`,
                `alertSolution`,
                `alertDate`,
                `alertFirstSeen`)
                VALUES
                (%d,
                 %d,
                 '%s',
                 '%s',
                 '%s',
                 '%s',
                 CURRENT_TIMESTAMP(),
                 $afsDate
                );",
            $type, $severity, $actionName, $title, $description, $solution);
        }
        elseif($type == WSS_PLUGIN_ALERT_TYPE_OVERWRITE)
        {
            //#! Find the record by actionName and update fields
            $result = $wpdb->get_var("SELECT alertId FROM $table WHERE alertActionName = '".$actionName."'; ");
            //#! found. do update
            if($result > 0){
                $query = $wpdb->prepare("UPDATE $table
                    SET
                    `alertType` = %d,
                    `alertSeverity` = %d,
                    `alertActionName` = '%s',
                    `alertTitle` = '%s',
                    `alertDescription` = '%s',
                    `alertSolution` = '%s',
                    `alertDate` = CURRENT_TIMESTAMP()
                    WHERE alertId = %d;",
                $type, $severity, $actionName, $title, $description, $solution,$result);
            }
            //#! record not found. insert query
            else {
                $query = $wpdb->prepare("INSERT INTO $table
                (`alertType`,
                `alertSeverity`,
                `alertActionName`,
                `alertTitle`,
                `alertDescription`,
                `alertSolution`,
                `alertDate`,
                `alertFirstSeen`)
                VALUES
                (%d,
                 %d,
                 '%s',
                 '%s',
                 '%s',
                 '%s',
                 CURRENT_TIMESTAMP(),
                 CURRENT_TIMESTAMP()
                );",
                $type, $severity, $actionName, $title, $description, $solution);
            }
        }
        $result = $wpdb->query($query);
        if($result === false){
            //#! MySQL error
            return false;
        }
        return true;
    }

    public static function getTableName($tableName = WSS_PLUGIN_ALERT_TABLE_NAME){
        global $wpdb;
        return $wpdb->prefix.$tableName;
    }

    /**
     * Get all alerts grouped by alertActionName
     * @return array
     */
    public static function getAlerts()
    {
        global $wpdb;
        $columns = "`alertId`,`alertType`,`alertSeverity`,`alertActionName`,`alertTitle`,`alertDescription`,`alertSolution`,`alertDate`,`alertFirstSeen`";
        return $wpdb->get_results("SELECT $columns FROM ".self::getTableName(WSS_PLUGIN_ALERT_TABLE_NAME)." GROUP BY `alertActionName`;");
    }

    // filter alerts by input
    public static function getAlertsBy($alertSeverity)
    {
        global $wpdb;
        $columns = "`alertId`,`alertType`,`alertSeverity`,`alertActionName`,`alertTitle`,`alertDescription`,`alertSolution`,`alertDate`,`alertFirstSeen`";
        return $wpdb->get_results("SELECT $columns FROM ".self::getTableName(WSS_PLUGIN_ALERT_TABLE_NAME)." WHERE `alertSeverity` = '$alertSeverity' GROUP BY `alertActionName`;");
    }

    public static function getChildAlerts($alertId, $alertType)
    {
        global $wpdb;
        $columns = "`alertId`,`alertType`,`alertSeverity`,`alertActionName`,`alertTitle`,`alertDescription`,`alertSolution`,`alertDate`,`alertFirstSeen`";
        return $wpdb->get_results("SELECT $columns FROM ".self::getTableName()." WHERE (alertId <> $alertId AND alertType = '$alertType') ORDER BY `alertDate` DESC");
    }

    /**
     * Retrieve the settings from database. This method will extract all methods found in the WsdSecurity class and provide them as
     * settings in the settings page. It will also auto update itself in case new methods are added to the class or if
     * some of them were removed.
     * @return array
     */
    public static function getSettings()
    {
        $className = 'WsdSecurity';
        if(! class_exists($className)){
            return array();
        }
        $settings = get_option(WSS_PLUGIN_SETTINGS_OPTION_NAME);
        $class = new ReflectionClass($className);
        $methods = $class->getMethods();

        if(empty($settings))
        {
            $settings = array();
            foreach($methods as $method)
            {
                $mn = $method->name;
                if($className != $method->class){
                    continue;
                }
                $comment = $method->getDocComment();
                if(false !== ($pos = strpos($mn,WsdSecurity::$methodPrefix))){
                    $settings[$mn] = array(
                        'name' => $mn,
                        'value' => 0, // 0 or 1 ; whether or not the option will show as selected by default in the plugin's settings page
                        'desc' => trim(str_replace(array('/**','*/'),'', $comment))
                    );
                }
            }
            add_option(WSS_PLUGIN_SETTINGS_OPTION_NAME, $settings);
        }
        else
        {
            $n1 = (isset($settings['keepNumEntriesLiveTraffic']) ? $settings['keepNumEntriesLiveTraffic'] : 500);
            $n2 = (isset($settings['liveTrafficRefreshRateAjax']) ? $settings['liveTrafficRefreshRateAjax'] : 10);
            // Check to see whether or not new methods were added or subtracted
            $numSettings = count($settings);
            $numMethods = count($methods);
            if($numMethods <> $numSettings)
            {
                // add new methods
                $_temp = array();
                foreach($methods as $method){
                    if($className != $method->class){
                        continue;
                    }
                    $comment = $method->getDocComment();
                    if(false === ($pos = strpos($method->name,WsdSecurity::$methodPrefix))){ continue; }
                    if(! isset($settings[$method->name])){
                        $settings[$method->name] = array(
                            'name' => $method->name,
                            'value' => 0,
                            'desc' => trim(str_replace(array('/**','*/'),'', $comment))
                        );
                    }
                    array_push($_temp, $method->name);
                }
                // remove missing methods
                foreach($settings as $k => &$entry){
                    if(! in_array($entry['name'], $_temp)){
                        unset($settings[$k]);
                    }
                }

                $settings['keepNumEntriesLiveTraffic'] = $n1;
                $settings['liveTrafficRefreshRateAjax'] = $n2;
                update_option(WSS_PLUGIN_SETTINGS_OPTION_NAME, $settings);
            }
        }
        return $settings;
    }

    /**
     * Check to see whether or not the provided setting is enabled (as the settings are configurable the user might chose to turn some of them off)
     * @param string $name The name of the setting to look for in the settings array
     * @return bool
     */
    public static function isSettingEnabled($name)
    {
        $settings = self::getSettings();
        return (isset($settings[$name]) ? $settings[$name]['value'] : false);
    }

    public static function activate(){
        global $wpdb;
        $charset_collate = '';

        if ( ! empty($wpdb->charset) ){$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";}
        if ( ! empty($wpdb->collate) ){$charset_collate .= " COLLATE $wpdb->collate";}

        // MUST HAVE "CREATE" RIGHTS if a table is not found and needs to be created
        $rights = WsdInfoServer::getDatabaseUserAccessRights();
        $hasCreateRight = in_array('CREATE', $rights['rightsHaving']);
        $table1 = self::getTableName(WSS_PLUGIN_ALERT_TABLE_NAME);
        $table2 = self::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME);

        if(! WsdUtil::tableExists($table1)){
            $query1 = "CREATE TABLE IF NOT EXISTS ".$table1." (
                          `alertId` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
                          `alertType` TINYINT NOT NULL DEFAULT 0 ,
                          `alertSeverity` INT NOT NULL DEFAULT 0 ,
                          `alertActionName` VARCHAR (255) NOT NULL,
                          `alertTitle` VARCHAR(255) NOT NULL ,
                          `alertDescription` TEXT NOT NULL ,
                          `alertSolution` TEXT NOT NULL ,
                          `alertDate` DATETIME NOT NULL default '0000-00-00 00:00:00',
                          `alertFirstSeen` DATETIME NOT NULL default '0000-00-00 00:00:00',
                          PRIMARY KEY (`alertId`) ,
                          UNIQUE INDEX `alertId_UNIQUE` (`alertId` ASC) ) $charset_collate;";
            if(! $hasCreateRight){
                $notices= get_option('wsd_plugin_install_error', array());
                $notices[]= '<strong>'.WSS_PLUGIN_NAME."</strong>: The database user needs the '<strong>CREATE</strong>' right in order to install this plugin.";
                update_option('wsd_plugin_install_error', $notices);
                return;
            }
            $result = @$wpdb->query($query1);
            if($result === false){
                //#! MySQL error
                $GLOBALS['WSS_PLUGIN_INSTALL_ERROR'] = 'Error running query: '.$query1;
                $notices= get_option('wsd_plugin_install_error', array());
                $notices[]= '<strong>'.WSS_PLUGIN_NAME."</strong>. Error running query: <strong><pre>$query1</pre></strong>.";
                update_option('wsd_plugin_install_error', $notices);
                return;
            }
        }

        if(! WsdUtil::tableExists($table2)){
            $query2 = "CREATE TABLE IF NOT EXISTS ".$table2." (
                         `entryId` bigint(20) unsigned NOT NULL auto_increment,
                         `entryTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                         `entryIp` text,
                         `entryReferrer` text,
                         `entryUA` text,
                         `entryRequestedUrl` text,
                         PRIMARY KEY (entryId)) $charset_collate;";
            if(! $hasCreateRight){
                $notices= get_option('wsd_plugin_install_error', array());
                $notices[]= '<strong>'.WSS_PLUGIN_NAME."</strong>: The database user needs the '<strong>CREATE</strong>' right in order to install this plugin.";
                update_option('wsd_plugin_install_error', $notices);
                return;
            }
            $result = @$wpdb->query($query2);
            if($result === false){
                //#! MySQL error
                $GLOBALS['WSS_PLUGIN_INSTALL_ERROR'] = 'Error running query: '.$query2;
                $notices= get_option('wsd_plugin_install_error', array());
                $notices[]= '<strong>'.WSS_PLUGIN_NAME."</strong>. Error running query: <strong><pre>$query2</pre></strong>.";
                update_option('wsd_plugin_install_error', $notices);
                return;
            }
        }

        add_option('WSD-PLUGIN-CAN-RUN-TASKS', 1);
    }
    public static function deactivate() {
        if(self::swpPluginInstalled()){
            return;
        }
        WsdScheduler::unregisterCronTasks();
        delete_option(WSS_PLUGIN_SETTINGS_OPTION_NAME);
        delete_option('wsd_plugin_install_error');
        delete_option('WSD-PLUGIN-CAN-RUN-TASKS');
    }
    public static function uninstall(){
        if(self::swpPluginInstalled()){
            return;
        }
        delete_option('WSS_PLUGIN_ENTRIES_LIVE_TRAFFIC');
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS ".WsdPlugin::getTableName(WSS_PLUGIN_ALERT_TABLE_NAME));
        $wpdb->query("DROP TABLE IF EXISTS ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME));
    }

    /**
     * Check to see whether or not the Secure WordPress plugin is installed
     * @return bool
     */
    public static function swpPluginInstalled()
    {
        $pluginPath = 'secure-wordpress/index.php';
        $pluginFilePath = trailingslashit(ABSPATH).'wp-content/plugins/'.$pluginPath;
        if(function_exists('is_plugin_active')){
            if(is_plugin_active($pluginPath)){
                return true;
            }
            else {
                // check plugins dir
                if(is_file($pluginFilePath)){
                    return true;
                }
            }
        }
        // check plugins dir
        if(is_file($pluginFilePath)){
            return true;
        }
        return false;
    }
}