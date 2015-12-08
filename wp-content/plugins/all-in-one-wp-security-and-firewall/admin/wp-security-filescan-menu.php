<?php

class AIOWPSecurity_Filescan_Menu extends AIOWPSecurity_Admin_Menu
{
    var $menu_page_slug = AIOWPSEC_FILESCAN_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs = array(
        'tab1' => 'File Change Detection', 
        );

    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1', 
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
        global $aiowps_feature_mgr;
        
        if (isset($_POST['fcd_scan_info']))
        {
            //Display scan file change info and clear the global alert variable
            //TODO: display file change details
            
            //Clear the global variable
            $aio_wp_security->configs->set_value('aiowps_fcds_change_detected', FALSE);
            $aio_wp_security->configs->save_config();
            
            //Display the last scan results
            $this->display_last_scan_results();
        }

        if (isset($_POST['aiowps_manual_fcd_scan']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-fcd-manual-scan-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for manual file change detection scan operation!",4);
                die(__('Nonce check failed for manual file change detection scan operation!','aiowpsecurity'));
            }

            $result = $aio_wp_security->filescan_obj->execute_file_change_detection_scan();
            //If this is first scan display special message
            if ($result['initial_scan'] == 1)
            {
                $this->show_msg_updated(__('The plugin has detected that this is your first file change detection scan. The file details from this scan will be used to detect file changes for future scans!','aiowpsecurity'));
            }
//            else
//            {
//                $aio_wp_security->debug_logger->log_debug("Manual File Change Detection scan operation failed!",4);
//                $this->show_msg_error(__('Manual File Change Detection scan operation failed!','aiowpsecurity'));
//            }
        }

        if(isset($_POST['aiowps_schedule_fcd_scan']))//Do form submission tasks
        {
            $error = '';
            $reset_scan_data = FALSE;
            $file_types = '';
            $files = '';

            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-scheduled-fcd-scan-nonce'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed for file change detection scan options save!",4);
                die("Nonce check failed for file change detection scan options save!");
            }

            $fcd_scan_frequency = sanitize_text_field($_POST['aiowps_fcd_scan_frequency']);
            if(!is_numeric($fcd_scan_frequency))
            {
                $error .= '<br />'.__('You entered a non numeric value for the "backup time interval" field. It has been set to the default value.','aiowpsecurity');
                $fcd_scan_frequency = '4';//Set it to the default value for this field
            }
            
            if (!empty($_POST['aiowps_fcd_exclude_filetypes']))
            {
                $file_types = trim($_POST['aiowps_fcd_exclude_filetypes']);
                //$file_types_array = preg_split( '/\r\n|\r|\n/', $file_types );

                //Get the currently saved config value and check if this has changed. If so do another scan to reset the scan data so it omits these filetypes
                if ($file_types != $aio_wp_security->configs->get_value('aiowps_fcd_exclude_filetypes'))
                {
                    $reset_scan_data = TRUE;
                }
            }
            
            if (!empty($_POST['aiowps_fcd_exclude_files']))
            {
                $files = trim($_POST['aiowps_fcd_exclude_files']);
                //Get the currently saved config value and check if this has changed. If so do another scan to reset the scan data so it omits these files/dirs
                if ($files != $aio_wp_security->configs->get_value('aiowps_fcd_exclude_files'))
                {
                    $reset_scan_data = TRUE;
                }
                
            }

            $email_address = sanitize_email($_POST['aiowps_fcd_scan_email_address']);
            if(!is_email($email_address))
            {
                $error .= '<p>'.__('You have entered an incorrect email address format. It has been set to your WordPress admin email as default.','aiowpsecurity').'</p>';
                $email_address = get_bloginfo('admin_email'); //Set the default value to the blog admin email
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','aiowpsecurity').$error);
            }

