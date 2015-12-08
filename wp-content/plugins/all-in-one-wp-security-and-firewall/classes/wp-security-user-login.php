<?php
class AIOWPSecurity_User_Login
{
    /**
     * This will store a URI query string key for passing messages to the login form
     * @var string
     */
    var $key_login_msg;

    function __construct() 
    {
        $this->initialize();
        remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
        add_filter('authenticate', array(&$this, 'aiowp_auth_login'), 10, 3);
        add_action('aiowps_force_logout_check', array(&$this, 'aiowps_force_logout_action_handler'));
        //add_action('wp_login', array(&$this, 'wp_login_action_handler'), 10, 2);
        add_action('clear_auth_cookie', array(&$this, 'wp_logout_action_handler'));
        add_filter('login_message', array(&$this, 'aiowps_login_message')); //WP filter to add or modify messages on the login page
    }
    
    protected function initialize()
    {
        $this->key_login_msg = 'aiowps_login_msg_id';
    }


    /*
     * This function will take care of the authentication operations
     * It will return a WP_User object if successful or WP_Error if not
     */
    function aiowp_auth_login($user, $username, $password)
    {
        global $aio_wp_security;
        $login_attempts_permitted = $aio_wp_security->configs->get_value('aiowps_max_login_attempts');
        
        $user_locked = $this->check_locked_user();
        if ($user_locked != NULL) {
            $aio_wp_security->debug_logger->log_debug("Login attempt from blocked IP range - ".$user_locked['failed_login_ip'],2);
            return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Login failed because your IP address has been blocked.
                                Please contact the administrator.', 'aiowpsecurity'));
        }
        
        if ( is_a($user, 'WP_User') ) { return $user; } //Existing WP core code
        
        if ( empty($username) || empty($password) ) { //Existing WP core code
            $error = new WP_Error();
            if (empty($username)){
                $error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.', 'aiowpsecurity'));
            }

            if (empty($password)){
                $error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.', 'aiowpsecurity'));
            }
            return $error;
        }
        
        $userdata = get_user_by('login',$username);
        if (!$userdata) 
        {
            //This means an unknown username is being used for login
            $this->increment_failed_logins($username);
            if($aio_wp_security->configs->get_value('aiowps_enable_login_lockdown')=='1')
            {
                if($login_attempts_permitted <= $this->get_login_fail_count()  || $aio_wp_security->configs->get_value('aiowps_enable_invalid_username_lockdown')=='1')
                {
                    $this->lock_the_user($username);
                }
            }
            if($aio_wp_security->configs->get_value('aiowps_set_generic_login_msg')=='1')
            {
                //Return generic error message if configured
                return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid login credentials.', 'aiowpsecurity'));
            } else
            {
                return new WP_Error('invalid_username', __('<strong>ERROR</strong>: Invalid username.', 'aiowpsecurity'));
            }
	}
        
        $userdata = apply_filters('wp_authenticate_user', $userdata, $password); //Existing WP core code
        if ( is_wp_error($userdata) ) { //Existing WP core code
                return $userdata;
        }

        if ( !wp_check_password($password, $userdata->user_pass, $userdata->ID) ) 
        {
            //This means wrong password was entered
            $this->increment_failed_logins($username);
            if($aio_wp_security->configs->get_value('aiowps_enable_login_lockdown')=='1')
            {
                if($login_attempts_permitted <= $this->get_login_fail_count())
                {
                    $this->lock_the_user($username);
                }
            }
            if($aio_wp_security->configs->get_value('aiowps_set_generic_login_msg')=='1')
            {
                //Return generic error message if configured
                return new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid login credentials.', 'aiowpsecurity'));
            } else 
            {
                return new WP_Error('incorrect_password', sprintf(__('<strong>ERROR</strong>: Incorrect password. <a href="%s" title="Password Lost and Found">Lost your password</a>?', 'aiowpsecurity'), site_url('wp-login.php?action=lostpassword', 'login')));
            }
        }

        $user =  new WP_User($userdata->ID);
        return $user;
    }
    
    /*
     * This function queries the aiowps_login_lockdown table.
     * If the release_date has not expired AND the current visitor IP addr matches
     * it will return a record 
     */
    function check_locked_user()
    {
        global $wpdb;
        $login_lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
        $ip = AIOWPSecurity_Utility_IP::get_user_ip_address(); //Get the IP address of user
        $ip_range = AIOWPSecurity_Utility_IP::get_sanitized_ip_range($ip); //Get the IP range of the current user
        $locked_user = $wpdb->get_row("SELECT * FROM $login_lockdown_table " .
                                        "WHERE release_date > now() AND " .
                                        "failed_login_ip LIKE '" . esc_sql($ip_range) . "%'", ARRAY_A);
        return $locked_user;
    }

