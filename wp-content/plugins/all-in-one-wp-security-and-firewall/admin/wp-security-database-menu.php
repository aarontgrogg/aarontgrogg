<?php

class AIOWPSecurity_Database_Menu extends AIOWPSecurity_Admin_Menu
{
    var $menu_page_slug = AIOWPSEC_DB_SEC_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs = array(
        'tab1' => 'DB Prefix', 
        'tab2' => 'DB Backup',
        );

    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1', 
        'tab2' => 'render_tab2',
        );
    
    function __construct() 
    {
        $this->render_menu_page();
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
    function render_menu_page() 
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
        global $wpdb, $aio_wp_security;
        $old_db_prefix = $wpdb->prefix;
        $new_db_prefix = '';
        $perform_db_change = false;

        if (isset($_POST['aiowps_db_prefix_change']))//Do form submission tasks
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-db-prefix-change-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for DB prefix change operation!",4);
                die(__('Nonce check failed for DB prefix change operation!','aiowpsecurity'));
            }
            
            //Let's first check if user's system allows writing to wp-config.php file. If plugin cannot write to wp-config we will not do the prefix change.
            $config_file = ABSPATH.'wp-config.php';
            $file_write = AIOWPSecurity_Utility_File::is_file_writable($config_file);
            if ($file_write == false)
            {
                $this->show_msg_error(__('The plugin has detected that it cannot write to the wp-config.php file. This feature can only be used if the plugin can successfully write to the wp-config.php file.', 'aiowpsecurity'));
            }
            else
            {
                if( isset($_POST['aiowps_enable_random_prefix'])) 
                {//User has elected to generate a random DB prefix
                    $string = AIOWPSecurity_Utility::generate_alpha_numeric_random_string('6');
                    $new_db_prefix = $string . '_';
                    $perform_db_change = true;
                }else 
                {
                    if (empty($_POST['aiowps_new_manual_db_prefix']))
                    {
                        $this->show_msg_error(__('Please enter a value for the DB prefix.', 'aiowpsecurity'));
                    }
                    else
                    {
                        //User has chosen their own DB prefix value
                        $new_db_prefix = wp_strip_all_tags( trim( $_POST['aiowps_new_manual_db_prefix'] ) );
                        $error = $wpdb->set_prefix( $new_db_prefix );
                        if(is_wp_error($error))
                        {
                            wp_die( __('<strong>ERROR</strong>: The table prefix can only contain numbers, letters, and underscores.', 'aiowpsecurity') );
                        }
                        $perform_db_change = true;
                    }
                }
            }
        }
        ?>
        <h2><?php _e('Change Database Prefix', 'aiowpsecurity')?></h2>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('Your WordPress DB is the most important asset of your website because it contains a lot of your site\'s precious information.', 'aiowpsecurity').'
            <br />'.__('The DB is also a target for hackers via methods such as SQL injections and malicious and automated code which targets certain tables.', 'aiowpsecurity').'
            <br />'.__('One way to add a layer of protection for your DB is to change the default WordPress table prefix from "wp_" to something else which will be difficult for hackers to guess.', 'aiowpsecurity').'
            <br />'.__('This feature allows you to easily change the prefix to a value of your choice or to a random value set by this plugin.', 'aiowpsecurity').'    
            </p>';
            ?>
        </div>

        <div class="postbox">
        <h3><label for="title"><?php _e('DB Prefix Options', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("db-security-db-prefix");
        ?>

        <div class="aio_yellow_box">
            <?php
            $backup_tab_link = '<a href="admin.php?page='.AIOWPSEC_DB_SEC_MENU_SLUG.'&tab=tab2">DB Backup</a>';
            $info_msg = '<p>'.sprintf( __('It is recommended that you perform a %s before using this feature', 'aiowpsecurity'), $backup_tab_link).'</p>';
            echo $info_msg;
            ?>
        </div>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-db-prefix-change-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Current DB Table Prefix', 'aiowpsecurity')?>:</th>
                <td>
                    <span class="aiowpsec_field_value"><strong><?php echo $wpdb->prefix; ?></strong></span>
                    <?php
                    //now let's display a warning notification if default prefix is used
                    if ($old_db_prefix == 'wp_') {
                        echo '&nbsp;&nbsp;&nbsp;<span class="aio_error_with_icon">'.__('Your site is currently using the default WordPress DB prefix value of "wp_". 
                            To increase your site\'s security you should consider changing the DB prefix value to another value.', 'aiowpsecurity').'</span>';
                    }
                    ?>                    
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Generate New DB Table Prefix', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_enable_random_prefix" type="checkbox" <?php if($aio_wp_security->configs->get_value('aiowps_enable_random_prefix')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want the plugin to generate a random 6 character string for the table prefix', 'aiowpsecurity'); ?></span>
                <br /><?php _e('OR', 'aiowpsecurity'); ?>
                <br /><input size="10" name="aiowps_new_manual_db_prefix" value="<?php //echo $aio_wp_security->configs->get_value('aiowps_new_manual_db_prefix'); ?>" />
                <span class="description"><?php _e('Choose your own DB prefix by specifying a string which contains letters and/or numbers and/or underscores. Example: xyz_', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
        </table>
        <input type="submit" name="aiowps_db_prefix_change" value="<?php _e('Change DB Prefix', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        <?php
        if ($perform_db_change)
        {
            //Do the DB prefix change operations
            $this->change_db_prefix($old_db_prefix,$new_db_prefix); 
        }
    }
    
    function render_tab2()
    {
        global $aio_wp_security;
        global $aiowps_feature_mgr;
        if (isset($_POST['aiowps_manual_db_backup']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-db-manual-change-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for manual DB backup operation!",4);
                die(__('Nonce check failed for manual DB backup operation!','aiowpsecurity'));
            }

            $result = $aio_wp_security->backup_obj->execute_backup();
            if ($result)
            {
                $backup_file_name = $aio_wp_security->backup_obj->last_backup_file_name;
                if (function_exists('is_multisite') && is_multisite()) 
                {
                    $aiowps_backup_file_path = $aio_wp_security->backup_obj->last_backup_file_dir_multisite . '/' . $backup_file_name;
                }
                else
                {
                    $aiowps_backup_dir = WP_CONTENT_DIR.'/'.AIO_WP_SECURITY_BACKUPS_DIR_NAME;
                    $aiowps_backup_file_path = $aiowps_backup_dir. '/' . $backup_file_name;
                }
                echo '<div id="message" class="updated fade"><p>';
                _e('DB Backup was successfully completed! You will receive the backup file via email if you have enabled "Send Backup File Via Email", otherwise you can retrieve it via FTP from the following directory:','aiowpsecurity');
                echo '<p>';
                _e('Your DB Backup File location: ');
                echo '<strong>'.$aiowps_backup_file_path.'</strong>';
                echo '</p>';
                echo '</p></div>';
            } 
            else
            {
                $aio_wp_security->debug_logger->log_debug("DB Backup - Backup operation failed!",4);
                $this->show_msg_error(__('DB Backup failed. Please check the permissions of the backup directory.','aiowpsecurity'));
            }
        }

        if(isset($_POST['aiowps_schedule_backups']))//Do form submission tasks
        {
            $error = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-scheduled-backup-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on scheduled DB backup options save!",4);
                die("Nonce check failed on scheduled DB backup options save!");
            }

            $backup_frequency = sanitize_text_field($_POST['aiowps_db_backup_frequency']);
            if(!is_numeric($backup_frequency))
            {
                $error .= '<br />'.__('You entered a non numeric value for the "backup time interval" field. It has been set to the default value.','aiowpsecurity');
                $backup_frequency = '4';//Set it to the default value for this field
            }
            
            $files_to_keep = sanitize_text_field($_POST['aiowps_backup_files_stored']);
            if(!is_numeric($files_to_keep))
            {
                $error .= '<br />'.__('You entered a non numeric value for the "number of backup files to keep" field. It has been set to the default value.','aiowpsecurity');
                $files_to_keep = '2';//Set it to the default value for this field
            }

            $email_address = sanitize_email($_POST['aiowps_backup_email_address']);
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
            $aio_wp_security->configs->set_value('aiowps_enable_automated_backups',isset($_POST["aiowps_enable_automated_backups"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_db_backup_frequency',absint($backup_frequency));
            $aio_wp_security->configs->set_value('aiowps_db_backup_interval',$_POST["aiowps_db_backup_interval"]);
            $aio_wp_security->configs->set_value('aiowps_backup_files_stored',absint($files_to_keep));
            $aio_wp_security->configs->set_value('aiowps_send_backup_email_address',isset($_POST["aiowps_send_backup_email_address"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_backup_email_address',$email_address);
            $aio_wp_security->configs->save_config();
            
            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
            $this->show_msg_settings_updated();
            
            //Let's check if backup interval was set to less than 24 hours
            if (isset($_POST["aiowps_enable_automated_backups"]) && ($backup_frequency < 24) && $_POST["aiowps_db_backup_interval"]==0)
            {
                $alert_user_msg = 'ATTENTION: You have configured your backups to occur at least once daily. For most websites we recommended that you choose a less frequent backup
                    schedule such as once every few days, once a week or once a month. Choosing a less frequent schedule will also help reduce your server load.';
                $this->show_msg_updated_st(__($alert_user_msg, 'aiowpsecurity'));
            }
        }
        
        ?>
        <div class="postbox">
        <h3><label for="title"><?php _e('Manual Backup', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-db-manual-change-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
            <span class="description"><?php _e('To create a new DB backup just click on the button below.', 'aiowpsecurity'); ?></span>                
            </tr>            
        </table>
        <input type="submit" name="aiowps_manual_db_backup" value="<?php _e('Create DB Backup Now', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Automated Scheduled Backups', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("db-security-db-backup");
        ?>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-scheduled-backup-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Enable Automated Scheduled Backups', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_enable_automated_backups" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_automated_backups')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want the system to automatically generate backups periodically based on the settings below', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row"><?php _e('Backup Time Interval', 'aiowpsecurity')?>:</th>
                <td><input size="5" name="aiowps_db_backup_frequency" value="<?php echo $aio_wp_security->configs->get_value('aiowps_db_backup_frequency'); ?>" />
                    <select id="backup_interval" name="aiowps_db_backup_interval">
                        <option value="0" <?php selected( $aio_wp_security->configs->get_value('aiowps_db_backup_interval'), '0' ); ?>><?php _e( 'Hours', 'aiowpsecurity' ); ?></option>
                        <option value="1" <?php selected( $aio_wp_security->configs->get_value('aiowps_db_backup_interval'), '1' ); ?>><?php _e( 'Days', 'aiowpsecurity' ); ?></option>
                        <option value="2" <?php selected( $aio_wp_security->configs->get_value('aiowps_db_backup_interval'), '2' ); ?>><?php _e( 'Weeks', 'aiowpsecurity' ); ?></option>
                    </select>
                <span class="description"><?php _e('Set the value for how often you would like an automated backup to occur', 'aiowpsecurity'); ?></span>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Number of Backup Files To Keep', 'aiowpsecurity')?>:</th>
                <td><input size="5" name="aiowps_backup_files_stored" value="<?php echo $aio_wp_security->configs->get_value('aiowps_backup_files_stored'); ?>" />
                <span class="description"><?php _e('Thie field allows you to choose the number of backup files you would like to keep in the backup directory', 'aiowpsecurity'); ?></span>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Send Backup File Via Email', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_send_backup_email_address" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_send_backup_email_address')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want the system to email you the backup file after a DB backup has been performed', 'aiowpsecurity'); ?></span>
                <br /><input size="30" name="aiowps_backup_email_address" value="<?php echo $aio_wp_security->configs->get_value('aiowps_backup_email_address'); ?>" />
                <span class="description"><?php _e('Enter an email address', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
        </table>
        <input type="submit" name="aiowps_schedule_backups" value="<?php _e('Save Settings', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        
        <?php
    }
    
    /*
     * Changes the DB prefix
     */
    function change_db_prefix($table_old_prefix, $table_new_prefix)
    {
        global $wpdb, $aio_wp_security;
        $old_prefix_length = strlen( $table_old_prefix );

        //Config file path
        $config_file = ABSPATH.'wp-config.php';

        //Get the table resource
        $result = mysql_list_tables(DB_NAME);

        //Count the number of tables
        $num_rows = mysql_num_rows( $result );
        $table_count = 0;

        //TODO - after reading up on internationalization mixed with html code I found that the WP experts say to do it as below. We will need to clean up other areas where we haven't used the following convention
        $info_msg_string = '<p class="aio_info_with_icon">'.__('Starting DB prefix change operations.....', 'aiowpsecurity').'</p>';
        
        $info_msg_string .= '<p class="aio_info_with_icon">'.sprintf( __('Your WordPress system has a total of %s tables and your new DB prefix will be: %s', 'aiowpsecurity'), '<strong>'.$num_rows.'</strong>', '<strong>'.$table_new_prefix.'</strong>').'</p>';
        echo ($info_msg_string);

        //Do a back of the config file
        if(!AIOWPSecurity_Utility_File::backup_a_file($config_file))
        {
            echo '<div class="aio_red_box"><p>'.__('Failed to make a backup of the wp-config.php file. This operation will not go ahead.', 'aiowpsecurity').'</p></div>';
            return;
        }
        else{
            echo '<p class="aio_success_with_icon">'.__('A backup copy of your wp-config.php file was created successfully!', 'aiowpsecurity').'</p>';
        }
        
        //Rename all the tables name
        for ($i = 0; $i < $num_rows; $i++)
        {
            //Get table name with old prefix
            $table_old_name = mysql_tablename($result, $i); 

            if ( strpos( $table_old_name, $table_old_prefix ) === 0 ) 
            {
                //Get table name with new prefix
                $table_new_name = $table_new_prefix . substr( $table_old_name, $old_prefix_length );
                
                //Write query to rename tables name
                $sql = "RENAME TABLE `".$table_old_name."` TO `".$table_new_name."`";
                //$sql = "RENAME TABLE %s TO %s";

                //Execute the query
                //if ( false === $wpdb->query($wpdb->prepare($sql, $table_old_name, $table_new_name)) ) //$wpdb->prepare is adding single quotes instead of backticks and hence causing the query to fail
                if ( false === $wpdb->query($sql) )
                {
                    $error = 1;
                    echo '<p class="aio_error_with_icon">'.sprintf( __('%s table name update failed', 'aiowpsecurity'), '<strong>'.$table_old_name.'</strong>').'</p>';
                    $aio_wp_security->debug_logger->log_debug("DB Security Feature - Unable to change prefix of table ".$table_old_name,4);
                } else {
                    $table_count++;
                }
            } else
            {
                continue;
            }
        }
        if ( @$error == 1 )
        {
            echo '<p class="aio_error_with_icon">'.sprintf( __('Please change the prefix manually for the above tables to: %s', 'aiowpsecurity'), '<strong>'.$table_new_prefix.'</strong>').'</p>';
        } else 
        {
            echo '<p class="aio_success_with_icon">'.sprintf( __('%s tables had their prefix updated successfully!', 'aiowpsecurity'), '<strong>'.$table_count.'</strong>').'</p>';
        }
        
        //Get wp-config.php file contents and modify it with new info
        $config_contents = file($config_file);
	foreach ($config_contents as $line_num => $line) {
            switch (substr($line,0,16)) {
                case '$table_prefix  =':
                    $config_contents[$line_num] = str_replace($table_old_prefix, $table_new_prefix, $line);
                    break;
            }
	}
        //Now let's modify the wp-config.php file
        if (AIOWPSecurity_Utility_File::write_content_to_file($config_file, $config_contents))
        {
            echo '<p class="aio_success_with_icon">'. __('wp-config.php file was updated successfully!', 'aiowpsecurity').'</p>';
        }else
        {
            echo '<p class="aio_error_with_icon">'.sprintf( __('The "wp-config.php" file was not able to be modified. Please modify this file manually using your favourite editor and search 
                    for variable "$table_prefix" and assign the following value to that variable: %s', 'aiowpsecurity'), '<strong>'.$table_new_prefix.'</strong>').'</p>';
            $aio_wp_security->debug_logger->log_debug("DB Security Feature - Unable to modify wp-config.php",4);
        }
        
        //Now let's update the options table
        $update_option_table_query = "UPDATE " . $table_new_prefix . "options 
                                                                  SET option_name = '".$table_new_prefix ."user_roles' 
                                                                  WHERE option_name = '".$table_old_prefix."user_roles' 
                                                                  LIMIT 1";

        if ( false === $wpdb->query($update_option_table_query) ) 
        {
            echo "<p class='error'>Changing value: ",
                     $table_old_prefix,
                     "user_roles in table ",
                     $table_new_prefix,
                     "options to  ",
                     $table_new_prefix,
                     "user_roles</p>";

            echo '<p class="aio_error_with_icon">'.sprintf( __('There was an error when updating the options table.', 'aiowpsecurity')).'</p>';
            $aio_wp_security->debug_logger->log_debug("DB Security Feature - Error when updating the options table",4);//Log the highly unlikely event of DB error
        } else 
        {
            echo '<p class="aio_success_with_icon">'.sprintf( __('The options table records which had references to the old DB prefix were updated successfully!', 'aiowpsecurity')).'</p>';
        }
        //Now let's update the user meta table
        $custom_sql = "SELECT user_id, meta_key 
                        FROM " . $table_new_prefix . "usermeta 
                        WHERE meta_key 
                        LIKE '" . $table_old_prefix . "%'";
		
        $meta_keys = $wpdb->get_results( $custom_sql );

        $error_update_usermeta = '';

        //Update all meta_key field values which have the old table prefix in user_meta table
        foreach ($meta_keys as $meta_key ) {

                //Create new meta key
                $new_meta_key = $table_new_prefix . substr( $meta_key->meta_key, $old_prefix_length );

                $update_user_meta_sql = "UPDATE " . $table_new_prefix . "usermeta 
                                                                SET meta_key='" . $new_meta_key . "' 
                                                                WHERE meta_key='" . $meta_key->meta_key . "'
                                                                AND user_id='" . $meta_key->user_id."'";

                if (false === $wpdb->query($update_user_meta_sql))
                {
                    $error_update_usermeta .= '<p class="aio_error_with_icon">'.sprintf( __('Error updating user_meta table where new meta_key = %s, old meta_key = %s and user_id = %s.', 'aiowpsecurity'),$new_meta_key,$meta_key->meta_key,$meta_key->user_id).'</p>';
                    echo $error_update_usermeta;
                    $aio_wp_security->debug_logger->log_debug("DB Security Feature - Error updating user_meta table where new meta_key = ".$new_meta_key." old meta_key = ".$meta_key->meta_key." and user_id = ".$meta_key->user_id,4);//Log the highly unlikely event of DB error
                }

        }
        echo '<p class="aio_success_with_icon">'.__('The usermeta table records which had references to the old DB prefix were updated successfully!', 'aiowpsecurity').'</p>';
        //Display tasks finished message
        $tasks_finished_msg_string = '<p class="aio_info_with_icon">'. __('DB prefix change tasks have been completed.', 'aiowpsecurity').'</p>';
        echo ($tasks_finished_msg_string);
    }    
} //end class