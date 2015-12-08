<?php

class AIOWPSecurity_Feature_Item_Manager
{
    var $feature_items;
    var $total_points = 0;
    var $total_achievable_points = 0;
    
    var $feature_point_1 = "5";
    var $feature_point_2 = "10";
    var $feature_point_3 = "15";
    var $feature_point_4 = "20";
    var $sec_level_basic = "1";
    var $sec_level_inter = "2";
    var $sec_level_advanced = "3";
    var $feature_active = "active";
    var $feature_inactive = "inactive";
    var $feature_partial = "partial";
        
    function __construct(){
        
    }
    
    function initialize_features()
    {
        $this->feature_items = array();
        //Settings Menu Features
        //WP Generator Meta
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("wp-generator-meta-tag", "Remove WP Generatore Meta Tag", $this->feature_point_1, $this->sec_level_basic);
        
        //User Accounts Menu Features
        //Change Admin Username
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("user-accounts-change-admin-user", "Change Admin Username", $this->feature_point_3, $this->sec_level_basic);
        //Change Display Name
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("user-accounts-display-name", "Change Display Name", $this->feature_point_1, $this->sec_level_basic);
        
        //User Login Menu Features
        //Locking Lockdown
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("user-login-login-lockdown", "Login Lockdown", $this->feature_point_4, $this->sec_level_basic);
        //Login whitelisting
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("whitelist-manager-ip-login-whitelisting", "Login IP Whitelisting", $this->feature_point_3, $this->sec_level_inter);
        //Force Logout
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("user-login-force-logout", "Force Logout", $this->feature_point_1, $this->sec_level_basic);

        //Database Security Menu Features
        //DB Prefix
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("db-security-db-prefix", "DB Prefix", $this->feature_point_2, $this->sec_level_inter);
        //DB Backup
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("db-security-db-backup", "DB Backup", $this->feature_point_4, $this->sec_level_basic);       
        
        //File System Security Menu Features
        //File Permissions
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("filesystem-file-permissions", "File Permissions", $this->feature_point_4, $this->sec_level_basic);
        //PHP File Editing
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("filesystem-file-editing", "File Editing", $this->feature_point_2, $this->sec_level_basic);       
        //Prevent Access WP Install Files
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("block-wp-files-access", "WordPress Files Access", $this->feature_point_2, $this->sec_level_basic);       
        
        //Blacklist Manager Menu Features
        //IP and user agent blacklisting
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("blacklist-manager-ip-user-agent-blacklisting", "IP and User Agent Blacklisting", $this->feature_point_3, $this->sec_level_inter);
        
        //Firewall Menu Features
        //Basic firewall
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-basic-rules", "Enable Basic Firewall", $this->feature_point_3, $this->sec_level_basic);
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-pingback-rules", "Enable Pingback Vulnerability Protection", $this->feature_point_3, $this->sec_level_basic);
        
        //Additional and Advanced firewall
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-enable-brute-force-attack-prevention", "Enable Brute Force Attack Prevention", $this->feature_point_4, $this->sec_level_inter);
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-disable-index-views", "Disable Index Views", $this->feature_point_1, $this->sec_level_inter);
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-disable-trace-track", "Disable Trace and Track", $this->feature_point_2, $this->sec_level_advanced);
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-forbid-proxy-comments", "Forbid Proxy Comments", $this->feature_point_2, $this->sec_level_advanced);
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-deny-bad-queries", "Deny Bad Queries", $this->feature_point_3, $this->sec_level_advanced);
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-advanced-character-string-filter", "Advanced Character String Filter", $this->feature_point_3, $this->sec_level_advanced);
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("firewall-enable-5g-blacklist", "5G Blacklist", $this->feature_point_4, $this->sec_level_advanced);

        //SPAM Prevention
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("block-spambots", "Block Spambots", $this->feature_point_2, $this->sec_level_basic);
        
        //Filescan
        //File change detection
        $this->feature_items[] = new AIOWPSecurity_Feature_Item("scan-file-change-detection", "File Change Detection", $this->feature_point_4, $this->sec_level_inter);       

    }
    
