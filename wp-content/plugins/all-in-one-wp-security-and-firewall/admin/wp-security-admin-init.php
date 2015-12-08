<?php
/* 
 * Inits the admin dashboard side of things.
 * Main admin file which loads all settings panels and sets up admin menus. 
 */
class AIOWPSecurity_Admin_Init
{
    var $main_menu_page;
    var $dashboard_menu;
    var $settings_menu;
    var $user_accounts_menu;
    var $user_login_menu;
    var $db_security_menu;
    var $filesystem_menu;
    var $whois_menu;
    var $blacklist_menu;
    var $firewall_menu;
    var $maintenance_menu;
    var $spam_menu;
    var $filescan_menu;

    function __construct()
    {
        //This class is only initialized if is_admin() is true
        $this->admin_includes();
        add_action('admin_menu', array(&$this, 'create_admin_menus'));

        //make sure we are on our plugin's menu pages
        if (isset($_GET['page']) && strpos($_GET['page'], AIOWPSEC_MENU_SLUG_PREFIX ) !== false ) {
            add_action('admin_print_scripts', array(&$this, 'admin_menu_page_scripts'));
            add_action('admin_print_styles', array(&$this, 'admin_menu_page_styles'));            
            add_action('init', array( &$this, 'init_hook_handler_for_admin_side')); 
        }
    }
    
    function admin_includes()
    {
        include_once('wp-security-admin-menu.php');
    }