    /*
     * This function queries the aiowps_failed_logins table and returns the number of failures for current IP range within allowed failure period
     */
    function get_login_fail_count()
    {
        global $wpdb, $aio_wp_security;
        $failed_logins_table = AIOWPSEC_TBL_FAILED_LOGINS;
        $login_retry_interval = $aio_wp_security->configs->get_value('aiowps_retry_time_period');
        $ip = AIOWPSecurity_Utility_IP::get_user_ip_address(); //Get the IP address of user
        $ip_range = AIOWPSecurity_Utility_IP::get_sanitized_ip_range($ip); //Get the IP range of the current user
        $login_failures = $wpdb->get_var("SELECT COUNT(ID) FROM $failed_logins_table " . 
                                "WHERE failed_login_date + INTERVAL " .
                                $login_retry_interval . " MINUTE > now() AND " . 
                                "login_attempt_ip LIKE '" . esc_sql($ip_range) . "%'");
        return $login_failures;
    }

    /*
     * Adds an entry to the aiowps_lockdowns table
     */
    function lock_the_user($username='')
    {
        global $wpdb, $aio_wp_security;
        $login_lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
        $lockout_time_length = $aio_wp_security->configs->get_value('aiowps_lockout_time_length');
        $ip = AIOWPSecurity_Utility_IP::get_user_ip_address(); //Get the IP address of user
        $ip_range = AIOWPSecurity_Utility_IP::get_sanitized_ip_range($ip); //Get the IP range of the current user
        $username = sanitize_user($username);
	$user = get_user_by('login',$username); //Returns WP_User object if exists
        if ($user)
        {
            //If the login attempt was made using a valid user set variables for DB storage later on
            $user_id = $user->ID;
        } else {
            //If the login attempt was made using a non-existent user then let's set user_id to blank and record the attempted user login name for DB storage later on
            $user_id = '';
        }
        $ip_range_str = esc_sql($ip_range).'.*';
        $insert = "INSERT INTO " . $login_lockdown_table . " (user_id, user_login, lockdown_date, release_date, failed_login_IP) " .
                        "VALUES ('" . $user_id . "', '" . $username . "', now(), date_add(now(), INTERVAL " .
                        $lockout_time_length . " MINUTE), '" . $ip_range_str . "')";
        $result = $wpdb->query($insert);
        if ($result > 0)
        {
            do_action('aiowps_lockdown_event', $ip_range, $username);
            $this->send_ip_lock_notification_email($username, $ip_range, $ip);
            $aio_wp_security->debug_logger->log_debug("The following IP address range has been locked out for exceeding the maximum login attempts: ".$ip_range,2);//Log the lockdown event
        }
        else if ($result == FALSE)
        {
            $aio_wp_security->debug_logger->log_debug("Error inserting record into ".$login_lockdown_table,4);//Log the highly unlikely event of DB error
        }
    }

    /*
     * Adds an entry to the aiowps_failed_logins table
     */
    function increment_failed_logins($username='')
    {
        global $wpdb, $aio_wp_security;
        //$login_attempts_permitted = $aio_wp_security->configs->get_value('aiowps_max_login_attempts');
        //$lockout_time_length = $aio_wp_security->configs->get_value('aiowps_lockout_time_length');
        $login_fails_table = AIOWPSEC_TBL_FAILED_LOGINS;
        $ip = AIOWPSecurity_Utility_IP::get_user_ip_address(); //Get the IP address of user
        $ip_range = AIOWPSecurity_Utility_IP::get_sanitized_ip_range($ip); //Get the IP range of the current user
        
        $username = sanitize_user($username);
	$user = get_user_by('login',$username); //Returns WP_User object if it exists
        if ($user)
        {
            //If the login attempt was made using a valid user set variables for DB storage later on
            $user_id = $user->ID;
        } else {
            //If the login attempt was made using a non-existent user then let's set user_id to blank and record the attempted user login name for DB storage later on
            $user_id = '';
        }
        $ip_range_str = esc_sql($ip_range).'.*';
        $insert = "INSERT INTO " . $login_fails_table . " (user_id, user_login, failed_login_date, login_attempt_ip) " .
                        "VALUES ('" . $user_id . "', '" . $username . "', now(), '" . $ip_range_str . "')";
        $result = $wpdb->query($insert);
        if ($result == FALSE)
        {
            $aio_wp_security->debug_logger->log_debug("Error inserting record into ".$login_fails_table,4);//Log the highly unlikely event of DB error
        }

    }

