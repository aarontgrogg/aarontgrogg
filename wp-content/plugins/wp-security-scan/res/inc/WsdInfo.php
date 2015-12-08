<?php
/**
 * Class WsdInfo
 * Static class. Provides common methods to inspect a WordPress installation settings
 */
class WsdInfo
{
    public static function getCurrentVersionInfo()
    {
        $c = get_site_transient( 'update_core' );
        if ( is_object($c))
        {
            if (empty($c->updates))
            {
                return '<span class="acx-icon-alert-success">'.__('You have the latest version of Wordpress.').'</span>';
            }

            if (!empty($c->updates[0]))
            {
                $c = $c->updates[0];

                if ( !isset($c->response) || 'latest' == $c->response ) {
                    return '<span class="acx-icon-alert-success">'.__('You have the latest version of Wordpress.').'</span>';
                }

                if ('upgrade' == $c->response)
                {
                    $lv = $c->current;
                    $m = '<span class="acx-icon-alert-critical">'.sprintf('A new version of Wordpress <strong>(%s)</strong> is available. You should upgrade to the latest version.', $lv).'</span>';
                    return __($m);
                }
            }
        }

        return '<span class="acx-icon-alert-critical">'.__('An error has occurred while trying to retrieve the status of your Wordpress version.').'</span>';
    }

    public static function getDatabasePrefixInfo()
    {
        global $table_prefix;

        if (strcasecmp('wp_', $table_prefix)==0) {
            return '<span class="acx-icon-alert-critical">'
            .__('Your database prefix should not be <code>wp_</code>.')
            .'(<a href="http://www.websitedefender.com/wordpress-security/wordpress-database-tables-prefix/" target="_blank">'.__('read more').'</a>)</span>';
        }

        return '<span class="acx-icon-alert-success">'.__('Your database prefix is not <code>wp_</code>.').'</span>';
    }

    public static function getWpVersionStatusInfo()
    {
        if (WsdSecurity::$isVersionHidden) {
            return '<span class="acx-icon-alert-success">'.__('The Wordpress version <code>is</code> hidden for all users but administrators.').'</span>';
        }
    }

    public static function getPhpStartupErrorStatusInfo()
    {
        $dse = strtolower(ini_get('display_startup_errors'));
        if ($dse == 0) {
            return '<span class="acx-icon-alert-success">'.__('Startup errors <code>are not</code> displayed.').'</span><br/>';
        }

        return '<span class="acx-icon-alert-critical">'.__('Startup errors <code>are displayed</code>.').'</span>'.'<br/>';
    }

    public static function getAdminUsernameInfo()
    {
        global $wpdb;

        $u = $wpdb->get_var("SELECT `ID` FROM $wpdb->users WHERE user_login='admin';");

        if (empty($u)) {
            return '<span class="acx-icon-alert-success">'.__('User <code>admin</code> was not found.').'</span>';
        }

        return '<span class="acx-icon-alert-critical">'.__('User <code>admin</code> was found! You should change it in order to avoid user enumeration attacks.').'</span>';
    }

    public static function getWpAdminHtaccessInfo()
    {
        $file = trailingslashit(ABSPATH).'wp-admin/.htaccess';
        if (is_file($file)) {
            return '<span class="acx-icon-alert-success">'.__('The <code>.htaccess</code> file was found in the <code>wp-admin</code> directory.').'</span>';
        }

        return '<span class="acx-icon-alert-info">'
        .__('The <code>.htaccess</code> file was not found in the <code>wp-admin</code> directory.')
        .'(<a href="http://www.websitedefender.com/wordpress-security/htaccess-files-wordpress-security/" target="_blank">'.__('read more').'</a>)</span>';
    }