            //Save all the form values to the options
            $aio_wp_security->configs->set_value('aiowps_enable_automated_fcd_scan',isset($_POST["aiowps_enable_automated_fcd_scan"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_fcd_scan_frequency',absint($fcd_scan_frequency));
            $aio_wp_security->configs->set_value('aiowps_fcd_scan_interval',$_POST["aiowps_fcd_scan_interval"]);
            $aio_wp_security->configs->set_value('aiowps_fcd_exclude_filetypes',$file_types);
            $aio_wp_security->configs->set_value('aiowps_fcd_exclude_files',$files);
            $aio_wp_security->configs->set_value('aiowps_send_fcd_scan_email',isset($_POST["aiowps_send_fcd_scan_email"])?'1':'');
            $aio_wp_security->configs->set_value('aiowps_fcd_scan_email_address',$email_address);
            $aio_wp_security->configs->save_config();

            //Recalculate points after the feature status/options have been altered
            $aiowps_feature_mgr->check_feature_status_and_recalculate_points();
            $this->show_msg_settings_updated();
            
            //Let's check if backup interval was set to less than 24 hours
            if (isset($_POST["aiowps_enable_automated_fcd_scan"]) && ($fcd_scan_frequency < 24) && $_POST["aiowps_fcd_scan_interval"]==0)
            {
                $alert_user_msg = 'ATTENTION: You have configured your file change detection scan to occur at least once daily. For most websites we recommended that you choose a less frequent
                    schedule such as once every few days, once a week or once a month. Choosing a less frequent schedule will also help reduce your server load.';
                $this->show_msg_updated(__($alert_user_msg, 'aiowpsecurity'));
            }
            
            if($reset_scan_data)
            {
                //Clear old scan row and ask user to perform a fresh scan to reset the data
                $aiowps_global_meta_tbl_name = AIOWPSEC_TBL_GLOBAL_META_DATA;
                $where = array('meta_key1' => 'file_change_detection', 'meta_value1' => 'file_scan_data');
                $wpdb->delete( $aiowps_global_meta_tbl_name, $where);
                $result = $aio_wp_security->filescan_obj->execute_file_change_detection_scan();
                $new_scan_alert = __('NEW SCAN COMPLETED: The plugin has detected that you have made changes to the "File Types To Ignore" or "Files To Ignore" fields.
                    In order to ensure that future scan results are accurate, the old scan data has been refreshed.', 'aiowpsecurity');
                $this->show_msg_updated($new_scan_alert);
            }

        }
        
        //Display an alert warning message if a file change was detected
        if ($aio_wp_security->configs->get_value('aiowps_fcds_change_detected'))
        {
            $error_msg = __('All In One WP Security & Firewall has detected that there was a change in your host\'s files.', 'aiowpsecurity');
            
            $button = '<div><form action="" method="POST"><input type="submit" name="fcd_scan_info" value="'.__('View Scan Details & Clear This Message', 'aiowpsecurity').'" class="button-secondary" /></form></div>';
            $error_msg .= $button;
            $this->show_msg_error($error_msg);
        } 

        
        ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('If given an opportunity hackers can insert their code or files into your system which they can then use to carry out malicious acts on your site.', 'aiowpsecurity').
            '<br />'.__('Being informed of any changes in your files can be a good way to quickly prevent a hacker from causing damage to your website.', 'aiowpsecurity').
            '<br />'.__('In general, WordPress core and plugin files and file types such as ".php" or ".js" should not change often and when they do, it is important that you are made aware when a change occurs and which file was affected.', 'aiowpsecurity').
            '<br />'.__('The "File Change Detection Feature" will notify you of any file change which occurs on your system, including the addition and deletion of files by performing a regular automated or manual scan of your system\'s files.', 'aiowpsecurity').
            '<br />'.__('This feature also allows you to exclude certain files or folders from the scan in cases where you know that they change often as part of their normal operation. (For example log files and certain caching plugin files may change often and hence you may choose to exclude such files from the file change detection scan)', 'aiowpsecurity').'</p>';
            ?>
        </div>

        <div class="postbox">
        <h3><label for="title"><?php _e('Manual File Change Detection Scan', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-fcd-manual-scan-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
            <span class="description"><?php _e('To perform a manual file change detection scan click on the button below.', 'aiowpsecurity'); ?></span>                
            </tr>            
        </table>
        <input type="submit" name="aiowps_manual_fcd_scan" value="<?php _e('Perform Scan Now', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Automated File Change Detection', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <?php
        //Display security info badge
        global $aiowps_feature_mgr;
        $aiowps_feature_mgr->output_feature_details_badge("scan-file-change-detection");
        ?>

        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-scheduled-fcd-scan-nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Enable Automated File Change Detection Scan', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_enable_automated_fcd_scan" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_enable_automated_fcd_scan')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want the system to automatically/periodically scan your files to check for file changes based on the settings below', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row"><?php _e('Scan Time Interval', 'aiowpsecurity')?>:</th>
                <td><input size="5" name="aiowps_fcd_scan_frequency" value="<?php echo $aio_wp_security->configs->get_value('aiowps_fcd_scan_frequency'); ?>" />
                    <select id="backup_interval" name="aiowps_fcd_scan_interval">
                        <option value="0" <?php selected( $aio_wp_security->configs->get_value('aiowps_fcd_scan_interval'), '0' ); ?>><?php _e( 'Hours', 'aiowpsecurity' ); ?></option>
                        <option value="1" <?php selected( $aio_wp_security->configs->get_value('aiowps_fcd_scan_interval'), '1' ); ?>><?php _e( 'Days', 'aiowpsecurity' ); ?></option>
                        <option value="2" <?php selected( $aio_wp_security->configs->get_value('aiowps_fcd_scan_interval'), '2' ); ?>><?php _e( 'Weeks', 'aiowpsecurity' ); ?></option>
                    </select>
                <span class="description"><?php _e('Set the value for how often you would like a scan to occur', 'aiowpsecurity'); ?></span>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('File Types To Ignore', 'aiowpsecurity')?>:</th>
                <td><textarea name="aiowps_fcd_exclude_filetypes" rows="5" cols="50"><?php echo $aio_wp_security->configs->get_value('aiowps_fcd_exclude_filetypes'); ?></textarea>
                    <br />
                    <span class="description"><?php _e('Enter each file type or extension on a new line which you wish to exclude from the file change detection scan.', 'aiowpsecurity'); ?></span>
                    <span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More Info', 'aiowpsecurity'); ?></span></span>
                    <div class="aiowps_more_info_body">
                            <?php 
                            echo '<p class="description">'.__('You can exclude file types from the scan which would not normally pose any security threat if they were changed. These can include things such as image files.', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('Example: If you want the scanner to ignore files of type jpg, png, and bmp, then you would enter the following:', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('jpg', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('png', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('bmp', 'aiowpsecurity').'</p>';
                            ?>
                    </div>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Files/Directories To Ignore', 'aiowpsecurity')?>:</th>
                <td><textarea name="aiowps_fcd_exclude_files" rows="5" cols="50"><?php echo $aio_wp_security->configs->get_value('aiowps_fcd_exclude_files'); ?></textarea>
                    <br />
                    <span class="description"><?php _e('Enter each file or directory on a new line which you wish to exclude from the file change detection scan.', 'aiowpsecurity'); ?></span>
                    <span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More Info', 'aiowpsecurity'); ?></span></span>
                    <div class="aiowps_more_info_body">
                            <?php 
                            echo '<p class="description">'.__('You can exclude specific files/directories from the scan which would not normally pose any security threat if they were changed. These can include things such as log files.', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('Example: If you want the scanner to ignore certain files in different directories or whole directories, then you would enter the following:', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('cache/config/master.php', 'aiowpsecurity').'</p>';
                            echo '<p class="description">'.__('somedirectory', 'aiowpsecurity').'</p>';
                            ?>
                    </div>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Send Email When Change Detected', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_send_fcd_scan_email" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_send_fcd_scan_email')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want the system to email you if a file change was detected', 'aiowpsecurity'); ?></span>
                <br /><input size="40" name="aiowps_fcd_scan_email_address" value="<?php echo $aio_wp_security->configs->get_value('aiowps_fcd_scan_email_address'); ?>" />
                <span class="description"><?php _e('Enter an email address', 'aiowpsecurity'); ?></span>
                </td>
            </tr>            
        </table>
        <input type="submit" name="aiowps_schedule_fcd_scan" value="<?php _e('Save Settings', 'aiowpsecurity')?>" class="button-primary" />
        </form>
        </div></div>
        
        <?php
    }
    
    /*
     * Outputs the last scan results in a postbox
     */
    function display_last_scan_results()
    {
        global $wpdb, $aio_wp_security;
        //Let's get the results array from the DB
        $query = "SELECT * FROM ".AIOWPSEC_TBL_GLOBAL_META_DATA." WHERE meta_key1='file_change_detection'";
        $scan_db_data = $wpdb->get_row($query, ARRAY_A);
        if ($scan_db_data === NULL)
        {
            //TODO: Failure scenario
            $aio_wp_security->debug_logger->log_debug("display_last_scan_results() - DB query for scan results data from global meta table returned NULL!",4);
            return;
        }
        $date_last_scan = $scan_db_data['date_time'];
        $scan_results_unserialized = maybe_unserialize($scan_db_data['meta_value5']);
        ?>
        <div class="postbox">
        <h3><label for="title"><?php _e('Latest File Change Scan Results', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <?php
        $files_added_output = "";
        $files_removed_output = "";
        $files_changed_output = "";
        if (!empty($scan_results_unserialized['files_added']))
        {
            //Output table of files added
            echo '<div class="aio_info_with_icon aio_spacer_10_tb">'.__('The following files were added to your host.', 'aiowpsecurity').'</div>';
            $files_added_output .= '<table class="widefat">';
            $files_added_output .= '<tr>';
            $files_added_output .= '<th>'.__('File','aiowpsecurity').'</th>';
            $files_added_output .= '<th>'.__('File Size','aiowpsecurity').'</th>';
            $files_added_output .= '<th>'.__('File Modified','aiowpsecurity').'</th>';
            $files_added_output .= '</tr>';
            foreach ($scan_results_unserialized['files_added'] as $key=>$value) {
                $files_added_output .= '<tr>';
                $files_added_output .= '<td>'.$key.'</td>';
                $files_added_output .= '<td>'.$value['filesize'].'</td>';
                $files_added_output .= '<td>'.date('Y-m-d H:i:s',$value['last_modified']).'</td>';
                $files_added_output .= '</tr>';
            }
            $files_added_output .= '</table>';
            echo $files_added_output;
        }
        echo '<div class="aio_spacer_15"></div>';
        if (!empty($scan_results_unserialized['files_removed']))
        {
            //Output table of files removed
            echo '<div class="aio_info_with_icon aio_spacer_10_tb">'.__('The following files were removed from your host.', 'aiowpsecurity').'</div>';
            $files_removed_output .= '<table class="widefat">';
            $files_removed_output .= '<tr>';
            $files_removed_output .= '<th>'.__('File','aiowpsecurity').'</th>';
            $files_removed_output .= '<th>'.__('File Size','aiowpsecurity').'</th>';
            $files_removed_output .= '<th>'.__('File Modified','aiowpsecurity').'</th>';
            $files_removed_output .= '</tr>';
            foreach ($scan_results_unserialized['files_removed'] as $key=>$value) {
                $files_removed_output .= '<tr>';
                $files_removed_output .= '<td>'.$key.'</td>';
                $files_removed_output .= '<td>'.$value['filesize'].'</td>';
                $files_removed_output .= '<td>'.date('Y-m-d H:i:s',$value['last_modified']).'</td>';
                $files_removed_output .= '</tr>';
            }
            $files_removed_output .= '</table>';
            echo $files_removed_output;
            
        }

        echo '<div class="aio_spacer_15"></div>';

        if (!empty($scan_results_unserialized['files_changed']))
        {
            //Output table of files changed
            echo '<div class="aio_info_with_icon aio_spacer_10_tb">'.__('The following files were changed on your host.', 'aiowpsecurity').'</div>';
            $files_changed_output .= '<table class="widefat">';
            $files_changed_output .= '<tr>';
            $files_changed_output .= '<th>'.__('File','aiowpsecurity').'</th>';
            $files_changed_output .= '<th>'.__('File Size','aiowpsecurity').'</th>';
            $files_changed_output .= '<th>'.__('File Modified','aiowpsecurity').'</th>';
            $files_changed_output .= '</tr>';
            foreach ($scan_results_unserialized['files_changed'] as $key=>$value) {
                $files_changed_output .= '<tr>';
                $files_changed_output .= '<td>'.$key.'</td>';
                $files_changed_output .= '<td>'.$value['filesize'].'</td>';
                $files_changed_output .= '<td>'.date('Y-m-d H:i:s',$value['last_modified']).'</td>';
                $files_changed_output .= '</tr>';
            }
            $files_changed_output .= '</table>';
            echo $files_changed_output;
        }
        
        ?>
        </div></div>
        <?php
    }    
} //end class