    /*
     * This function queries the aiowps_failed_logins table and returns the number of failures for current IP range within allowed failure period
     */
    function send_ip_lock_notification_email($username, $ip_range, $ip)
    {
        global $aio_wp_security;
        $email_notification_enabled = $aio_wp_security->configs->get_value('aiowps_enable_email_notify');
        $to_email_address = $aio_wp_security->configs->get_value('aiowps_email_address');
        $email_msg = '';
        if ($email_notification_enabled == 1)
        {
            $subject = '['.get_option('siteurl').'] '. __('Site Lockout Notification','aiowpsecurity');
            $email_msg .= __('A lockdown event has occurred due to too many failed login attempts or invalid username:','aiowpsecurity')."\n";
            $email_msg .= __('Username: '.($username?$username:"Unknown"),'aiowpsecurity')."\n";
            $email_msg .= __('IP Address: '.$ip,'aiowpsecurity')."\n\n";
            $email_msg .= __('IP Range: '.$ip_range.'.*','aiowpsecurity')."\n\n";
            $email_msg .= __('Log into your site\'s WordPress administration panel to see the duration of the lockout or to unlock the user.','aiowpsecurity')."\n";
            $email_header = 'From: '.get_bloginfo( 'name' ).' <'.get_bloginfo('admin_email').'>' . "\r\n\\";
            $sendMail = wp_mail($to_email_address, $subject, $email_msg, $email_header);
        }
    }

    
    /*
     * This function will check the settings and log the user after the configured time period
     */
    function aiowps_force_logout_action_handler()
    {
        global $aio_wp_security;
        //$aio_wp_security->debug_logger->log_debug("Force Logout - Checking if any user need to be logged out...");
        if($aio_wp_security->configs->get_value('aiowps_enable_forced_logout')=='1') //if this feature is enabled then do something
        {
            if(is_user_logged_in())
            {
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
                $current_time = current_time('mysql');
                $login_time = $this->get_wp_user_last_login_time($user_id);
                $diff = strtotime($current_time) - strtotime($login_time);
                $logout_time_interval_value = $aio_wp_security->configs->get_value('aiowps_logout_time_period');
                $logout_time_interval_val_seconds = $logout_time_interval_value * 60;
                if($diff > $logout_time_interval_val_seconds)
                {
                    $aio_wp_security->debug_logger->log_debug("Force Logout - This user logged in more than (".$logout_time_interval_value.") minutes ago. Doing a force log out for the user with username: ".$current_user->user_login);
                    $this->wp_logout_action_handler(); //this will register the logout time/date in the logout_date column
                    
                    $curr_page_url = AIOWPSecurity_Utility::get_current_page_url();
                    $after_logout_payload = 'redirect_to='.$curr_page_url.'&msg='.$this->key_login_msg.'=session_expired';
                    $encrypted_payload = base64_encode($after_logout_payload);
                    $logout_url = AIOWPSEC_WP_URL.'?aiowpsec_do_log_out=1';
                    $logout_url = AIOWPSecurity_Utility::add_query_data_to_url($logout_url, 'al_additional_data', $encrypted_payload);
                    AIOWPSecurity_Utility::redirect_to_url($logout_url);
                }
            }
        }
    }
    
    function get_wp_user_last_login_time($user_id)
    {
        $last_login = get_user_meta($user_id, 'last_login_time', true);
        return $last_login;
    }

    function wp_login_action_handler($user_login, $user) 
    {
        global $wpdb, $aio_wp_security;
        $login_activity_table = AIOWPSEC_TBL_USER_LOGIN_ACTIVITY;
        $login_date_time = current_time('mysql');
        update_user_meta($user->ID, 'last_login_time', $login_date_time); //store last login time in meta table
        $curr_ip_address = AIOWPSecurity_Utility_IP::get_user_ip_address();
        $insert = "INSERT INTO " . $login_activity_table . " (user_id, user_login, login_date, login_ip) " .
                        "VALUES ('" . $user->ID . "', '" . $user_login . "', '" . $login_date_time . "', '" . $curr_ip_address . "')";
        $result = $wpdb->query($insert);
        if ($result == FALSE)
        {
            $aio_wp_security->debug_logger->log_debug("Error inserting record into ".$login_activity_table,4);//Log the highly unlikely event of DB error
        }
        
    }