    function get_feature_item_by_id($feature_id)
    {
        foreach($this->feature_items as $item)
        {
            if($item->feature_id == $feature_id)
            {
                return $item;
            }
        }
        return "";
    }
    
    function output_feature_details_badge($feature_id)
    {
        $cau_feature_item = $this->get_feature_item_by_id($feature_id);
        $cau_security_level = $cau_feature_item->security_level;
        $cau_security_points = $cau_feature_item->item_points;
        $cau_your_points = 0;
        if($cau_feature_item->feature_status == $this->feature_active){
            $cau_your_points = $cau_security_points;
        }
        $level_str = $cau_feature_item->get_security_level_string($cau_security_level);
        ?>
        <div class="aiowps_feature_details_badge">
                <div class="aiowps_feature_details_badge_difficulty" title="Feature Difficulty">
                    <span class="aiowps_feature_details_badge_difficulty_text"><?php _e($level_str, 'aiowpsecurity'); ?></span>
                </div>
                <div class="aiowps_feature_details_badge_points" title="Security Points">
                    <span class="aiowps_feature_details_badge_points_text"><?php echo $cau_your_points .'/'. $cau_security_points; ?></span>
                </div>
        </div>
        <?php
    }
    
    function check_feature_status_and_recalculate_points()
    {
        $this->check_and_set_feature_status();
        $this->calculate_total_points();
    }
    
    function check_and_set_feature_status()
    {
        foreach($this->feature_items as $item)
        {
            if($item->feature_id == "wp-generator-meta-tag")
            {
                $this->check_remove_wp_generator_meta_feature($item);
            }            

            if($item->feature_id == "user-accounts-change-admin-user")
            {
                $this->check_user_accounts_change_admin_user_feature($item);
            }
            if($item->feature_id == "user-accounts-display-name")
            {
                $this->check_user_accounts_display_name_feature($item);
            }

            if($item->feature_id == "db-security-db-prefix")
            {
                $this->check_db_security_db_prefix_feature($item);
            }
            if($item->feature_id == "db-security-db-backup")
            {
                $this->check_db_security_db_backup_feature($item);
            }

            if($item->feature_id == "user-login-login-lockdown")
            {
                $this->check_login_lockdown_feature($item);
            }
            if($item->feature_id == "whitelist-manager-ip-login-whitelisting")
            {
                $this->check_login_whitelist_feature($item);
            }
            if($item->feature_id == "user-login-force-logout")
            {
                $this->check_force_logout_feature($item);
            }
            
            if($item->feature_id == "filesystem-file-permissions")
            {
                $this->check_filesystem_permissions_feature($item);
            }            
            if($item->feature_id == "filesystem-file-editing")
            {
                $this->check_filesystem_file_editing_feature($item);
            }            
            if($item->feature_id == "block-wp-files-access")
            {
                $this->check_block_wp_files_access_feature($item);
            }            

            if($item->feature_id == "blacklist-manager-ip-user-agent-blacklisting")
            {
                $this->check_enable_ip_useragent_blacklist_feature($item);
            }
            
            if($item->feature_id == "firewall-basic-rules")
            {
                $this->check_enable_basic_firewall_feature($item);
            }
            
            if($item->feature_id == "firewall-pingback-rules")
            {
                $this->check_enable_pingback_firewall_feature($item);
            }

            
            if($item->feature_id == "firewall-enable-brute-force-attack-prevention")
            {
                $this->check_enable_bfap_firewall_feature($item);
            }
            if($item->feature_id == "firewall-disable-index-views")
            {
                $this->check_disable_index_views_firewall_feature($item);
            }
            if($item->feature_id == "firewall-disable-trace-track")
            {
                $this->check_disable_trace_track_firewall_feature($item);
            }
            if($item->feature_id == "firewall-forbid-proxy-comments")
            {
                $this->check_forbid_proxy_comments_firewall_feature($item);
            }
            if($item->feature_id == "firewall-deny-bad-queries")
            {
                $this->check_deny_bad_queries_firewall_feature($item);
            }
            if($item->feature_id == "firewall-advanced-character-string-filter")
            {
                $this->check_advanced_char_string_filter_firewall_feature($item);
            }
            if($item->feature_id == "firewall-enable-5g-blacklist")
            {
                $this->check_enable_5G_blacklist_firewall_feature($item);
            }
            
            if($item->feature_id == "block-spambots")
            {
                $this->check_enable_block_spambots_feature($item);
            }
            
            if($item->feature_id == "scan-file-change-detection")
            {
                $this->check_enable_fcd_scan_feature($item);
            }
            
        }
    }
    