    public static function getDatabaseUserAccessRightsInfo()
    {
        $rights = WsdInfoServer::getDatabaseUserAccessRights();
        $rightsNeeded = $rights['rightsNeeded'];
        $rightsHaving = $rights['rightsHaving'];
        $rightsMissing = array_diff($rightsNeeded, $rightsHaving);

        if ($rights['rightsTooMuch']) {
            $e = '<code>'.implode('</code>, <code>', $rightsNeeded).'</code>';
            $m = sprintf(__("Your currently used User to access the WordPress Database <code>holds too many rights</code>.
                We suggest that you limit his rights (to only %s) or to use another User with more limited rights instead, to increase your website's Security."),$e);
            return '<span class="acx-icon-alert-info">'.$m.'</span>';
        }
        else
        {
            if($rights['rightsEnough']){
                $m = __("Your currently used User to access the WordPress Database holds the appropriate rights to interact with the database.");
                return '<span class="acx-icon-alert-success">'.$m.'</span>';
            }
            else {
                $missing = '<code>'.implode('</code>, <code>', $rightsMissing).'</code>';
                $m = sprintf(__("Your currently used User to access the WordPress Database is missing the following rights to interact with the database: %s"),$missing);
                return '<span class="acx-icon-alert-info">'.$m.'</span>';
            }
        }
    }

    public static function getWpContentIndexInfo()
    {
        if (is_file(trailingslashit(WP_CONTENT_DIR).'index.php')) {
            return '<span class="acx-icon-alert-success">'.__('The <code>index.php</code> file <code>was found</code> in the wp-content directory.').'</span>'.'<br/>';
        }

        return '<span class="acx-icon-alert-info">'.__('The <code>index.php</code> file <code>was not found</code> in the wp-content directory! You should create one in order to prevent directory listings.').'</span>'.'<br/>';
    }

    public static function getWpContentPluginsIndexInfo()
    {
        $dirPath = trailingslashit(WP_CONTENT_DIR).'plugins/';
        if(!is_dir($dirPath)) { return ''; }
        if (is_file($dirPath.'index.php')) {
            return '<span class="acx-icon-alert-success">'.__('The <code>index.php</code> file <code>was found</code> in the plugins directory.').'</span>'.'<br/>';
        }

        return '<span class="acx-icon-alert-info">'.acxt_t('The <code>index.php</code> file <code>was not found</code> in the plugins directory! You should create one in order to prevent directory listings.').'</span>'.'<br/>';
    }

    public static function getWpContentThemesIndexInfo()
    {
        $dirPath = trailingslashit(WP_CONTENT_DIR).'themes/';
        if(!is_dir($dirPath)) { return ''; }
        if (is_file($dirPath.'index.php')) {
            return '<span class="acx-icon-alert-success">'.__('The <code>index.php</code> file <code>was found</code> in the themes directory.').'</span>'.'<br/>';
        }

        return '<span class="acx-icon-alert-info">'.__('The <code>index.php</code> file <code>was not found</code> in the themes directory! You should create one in order to prevent directory listings.').'</span>'.'<br/>';
    }

    public static function getWpContentUploadsIndexInfo()
    {
        $dirPath = trailingslashit(WP_CONTENT_DIR).'uploads/';
        if(!is_dir($dirPath)) { return ''; }
        if (is_file($dirPath.'index.php')) {
            return '<span class="acx-icon-alert-success">'.__('The <code>index.php</code> file <code>was found</code> in the uploads directory.').'</span>'.'<br/>';
        }
        return '<span class="acx-icon-alert-info">'.__('The <code>index.php</code> file <code>was not found</code> in the uploads directory! You should create one in order to prevent directory listings.').'</span>'.'<br/>';
    }

}

class WsdInfoServer extends WsdInfo
{
    public static function getServerInfo()
    {
        global $wpdb;
        $sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
        $mysqlinfo = $wpdb->get_results("SHOW VARIABLES LIKE 'sql_mode'");
        if (is_array($mysqlinfo)) $sql_mode = $mysqlinfo[0]->Value;
        if (empty($sql_mode)) $sql_mode = __('Not set');
        $sm = ini_get('safe_mode');
        if (strcasecmp('On', $sm) == 0) { $safe_mode = __('On'); }
        else { $safe_mode = __('Off'); }
        if(ini_get('allow_url_fopen')) $allow_url_fopen = __('On');
        else $allow_url_fopen = __('Off');
        if(ini_get('upload_max_filesize')) $upload_max = ini_get('upload_max_filesize');
        else $upload_max = __('N/A');
        if(ini_get('post_max_size')) $post_max = ini_get('post_max_size');
        else $post_max = __('N/A');
        if(ini_get('max_execution_time')) $max_execute = ini_get('max_execution_time');
        else $max_execute = __('N/A');
        if(ini_get('memory_limit')) $memory_limit = ini_get('memory_limit');
        else $memory_limit = __('N/A');
        if (function_exists('memory_get_usage')) $memory_usage = round(memory_get_usage() / 1024 / 1024, 2) . __(' MByte');
        else $memory_usage = __('N/A');
        if (is_callable('exif_read_data')) $exif = __('Yes'). " ( V" . substr(phpversion('exif'),0,4) . ")" ;
        else $exif = __('No');
        if (is_callable('iptcparse')) $iptc = __('Yes');
        else $iptc = __('No');
        if (is_callable('xml_parser_create')) $xml = __('Yes');
        else $xml = __('No');

        $sqlModeText = __('SQL Mode (sql_mode) is a MySQL system variable. By means of this variable the MySQL Server SQL Mode is controlled.
            Many operational characteristics of MySQL Server can be configured by setting the SQL Mode.
            By setting the SQL Mode appropriately, a client program can instruct the server how strict or forgiving to be about accepting input data, enable or disable behaviors relating to standard SQL conformance,
            or provide better compatibility with other database systems. By default, the server uses a sql_mode value of  \'\'  (the empty string), which enables no restrictions.
            Thus, the server operates in forgiving mode (non-strict mode) by default. In non-strict mode, the MySQL server converts erroneous input values to the closest legal
            values (as determined from column definitions) and continues on its way.');
        $phpSafeModeText = __('The PHP Safe Mode (safe_mode) is an attempt to solve the shared-server security problem.
            It is architecturally incorrect to try to solve this problem at the PHP level, but since the alternatives at the web server and OS levels aren\'t
            very realistic, many people, especially ISP\'s, use safe mode for now.');
        $phpAllowFopenText = __('PHP allow_url_fopen option, if enabled (allows PHP\'s file functions - such as \'file_get_contents()\' and the \'include\' and \'require\' statements),
            can retrieve data from remote locations, like an FTP or web site, which may pose a security risk.');
        $phpMemoryLimitText = __('PHP memory_limit option sets the maximum amount of memory in bytes that a script is allowed to allocate.
            By enabling a realistic memory_limit you can protect your applications from certain types of Denial of Service attacks, and also from bugs in
            applications (such as infinite loops, poor use of image based functions, or other memory intensive mistakes).');
        $phpMaxUploadSizeText = __('PHP upload_max_filesize option limits the maximum size of files that PHP will accept through uploads. Attackers may attempt to send grossly oversized files to exhaust your system resources;
            by setting a realistic value here you can mitigate some of the damage by those attacks.');
        $phpMaxPostSizeText = __('PHP post_max_size option limits the maximum size of the POST request that PHP will process. Attackers may attempt to send grossly oversized POST requests to exhaust your system resources;
            by setting a realistic value here you can mitigate some of the damage by those attacks.');
        $phpScriptExecTimeText = __('PHP max_execution_time option sets the maximum time in seconds a script is allowed to run before it is terminated by the parser.
            This helps prevent poorly written scripts from tying up the server.');
        $exifText = __('PHP exif extension enables you to work with image meta data. For example, you may use exif functions to read meta data of pictures taken from digital cameras by working with
            information stored in the headers of the JPEG and TIFF images.');
        $iptcText = __('IPTC data is a method of storing textual information in images defined by the International Press Telecommunications Council.
            It was developed for press photographers who need to attach information to images when they are submitting them electronically but it is useful for all photographers.
            It provides a standard way of storing information such as captions, keywords, location. Because the information is stored in the image in a standard way this information
            can be accessed by other IPTC aware applications.');
        $xmlText = __('XML (eXtensible Markup Language) is a data format for structured document interchange on the Web. It is a standard defined by the World Wide Web Consortium (W3C).');

        $str = '<script type="text/javascript" src="'. WsdUtil::jsUrl('wsdplugin_glossary_tooltip.js').'"></script>';
        $str .= '<ul class="acx-common-list">';
        $str .= '<li>'. __('Operating System').' : <strong> '.PHP_OS.'</strong></li>';
        $str .= '<li>'. __('Server').' : <strong>'.$_SERVER["SERVER_SOFTWARE"].'</strong></li>';
        $str .= '<li>'. __('Memory usage').' : <strong>'.$memory_usage.'</strong></li>';
        $str .= '<li>'. __('PHP Version').' : <strong>'. PHP_VERSION.'</strong></li>';
        $str .= '<li>'. __('MYSQL Version').' : <strong>'.$sqlversion.'</strong></li>';
        $str .= '</ul>';

        $str .= '<p class="clear" style="margin-top: 7px;"></p>';

        $str .= '<ul class="acx-common-list">';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$sqlModeText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('SQL Mode').' : <strong>'.$sql_mode.'</strong></li>';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$phpSafeModeText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP Safe Mode').' : <strong>'. $safe_mode.'</strong></li>';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$phpAllowFopenText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP Allow URL fopen').' : <strong>'. $allow_url_fopen.'</strong></li>';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$phpMemoryLimitText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP Memory Limit').' : <strong>'. $memory_limit.'</strong></li>';
        $str .= '<li class="wsdplugin-tooltip"onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$phpMaxUploadSizeText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP Max Upload Size').' : <strong>'. $upload_max.'</strong></li>';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$phpMaxPostSizeText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP Max Post Size').' : <strong>'. $post_max.'</strong></li>';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$phpScriptExecTimeText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP Max Script Execute Time').' : <strong>'. $max_execute.'s</strong></li>';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$exifText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP Exif support').' : <strong>'. $exif.'</strong></li>';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$iptcText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP IPTC support').' : <strong>'. $iptc.'</strong></li>';
        $str .= '<li class="wsdplugin-tooltip" onmouseover="wsdplugin_glossary_tooltip.show(this);" data-bind-title="'.$xmlText.'" onmouseout="wsdplugin_glossary_tooltip.hide();">'. __('PHP XML support').' : <strong>'. $xml.'</strong></li>';
        $str .= '</ul>';
        return $str;
    }

    /**
     * @public
     * @static
     * @global $wpdb, DB_USER, DB_HOST
     *
     * Retrieve the rights the current used user to connect to the database server has.
     *
     * @return array  array('rightsEnough' => true|false, 'rightsTooMuch' => true|false, 'rightsMissing' => array, 'rightsNeeded' => array);
     */
    public static function getDatabaseUserAccessRights()
    {
        global $wpdb;

        $rightsNeeded = array('SELECT','INSERT','UPDATE','DELETE','ALTER');
        $data = array(
            'rightsEnough' => false,
            'rightsTooMuch' => false,
            'rightsHaving' => array(),
            'rightsNeeded' => $rightsNeeded
        );

        $rights = $wpdb->get_results("SHOW GRANTS FOR '".DB_USER."'@'".DB_HOST."'", ARRAY_N);

        if (empty($rights)) {
            //return $data;
            $rights = $wpdb->get_results("SHOW GRANTS FOR current_user()", ARRAY_N);
            if(empty($rights)){
                return $data;
            }
        }

        foreach($rights as $_right)
        {
            $right = $_right[0];
            //#! no rights
            if(preg_match("/GRANT USAGE ON/", $right)){
                continue;
            }
            //#! way too many. db scope || global scope
            $right = str_replace('\\','',$right);
            $pattern = "/ALL PRIVILEGES ON `?".preg_quote(DB_NAME)."`?/";
            if(preg_match($pattern, $right) || preg_match("/ALL PRIVILEGES ON \*\./", $right)){
                array_push($rightsNeeded, 'CREATE');
                $data = array(
                    'rightsEnough' => true,
                    'rightsTooMuch' => true,
                    'rightsHaving' => $rightsNeeded,
                    'rightsNeeded' => $rightsNeeded
                );
                break;
            }
            //#! more secure. db scope || global scope
            elseif(preg_match_all("/GRANT (.*) ON `?".preg_quote(DB_NAME)."`?/",$right,$matches) || preg_match_all("/GRANT (.*) ON \*\./",$right,$matches)){
                if(! empty($matches[1][0])){
                    $foundRights = explode(',', $matches[1][0]);
                    $foundRights = array_map("trim", $foundRights);
                    $missingRights = array_diff($rightsNeeded, $foundRights);
                    $data = array(
                        'rightsEnough' => (empty($missingRights) ? true : false),
                        'rightsTooMuch' => false,
                        'rightsHaving' => $foundRights,
                        'rightsNeeded' => $rightsNeeded
                    );
                    break;
                }
            }
        }
        return $data;
    }
}