    function check_user_logged_in($user_login) 
    {
          // get the online users list
        $logged_in_users = get_transient('users_online');

        //If user is in the transient list and last activity was less than 15 minutes ago they are classed as being online
        return isset($logged_in_users[$user_id]) && ($logged_in_users[$user_id]['last_activity'] > (current_time('timestamp') - (15 * 60)));

    }

    /**
     * The handler for logout events, ie, uses the WP "clear_auth_cookies" action.
     
     * Modifies the login activity record for the current user by registering the logout time/date in the logout_date column.
     * (NOTE: Because of the way we are doing a force logout, the "clear_auth_cookies" hook does not fire.
     * upon auto logout. The current workaround is to call this function directly from the aiowps_force_logout_action_handler() when 
     * an auto logout occurs due to the "force logout" feature). 
     *
     */
    function wp_logout_action_handler() 
    {
        global $wpdb, $aio_wp_security;
        $current_user = wp_get_current_user();
        $ip_addr = AIOWPSecurity_Utility_IP::get_user_ip_address();
        $user_id = $current_user->ID;
        //Clean up transients table
        $this->update_user_online_transient($user_id, $ip_addr);

        $login_activity_table = AIOWPSEC_TBL_USER_LOGIN_ACTIVITY;
        $logout_date_time = current_time('mysql');
        $data = array('logout_date' => $logout_date_time);
        $where = array('user_id' => $user_id,
                        'login_ip' => $ip_addr,
                        'logout_date' => '0000-00-00 00:00:00');
        $result = $wpdb->update($login_activity_table, $data, $where);
        if ($result == FALSE)
        {
            $aio_wp_security->debug_logger->log_debug("Error inserting record into ".$login_activity_table,4);//Log the highly unlikely event of DB error
        }
    }

    /**
     * This will clean up the "users_online" transient entry for the current user. 
     *
     */
    function update_user_online_transient($user_id, $ip_addr) 
    {
        global $aio_wp_security;
        $logged_in_users = (AIOWPSecurity_Utility::is_multisite_install() ? get_site_transient('users_online') : get_transient('users_online'));
        //$logged_in_users = get_transient('users_online');
        if ($logged_in_users === false || $logged_in_users == NULL)
        {
            return;
        }
        $j = 0;
        foreach ($logged_in_users as $value)
        {
            if ($value['user_id'] == $user_id && strcmp($value['ip_address'], $ip_addr) == 0)
            {
                unset($logged_in_users[$j]);
                break;
            }
            $j++;
        }
        //Save the transient
        AIOWPSecurity_Utility::is_multisite_install() ? set_site_transient('users_online', $logged_in_users, 30 * 60) : set_transient('users_online', $logged_in_users, 30 * 60);
        //set_transient('users_online', $logged_in_users, 30 * 60); //Set transient with the data obtained above and also set the expiry to 30min
        return;
    }
    
    /**
     * The handler for the WP "login_message" filter
     * Adds custom messages to the other messages that appear above the login form.
     *
     * NOTE: This method is automatically called by WordPress for displaying
     * text above the login form.
     *
     * @param string $message  the output from earlier login_message filters
     * @return string
     *
     */
    function aiowps_login_message($message = '') 
    {
        global $aio_wp_security;
        $msg = '';
        if(isset($_GET[$this->key_login_msg]) && !empty($_GET[$this->key_login_msg]))
        {
            $logout_msg = strip_tags($_GET[$this->key_login_msg]);
        }
        if (!empty($logout_msg))
        {
            switch ($logout_msg) {
                    case 'session_expired':
                            $msg = sprintf(__('Your session has expired because it has been over %d minutes since your last login.', 'aiowpsecurity'), $aio_wp_security->configs->get_value('aiowps_logout_time_period'));
                            $msg .= ' ' . __('Please log back in to continue.', 'aiowpsecurity');
                            break;
                    case 'admin_user_changed':
                            $msg = __('You were logged out because you just changed the "admin" username.', 'aiowpsecurity');
                            $msg .= ' ' . __('Please log back in to continue.', 'aiowpsecurity');
                            break;
                    default:
            }
        }
        if (!empty($msg))
        {
            $msg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
            $message .= '<p class="login message">'. $msg . '</p>';
        }
        return $message;
    }
    
}