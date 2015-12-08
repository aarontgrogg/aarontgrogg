<?php if(! defined('WSS_PLUGIN_PREFIX')) return;

/**
 * Class WsdUtil
 * Static class. Provides utility methods for various tasks
 */
class WsdUtil
{
    /**
     * @return bool
     * Convenient method to check whether or not the plugin's resources can be loaded
     */
    public static function canLoad() { return ((false === ($pos = stripos($_SERVER['REQUEST_URI'], WSS_PLUGIN_PREFIX))) ? false : true); }
    public static function cssUrl($fileName){ return WSS_PLUGIN_URL.'res/css/'.$fileName; }
    public static function imageUrl($fileName){ return WSS_PLUGIN_URL.'res/images/'.$fileName; }
    public static function jsUrl($fileName) { return WSS_PLUGIN_URL.'res/js/'.$fileName; }
    public static function resUrl() { return WSS_PLUGIN_URL.'res/'; }
    public static function includePage($fileName)
    {
        if(! self::canLoad()) { return; }
        $dirPath = WSS_PLUGIN_DIR.'res/pages/';
        if(! is_dir($dirPath)) { return; }
        if(! is_readable($dirPath)) { return; }
        $fname = $dirPath.$fileName;
        if(false !== ($pos = strpos($fname, '../')) || false !== ($pos = strpos($fname, './'))){ return; }
        if(! is_file($fname) || ! is_readable($fname)) { return; }
        include($fname);
    }

    /**
     * @public
     * @static
     * Load the text domain
     * @return void
     */
    public static function loadTextDomain(){ if ( function_exists('load_plugin_textdomain') ) { load_plugin_textdomain(WSS_PLUGIN_TEXT_DOMAIN, false, WSS_PLUGIN_DIR.'res/languages/'); } }

    /**
     * @public
     * @static
     * @uses self::checkFileName()
     *
     * Retrieve the content of the specified template file.
     *
     * @param type $fileName the name of the template file to load.
     * Without the ".php" file extension.
     * @param array $data The data to send to the template file
     * @return string The parsed content of the template file
     */
    public static function loadTemplate($fileName, array $data = array())
    {
        self::checkFileName($fileName);
        $str = '';
        $file = WSS_PLUGIN_DIR.'res/pages/tpl/'.$fileName.'.php';
        if (is_file($file))
        {
            ob_start();
            if (!empty($data)) {
                extract($data);
            }
            include($file);
            $str = ob_get_contents();
            ob_end_clean();
        }
        return $str;
    }

    /**
     * @public
     * @static
     * @uses wp_die()
     *
     * Check the specified file name for directory traversal attacks.
     * Exits the script if the "..[/]" is found in the $fileName.
     *
     * @param string $fileName The name of the file to check
     * @return void
     */
    public static function checkFileName($fileName)
    {
        $fileName = trim($fileName);
        //@@ Check for directory traversal attacks
        if (preg_match("/\.\.\//",$fileName)) {
            wp_die('Invalid Request!');
        }
    }

    /**
     * @public
     * @static
     *
     * Attempts to write the provided $data into the specified $file
     * using either file_put_contents or fopen/fwrite functions (whichever is available).
     *
     * @param  string $file The path to the file
     * @param string $data The content to write into the file
     * @param resource $fh The file handle to use if fopen function is available. Optional, defaults to null
     *
     * @return int  The number of bytes written to the file, otherwise -1.
     */
    public static function writeFile($file, $data, $fh = null)
    {
        if(! is_null($fh) && is_resource($fh)){
            fwrite($fh,$data);
            return strlen($data);
        }
        else {
            if (function_exists('file_put_contents')) {
                return file_put_contents($file,$data);
            }
        }
        return -1;
    }

    /**
     * @public
     * @param array $acxFileList
     * Apply the suggested permissions for the list of files
     * provided in the global $acxFileList array.
     * @return array  array('success' => integer, 'failed' => integer)
     */
    public static function changeFilePermissions($acxFileList)
    {
        if (empty($acxFileList)) {
            return array();
        }
        // chmod doesn't work on windows... :/
        if (self::isWinOs()) {
            return array();
        }

        $s = $f = 0;
        foreach($acxFileList as $k => $v)
        {
            $filePath = $v['filePath'];
            $sp = $v['suggestedPermissions'];
            $sp = (is_string($sp) ? octdec($sp) : $sp);

            //@ include directories too
            if (file_exists($filePath))
            {
                if (false !== @chmod($filePath, $sp)) {
                    $s++;
                }
                else { $f++; }
            }
            else {
                // if no path provided
                if(empty($filePath)){
                    $f++;
                    continue;
                }
                // try to create the missing files
                if(false !== file_put_contents($filePath, '')){
                    if (false !== @chmod($filePath, $sp)) {
                        $s++;
                    }
                    else { $f++; }
                }
                else { $f++; }
            }
        }
        return array('success' => $s, 'failed' => $f);
    }

