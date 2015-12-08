<?php

class AIOWPSecurity_Utility
{
    function __construct(){
        //NOP
    }
    
    static function get_current_page_url() 
    {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
	    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} 
	else{
	    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
    }
    
    static function redirect_to_url($url,$delay='0',$exit='1')
    {
        if(empty($url)){
            echo "<br /><strong>Error! The URL value is empty. Please specify a correct URL value to redirect to!</strong>";
            exit;
        }
        if (!headers_sent()){
            header('Location: ' . $url);
        }
        else{
            echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'" />';
        }
        if($exit == '1'){
            exit;
        }
    }

    static function get_logout_url_with_after_logout_url_value($after_logout_url)
    {
        return AIOWPSEC_WP_URL.'?aiowpsec_do_log_out=1&after_logout='.$after_logout_url;        
    }
    
    /*
     * Checks if a particular username exists in the WP Users table
     */
    static function check_user_exists($username) 
    {
        global $wpdb;

        //if username is empty just return false
        if ( $username == '' ) {
            return false;
        }
        
        //If multisite 
        if (AIOWPSecurity_Utility::is_multisite_install()){
            $blog_id = get_current_blog_id();
            $admin_users = get_users('blog_id='.$blog_id.'orderby=login&role=administrator');
            $acct_name_exists = false;
            foreach ($admin_users as $user)
            {
                if ($user->user_login == $username){
                    $acct_name_exists = true;
                    break;
                }
            }
            return $acct_name_exists;
        }
        
        //check users table
        $user = $wpdb->get_var( "SELECT user_login FROM `" . $wpdb->users . "` WHERE user_login='" . sanitize_text_field( $username ) . "';" );
        $userid = $wpdb->get_var( "SELECT ID FROM `" . $wpdb->users . "` WHERE ID='" . sanitize_text_field( $username ) . "';" );

        if ( $user == $username || $userid == $username ) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * This function will return a list of user accounts which have login and nick names which are identical
     */
    static function check_identical_login_and_nick_names() {
        global $wpdb;
        $accounts_found = $wpdb->get_results( "SELECT ID,user_login FROM `" . $wpdb->users . "` WHERE user_login<=>display_name;", ARRAY_A);
        return $accounts_found;
    }

    
    static function add_query_data_to_url($url, $name, $value)
    {
        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= $name . '='. $value;
        return $url;
    }

    
    /*
     * Generates a random alpha-numeric number
     */
    static function generate_alpha_numeric_random_string($string_length)
    {
        //Charecters present in table prefix
        $allowed_chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = '';
        //Generate random string
        for ($i = 0; $i < $string_length; $i++) {
            $string .= $allowed_chars[rand(0, strlen($allowed_chars) - 1)];
        }
        return $string;
    }
    
    static function set_cookie_value($cookie_name, $cookie_value, $expiry_seconds = 86400, $path = '/', $cookie_domain = '')
    {
        $expiry_time = time() + intval($expiry_seconds);
        if(empty($cookie_domain)){
            $cookie_domain = COOKIE_DOMAIN;
        }
        setcookie($cookie_name, $cookie_value, $expiry_time, $path, $cookie_domain);
    }
    
    static function get_cookie_value($cookie_name)
    {
        if(isset($_COOKIE[$cookie_name])){
            return $_COOKIE[$cookie_name];
        }
        return "";
    }
    
    static function is_multisite_install()
    {
	if (function_exists('is_multisite') && is_multisite()){
            return true;
	}else{
            return false;
	}
    }
    
    //This is a general yellow box message for when we want to suppress a feature's config items because site is subsite of multi-site
    static function display_multisite_message()
    {
        echo '<div class="aio_yellow_box">';
        echo '<p>'.__('The plugin has detected that you are using a Multi-Site WordPress installation.', 'aiowpsecurity').'</p>
              <p>'.__('This feature can only be configured by the "superadmin" on the main site.', 'aiowpsecurity').'</p>';
        echo '</div>';
    }
    
    /*
     * Modifies the wp-config.php file to disable PHP file editing from the admin panel
     * This func will add the following code:
     * define('DISALLOW_FILE_EDIT', false);
     * 
     * NOTE: This function will firstly check if the above code already exists and it will modify the bool value, otherwise it will insert the code mentioned above
     */
    static function disable_file_edits()
    {
        global $aio_wp_security;
        $edit_file_config_entry_exists = false;
        
        //Config file path
        $config_file = ABSPATH.'wp-config.php';

        //Get wp-config.php file contents so we can check if the "DISALLOW_FILE_EDIT" variable already exists
        $config_contents = file($config_file);

        foreach ($config_contents as $line_num => $line) 
        {
            if (strpos($line, "'DISALLOW_FILE_EDIT', false"))
            {
                $config_contents[$line_num] = str_replace('false', 'true', $line);
                $edit_file_config_entry_exists = true;
                //$this->show_msg_updated(__('Settings Saved - The ability to edit PHP files via the admin the panel has been DISABLED.', 'aiowpsecurity'));
            } else if(strpos($line, "'DISALLOW_FILE_EDIT', true"))
            {
                $edit_file_config_entry_exists = true;
                //$this->show_msg_updated(__('Your system config file is already configured to disallow PHP file editing.', 'aiowpsecurity'));
                return true;
                
            }
            
            //For wp-config.php files originating from early WP versions we will remove the closing php tag
            if (strpos($line, "?>") !== false)
            {
                $config_contents[$line_num] = str_replace("?>", "", $line);
            }
	}
        
        if (!$edit_file_config_entry_exists)
        {
            //Construct the config code which we will insert into wp-config.php
            $new_snippet = '//Disable File Edits' . PHP_EOL;
            $new_snippet .= 'define(\'DISALLOW_FILE_EDIT\', true);';
            $config_contents[] = $new_snippet; //Append the new snippet to the end of the array
        }
        
        //Make a backup of the config file
        if(!AIOWPSecurity_Utility_File::backup_a_file($config_file))
        {
            $this->show_msg_error(__('Failed to make a backup of the wp-config.php file. This operation will not go ahead.', 'aiowpsecurity'));
            //$aio_wp_security->debug_logger->log_debug("Disable PHP File Edit - Failed to make a backup of the wp-config.php file.",4);
            return false;
        }
        else{
            //$this->show_msg_updated(__('A backup copy of your wp-config.php file was created successfully....', 'aiowpsecurity'));
        }

        //Now let's modify the wp-config.php file
        if (AIOWPSecurity_Utility_File::write_content_to_file($config_file, $config_contents))
        {
            //$this->show_msg_updated(__('Settings Saved - Your system is now configured to not allow PHP file editing.', 'aiowpsecurity'));
            return true;
        }else
        {
            //$this->show_msg_error(__('Operation failed! Unable to modify wp-config.php file!', 'aiowpsecurity'));
            $aio_wp_security->debug_logger->log_debug("Disable PHP File Edit - Unable to modify wp-config.php",4);
            return false;
        }
    }

    /*
     * Modifies the wp-config.php file to allow PHP file editing from the admin panel
     * This func will modify the following code by replacing "true" with "false":
     * define('DISALLOW_FILE_EDIT', true);
     */
    
    static function enable_file_edits()
    {
        global $aio_wp_security;
        $edit_file_config_entry_exists = false;
        
        //Config file path
        $config_file = ABSPATH.'wp-config.php';

        //Get wp-config.php file contents
        $config_contents = file($config_file);
	foreach ($config_contents as $line_num => $line) 
        {
            if (strpos($line, "'DISALLOW_FILE_EDIT', true"))
            {
                $config_contents[$line_num] = str_replace('true', 'false', $line);
                $edit_file_config_entry_exists = true;
            } else if(strpos($line, "'DISALLOW_FILE_EDIT', false"))
            {
                $edit_file_config_entry_exists = true;
                //$this->show_msg_updated(__('Your system config file is already configured to allow PHP file editing.', 'aiowpsecurity'));
                return true;
            }
        }
        
        if (!$edit_file_config_entry_exists)
        {
            //if the DISALLOW_FILE_EDIT settings don't exist in wp-config.php then we don't need to do anything
            //$this->show_msg_updated(__('Your system config file is already configured to allow PHP file editing.', 'aiowpsecurity'));
            return true;
        } else
        {
            //Now let's modify the wp-config.php file
            if (AIOWPSecurity_Utility_File::write_content_to_file($config_file, $config_contents))
            {
                //$this->show_msg_updated(__('Settings Saved - Your system is now configured to allow PHP file editing.', 'aiowpsecurity'));
                return true;
            }else
            {
                //$this->show_msg_error(__('Operation failed! Unable to modify wp-config.php file!', 'aiowpsecurity'));
                //$aio_wp_security->debug_logger->log_debug("Disable PHP File Edit - Unable to modify wp-config.php",4);
                return false;
            }
        }
    }
    
    
}