    function calculate_total_points()
    {
        foreach($this->feature_items as $item)
        {
            if($item->feature_status == "active")
            {
                $this->total_points = $this->total_points + intval($item->item_points);
            }
        }
    }
    
    function get_total_site_points()
    {
        return $this->total_points;
    }
    
    function get_total_achievable_points()
    {
        foreach($this->feature_items as $item)
        {
            $this->total_achievable_points = $this->total_achievable_points + intval($item->item_points);
        }
        return $this->total_achievable_points;
    }
    
    function check_remove_wp_generator_meta_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_remove_wp_generator_meta_info') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_user_accounts_change_admin_user_feature($item)
    {
        if (AIOWPSecurity_Utility::check_user_exists('admin')) {
             $item->set_feature_status($this->feature_inactive);
        }
        else
        {
            $item->set_feature_status($this->feature_active);
        }
    }
   
    function check_user_accounts_display_name_feature($item)
    {
        if (AIOWPSecurity_Utility::check_identical_login_and_nick_names()) {
             $item->set_feature_status($this->feature_inactive);
        }
        else
        {
            $item->set_feature_status($this->feature_active);
        }
    }

    function check_login_lockdown_feature($item)
        {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_login_lockdown') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }
    
    function check_login_whitelist_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_whitelisting') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_force_logout_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_forced_logout') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_db_security_db_prefix_feature($item)
    {
        global $wpdb;
        if ($wpdb->prefix == 'wp_') {
             $item->set_feature_status($this->feature_inactive);
        }
        else
        {
            $item->set_feature_status($this->feature_active);
        }
    }
    
    function check_db_security_db_backup_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_automated_backups') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_filesystem_permissions_feature($item)
    {
        //TODO
        $is_secure = 1;
        $util = new AIOWPSecurity_Utility_File;
        $files_dirs_to_check = $util->files_and_dirs_to_check;
        foreach ($files_dirs_to_check as $file_or_dir)
        {
            $actual_perm = AIOWPSecurity_Utility_File::get_file_permission($file_or_dir['path']);
            $is_secure = $is_secure*AIOWPSecurity_Utility_File::is_file_permission_secure($file_or_dir['permissions'], $actual_perm);
        }
        
        //Only if all of the files' permissions are deemed secure give this a thumbs up
        if ($is_secure == 1)
        {
            $item->set_feature_status($this->feature_active);
        }
        else 
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_filesystem_file_editing_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_disable_file_editing') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_block_wp_files_access_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_prevent_default_wp_file_access') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }
    
    function check_enable_ip_useragent_blacklist_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_blacklisting') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }
    
    function check_enable_basic_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_basic_firewall') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_enable_pingback_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_pingback_firewall') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    
    function check_disable_trace_track_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_disable_trace_and_track') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_disable_index_views_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_disable_index_views') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_enable_bfap_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_brute_force_attack_prevention') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_forbid_proxy_comments_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_forbid_proxy_comments') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_deny_bad_queries_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_deny_bad_query_strings') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_advanced_char_string_filter_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_advanced_char_string_filter') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }

    function check_enable_5G_blacklist_firewall_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_5g_firewall') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }
    
    function check_enable_block_spambots_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_spambot_blocking') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }
    
    function check_enable_fcd_scan_feature($item)
    {
        global $aio_wp_security;
        if ($aio_wp_security->configs->get_value('aiowps_enable_automated_fcd_scan') == '1') {
            $item->set_feature_status($this->feature_active);
        }
        else
        {
            $item->set_feature_status($this->feature_inactive);
        }
    }
    
}