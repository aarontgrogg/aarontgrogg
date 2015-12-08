<?php 

if (!class_exists('AIO_WP_Security')){

class AIO_WP_Security{
    var $version = '2.6';
    var $db_version = '1.3';
    var $plugin_url;
    var $plugin_path;
    var $configs;
    var $admin_init;
    var $debug_logger;
    var $cron_handler;
    var $user_login_obj;
    var $backup_obj;
    var $filescan_obj;

    function __construct()
    {
        $this->load_configs();
        $this->define_constants();
        $this->includes();
        $this->loader_operations();

        add_action('init', array(&$this, 'wp_security_plugin_init'), 0);
        do_action('aiowpsecurity_loaded');
    }
    
    function plugin_url()
    { 
        if ($this->plugin_url) return $this->plugin_url;
        return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
    }

    function plugin_path()
    { 	
        if ($this->plugin_path) return $this->plugin_path;		
        return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
    
    function load_configs()
    {
        include_once('classes/wp-security-config.php');
        $this->configs = AIOWPSecurity_Config::get_instance();
    }
    
    function define_constants()
    {
        define('AIO_WP_SECURITY_VERSION', $this->version);
        define('AIO_WP_SECURITY_DB_VERSION', $this->db_version);
        define('AIOWPSEC_WP_URL', site_url());
        define('AIO_WP_SECURITY_URL', $this->plugin_url());
        define('AIO_WP_SECURITY_PATH', $this->plugin_path());
        define('AIO_WP_SECURITY_BACKUPS_DIR_NAME', 'aiowps_backups');
        define('AIO_WP_SECURITY_BACKUPS_PATH', AIO_WP_SECURITY_PATH.'/backups');
        define('AIO_WP_SECURITY_LIB_PATH', AIO_WP_SECURITY_PATH.'/lib');
        define('AIOWPSEC_MANAGEMENT_PERMISSION', 'add_users');
        define('AIOWPSEC_MENU_SLUG_PREFIX', 'aiowpsec');
        define('AIOWPSEC_MAIN_MENU_SLUG', 'aiowpsec');
        define('AIOWPSEC_SETTINGS_MENU_SLUG', 'aiowpsec_settings');
        define('AIOWPSEC_USER_ACCOUNTS_MENU_SLUG', 'aiowpsec_useracc');
        define('AIOWPSEC_USER_LOGIN_MENU_SLUG', 'aiowpsec_userlogin');
        define('AIOWPSEC_DB_SEC_MENU_SLUG', 'aiowpsec_database');
        define('AIOWPSEC_FILESYSTEM_MENU_SLUG', 'aiowpsec_filesystem');
        define('AIOWPSEC_WHOIS_MENU_SLUG', 'aiowpsec_whois');
        define('AIOWPSEC_BLACKLIST_MENU_SLUG', 'aiowpsec_blacklist');
        define('AIOWPSEC_FIREWALL_MENU_SLUG', 'aiowpsec_firewall');
        define('AIOWPSEC_MAINTENANCE_MENU_SLUG', 'aiowpsec_maintenance');
        define('AIOWPSEC_SPAM_MENU_SLUG', 'aiowpsec_spam');
        define('AIOWPSEC_FILESCAN_MENU_SLUG', 'aiowpsec_filescan');
        
        global $wpdb;
        define('AIOWPSEC_TBL_LOGIN_LOCKDOWN', $wpdb->prefix . 'aiowps_login_lockdown');
        define('AIOWPSEC_TBL_FAILED_LOGINS', $wpdb->prefix . 'aiowps_failed_logins');
        define('AIOWPSEC_TBL_USER_LOGIN_ACTIVITY', $wpdb->prefix . 'aiowps_login_activity');
        define('AIOWPSEC_TBL_GLOBAL_META_DATA', $wpdb->prefix . 'aiowps_global_meta');

    }

    function includes()
    {
        //Load common files for everywhere
        include_once('classes/wp-security-debug-logger.php');
        include_once('classes/wp-security-utility.php');
        include_once('classes/wp-security-utility-htaccess.php');
        include_once('classes/wp-security-utility-ip-address.php');
        include_once('classes/wp-security-utility-file.php');
        include_once('classes/wp-security-general-init-tasks.php');
        
        include_once('classes/wp-security-user-login.php');
        include_once('classes/wp-security-backup.php');
        include_once('classes/wp-security-file-scan.php');
        include_once('classes/wp-security-cronjob-handler.php');
        include_once('classes/grade-system/wp-security-feature-item.php');
        include_once('classes/grade-system/wp-security-feature-item-manager.php');
        
        if (is_admin()){ //Load admin side only files
            include_once('classes/wp-security-configure-settings.php');
            include_once('admin/wp-security-admin-init.php');
            include_once('admin/general/wp-security-list-table.php');
            
        }
        else{ //Load front end side only files
        }
    }

    function loader_operations()
    {
        add_action('plugins_loaded',array(&$this, 'plugins_loaded_handler'));//plugins loaded hook
        $this->debug_logger = new AIOWPSecurity_Logger();
        if(is_admin()){
            $this->admin_init = new AIOWPSecurity_Admin_Init();
        }
    }
    
    static function activate_handler()
    {   
        //Only runs when the plugin activates
        include_once ('classes/wp-security-installer.php');
        AIOWPSecurity_Installer::run_installer();
        wp_schedule_event(time(), 'hourly', 'aiowps_hourly_cron_event'); //schedule an hourly cron event
        //wp_schedule_event(time(), 'daily', 'aiowps_daily_cron_event'); //schedule an daily cron event
    }
    
    static function deactivate_handler()
    {
        //Only runs with the pluign is deactivated
        include_once ('classes/wp-security-deactivation-tasks.php');
        //AIOWPSecurity_Deactivation::run_deactivation_tasks();
        wp_clear_scheduled_hook('aiowps_hourly_cron_event');
        //wp_clear_scheduled_hook('aiowps_daily_cron_event');
        if (AIOWPSecurity_Utility::is_multisite_install())
        {
            delete_site_transient('users_online');
        }
        else
        {
            delete_transient('users_online');
        }
    }
    
    function db_upgrade_handler()
    {
        if(is_admin()){//Check if DB needs to be upgraded
            if (get_option('aiowpsec_db_version') != AIO_WP_SECURITY_DB_VERSION) {
                include_once ('classes/wp-security-installer.php');
                AIOWPSecurity_Installer::run_installer();
            }
        }
    }
    
    function plugins_loaded_handler()
    {
        //Runs when plugins_loaded action gets fired
        if(is_admin()){
            //Do plugins_loaded operations for admin side
            $this->db_upgrade_handler();
        }
        $this->do_additional_plugins_loaded_tasks();
    }
    
    function wp_security_plugin_init()
    {
        //Set up localisation
	load_plugin_textdomain('aiowpsecurity', false, dirname(plugin_basename(__FILE__ )) . '/languages/');

        //Actions, filters, shortcodes goes here       
        $this->user_login_obj = new AIOWPSecurity_User_Login();//Do the user login operation tasks
        $this->backup_obj = new AIOWPSecurity_Backup();//Object to handle backup tasks
        $this->filescan_obj = new AIOWPSecurity_Filescan();//Object to handle backup tasks 
        $this->cron_handler = new AIOWPSecurity_Cronjob_Handler();
        
        add_action('wp_head',array(&$this, 'aiowps_header_content'));
                
        add_action('wp_login', array('AIOWPSecurity_User_Login', 'wp_login_action_handler'), 10, 2);
        do_action('aiowps_force_logout_check');
        new AIOWPSecurity_General_Init_Tasks();
    }

    function aiowps_header_content()
    {
        //NOP
    }
    
    function do_additional_plugins_loaded_tasks()
    {
        if(isset($_GET['aiowpsec_do_log_out']))
        {
            wp_logout();
            if(isset($_GET['after_logout']))//Redirect to the after logout url directly
            {
                $after_logout_url = esc_url($_GET['after_logout']);
                AIOWPSecurity_Utility::redirect_to_url($after_logout_url);
            }
            if(isset($_GET['al_additional_data']))//Inspect the payload and do redirect to login page with a msg and redirect url
            {
                $payload = strip_tags($_GET['al_additional_data']);
                $decoded_payload = base64_decode($payload);
                parse_str($decoded_payload); 
                if(!empty($redirect_to)){
                    $login_url = AIOWPSecurity_Utility::add_query_data_to_url(wp_login_url(),'redirect_to',$redirect_to);
                }
                if(!empty($msg)){
                    $login_url .= '&'.$msg;
                }
                if(!empty($login_url)){
                    AIOWPSecurity_Utility::redirect_to_url($login_url);
                }
            }
        }
    }    
    
}//End of class

}//End of class not exists check

$GLOBALS['aio_wp_security'] = new AIO_WP_Security();