    public static function getFilePermissions($filePath)
    {
        if (!function_exists('fileperms')) {
            return '-1';
        }
        if (!file_exists($filePath)) {
            return '-1';
        }
        clearstatcache();
        return substr(sprintf("%o", fileperms($filePath)), -4);
    }

    public static function normalizePath($path) {
        return str_replace('\\', '/', $path);
    }

    public static function isWinOs(){
        return ((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? true : false);
    }

    /**
     * Check to see whether or not the current user is an administrator
     * @return bool
     */
    public static function isAdministrator(){
        self::loadPluggable();
        return user_can(wp_get_current_user(),'update_core');
    }

    /**
     * Check to see whether or not the specified table exists in the database
     * @param $tableName The table to check for existence. It requires the full qualified name of the table
     *                      - which means the prefix must be there as well.
     * @return bool
     */
    public static function tableExists($tableName)
    {
        global $wpdb;
        $result = $wpdb->get_var("SHOW TABLES LIKE '$tableName'");
        return (is_null($result) ? false : true);
    }

    /**
     * @public
     * @uses wp_die()
     *
     * Backup the database and save the script to the plug-in's backups directory.
     * This directory must be writable!
     *
     * @return string The name of the generated backup file or empty string on failure.
     */
    public static function backupDatabase()
    {
        if (!is_writable(WSS_PLUGIN_BACKUPS_DIR))
        {
            $s = sprintf(__('The %s directory <strong>MUST</strong> be writable for this feature to work!'), WSS_PLUGIN_BACKUPS_DIR);
            wp_die($s);
        }

        $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
        if (!$link) {
            wp_die(__('Error: Cannot connect to database!'));
        }
        if (!mysql_select_db(DB_NAME,$link)) {
            wp_die(__('Error: Could not select the database!'));
        }

        //get all of the tables
        $tables = array();
        $result = mysql_query('SHOW TABLES');
        while($row = mysql_fetch_row($result))
        {
            if(! empty($row[0])){
                $tables[] = $row[0];
            }
        }

        if (empty($tables))
        {
            wp_die(__('Could not retrieve the list of tables from the database!'));
        }

        $h = null;
        $time = gmdate("m-j-Y-h-i-s", time());
        $rand = self::makeSeed()+rand(12131, 9999999);
        $fname = 'bck_'.$time.'_'.$rand.'.sql';
        $filePath = WSS_PLUGIN_BACKUPS_DIR.$fname;

        if(function_exists('fopen') && function_exists('fwrite') && function_exists('fclose'))
        {
            $h = fopen($filePath,'a+');
            self::__doBackup($filePath, $tables, $h);
            fclose($h);
        }
        else {
            if(function_exists('file_put_contents')){
                self::__doBackup($filePath, $tables, $h);
            }
        }
        if(! is_file($filePath)){
            return '';
        }
        $fs = @filesize($filePath);
        return (($fs > 0) ? $fname : '');
    }

    /**
     * @private
     */
    private static function __doBackup($filePath, array $tables = array(), $h = null)
    {
        $data = 'CREATE DATABASE IF NOT EXISTS '.DB_NAME.';'.PHP_EOL;
        $data .= 'USE '.DB_NAME.';'.PHP_EOL;
        self::writeFile($filePath, $data, $h);

        //cycle through
        foreach($tables as $table)
        {
            $result = mysql_query('SELECT * FROM '.$table);
            $num_fields = mysql_num_fields($result);

            $data = 'DROP TABLE IF EXISTS '.$table.';';
            $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
            $data .= $row2[1].';'.PHP_EOL;
            self::writeFile($filePath, $data, $h);

            for ($i = 0; $i < $num_fields; $i++)
            {
                while($row = mysql_fetch_row($result))
                {
                    $data = 'INSERT INTO '.$table.' VALUES(';
                    for($j=0; $j<$num_fields; $j++)
                    {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = @preg_replace("/\n(\s*\n)+/",PHP_EOL,$row[$j]);
                        if (isset($row[$j])) { $data .= '"'.$row[$j].'"' ; } else { $data .= '""'; }
                        if ($j<($num_fields-1)) { $data .= ','; }
                    }
                    $data .= ");".PHP_EOL;
                    self::writeFile($filePath, $data, $h);
                }
            } //#! end for
        } //#! end foreach
    }


    /**
     * @public
     * Retrieve the list of all available backup files from the backups directory
     * @return array
     */
    public static function getAvailableBackupFiles()
    {
        $files = glob(WSS_PLUGIN_BACKUPS_DIR.'*.sql');
        if (empty($files)) { return array();}
        return array_map('basename', $files/*, array('.sql')*/);
    }


    /**
     * @public
     * Create a number
     * @return double
     */
    public static function makeSeed()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)$sec + ((float)$usec * 100000);
    }


    /**
     * @public
     * @global object $wpdb
     * Get the list of tables to modify
     * @return array
     */
    public static function getTablesToAlter()
    {
        global $wpdb;
        return $wpdb->get_results("SHOW TABLES LIKE '".$GLOBALS['table_prefix']."%'", ARRAY_N);
    }



    /**
     * @public
     * @global object $wpdb
     * Rename tables from database
     * @param array the list of tables to rename
     * @param string $currentPrefix the current prefix in use
     * @param string $newPrefix the new prefix to use
     * @return array
     */
    public static function renameTables($tables, $currentPrefix, $newPrefix)
    {
        global $wpdb;
        $changedTables = array();
        foreach ($tables as $k=>$table){
            $tableOldName = $table[0];
            // Try to rename the table
            $tableNewName = substr_replace($tableOldName, $newPrefix, 0, strlen($currentPrefix));
            // Try to rename the table
            $wpdb->query("RENAME TABLE `{$tableOldName}` TO `{$tableNewName}`");
            array_push($changedTables, $tableNewName);
        }
        return $changedTables;
    }



    /**
     * @public
     * @global object $wpdb
     * Rename some fields from options & usermeta tables in order to reflect the prefix change
     * @param string $oldPrefix the existent db prefix
     * @param string $newPrefix the new prefix to use
     * @return string
     */
    public static function renameDbFields($oldPrefix,$newPrefix)
    {
        global $wpdb;
        $str = '';
        if (false === $wpdb->query("UPDATE {$newPrefix}options SET option_name='{$newPrefix}user_roles' WHERE option_name='{$oldPrefix}user_roles';")) {
            $str .= '<br/>'.sprintf(__('Changing value: %suser_roles in table <strong>%soptions</strong>: <span style="color:#ff0000;">Failed</span>'),$newPrefix, $newPrefix);
        }
        $query = 'UPDATE '.$newPrefix.'usermeta
                SET meta_key = CONCAT(replace(left(meta_key, ' . strlen($oldPrefix) . "), '{$oldPrefix}', '{$newPrefix}'), SUBSTR(meta_key, " . (strlen($oldPrefix) + 1) . "))
            WHERE
                meta_key IN ('{$oldPrefix}autosave_draft_ids', '{$oldPrefix}capabilities', '{$oldPrefix}metaboxorder_post', '{$oldPrefix}user_level', '{$oldPrefix}usersettings',
                '{$oldPrefix}usersettingstime', '{$oldPrefix}user-settings', '{$oldPrefix}user-settings-time', '{$oldPrefix}dashboard_quick_press_last_post_id')";

        if (false === $wpdb->query($query)) {
            $str .= '<br/>'.sprintf(__('Changing values in table <strong>%susermeta</strong>: <span style="color:#ff0000;">Failed</span>'), $newPrefix);
        }
        if (!empty($str)) {
            $str = __('Changing database prefix').': '.$str;
        }
        return $str;
    }



    /**
     * @public
     * Update the wp-config file to reflect the table prefix change.
     * The wp file must be writable for this operation to work!
     *
     * @param string $wsd_wpConfigFile The path to the wp-config file
     * @param string $newPrefix The new prefix to use instead of the old one
     * @return int the number of bytes written to te file or -1 on error
     */
    public static function updateWpConfigTablePrefix($wsd_wpConfigFile, $newPrefix)
    {
        // If file is not writable...
        if (!is_writable($wsd_wpConfigFile)){
            return -1;
        }

        // We need the 'file' function...
        if (!function_exists('file')) {
            return -1;
        }

        // Try to update the wp-config file
        $lines = file($wsd_wpConfigFile);
        $fcontent = '';
        $result = -1;
        foreach($lines as $line)
        {
            $line = ltrim($line);
            if (!empty($line)){
                if (strpos($line, '$table_prefix') !== false){
                    $line = preg_replace("/=(.*)\;/", "= '".$newPrefix."';", $line);
                }
            }
            $fcontent .= $line;
        }
        if (!empty($fcontent))
        {
            // Save wp-config file
            $result = self::writeFile($wsd_wpConfigFile, $fcontent);
        }
        return $result;
    }



    private static $_pluginID = 'acx_plugin_dashboard_widget';

    /**
     * @public
     * @static
     * @const WSS_PLUGIN_BLOG_FEED
     * Retrieve and display a list of links for an existing RSS feed, limiting the selection to the 5 most recent items.
     * @return void
     */
    public static function displayDashboardWidget()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $opt = get_option('WSD-RSS-WGT-DISPLAY');
            if (empty($opt) || ($opt == 'no')) {
                update_option('WSD-RSS-WGT-DISPLAY', 'no');
                self::_hideDashboardWidget();
                return;
            }
        }

        //@ flag
        $run = false;

        //@ check cache
        $optData = get_option('wsd_feed_data');
        if (! empty($optData))
        {
            if (is_object($optData))
            {
                $lastUpdateTime = @$optData->expires;
                // invalid cache
                if (empty($lastUpdateTime)) { $run = true; }
                else
                {
                    $nextUpdateTime = $lastUpdateTime+(24*60*60);
                    if ($nextUpdateTime >= $lastUpdateTime)
                    {
                        $data = @$optData->data;
                        if (empty($data)) { $run = true; }
                        else {
                            // still a valid cache
                            echo $data;
                            return;
                        }
                    }
                    else { $run = true; }
                }
            }
            else { $run = true; }
        }
        else { $run = true; }

        if (!$run) { return; }

        $rss = fetch_feed(WSS_PLUGIN_BLOG_FEED);

        $out = '';
        if (is_wp_error( $rss ) )
        {
            $out = '<li>'.__('An error has occurred while trying to load the rss feed!').'</li>';
            echo $out;
            return;
        }
        else
        {
            // Limit to 5 entries.
            $maxitems = $rss->get_item_quantity(5);

            // Build an array of all the items,
            $rss_items = $rss->get_items(0, $maxitems);

            $out .= '<ul>';
            if ($maxitems == 0)
            {
                $out.= '<li>'.__('There are no entries for this rss feed!').'</li>';
            }
            else
            {
                foreach ( $rss_items as $item ) :
                    $url = esc_url($item->get_permalink());
                    $out.= '<li>';
                    $out.= '<h4><a href="'.$url.'" target="_blank" title="Posted on '.$item->get_date('F j, Y | g:i a').'">';
                    $out.= esc_html( $item->get_title() );
                    $out.= '</a></h4>';
                    $out.= '<p>';
                    $d = utf8_decode( $item->get_description());
                    $p = substr($d, 0, 120).' <a href="'.$url.'" target="_blank" title="Read all article">[...]</a>';
                    $out.= $p;
                    $out.= '</p>';
                    $out.= '</li>';
                endforeach;
            }
            $out.= '</ul>';
            $out .= '<div style="border-top: solid 1px #ccc; margin-top: 4px; padding: 2px 0;">';
            $out .= '<p style="margin: 5px 0 0 0; padding: 0 0; line-height: normal; overflow: hidden;">';
            $out .= '<a href="http://feeds.acunetix.com/acunetixwebapplicationsecurityblog"
                                style="float: left; display: block; width: 50%; text-align: right; margin-left: 30px;
                                padding-right: 22px; background: url('.self::imageUrl('rss.png').') no-repeat right center;"
                                target="_blank">'.__('Follow us on RSS').'</a>';
            $out .= '</p>';
            $out .= '</div>';
        }

        // Update cache
        $obj = new stdClass();
        $obj->expires = time();
        $obj->data = $out;
        update_option('wsd_feed_data', $obj);

        echo $out;
    }

    /**
     * @public
     * @static
     * Add the rss widget to dashboard
     * @return void
     */
    public static function addDashboardWidget()
    {
        $rssWidgetData = get_option('WSD-RSS-WGT-DISPLAY');
        if(($rssWidgetData == 'yes')){
           wp_add_dashboard_widget('acx_plugin_dashboard_widget', __('Acunetix news and updates'), array(get_class(),'displayDashboardWidget'));
        }
    }
    /**
     * Hide the dashboard rss widget
     * @static
     * @public
     */
    public static function _hideDashboardWidget() { echo '<script>document.getElementById("'.self::$_pluginID.'").style.display = "none";</script>'; }


    public static function loadPluggable(){ @require_once(ABSPATH.'wp-includes/pluggable.php'); }












}