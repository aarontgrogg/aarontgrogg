<?php

class AIOWPSecurity_User_Login_Menu extends AIOWPSecurity_Admin_Menu
{
    var $menu_page_slug = AIOWPSEC_USER_LOGIN_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs = array(
        'tab1' => 'Login Lockdown', 
        'tab2' => 'Login Whitelist',
        'tab3' => 'Failed Login Records',
        'tab4' => 'Force Logout',
        'tab5' => 'Account Activity Logs',
        'tab6' => 'Logged In Users',

        );
    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1', 
        'tab2' => 'render_tab2',
        'tab3' => 'render_tab3',
        'tab4' => 'render_tab4',
        'tab5' => 'render_tab5',
        'tab6' => 'render_tab6',
        );
    
    function __construct() 
    {
        $this->render_user_login_menu_page();
    }
    
    function get_current_tab() 
    {
        $tab_keys = array_keys($this->menu_tabs);
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $tab_keys[0];
        return $tab;
    }

    /*
     * Renders our tabs of this menu as nav items
     */
    function render_menu_tabs() 
    {
        $current_tab = $this->get_current_tab();

        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->menu_tabs as $tab_key => $tab_caption ) 
        {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
        }
        echo '</h2>';
    }
    
    /*
     * The menu rendering goes here
     */
    function render_user_login_menu_page() 
    {
        $tab = $this->get_current_tab();
        ?>
        <div class="wrap">
        <div id="poststuff"><div id="post-body">
        <?php 
        $this->render_menu_tabs();
        //$tab_keys = array_keys($this->menu_tabs);
        call_user_func(array(&$this, $this->menu_tabs_handler[$tab]));
        ?>
        </div></div>
        </div><!-- end of wrap -->
        <?php
    }
    
    function render_tab1() 
    {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        include_once 'wp-security-list-locked-ip.php'; //For rendering the AIOWPSecurity_List_Table in tab1
        $locked_ip_list = new AIOWPSecurity_List_Locked_IP(); //For rendering the AIOWPSecurity_List_Table in tab1

        if(isset($_POST['aiowps_login_lockdown']))//Do form submission tasks
        {
            $error = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-login-lockdown-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on login lockdown options save!",4);
                die("Nonce check failed on login lockdown options save!");
            }

            $max_login_attempt_val = sanitize_text_field($_POST['aiowps_max_login_attempts']);
            if(!is_numeric($max_login_attempt_val))
            {
                $error .= '<br />'.__('You entered a non numeric value for the max login attempts field. It has been set to the default value.','aiowpsecurity');
                $max_login_attempt_val = '3';//Set it to the default value for this field
            }
            
            $login_retry_time_period = sanitize_text_field($_POST['aiowps_retry_time_period']);
            if(!is_numeric($login_retry_time_period))
            {
                $error .= '<br />'.__('You entered a non numeric value for the login retry time period field. It has been set to the default value.','aiowpsecurity');
                $login_retry_time_period = '5';//Set it to the default value for this field
            }

            $lockout_time_length = sanitize_text_field($_POST['aiowps_lockout_time_length']);
            if(!is_numeric($lockout_time_length))
            {
                $error .= '<br />'.__('You entered a non numeric value for the lockout time length field. It has been set to the default value.','aiowpsecurity');
                $lockout_time_length = '60';//Set it to the default value for this field
            }
            
            $email_address = sanitize_email($_POST['aiowps_email_address']);
            if(!is_email($email_address))
            {
                $error .= '<br />'.__('You have entered an incorrect email address format. It has been set to your WordPress admin email as default.','aiowpsecurity');
                $email_address = get_bloginfo('admin_email'); //Set the default value to the blog admin email
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','aiowpsecurity').$error);
            }

            //Save all the form values to the options
            $aio_wp_security->configs->set_value('aiowps_enable_login_lockdown',isset($_POST["aiowps_enable_login_lockdown"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_max_login_attempts',absint($max_login_attempt_val));
            $aio_wp_security->configs->set_value('aiowps_retry_time_period',absint($login_retry_time_period));
            $aio_wp_security->configs->set_value('aiowps_lockout_time_length',absint($lockout_time_length));
            $aio_wp_security->configs->set_value('aiowps_set_generic_login_msg',isset($_POST["aiowps_set_generic_login_msg"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_invalid_username_lockdown',isset($_POST["aiowps_enable_invalid_username_lockdown"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_enable_email_notify',isset($_POST["aiowps_enable_email_notify"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_email_address',$email_address);
            $aio_wp_security->configs->save_config();
            
            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
            
            $this->show_msg_settings_updated();
        }
        
                
        if(isset($_REQUEST['action'])) //Do list table form row action tasks
        {
            if($_REQUEST['action'] == 'delete_blocked_ip'){ //Delete link was clicked for a row in list table
                $locked_ip_list->delete_lockdown_records(strip_tags($_REQUEST['lockdown_id']));
            }
            
            if($_REQUEST['action'] == 'unlock_ip'){ //Unlock link was clicked for a row in list table
                $locked_ip_list->unlock_ip_range(strip_tags($_REQUEST['lockdown_id']));
            }
        }
        ?>
        <h2><?php _e('Login Lockdown Configuration', 'aiowpsecurity')?></h2>
        <div class="aio_blue_box">
            <?php
            $brute_force_login_feature_link = '<a href="admin.php?page='.AIOWPSEC_FIREWALL_MENU_SLUG.'&tab=tab4">Cookie-Based Brute Force Login Prevention</a>';
            echo '<p>'.__('One of the ways hackers try to compromise sites is via a ', 'aiowpsecurity').'<strong>'.__('Brute Force Login Attack', 'aiowpsecurity').'</strong>.
            <br />'.__('This is where attackers use repeated login attempts until they guess the password.', 'aiowpsecurity').'
            <br />'.__('Apart from choosing strong passwords, monitoring and blocking IP addresses which are involved in repeated login failures in a short period of time is a very effective way to stop these types of attacks.', 'aiowpsecurity').
            '<p>'.sprintf( __('You may also want to checkout our %s feature for another secure way to protect against these types of attacks.', 'aiowpsecurity'), $brute_force_login_feature_link).'</p>';
            ?>
        </div>

        <div class="postbox">
        <h3><label for="title"><?php _e('Login Lockdown Options', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("user-login-login-lockdown");
        ?>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-login-lockdown-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Enable Login Lockdown Feature', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_enable_login_lockdown" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_login_lockdown')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want to enable the login lockdown feature and apply the settings below', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row"><?php _e('Max Login Attempts', 'aiowpsecurity')?>:</th>
                <td><input size="5" name="aiowps_max_login_attempts" value="<?php echo $aio_wp_security->configs->get_value('aiowps_max_login_attempts'); ?>" />
                <span class="description"><?php _e('Set the value for the maximum login retries before IP address is locked out', 'aiowpsecurity'); ?></span>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Login Retry Time Period (min)', 'aiowpsecurity')?>:</th>
                <td><input size="5" name="aiowps_retry_time_period" value="<?php echo $aio_wp_security->configs->get_value('aiowps_retry_time_period'); ?>" />
                <span class="description"><?php _e('If the maximum number of failed login attempts for a particular IP address occur within this time period the plugin will lock out that address', 'aiowpsecurity'); ?></span>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Time Length of Lockout (min)', 'aiowpsecurity')?>:</th>
                <td><input size="5" name="aiowps_lockout_time_length" value="<?php echo $aio_wp_security->configs->get_value('aiowps_lockout_time_length'); ?>" />
                <span class="description"><?php _e('Set the length of time for which a particular IP address will be prevented from logging in', 'aiowpsecurity'); ?></span>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Display Generic Error Message', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_set_generic_login_msg" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_set_generic_login_msg')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want to show a generic error message when a login attempt fails', 'aiowpsecurity'); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Instantly Lockout Invalid Usernames', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_enable_invalid_username_lockdown" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_invalid_username_lockdown')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want to instantly lockout login attempts with usernames which do not exist on your system', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
            
            <tr valign="top">
                <th scope="row"><?php _e('Notify By Email', 'aiowpsecurity')?>:</th>
                <td>
                    <input name="aiowps_enable_email_notify" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_email_notify')=='1') echo ' checked="checked"'; ?> value="1"/>
                    <span class="description"><?php _e('Check this if you want to receive an email when someone has been locked out due to maximum failed login attempts', 'aiowpsecurity'); ?></span>
                    <br /><input size="30" name="aiowps_email_address" value="<?php echo $aio_wp_security->configs->get_value('aiowps_email_address'); ?>" />
                    <span class="description"><?php _e('Enter an email address', 'aiowpsecurity'); ?></span>
                </td> 
            </tr>
        </table>
        <input type="submit" name="aiowps_login_lockdown" value="<?php _e('Save Settings', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Currently Locked Out IP Address Ranges', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
            <?php 
            //Fetch, prepare, sort, and filter our data...
            $locked_ip_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
            <form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
            <input type="hidden" name="tab" value="<?php echo $_REQUEST['tab']; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $locked_ip_list->display(); ?>
            </form>
        </div></div>
        <?php
    }
    
    function render_tab2() 
    {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        $result = 1;
        $your_ip_address = AIOWPSecurity_Utility_IP::get_user_ip_address();
        if (isset($_POST['aiowps_save_whitelist_settings']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-whitelist-settings-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for save whitelist settings!",4);
                die(__('Nonce check failed for save whitelist settings!','aiowpsecurity'));
            }
            
            if (isset($_POST["aiowps_enable_whitelisting"]) && empty($_POST['aiowps_allowed_ip_addresses']))
            {
                $this->show_msg_error('You must submit at least one IP address!','aiowpsecurity');
            }
            else
            {
                if (!empty($_POST['aiowps_allowed_ip_addresses']))
                {
                    $ip_addresses = $_POST['aiowps_allowed_ip_addresses'];
                    $ip_list_array = AIOWPSecurity_Utility_IP::create_ip_list_array_from_string_with_newline($ip_addresses);
                    $payload = AIOWPSecurity_Utility_IP::validate_ip_list($ip_list_array, 'whitelist');
                    if($payload[0] == 1){
                        //success case
                        $result = 1;
                        $list = $payload[1];
                        $banned_ip_data = implode(PHP_EOL, $list);
                        $aio_wp_security->configs->set_value('aiowps_allowed_ip_addresses',$banned_ip_data);
                        $_POST['aiowps_allowed_ip_addresses'] = ''; //Clear the post variable for the banned address list
                    }
                    else{
                        $result = -1;
                        $error_msg = $payload[1][0];
                        $this->show_msg_error($error_msg);
                    }
                    
                }
                else
                {
                    $aio_wp_security->configs->set_value('aiowps_allowed_ip_addresses',''); //Clear the IP address config value
                }

                if ($result == 1)
                {
                    $aio_wp_security->configs->set_value('aiowps_enable_whitelisting',isset($_POST["aiowps_enable_whitelisting"])?'1':'');
                    $aio_wp_security->configs->save_config(); //Save the configuration
                    
                    //Recalculate points after the feature status/options have been altered
                    $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
                    
                    $this->show_msg_settings_updated();

                    $write_result = AIOWPSecurity_Utility_Htaccess::write_to_htaccess(); //now let's write to the .htaccess file
                    if ($write_result == -1)
                    {
                        $this->show_msg_error(__('The plugin was unable to write to the .htaccess file. Please edit file manually.','aiowpsecurity'));
                        $aio_wp_security->debug_logger->log_debug("AIOWPSecurity_whitelist_Menu - The plugin was unable to write to the .htaccess file.");
                    }
                }
            }
        }
        ?>
        <h2><?php _e('Login Whitelist', 'aiowpsecurity')?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('The All In One WP Security Whitelist feature gives you the option of only allowing certain IP addresses or ranges to have access to your WordPress login page.', 'aiowpsecurity').'
            <br />'.__('This feature will deny login access for all IP addresses which are not in your whitelist as configured in the settings below.', 'aiowpsecurity').'
            <br />'.__('The plugin achieves this by writing the appropriate directives to your .htaccess file.', 'aiowpsecurity').'
            <br />'.__('By allowing/blocking IP addresses via the .htaccess file your are using the most secure first line of defence because login access will only be granted to whitelisted IP addresses and other addresses will be blocked as soon as they try to access your login page.', 'aiowpsecurity').'    
            </p>';
            ?>
        </div>

        <div class="postbox">
        <h3><label for="title"><?php _e('Login IP Whitelist Settings', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("whitelist-manager-ip-login-whitelisting");
        ?>    
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-whitelist-settings-nonce'); ?>            
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Enable IP Whitelisting', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_enable_whitelisting" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_whitelisting')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want to enable the whitelisting of selected IP addresses specified in the settings below', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row"><?php _e('Your Current IP Address', 'aiowpsecurity')?>:</th>                
                <td>
                <input size="20" name="aiowps_user_ip" type="text" value="<?php echo $your_ip_address; ?>" disabled/>
                <span class="description"><?php _e('You can copy and paste this address in the text box below if you want to include it in your login whitelist.', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row"><?php _e('Enter Whitelisted IP Addresses:', 'aiowpsecurity')?></th>
                <td>
                    <textarea name="aiowps_allowed_ip_addresses" rows="5" cols="50"><?php echo ($result == -1)?$_POST['aiowps_allowed_ip_addresses']:$aio_wp_security->configs->get_value('aiowps_allowed_ip_addresses'); ?></textarea>
                    <br />
                    <span class="description"><?php _e('Enter one or more IP addresses or IP ranges you wish to include in your whitelist. Only the addresses specified here will have access to the WordPress login page.','aiowpsecurity');?></span>
                    <span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More Info', 'aiowpsecurity'); ?></span></span>
                    <div class="aiowps_more_info_body">
                            <?php 
                            echo '<p class="description">'.__('Each IP address must be on a new line.', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('To specify an IP range use a wildcard "*" character. Acceptable ways to use wildcards is shown in the examples below:', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('Example 1: 195.47.89.*', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('Example 2: 195.47.*.*', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('Example 3: 195.*.*.*', 'aiowpsecurity').'</p>';
                            ?>
                    </div>

                </td>
            </tr>
        </table>
        <input type="submit" name="aiowps_save_whitelist_settings" value="<?php _e('Save Settings', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        <?php
    }

    function render_tab3()
    {
        global $aio_wp_security, $wpdb;
        if (isset($_POST['aiowps_delete_failed_login_records']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-delete-failed-login-records-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for delete all failed login records operation!",4);
                die(__('Nonce check failed for delete all failed login records operation!','aiowpsecurity'));
            }
            $failed_logins_table = AIOWPSEC_TBL_FAILED_LOGINS;
            //Delete all records from the failed logins table
            $result = $wpdb->query("truncate $failed_logins_table");
                    
            if ($result === FALSE)
            {
                $aio_wp_security->debug_logger->log_debug("User Login Feature - Delete all failed login records operation failed!",4);
                $this->show_msg_error(__('User Login Feature - Delete all failed login records operation failed!','aiowpsecurity'));
            } 
            else
            {
                $this->show_msg_updated(__('All records from the Failed Logins table were deleted successfully!','aiowpsecurity'));
            }
        }

        include_once 'wp-security-list-login-fails.php'; //For rendering the AIOWPSecurity_List_Table in tab2
        $failed_login_list = new AIOWPSecurity_List_Login_Failed_Attempts(); //For rendering the AIOWPSecurity_List_Table in tab2
        if(isset($_REQUEST['action'])) //Do row action tasks for list table form for failed logins
        {
            if($_REQUEST['action'] == 'delete_failed_login_rec'){ //Delete link was clicked for a row in list table
                $failed_login_list->delete_login_failed_records(strip_tags($_REQUEST['failed_login_id']));
            }
        }
        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('This tab displays the failed login attempts for your site.', 'aiowpsecurity').'
            <br />'.__('The information below can be handy if you need to do security investigations because it will show you the IP range, username and ID (if applicable) and the time/date of the failed login attempt.', 'aiowpsecurity').'
            </p>';
            ?>
        </div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Failed Login Records', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
            <?php 
            //Fetch, prepare, sort, and filter our data...
            $failed_login_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
            <form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
            <input type="hidden" name="tab" value="<?php echo $_REQUEST['tab']; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $failed_login_list->display(); ?>
            </form>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Delete All Failed Login Records', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-delete-failed-login-records-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
            <span class="description"><?php _e('Click this button if you wish to delete all failed login records in one go.', 'aiowpsecurity'); ?></span>                
            </tr>            
        </table>
        <input type="submit" name="aiowps_delete_failed_login_records" value="<?php _e('Delete All Failed Login Records', 'aiowpsecurity')?>" class="button-primary" onclick="return confirm('Are you sure you want to delete all records?')"/>
        </form>
        </div></div>

        <?php
    }

    function render_tab4()
    {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        
        if(isset($_POST['aiowpsec_save_force_logout_settings']))//Do form submission tasks
        {
            $error = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-force-logout-settings-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on force logout options save!",4);
                die("Nonce check failed on force logout options save!");
            }

            $logout_time_period = sanitize_text_field($_POST['aiowps_logout_time_period']);
            if(!is_numeric($logout_time_period))
            {
                $error .= '<br />'.__('You entered a non numeric value for the logout time period field. It has been set to the default value.','aiowpsecurity');
                $logout_time_period = '1';//Set it to the default value for this field
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','aiowpsecurity').$error);
            }

            //Save all the form values to the options
            $aio_wp_security->configs->set_value('aiowps_logout_time_period',absint($logout_time_period));
            $aio_wp_security->configs->set_value('aiowps_enable_forced_logout',isset($_POST["aiowps_enable_forced_logout"])?'1':'');
            $aio_wp_security->configs->save_config();
            
            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
            
            $this->show_msg_settings_updated();
        }
        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Setting an expiry period for your WP administration session is a simple way to protect against unauthorized access to your site from your computer.', 'aiowpsecurity').'
            <br />'.__('This feature allows you to specify a time period in minutes after which the admin session will expire and the user will be forced to log back in.', 'aiowpsecurity').'
            </p>';
            ?>
        </div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Force User Logout Options', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("user-login-force-logout");
        ?>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-force-logout-settings-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Enable Force WP User Logout', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_enable_forced_logout" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_forced_logout')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want to force a wp user to be logged out after a configured amount of time', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row"><?php _e('Logout the WP User After XX Minutes', 'aiowpsecurity')?>:</th>
                <td><input size="5" name="aiowps_logout_time_period" value="<?php echo $aio_wp_security->configs->get_value('aiowps_logout_time_period'); ?>" />
                <span class="description"><?php _e('(Minutes) The user will be forced to log back in after this time period has elapased.', 'aiowpsecurity'); ?></span>
                </td> 
            </tr>
        </table>
        <input type="submit" name="aiowpsec_save_force_logout_settings" value="<?php _e('Save Settings', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>        
        <?php
    }
    
    function render_tab5()
    {
        include_once 'wp-security-list-acct-activity.php'; //For rendering the AIOWPSecurity_List_Table in tab4
        $acct_activity_list = new AIOWPSecurity_List_Account_Activity(); //For rendering the AIOWPSecurity_List_Table in tab2
        if(isset($_REQUEST['action'])) //Do row action tasks for list table form for login activity display
        {
            if($_REQUEST['action'] == 'delete_acct_activity_rec'){ //Delete link was clicked for a row in list table
                $acct_activity_list->delete_login_activity_records(strip_tags($_REQUEST['activity_login_rec']));
            }
        }
        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('This tab displays the login activity for WordPress admin accounts registered with your site.', 'aiowpsecurity').'
            <br />'.__('The information below can be handy if you need to do security investigations because it will show you the last 50 recent login events by username, IP address and time/date.', 'aiowpsecurity').'
            </p>';
            ?>
        </div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Account Activity Logs', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
            <?php 
            //Fetch, prepare, sort, and filter our data...
            $acct_activity_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
            <form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
            <input type="hidden" name="tab" value="<?php echo $_REQUEST['tab']; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $acct_activity_list->display(); ?>
            </form>
        </div></div>
        <?php
    }
    
    function render_tab6()
    {
        $logged_in_users = (AIOWPSecurity_Utility::is_multisite_install() ? get_site_transient('users_online') : get_transient('users_online'));
        
        global $aio_wp_security;
        include_once 'wp-security-list-logged-in-users.php'; //For rendering the AIOWPSecurity_List_Table
        $user_list = new AIOWPSecurity_List_Logged_In_Users();
        
        if (isset($_POST['aiowps_refresh_logged_in_user_list']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-logged-in-users-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for users logged in list!",4);
                die(__('Nonce check failed for users logged in list!','aiowpsecurity'));
            }
            
            $user_list->prepare_items();
        
//        if(isset($_REQUEST['action'])) //Do list table form row action tasks
//        {
            //no actions for now
//        }
        }

        ?>
        <div class="postbox">
        <h3><label for="title"><?php _e('Refresh Logged In User Data', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-logged-in-users-nonce'); ?>
        <input type="submit" name="aiowps_refresh_logged_in_user_list" value="<?php _e('Refresh Data', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('This tab displays all users who are currently logged into your site.', 'aiowpsecurity').'
                <br />'.__('If you suspect there is a user or users who are logged in which should not be, you can block them by inspecting the IP addresses from the data below and adding them to your whitelist.', 'aiowpsecurity').'
            </p>';
            ?>
        </div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Currently Logged In Users', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
            <?php
            //Fetch, prepare, sort, and filter our data...
            $user_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
            <form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
            <input type="hidden" name="tab" value="<?php echo $_REQUEST['tab']; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $user_list->display(); ?>
            </form>
        </div></div>
        <?php

    }

    /*
     * This function will unlock an IP range by modifying the "release_date" column of a record in the "login_lockdown" table
     */
    function unlock_ip_range($entries)
    {
        global $wpdb, $aio_wp_security;
        $lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
        if (is_array($entries))
        {
            //Unlock multiple records
            $id_list = "(" .implode(",",$entries) .")"; //Create comma separate list for DB operation
            $unlock_command = "UPDATE ".$lockdown_table." SET release_date = now() WHERE ID IN ".$id_list;
            $result = $wpdb->query($unlock_command);
            if($result != NULL)
            {
                $this->show_msg_updated(__('The selected IP ranges were unlocked successfully!','aiowpsecurity'));
            }
        } elseif ($entries != NULL)
        {
            //Delete single record
            $unlock_command = "UPDATE ".$lockdown_table." SET release_date = now() WHERE ID = '".absint($entries)."'";
            $result = $wpdb->query($unlock_command);
            if($result != NULL)
            {
                $this->show_msg_updated(__('The selected IP range was unlocked successfully!','aiowpsecurity'));
            }
        }
        //$aio_wp_security->debug_logger->log_debug("IP range unlocked from login_lockdown table - lockdown ID: ".$lockdown_id,0);
    }
    
    /*
     * This function will delete selected records from the "login_lockdown" table.
     * The function accepts either an array of IDs or a single ID
     */
    function delete_lockdown_records($entries)
    {
        global $wpdb, $aio_wp_security;
        $lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
        if (is_array($entries))
        {
            //Delete multiple records
            $id_list = "(" .implode(",",$entries) .")"; //Create comma separate list for DB operation
            $delete_command = "DELETE FROM ".$lockdown_table." WHERE ID IN ".$id_list;
            $result = $wpdb->query($delete_command);
            if($result != NULL)
            {
                $this->show_msg_updated(__('The selected records were deleted successfully!','aiowpsecurity'));
            }
        } elseif ($entries != NULL)
        {
            //Delete single record
            $delete_command = "DELETE FROM ".$lockdown_table." WHERE ID = '".absint($entries)."'";
            $result = $wpdb->query($delete_command);
            if($result != NULL)
            {
                $this->show_msg_updated(__('The selected record was deleted successfully!','aiowpsecurity'));
            }
            //$aio_wp_security->debug_logger->log_debug("Record deleted from login_lockdown table - lockdown ID: ".$entries,0);
        }
    }
    
} //end class