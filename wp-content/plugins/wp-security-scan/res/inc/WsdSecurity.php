<?php if(! defined('WSS_PLUGIN_PREFIX')) return;
/**
 * Class WsdSecurity
 * Static class. Provides security fixes for WordPress
 */

class WsdSecurity extends WsdPlugin
{
    /**
     * The prefix each method should have in order to be executed automatically.
     * @var string Defaults to 'fix_'
     */
    public static $methodPrefix = 'fix_';
    public static $isVersionHidden = false;

    /** Hide WordPress version for all users but administrators */
    public static function fix_hideWpVersion()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_version_hidden']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_version_hidden']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_CRITICAL,
                __('WordPress version is displayed for all users'),
                __('<p>Displaying your WordPress version on frontend and in the backend\'s footer to all visitors
                        and users of your website is a security risk because if a hacker knows which version of WordPress a website is running, it can make it easier for him to target a known WordPress security issue.</p>'),
                sprintf(__('<p>This plugin can automatically hide your WordPress version from frontend, backend and rss feeds if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                        'Hide WordPress version for all users but administrators')
            );
            return;
        }

        $isAdmin = WsdUtil::isAdministrator();

        //@@ back-end
        if(is_admin())
        {
            if(! $isAdmin){
                function __hideFooterVersion(){ return ' ';}
                add_filter( 'update_footer', '__hideFooterVersion',800);
                self::$isVersionHidden = true;
            }
            // version hidden
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                __('WordPress version is only displayed to administrator users'),
                __('<p>Displaying your WordPress version on frontend and in the backend\'s footer to all visitors
                        and users of your website is a security risk because if a hacker knows which version of WordPress a website is running, it can make it easier for him to target a known WordPress security issue.</p>')
            );
        }
        //@@ front-end
        else {
            if (!$isAdmin)
            {
                global $wp_version, $wp_db_version, $manifest_version, $tinymce_version;

                // random values
                $v = intval( rand(0, 9999) );
                $d = intval( rand(9999, 99999) );
                $m = intval( rand(99999, 999999) );
                $t = intval( rand(999999, 9999999) );

                if ( function_exists('the_generator') )
                {
                    // eliminate version for wordpress >= 2.4
                    remove_filter( 'wp_head', 'wp_generator' );
                    $actions = array( 'rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head' );
                    foreach ( $actions as $action ) {
                        remove_action( $action, 'the_generator' );
                    }
                    // for vars
                    $wp_version = $v;
                    $wp_db_version = $d;
                    $manifest_version = $m;
                    $tinymce_version = $t;
                }
                else {
                    // for wordpress < 2.4
                    add_filter( "bloginfo_rss('version')", create_function('$a', "return $v;") );
                    // for rdf and rss v0.92
                    $wp_version = $v;
                    $wp_db_version = $d;
                    $manifest_version = $m;
                    $tinymce_version = $t;
                }
                self::$isVersionHidden = true;
            }
            // version hidden
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                __('WordPress version is only displayed to administrator users'),
                __('<p>Displaying your WordPress version on frontend and in the backend\'s footer to all visitors
                        and users of your website is a security risk because if a hacker knows which version of WordPress a website is running, it can make it easier for him to target a known WordPress security issue.</p>')
            );
        }
    }

    /** Remove various meta tags generators from the blog's head tag for non-administrators. */
    public static function fix_removeWpMetaGeneratorsFrontend()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_generators_frontend']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_generators_frontend']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_CRITICAL,
                __('WordPress meta tags are displayed on frontend to all users'),
                __('<p>By default, WordPress creates a few meta tags, among which is the currently installed version, that give a hacker the knowledge about your WordPress installation. At the moment, these meta tags are available for anyone to see, which is a potentially security risk.</p>'),
                sprintf(__('<p>This plugin can automatically hide your WordPress\'s default meta tags if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Remove various meta tags generators from the blog\'s head tag for non-administrators')
            );
            return;
        }

        if (!is_admin())
        {
            if(!WsdUtil::isAdministrator()){
                //@@ remove various meta tags generators from blog's head tag
                function acx_filter_generator($gen, $type)
                {
                    switch ( $type ) {
                        case 'html':
                            $gen = '<meta name="generator" content="WordPress">';
                            break;
                        case 'xhtml':
                            $gen = '<meta name="generator" content="WordPress" />';
                            break;
                        case 'atom':
                            $gen = '<generator uri="http://wordpress.org/">WordPress</generator>';
                            break;
                        case 'rss2':
                            $gen = '<generator>http://wordpress.org/?v=</generator>';
                            break;
                        case 'rdf':
                            $gen = '<admin:generatorAgent rdf:resource="http://wordpress.org/?v=" />';
                            break;
                        case 'comment':
                            $gen = '<!-- generator="WordPress" -->';
                            break;
                    }
                    return $gen;
                }
                foreach ( array( 'html', 'xhtml', 'atom', 'rss2', 'rdf', 'comment' ) as $type ) {
                    add_filter( "get_the_generator_".$type, 'acx_filter_generator', 10, 2 );
                }
            }
        }
        // version hidden
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('WordPress meta tags are only displayed on frontend to administrator users'),
            __('<p>By default, WordPress creates a few meta tags, among which is the currently installed version, that give a hacker the knowledge about your WordPress installation.
                At the moment, all WordPress\'s defaults meta tags are hidden for all users but administrators.</p>')
        );
    }

    /** Remove Really Simple Discovery meta tags from front-end */
    public static function fix_removeReallySimpleDiscovery()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_rsd_frontend']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_rsd_frontend']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                __('WordPress Really Simple Discovery tag is displayed on frontend to all users'),
                sprintf(__('<p>By default, WordPress creates the <strong>rsd meta tag</strong> to allow bloggers to consume services like Flickr using the <a href="%s" target="%s">XML-RPC</a> protocol.
                            If you don\'t use such services it is recommended to hide this meta tag.</p>'),
                        'http://en.wikipedia.org/wiki/XML-RPC', '_blank'),
                sprintf(__('<p>This plugin can automatically hide the rsd meta tag if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Remove Really Simple Discovery meta tags from front-end')
            );
            return;
        }

        if (!is_admin()) {
            if(!WsdUtil::isAdministrator() && function_exists('rsd_link')) {
                remove_action('wp_head', 'rsd_link');
            }
        }
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('WordPress Really Simple Discovery tag is only displayed on frontend to administrator users.'),
            sprintf(__('<p>By default, WordPress creates the <strong>rsd meta tag</strong> to allow bloggers to consume services like Flickr using the <a href="%s" target="%s">XML-RPC</a> protocol.
                            If you don\'t use such services it is recommended to hide this meta tag.</p>'),
                'http://en.wikipedia.org/wiki/XML-RPC', '_blank')
        );
    }

    /** Remove Windows Live Writer meta tags from front-end */
    public static function fix_removeWindowsLiveWriter()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_wlw_frontend']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_wlw_frontend']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                __('WordPress Windows Live Writer tag is displayed on frontend for all users'),
                sprintf(__('<p>By default, WordPress creates the wlw meta tag to allow bloggers to publish their articles using the <strong>"%s"</strong> application.
                        It is recommended to hide this meta tag from all visitors. If the option <strong>"%s"</strong> is checked on the plugin\'s settings page, this meta tag
                        will still be available for administrator users to use the <strong>"%s"</strong> application to publish their blog posts.</p>'),
                    'Windows Live Writer', 'Remove Windows Live Writer meta tags from front-end', 'Windows Live Writer'),
                sprintf(__('<p>This plugin can automatically hide the wlw meta tag if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Remove Windows Live Writer meta tags from front-end"')
            );
            return;
        }

        if (!is_admin() && function_exists('wlwmanifest_link')) {
            if(!WsdUtil::isAdministrator()) {
                remove_action('wp_head', 'wlwmanifest_link');
            }
        }
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('WordPress Windows Live Writer tag is only displayed on frontend for administrator users'),
            sprintf(__('<p>By default, WordPress creates the wlw meta tag to allow bloggers to publish their articles using the <strong>"%s"</strong> application.
                        It is recommended to hide this meta tag from all visitors. If the option <strong>"%s"</strong> is checked on the plugin\'s settings page, this meta tag
                        will still be available for administrator users to use the <strong>"%s"</strong> application to publish their blog posts.</p>'),
                'Windows Live Writer', 'Remove Windows Live Writer meta tags from front-end', 'Windows Live Writer')
        );
    }

    /** Disable error reporting (php + db) for all but administrators */
    public static function fix_disableErrorReporting()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_error_reporting']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_error_reporting']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_CRITICAL,
                __('The check for PHP and database error reporting is disabled'),
                sprintf(__('<p>By default, WordPress hides database errors, but there are times when a plugin might enable them thus it is very important to have this type of errors turned off
                            so if there is an error during a connection to the database the user will not get access to the error message generated during that request.</p>
                            <p>As regarding the PHP errors, with the <strong>display_error</strong> PHP configuration directive enabled, untrusted sources can see detailed web application environment
                            error messages which include sensitive information that can be used to craft further attacks.</p>
                            <p>Attackers will do anything to collect information in order to design their attack in a more sophisticated way to eventually hack your website or web application, and causing
                            errors to display is a common starting point. Website errors can always occur, but they should be suppressed from being displayed back to the public.</p>
                            <p>Therefore we highly recommend you to have the <strong>"%s"</strong> option checked on the plugin\'s settings page to ensure PHP and
                            database errors will be hidden from all users. For more information, please check the following <a href="%s" target="%s">article</a>.</p>'),
                    'Disable error reporting (php + db) for all but administrators', 'http://www.acunetix.com/blog/web-security-zone/articles/php-security-directive-your-website-is-showing-php-errors/', '_blank'),
                sprintf(__('<p>This plugin can do this automatically if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                    'Disable error reporting (php + db) for all but administrators')
            );
            return;
        }
        if(! WsdUtil::isAdministrator())
        {
            @error_reporting(0);
            @ini_set('display_errors','Off');
            @ini_set('display_startup_errors', 0);
            global $wpdb;
            $wpdb->hide_errors();
            $wpdb->suppress_errors();
        }
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('Error reporting, PHP and database, is enabled only for administrator users'),
            sprintf(__('<p>By default, WordPress hides database errors, but there are times when a plugin might enable them thus it is very important to have this type of errors turned off
                        so if there is an error during a connection to the database the user will not get access to the error message generated during that request.</p>
                        <p>As regarding the PHP errors, with the <strong>display_error</strong> PHP configuration directive enabled, untrusted sources can see detailed web application environment
                        error messages which include sensitive information that can be used to craft further attacks.</p>
                        <p>Attackers will do anything to collect information in order to design their attack in a more sophisticated way to eventually hack your website or web application, and causing
                        errors to display is a common starting point. Website errors can always occur, but they should be suppressed from being displayed back to the public.</p>
                        <p>Therefore we highly recommend you to have the <strong>"%s"</strong> option checked on the plugin\'s settings page to ensure PHP and
                        database errors will be hidden from all users. For more information, please check the following <a href="%s" target="%s">article</a>.</p>'),
                'Disable error reporting (php + db) for all but administrators', 'http://www.acunetix.com/blog/web-security-zone/articles/php-security-directive-your-website-is-showing-php-errors/', '_blank')
        );
    }

    /** Remove core update notifications from back-end for all but administrators */
    public static function fix_removeCoreUpdateNotification()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_core_update_notif']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_core_update_notif']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_CRITICAL,
                __('Core update notifications are displayed to all users'),
                __('<p>These notifications are displayed at the top of the screen by the WordPress platform whenever the website was updated or needs an update.</p>
                    <p>These notifications should only be viewed by the website\'s administrators and not visible to any other users registered with that website.</p>'),
                sprintf(__('<p>This plugin can automatically hide these notifications if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Remove core update notifications from back-end for all but administrators')
            );
            return;
        }

        if (!WsdUtil::isAdministrator())
        {
            add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_notices', 'maintenance_nag' );" ) );
            add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_notices', 'update_nag', 3 );" ) );
            add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_init', '_maybe_update_core' );" ) );
            add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ) );
            add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
            remove_action( 'wp_version_check', 'wp_version_check' );
            remove_action( 'admin_init', '_maybe_update_core' );
            add_filter( 'pre_transient_update_core', create_function( '$a', "return null;" ) );
            add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
        }
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('Core update notifications are only displayed to administrator users.'),
            __('<p>These notifications are displayed at the top of the screen by the WordPress platform whenever the website was updated or needs an update.</p>
                <p>Currently, these notifications are only displayed to administrator users.</p>')
        );
    }

    /** Remove plug-ins update notifications from back-end */
    public static function fix_removePluginUpdateNotifications()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_plugins_update_notif']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_plugins_update_notif']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                __('Plugins update notifications are displayed to all users'),
                __('<p>These notifications are displayed at the top of the screen by the WordPress platform whenever the blog administrator
                        needs to be informed about an available update for a plugin.</p>
                    <p>These notifications should only be viewed by the website\'s administrators and not visible to any other users registered with that website.</p>'),
                sprintf(__('<p>This plugin can automatically hide these notifications if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Remove plug-ins update notifications from back-end')
            );
            return;
        }

        if (!WsdUtil::isAdministrator())
        {
            add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_init', 'wp_plugin_update_rows' );" ), 2 );
            add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_init', '_maybe_update_plugins' );" ), 2 );
            add_action( 'admin_menu', create_function( '$a', "remove_action( 'load-plugins.php', 'wp_update_plugins' );" ) );
            add_action( 'admin_init', create_function( '$a', "remove_action( 'admin_init', 'wp_update_plugins' );" ), 2 );
            add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_update_plugins' );" ), 2 );
            add_filter( 'pre_option_update_plugins', create_function( '$a', "return null;" ) );
            remove_action( 'load-plugins.php', 'wp_update_plugins' );
            remove_action( 'load-update.php', 'wp_update_plugins' );
            remove_action( 'admin_init', '_maybe_update_plugins' );
            remove_action( 'wp_update_plugins', 'wp_update_plugins' );
            remove_action( 'load-update-core.php', 'wp_update_plugins' );
            add_filter( 'pre_transient_update_plugins', create_function( '$a', "return null;" ) );
        }
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('Plugins update notifications are only displayed to administrator users'),
            __('<p>Currently, these notifications are only displayed to administrator users.</p>')
        );
    }

    /** Remove themes update notifications from back-end */
    public static function fix_removeThemeUpdateNotifications()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_themes_update_notif']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_themes_update_notif']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                __('Themes update notifications are displayed to all users.'),
                __('<p>These notifications are displayed at the top of the screen by the WordPress platform whenever the blog administrator
                        needs to be informed about an available update for a theme.</p>
                    <p>These notifications should only be viewed by the website\'s administrators and not visible to any other users registered with that website.</p>'),
                sprintf(__('<p>This plugin can automatically hide these notifications if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Remove themes update notifications from back-end')
            );
            return;
        }

        if (!WsdUtil::isAdministrator())
        {
            remove_action( 'load-themes.php', 'wp_update_themes' );
            remove_action( 'load-update.php', 'wp_update_themes' );
            remove_action( 'admin_init', '_maybe_update_themes' );
            remove_action( 'wp_update_themes', 'wp_update_themes' );
            remove_action( 'load-update-core.php', 'wp_update_themes' );
            add_filter( 'pre_transient_update_themes', create_function( '$a', "return null;" ) );
        }
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('Themes update notifications are only displayed to administrator users'),
            __('<p>Currently, these notifications are only displayed to administrator users.</p>')
        );
    }

    /** Remove login error notifications from front-end */
    public static function fix_removeLoginErrorNotificationsFrontEnd()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_login_errors']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_login_errors']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                __('WordPress login errors are displayed.'),
                __('<p>Every time a failed login is encountered, the WordPress platform generates an error message that is displayed to the user.
                        This is a potential security risk because it let\'s the user know of his mistake (be it a wrong user name or password) thus making your
                        WordPress website more vulnerable to attacks.</p>
                    <p>We strongly recommend you to hide these login error messages from all users to ensure a better security of your blog.</p>'),
                sprintf(__('<p>This plugin can automatically hide these notifications if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Remove login error notifications from front-end')
            );
            return;
        }
        $str = '<link rel="stylesheet" type="text/css" href="'.WsdUtil::cssUrl('acx-styles-extra.css').'"/>';
        add_action('login_head', create_function('$a', "echo '{$str}';"));
        add_filter('login_errors', create_function('$a', "return null;"));
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('WordPress login errors are not displayed.'),
            __('<p>Currently, these errors are hidden to all users.</p>')
        );
    }

    /** Hide admin notifications for non admins. */
    public static function fix_hideAdminNotifications()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_wp_admin_notices']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_admin_notices']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                __('WordPress admin notifications are displayed to all users.'),
                __('<p>These notifications are displayed at the top of the screen by the WordPress platform whenever the blog administrator
                       needs to be informed about an event that has occurred inside WordPress, it could be about an available update for the
                       WordPress platform, a plugin or a theme that was updated or needs an update or to be configured, etc.</p>
                    <p>These notifications should only be viewed by the website\'s administrators and not visible to any other users registered with that website.</p>'),
                sprintf(__('<p>This plugin can automatically hide these notifications if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Hide admin notifications for non admins')
            );
            return;
        }

        if (!WsdUtil::isAdministrator())
        {
            add_action('init', create_function('$a', "remove_action('init', 'wp_version_check');"), 2);
            add_filter('pre_option_update_core', create_function('$a', "return null;"));
        }
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('WordPress admin notifications are only displayed to administrator users.'),
            __('<p>These notifications are displayed at the top of the screen by the WordPress platform whenever the blog administrator
                       needs to be informed about an event that has occurred inside WordPress, it could be about an available update for the
                       WordPress platform, a plugin or a theme that was updated or needs an update or to be configured, etc.</p>
                <p>Currently, these notifications are displayed only to administrator users.</p>'));
    }

    /** Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing */
    public static function fix_preventDirectoryListing()
    {
        global $wsdPluginAlertsArray;

        $actionName = $wsdPluginAlertsArray['fix_wp_dir_listing']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_dir_listing']['type'];
        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                __('Directory listing check is disabled. This option should be enabled.'),
                __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                    The specific risks and consequences vary depending on which files are listed and accessible.
                    Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                sprintf(__('<p>This plugin can automatically create an empty <strong>index.php</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if
                    the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                    'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
            );
            return;
        }
        else {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                __('Directory listing check is enabled.'),
                __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                    The specific risks and consequences vary depending on which files are listed and accessible.
                    Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
            );
        }

        $data = '<?php exit;?>';
        $contentDir = trailingslashit(WP_CONTENT_DIR);
        $pluginsDir = $contentDir.'plugins';
        $themesDir = $contentDir.'themes';
        $uploadsDir = $contentDir.'uploads';

        $actionName = $wsdPluginAlertsArray['fix_wp_index_content']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_index_content']['type'];
        $file = $contentDir.'/index.php';
        if(is_dir($contentDir)){
            if(is_file($file)){
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                    sprintf(__('<strong>"%s"</strong> directory is secure from directory listing.'),'/wp-content'),
                    __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                    The specific risks and consequences vary depending on which files are listed and accessible.
                    Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                );
            }
            else {
                if (is_writable($contentDir))
                {
                    WsdUtil::writeFile($file,$data);
                    @chmod($file,'0644');
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                        sprintf(__('<strong>"%s"</strong> directory is secure from directory listing.'),'/wp-content'),
                        __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                    );
                }
                else {
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                        sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content'),
                        __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                        sprintf(__('<p>This plugin can automatically create an empty <strong>index.php</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if
                            the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                            'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
                    );
                }
            }
        }

        $actionName = $wsdPluginAlertsArray['fix_wp_index_plugins']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_index_plugins']['type'];
        $file = $pluginsDir.'/index.php';
        if(is_dir($pluginsDir)){
            if(is_file($file)){
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                    sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/plugins'),
                    __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                );
            }
            else {
                if (is_writable($pluginsDir))
                {
                    WsdUtil::writeFile($file,$data);
                    @chmod($file,'0644');
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                        sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/plugins'),
                        __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                    );
                }
                else {
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                        sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/plugins'),
                        __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                        sprintf(__('<p>This plugin can automatically create an empty <strong>index.php</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if
                            the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                            'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
                    );
                }
            }
        }

        $actionName = $wsdPluginAlertsArray['fix_wp_index_themes']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_index_themes']['type'];
        $file = $themesDir.'/index.php';
        if(is_dir($themesDir)){
            if(is_file($file)){
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                    sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/themes'),
                    __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                );
            }
            else {
                if (is_writable($themesDir))
                {
                    WsdUtil::writeFile($file,$data);
                    @chmod($file,'0644');
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                        sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/themes'),
                        __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                    );
                }
                else {
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                        sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/themes'),
                        __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                        sprintf(__('<p>This plugin can automatically create an empty <strong>index.php</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if
                            the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                            'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
                    );
                }
            }
        }

        $actionName = $wsdPluginAlertsArray['fix_wp_index_uploads']['name'];
        $alertType = $wsdPluginAlertsArray['fix_wp_index_uploads']['type'];
        $file = $uploadsDir.'/index.php';
        if(is_dir($uploadsDir)){
            if(is_file($file)){
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                    sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/uploads'),
                    __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                );
            }
            else {
                if (is_writable($uploadsDir))
                {
                    WsdUtil::writeFile($file,$data);
                    @chmod($file,'0644');
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                        sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/uploads'),
                        __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                    );
                }
                else {
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                        sprintf(__('<strong>"%s"</strong> directory is not secure from directory listing.'),'/wp-content/uploads'),
                        __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory.
                            The specific risks and consequences vary depending on which files are listed and accessible.
                            Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                        sprintf(__('<p>This plugin can automatically create an empty <strong>index.php</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if
                            the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                            'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
                    );
                }
            }
        }
    }

    /** Remove the version parameter from urls */
    public static function fix_removeWpVersionFromLinks($src = '')
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['fix_remove_wp_version_links']['name'];
        $alertType = $wsdPluginAlertsArray['fix_remove_wp_version_links']['type'];

        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_CRITICAL,
                __('WordPress version is displayed in links for all users'),
                __('<p>By default, WordPress will display the current version in links to javascript scripts or stylesheets.
                    Therefore, if anyone has access to this information it might be a security risk because if a hacker knows which version of WordPress a website is running,
                    it can make it easier for him to target a known WordPress security issue.</p>'),
                sprintf(__('<p>This plugin can automatically hide the WordPress version from links if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'), 'Remove the version parameter from urls')
            );
            return;
        }

        if (!WsdUtil::isAdministrator())
        {
            add_filter('script_loader_src', array('WsdSecurityHelper', '__removeWpVersionFromLinks'));
            add_filter('style_loader_src', array('WsdSecurityHelper', '__removeWpVersionFromLinks'));
        }
        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
            __('WordPress version displayed in links only for administrator users.'),
            __('<p>By default, WordPress will display the current version in links to javascript scripts or stylesheets.
                    Therefore, if anyone has access to this information it might be a security risk because if a hacker knows which version of WordPress a website is running,
                    it can make it easier for him to target a known WordPress security issue.</p>')
        );
    }

    /** Empty the content of the readme.html file from the root directory. */
    public static function fix_emptyReadmeFileFromRoot()
    {
        global $wsdPluginAlertsArray;

        // if the file is 404 or not readable or empty, there is no need to display the alert
        $filePath = trailingslashit(ABSPATH).'readme.html';
        if(! is_file($filePath)){ return; }
        if(! is_readable($filePath)) { return; }
        $fsize = @filesize($filePath);
        if(false !== $fsize && $fsize == 0) { return; }

        $actionName = $wsdPluginAlertsArray['fix_empty_root_readme_file']['name'];
        $alertType = $wsdPluginAlertsArray['fix_empty_root_readme_file']['type'];
        /* This check is important so this function will run only if the user enables it. */
        if(! self::isSettingEnabled(__FUNCTION__)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                sprintf(__('Clearing the content of the <strong>"%s"</strong> file from the <strong>"%s"</strong> directory is disabled.'), 'readme.html', 'root'),
                __('<p>A default WordPress installation contains a readme.html file. This file is a simple html file that does not contain executable content that can be exploited by hackers or malicious users.
                        Still, this file can provide hackers the version of your WordPress installation, therefore it is important to either delete this file or make it inaccessible for your visitors.</p>'),
                sprintf(__('<p>This plugin can automatically delete its content (assuming the file exists) if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.
                    You can also delete this file manually by connecting to your website through an FTP connection.</p>'), 'Empty the content of the readme.html file from the root directory')
            );
            return;
        }
        else {
            // clear the content of the file
            $result = file_put_contents($filePath,'');
            // failure
            if(false === $result){
                // todo
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                    sprintf(__('The content of the <strong>"%s"</strong> file from the <strong>"%s"</strong> directory could not be deleted.'), 'readme.html', 'root'),
                    __('<p>A default WordPress installation contains a readme.html file. This file is a simple html file that does not contain executable content that can be exploited by hackers or malicious users.
                        Still, this file can provide hackers the version of your WordPress installation, therefore it is important to either delete this file or make it inaccessible for your visitors.</p>'),
                    __('<p>We have encountered an error while trying to delete the content of this file, thus you will have to manually delete it or make it inaccessible from your visitors by setting the file permissions to <strong>0440</strong> or lower.</p>')
                );
                return;
            }
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                __('The content of the readme.html file from the root directory has been deleted.'),
                __('<p>A default WordPress installation contains a readme.html file. This file is a simple html file that does not contain executable content that can be exploited by hackers or malicious users.
                        Still, this file can provide hackers the version of your WordPress installation, therefore it is important to either delete this file or make it inaccessible for your visitors.</p>')
            );
        }

    }

}

class WsdSecurityHelper
{
    /**
     * @private
     * @param $src
     * @return mixed
     */
    public static function __removeWpVersionFromLinks($src)
    {
        // Just the URI without the query string.
        $src = preg_replace("/\?ver=(.*)/mi", '', $src);
        return $src;
    }

}