    function admin_menu_page_scripts() 
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('postbox');
        wp_enqueue_script('dashboard');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('media-upload');
        wp_register_script('aiowpsec-admin-js', AIO_WP_SECURITY_URL. '/js/wp-security-admin-script.js', array('jquery'));
        wp_enqueue_script('aiowpsec-admin-js');
        wp_register_script('aiowpsec-pw-tool-js', AIO_WP_SECURITY_URL. '/js/password-strength-tool.js', array('jquery')); // We will enqueue this in the user acct menu class
    }
    
    function admin_menu_page_styles() 
    {
        wp_enqueue_style('dashboard');
        wp_enqueue_style('thickbox');
        wp_enqueue_style('global');
        wp_enqueue_style('wp-admin');
        wp_enqueue_style('aiowpsec-admin-css', AIO_WP_SECURITY_URL. '/css/wp-security-admin-styles.css');
    }
    
    function init_hook_handler_for_admin_side()
    {
        $this->aiowps_media_uploader_modification();
        $this->initialize_feature_manager();
        $this->do_other_admin_side_init_tasks();
    }

    function aiowps_media_uploader_modification()
    {
        //For changing button text inside media uploader (thickbox)
        global $pagenow;
        if ('media-upload.php' == $pagenow || 'async-upload.php' == $pagenow)
        {
            // Here we will customize the 'Insert into Post' Button text inside Thickbox
            add_filter( 'gettext', array($this, 'aiowps_media_uploader_replace_thickbox_text'), 1, 2);
        }
    }

    function aiowps_media_uploader_replace_thickbox_text($translated_text, $text)
    {
        if ('Insert into Post' == $text)
        {
            $referer = strpos(wp_get_referer(), 'aiowpsec');
            if ($referer != '')
            {
                return ('Select File');
            }
        }
        return $translated_text;
    }

    function initialize_feature_manager()
    {
        $aiowps_feature_mgr  = new AIOWPSecurity_Feature_Item_Manager();
        $aiowps_feature_mgr->initialize_features();
        $aiowps_feature_mgr->check_and_set_feature_status();
        $aiowps_feature_mgr->calculate_total_points(); 
        $GLOBALS['aiowps_feature_mgr'] = $aiowps_feature_mgr;
    }
    
    function do_other_admin_side_init_tasks()
    {
        if (isset($_GET['page']) && $_GET['page'] == AIOWPSEC_FIREWALL_MENU_SLUG && isset($_GET['tab']) && $_GET['tab'] == 'tab4')
        {
            global $aio_wp_security;
            if(isset($_POST['aiowps_do_cookie_test_for_bfla'])){
                AIOWPSecurity_Utility::set_cookie_value("aiowps_cookie_test", "1");
                $cur_url = "admin.php?page=".AIOWPSEC_FIREWALL_MENU_SLUG."&tab=tab4";
                $redirect_url = AIOWPSecurity_Utility::add_query_data_to_url($cur_url, "aiowps_cookie_test", "1");
                AIOWPSecurity_Utility::redirect_to_url($redirect_url);
            }
            
            if(isset($_POST['aiowps_enable_brute_force_attack_prevention']))//Enabling the BFLA feature so drop the cookie again
            {
                $brute_force_feature_secret_word = sanitize_text_field($_POST['aiowps_brute_force_secret_word']);
                if(empty($brute_force_feature_secret_word)){
                    $brute_force_feature_secret_word = "aiowps_secret";
                }
                AIOWPSecurity_Utility::set_cookie_value($brute_force_feature_secret_word, "1");
            }

            if(isset($_REQUEST['aiowps_cookie_test']))
            {
                $cookie_val = AIOWPSecurity_Utility::get_cookie_value("aiowps_cookie_test");
                if(empty($cookie_val))
                {
                    $aio_wp_security->configs->set_value('aiowps_cookie_test_success','');
                }
                else
                {
                    $aio_wp_security->configs->set_value('aiowps_cookie_test_success','1');
                }
                $aio_wp_security->configs->save_config();//save the value
            }
        }

        if(isset($_POST['aiowps_save_wp_config']))//the wp-config backup operation
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-save-wp-config-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on wp_config file save!",4);
                die("Nonce check failed on wp_config file save!");
            }
            $wp_config_path = ABSPATH . 'wp-config.php';
            $result = AIOWPSecurity_Utility_File::backup_a_file($wp_config_path); //Backup the wp_config.php file
            AIOWPSecurity_Utility_File::download_a_file_option1($wp_config_path, "wp-config-backup.txt");
        }
    }
    
    function create_admin_menus()
    {
        $menu_icon_url = AIO_WP_SECURITY_URL.'/images/plugin-icon.png';
        $this->main_menu_page = add_menu_page(__('WP Security', 'aiowpsecurity'), __('WP Security', 'aiowpsecurity'), AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_MAIN_MENU_SLUG , array(&$this, 'handle_dashboard_menu_rendering'), $menu_icon_url);
        add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('Dashboard', 'aiowpsecurity'),  __('Dashboard', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_MAIN_MENU_SLUG, array(&$this, 'handle_dashboard_menu_rendering'));
        add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('Settings', 'aiowpsecurity'),  __('Settings', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_SETTINGS_MENU_SLUG, array(&$this, 'handle_settings_menu_rendering'));
        add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('User Accounts', 'aiowpsecurity'),  __('User Accounts', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_USER_ACCOUNTS_MENU_SLUG, array(&$this, 'handle_user_accounts_menu_rendering'));
        add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('User Login', 'aiowpsecurity'),  __('User Login', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_USER_LOGIN_MENU_SLUG, array(&$this, 'handle_user_login_menu_rendering'));
        add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('Database Security', 'aiowpsecurity'),  __('Database Security', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_DB_SEC_MENU_SLUG, array(&$this, 'handle_database_menu_rendering'));
        if (AIOWPSecurity_Utility::is_multisite_install() && get_current_blog_id() != 1){
            //Suppress the firewall menu if site is a multi site AND not the main site
        }else{
            add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('Filesystem Security', 'aiowpsecurity'),  __('Filesystem Security', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_FILESYSTEM_MENU_SLUG, array(&$this, 'handle_filesystem_menu_rendering'));
        }
        add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('WHOIS Lookup', 'aiowpsecurity'),  __('WHOIS Lookup', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_WHOIS_MENU_SLUG, array(&$this, 'handle_whois_menu_rendering'));
        if (AIOWPSecurity_Utility::is_multisite_install() && get_current_blog_id() != 1){
            //Suppress the firewall menu if site is a multi site AND not the main site
        }else{
            add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('Blacklist Manager', 'aiowpsecurity'),  __('Blacklist Manager', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_BLACKLIST_MENU_SLUG, array(&$this, 'handle_blacklist_menu_rendering'));
        }
        if (AIOWPSecurity_Utility::is_multisite_install() && get_current_blog_id() != 1){
            //Suppress the firewall menu if site is a multi site AND not the main site
        }else{
            add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('Firewall', 'aiowpsecurity'),  __('Firewall', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_FIREWALL_MENU_SLUG, array(&$this, 'handle_firewall_menu_rendering'));
        }
        add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('SPAM Prevention', 'aiowpsecurity'),  __('SPAM Prevention', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_SPAM_MENU_SLUG, array(&$this, 'handle_spam_menu_rendering'));
        if (AIOWPSecurity_Utility::is_multisite_install() && get_current_blog_id() != 1){
            //Suppress the filescan menu if site is a multi site AND not the main site
        }else{
            add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('Scanner', 'aiowpsecurity'),  __('Scanner', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_FILESCAN_MENU_SLUG, array(&$this, 'handle_filescan_menu_rendering'));
        }
        add_submenu_page(AIOWPSEC_MAIN_MENU_SLUG, __('Maintenance', 'aiowpsecurity'),  __('Maintenance', 'aiowpsecurity') , AIOWPSEC_MANAGEMENT_PERMISSION, AIOWPSEC_MAINTENANCE_MENU_SLUG, array(&$this, 'handle_maintenance_menu_rendering'));
        do_action('aiowpsecurity_admin_menu_created');
    }
        
    function handle_dashboard_menu_rendering()
    {
        include_once('wp-security-dashboard-menu.php');
        $this->dashboard_menu = new AIOWPSecurity_Dashboard_Menu();
    }

    function handle_settings_menu_rendering()
    {
        include_once('wp-security-settings-menu.php');
        $this->settings_menu = new AIOWPSecurity_Settings_Menu();
        
    }
    
    function handle_user_accounts_menu_rendering()
    {
        include_once('wp-security-user-accounts-menu.php');
        $this->user_accounts_menu = new AIOWPSecurity_User_Accounts_Menu();
    }
    
    function handle_user_login_menu_rendering()
    {
        include_once('wp-security-user-login-menu.php');
        $this->user_login_menu = new AIOWPSecurity_User_Login_Menu();
    }
    
    function handle_database_menu_rendering()
    {
        include_once('wp-security-database-menu.php');
        $this->db_security_menu = new AIOWPSecurity_Database_Menu();
    }

    function handle_filesystem_menu_rendering()
    {
        include_once('wp-security-filesystem-menu.php');
        $this->filesystem_menu = new AIOWPSecurity_Filesystem_Menu();
    }

    function handle_whois_menu_rendering()
    {
        include_once('wp-security-whois-menu.php');
        $this->whois_menu = new AIOWPSecurity_WhoIs_Menu();
    }

    function handle_blacklist_menu_rendering()
    {
        include_once('wp-security-blacklist-menu.php');
        $this->blacklist_menu = new AIOWPSecurity_Blacklist_Menu();
    }

    function handle_firewall_menu_rendering()
    {
        include_once('wp-security-firewall-menu.php');
        $this->firewall_menu = new AIOWPSecurity_Firewall_Menu();
    }
    
    function handle_maintenance_menu_rendering()
    {
        include_once('wp-security-maintenance-menu.php');
        $this->maintenance_menu = new AIOWPSecurity_Maintenance_Menu();
    }
    
    function handle_spam_menu_rendering()
    {
        include_once('wp-security-spam-menu.php');
        $this->spam_menu = new AIOWPSecurity_Spam_Menu();
    }
    
    function handle_filescan_menu_rendering()
    {
        include_once('wp-security-filescan-menu.php');
        $this->filescan_menu = new AIOWPSecurity_Filescan_Menu();
    }
    
}//End of class

