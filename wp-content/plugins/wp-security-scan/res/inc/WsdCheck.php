<?php if(! defined('WSS_PLUGIN_PREFIX')) return;
/**
 * Class WsdCheck
 * Static class. Provides security checks for Wordpress
 */
class WsdCheck extends WsdPlugin
{
    // type: stack - cron
    public static function adminUsername()
    {
        global $wpdb, $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['check_username_admin']['name'];
        $alertType = $wsdPluginAlertsArray['check_username_admin']['type'];

        $u = $wpdb->get_var("SELECT `ID` FROM $wpdb->users WHERE user_login='admin';");
        if(empty($u)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                sprintf(__('User <strong>"%s"</strong> (with administrative rights) was not found'), 'admin'),
                sprintf(__('<p>One well known and dangerous WordPress security vulnerability is User Enumeration, in which a
                            malicious user is able to enumerate a valid WordPress user account to launch a brute force attack against it.
                            In order to help deter this type of attack, it is important not to have the default <a href="%s" target="%s">WordPress administrator</a>
                            username enabled on your blog.</p>'), 'http://www.acunetix.com/blog/web-security-zone/articles/default-wordpress-administrator-account/', '_blank')
            );
        }
        else {
            // check to see if this user is an administrator
            $userRole = $wpdb->get_var("SELECT meta_value FROM ".$wpdb->usermeta. " WHERE user_id = $u AND meta_key = '".$wpdb->prefix."user_level'");
            if(! empty($userRole)){
                $userRole = intval($userRole);
                if(in_array($userRole, array(8,9,10))){
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_CRITICAL,
                        sprintf(__('The default user <strong>"%s"</strong> was found'), 'admin'),
                        sprintf(__('<p>One well known and dangerous WordPress security vulnerability is User Enumeration, in which a malicious user is able to enumerate
                            a valid WordPress user account to launch a brute force attack against it.</p>
                            <p>In order to help deter this type of attack, you should change your default <a href="%s" target="%s">WordPress administrator</a>
                            username to something more difficult to guess.</p>'), 'http://www.acunetix.com/blog/web-security-zone/articles/default-wordpress-administrator-account/', '_blank'),
                        // solution
                        __('<p>Do not make the following change unless you are comfortable working with PHPMyAdmin and MySQL. If not, ask someone who is familiar with WordPress and MySQL to assist you. </p>
                            <p>Also, it is of utmost importance to backup your whole blog - including the database - before making any of the changes described below.</p>
                            <p>To change your WordPress default admin username, navigate to your web host\'s MySQL administration tool (probably PHPMyAdmin) and browse to your WordPress database.
                            Locate the users table, in which you will find a user_login column. One of the rows will contain admin in the field.
                            Change this to a complex and hard-to-guess name, which ideally consists of alpha-numeric characters.</p>
                            <p><strong>IMPORTANT:</strong> Even if the username is hard to guess, you will still need a very strong password.</p>'));
                    return;
                }
            }
        }
    }

    // type: overwrite
    public static function check_tablePrefix()
    {
        global $wpdb, $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['check_table_prefix']['name'];
        $alertType = $wsdPluginAlertsArray['check_table_prefix']['type'];

        if(strcasecmp('wp_', $wpdb->prefix)==0){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_CRITICAL,
                sprintf(__('The default WordPress database prefix (<strong>%s</strong>) is used'), $wpdb->prefix),
                __('<p>The majority of reported WordPress database security attacks were performed by exploiting SQL Injection vulnerabilities.
                        By renaming the WordPress database table prefixes you are securing your WordPress blog and website from zero day SQL injections attacks.</p>
                    <p>Therefore by renaming the WordPress database table prefixes, you are automatically enforcing your WordPress database security against such dangerous attacks
                        because the attacker would not be able to guess the table names.</p>
                    <p>We recommend to use difficult to guess prefixes, like long random strings which include both letters and numbers.</p>'),
                sprintf(__('<p>This plugin can automatically <a href="%s">do this</a> for you, but if you want to do it manually then please read this <a href="%s" target="%s">article</a> first.</p>'),
                        'admin.php?page=wsd_database',
                        'http://www.websitedefender.com/wordpress-security/change-wordpress-database-prefix/', '_blank')
            );
        }
        else {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                __('The default WordPress database prefix is not used'),
                __('<p>The majority of reported WordPress database security attacks were performed by exploiting SQL Injection vulnerabilities.
                        By renaming the WordPress database table prefixes you are securing your WordPress blog and website from zero day SQL injections attacks.</p>
                    <p>Therefore by renaming the WordPress database table prefixes, you are automatically enforcing your WordPress database security against such dangerous attacks because the attacker would not be able to guess the table names.</p>')
            );
        }
    }

    // type: overwrite
    public static function check_currentVersion()
    {
        global $wsdPluginAlertsArray;
        $actionName = $wsdPluginAlertsArray['check_wp_current_version']['name'];
        $alertType = $wsdPluginAlertsArray['check_wp_current_version']['type'];

        $c = get_site_transient('update_core');
        if(is_object($c))
        {
            if(empty($c->updates)) {
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                    __('You have the latest version of WordPress installed'),
                    __('<p>The latest WordPress version is usually more stable and secure, and is only released to include new features or fix technical and WordPress security bugs;
                            making it an important part of your website administration to keep up to date since some fixes might resolve security issues.<p>
                        <p>Running an older WordPress version could put your blog security at risk, allowing a hacker to exploit known vulnerabilities for your specific version and take full control over your web server.</p>')
                );
            }
            else {
                if (!empty($c->updates[0]))
                {
                    $c = $c->updates[0];
                    if (!isset($c->response) || 'latest' == $c->response ) {
                        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                            __('You have the latest version of WordPress installed'),
                            __('<p>The latest WordPress version is usually more stable and secure, and is only released to include new features or fix technical and WordPress security bugs;
                            making it an important part of your website administration to keep up to date since some fixes might resolve security issues.<p>
                        <p>Running an older WordPress version could put your blog security at risk, allowing a hacker to exploit known vulnerabilities for your specific version and take full control over your web server.</p>')
                        );
                    }
                    elseif ('upgrade' == $c->response) {
                        self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_CRITICAL,
                            sprintf(__('An updated version of WordPress is available: <strong>%s</strong>'), $c->current),
                            __('<p>The latest WordPress version is usually more stable and secure, and is only released to include new features or fix technical and WordPress security bugs;
                                    making it an important part of your website administration to keep up to date since some fixes might resolve security issues.<p>
                                <p>Running an older WordPress version could put your blog security at risk, allowing a hacker to exploit known vulnerabilities for your specific version and take full control over your web server.</p>'),
                            sprintf(__('<p>It is recommended to update your WordPress installation as soon as possible. More information on updating WordPress manually and automatically
                                    can be found on the official <a href="%s" target="%s">WordPress site</a>.</p>'), 'http://codex.wordpress.org/Updating_WordPress', '_blank')
                        );
                    }
                }
            }
        }
    }

    // type: overwrite
    public static function check_files()
    {
        global $wsdPluginAlertsArray;

        $actionName = $wsdPluginAlertsArray['check_index_wp_content']['name'];
        $alertType = $wsdPluginAlertsArray['check_index_wp_content']['type'];
        $path = WP_CONTENT_DIR.'/index.php';
        if(!is_file($path)) {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                sprintf(__('The <strong>"%s"</strong> file was not found in the <strong>"%s"</strong> directory'), 'index.php', '/wp-content'),
                __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory. The specific risks and consequences vary depending on which files are listed and accessible.</p>
                    <p>Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                sprintf(__('<p>This plugin can automatically create an empty <strong>"%s"</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if
                            the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                            'index.php', 'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
            );
        }
        else {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                sprintf(__('The <strong>"%s"</strong> file was found in the <strong>"%s"</strong> directory'), 'index.php', '/wp-content'),
                __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory. The specific risks and consequences vary depending on which files are listed and accessible.</p>
                    <p>Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
            );
        }

        $actionName = $wsdPluginAlertsArray['check_index_wp_plugins']['name'];
        $alertType = $wsdPluginAlertsArray['check_index_wp_plugins']['type'];
        $path = WP_CONTENT_DIR.'/plugins/index.php';
        if(!is_file($path)) {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                sprintf(__('The <strong>"%s"</strong> file was not found in the <strong>"%s"</strong> directory'), 'index.php', '/wp-content/plugins'),
                __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory. The specific risks and consequences vary depending on which files are listed and accessible.</p>
                    <p>Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                sprintf(__('<p>This plugin can automatically create an empty <strong>"%s"</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if
                            the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                        'index.php', 'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
            );
        }
        else {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                sprintf(__('The <strong>"%s"</strong> file was found in the <strong>"%s"</strong> directory'), 'index.php', '/wp-content/plugins'),
                __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory. The specific risks and consequences vary depending on which files are listed and accessible.</p>
                    <p>Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
            );
        }

        $actionName = $wsdPluginAlertsArray['check_index_wp_themes']['name'];
        $alertType = $wsdPluginAlertsArray['check_index_wp_themes']['type'];
        $path = WP_CONTENT_DIR.'/themes/index.php';
        if(!is_file($path)) {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                sprintf(__('The <strong>"%s"</strong> file was not found in the <strong>"%s"</strong> directory'), 'index.php', '/wp-content/themes'),
                __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory. The specific risks and consequences vary depending on which files are listed and accessible.</p>
                    <p>Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                sprintf(__('<p>This plugin can automatically create an empty <strong>"%s"</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if
                            the option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                            'index.php', 'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
            );
        }
        else {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                sprintf(__('The <strong>"%s"</strong> file was found in the <strong>"%s"</strong> directory'), 'index.php', '/wp-content/themes'),
                __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory. The specific risks and consequences vary depending on which files are listed and accessible.</p>
                    <p>Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
            );
        }

        $actionName = $wsdPluginAlertsArray['check_index_wp_uploads']['name'];
        $alertType = $wsdPluginAlertsArray['check_index_wp_uploads']['type'];
        $path = WP_CONTENT_DIR.'/uploads';
        if(is_dir($path))
        {
            if(!is_file(WP_CONTENT_DIR.'/uploads/index.php')) {
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                    sprintf(__('The <strong>"%s"</strong> file was not found in the <strong>"%s"</strong> directory'), 'index.php', '/wp-content/uploads'),
                    __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory. The specific risks and consequences vary depending on which files are listed and accessible.</p>
                        <p>Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>'),
                    sprintf(__('<p>This plugin can automatically create an empty <strong>"%s"</strong> file in the following directories: wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads if the
                        option <strong>"%s"</strong> is checked on the plugin\'s settings page.</p>'),
                        'index.php', 'Try to create the index.php file in the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listing')
                );
            }
            else {
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                    sprintf(__('The <strong>"%s"</strong> file was found in the <strong>"%s"</strong> directory'), 'index.php', '/wp-content/uploads'),
                    __('<p>A directory listing provides an attacker with the complete index of all the resources located inside of the directory. The specific risks and consequences vary depending on which files are listed and accessible.</p>
                    <p>Therefore, it is important to protect your directories by having an empty index.php or index.htm file inside them.</p>')
                );
            }
        }

        $actionName = $wsdPluginAlertsArray['check_htaccess_wp_admin']['name'];
        $alertType = $wsdPluginAlertsArray['check_htaccess_wp_admin']['type'];
        $path = ABSPATH.'wp-admin/.htaccess';
        if(!is_file($path)){
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                sprintf(__('The <strong>"%s"</strong> file was not found in the <strong>"%s"</strong> directory'), '.htaccess', 'wp-admin'),
                __('<p>An .htaccess file is a configuration file which provides the ability to specify configuration settings for a specific directory in a website.
                    The .htaccess file can include one or more configuration settings which apply only for the directory in which the .htaccess file has been placed.
                    So while web servers have their own main configuration settings file, the .htaccess file can be used to override their main configuration settings.</p>'),
                sprintf(__('<p>Please refer to this <a href="%s" target="%s">article</a> for more information on how to create an .htaccess file.</p>'),
                    'http://www.acunetix.com/blog/web-security-zone/articles/what-is-an-htaccess-file/', '_blank')
            );
        }
        else {
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                sprintf(__('The <strong>"%s"</strong> file was found in the <strong>"%s"</strong> directory'), '.htaccess', 'wp-admin'),
                __('<p>An .htaccess file is a configuration file which provides the ability to specify configuration settings for a specific directory in a website.
                    The .htaccess file can include one or more configuration settings which apply only for the directory in which the .htaccess file has been placed.
                    So while web servers have their own main configuration settings file, the .htaccess file can be used to override their main configuration settings.</p>')
            );
        }

        $actionName = $wsdPluginAlertsArray['check_readme_wp_root']['name'];
        $alertType = $wsdPluginAlertsArray['check_readme_wp_root']['type'];
        $path = ABSPATH.'readme.html';
        if(is_file($path))
        {
            if(is_readable($path)){
                $fsize = @filesize($path);
                // couldn't retrieve the file's size
                if($fsize > 0){
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_MEDIUM,
                        __('The <strong>readme.html</strong> file was found in the root directory'),
                        __('<p>A default WordPress installation contains a readme.html file.
                                This file is a simple html file that does not contain executable content that can be exploited by hackers or malicious users.
                                Still, this file can provide hackers the version of your WordPress installation, therefore it is important to either delete this file or make it inaccessible for your visitors.</p>'),
                        sprintf(__('<p>This plugin can automatically delete its content if the option <strong>"%s"</strong> is checked on the plugin\'s settings page.
                                You can also delete this file manually by connecting to your website through an FTP connection.</p>'),'Empty the content of the readme.html file from the root directory.')
                    );
                }
                else {
                    self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                        __('The <strong>readme.html</strong> file is either empty or not accessible.'),
                        __('<p>A default WordPress installation contains a readme.html file.
                                This file is a simple html file that does not contain executable content that can be exploited by hackers or malicious users.
                                Still, this file can provide hackers the version of your WordPress installation, therefore it is important to either delete this file or make it inaccessible for your visitors.</p>')
                    );
                }
            }
            else {
                self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                    __('The <strong>readme.html</strong> file is not accessible.'),
                    __('<p>A default WordPress installation contains a readme.html file.
                            This file is a simple html file that does not contain executable content that can be exploited by hackers or malicious users.
                            Still, this file can provide hackers the version of your WordPress installation, therefore it is important to either delete this file or make it inaccessible for your visitors.</p>')
                );
            }
        }
        else{
            // file not found or file is not accessible
            self::alert($actionName, $alertType, WSS_PLUGIN_ALERT_INFO,
                __('The <strong>readme.html</strong> file was not found in the root directory'),
                __('<p>A default WordPress installation contains a readme.html file.
                        This file is a simple html file that does not contain executable content that can be exploited by hackers or malicious users.
                        Still, this file can provide hackers the version of your WordPress installation, therefore it is important to either delete this file or make it inaccessible for your visitors.</p>')
            );
        }
    }

}