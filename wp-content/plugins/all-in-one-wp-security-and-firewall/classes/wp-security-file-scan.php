<?php
class AIOWPSecurity_Filescan
{

    function __construct() 
    {
        add_action('aiowps_perform_fcd_scan_tasks', array(&$this, 'aiowps_scheduled_fcd_scan_handler'));
    }
    
    /**
     * This function will recursively scan through all directories starting from the specified location
     * It will store the path/filename, last_modified and filesize values in a multi-dimensional associative array
     */
    function execute_file_change_detection_scan() 
    {
        global $aio_wp_security;
        $scan_result = array();
        if($this->has_scan_data()){
            $scanned_data = $this->do_file_change_scan(); //Scan the filesystem and get details
            $last_scan_data = $this->get_last_scan_data();
            $scan_result = $this->compare_scan_data($last_scan_data,$scanned_data);
            $scan_result['initial_scan'] = '';
            $this->save_scan_data_to_db($scanned_data, 'update', $scan_result);
            if (!empty($scan_result['files_added']) || !empty($scan_result['files_removed']) || !empty($scan_result['files_changed'])){
                //This means there was a change detected
                $aio_wp_security->configs->set_value('aiowps_fcds_change_detected', TRUE);
                $aio_wp_security->configs->save_config();
                $aio_wp_security->debug_logger->log_debug("File Change Detection Feature: change to filesystem detected!");
                
                $this->aiowps_send_file_change_alert_email(); //Send file change scan results via email if applicable
            }
            return $scan_result;
        }
        else{
            $scanned_data = $this->do_file_change_scan();
            $this->save_scan_data_to_db($scanned_data);
            $scan_result['initial_scan'] = '1';
            return $scan_result;
        }
    }
    
    function aiowps_send_file_change_alert_email()
    {
        global $aio_wp_security;
        if ( $aio_wp_security->configs->get_value('aiowps_send_fcd_scan_email') == '1' ) 
        {
            //Get the right email address.
            if ( is_email( $aio_wp_security->configs->get_value('aiowps_fcd_scan_email_address') ) ) 
            {
                    $toaddress = $aio_wp_security->configs->get_value('aiowps_fcd_scan_email_address');
            } else 
            {
                    $toaddress = get_site_option( 'admin_email' );
            }

            $to = $toaddress;
            $headers = 'From: ' . get_option( 'blogname' ) . ' <' . $to . '>' . PHP_EOL;
            $subject = __( 'All In One WP Security - File change detected!', 'aiowpsecurity' ) . ' ' . date( 'l, F jS, Y \a\\t g:i a', current_time( 'timestamp' ) );
            //$attachment = array();
            $message = __( 'A file change was detected on your system for site URL', 'aiowpsecurity' ) . ' ' . get_option( 'siteurl' ) . __( '. Scan was generated on', 'aiowpsecurity' ) . ' ' . date( 'l, F jS, Y \a\\t g:i a', current_time( 'timestamp' ) );
            $message .= "\r\n".__( 'Login to your site to view the scan details.', 'aiowpsecurity' );

            wp_mail( $to, $subject, $message, $headers );
        }
    }
    
    function aiowps_scheduled_fcd_scan_handler()
    {
        global $aio_wp_security;
        if($aio_wp_security->configs->get_value('aiowps_enable_automated_fcd_scan')=='1')
        {
            $aio_wp_security->debug_logger->log_debug_cron("Filescan - Scheduled fcd_scan is enabled. Checking now to see if scan needs to be done...");
            $current_time = strtotime(current_time('mysql'));
            $fcd_scan_frequency = $aio_wp_security->configs->get_value('aiowps_fcd_scan_frequency'); //Number of hours or days or months interval
            $interval_setting = $aio_wp_security->configs->get_value('aiowps_fcd_scan_interval'); //Hours/Days/Months
            switch($interval_setting)
            {
                case '0':
                    $interval = 'hours';
                    break;
                case '1':
                    $interval = 'days';
                    break;
                case '2':
                    $interval = 'weeks';
                    break;
            }
            $last_fcd_scan_time = $aio_wp_security->configs->get_value('aiowps_last_fcd_scan_time');
            if ($last_fcd_scan_time != NULL)
            {
                $last_fcd_scan_time = strtotime($aio_wp_security->configs->get_value('aiowps_last_fcd_scan_time'));
                $next_fcd_scan_time = strtotime("+".abs($fcd_scan_frequency).$interval, $last_fcd_scan_time);
                if ($next_fcd_scan_time <= $current_time)
                {
                    //It's time to do a filescan
                    $result = $this->execute_file_change_detection_scan(ABSPATH);
//                    if ($result)
//                    {
                        $aio_wp_security->configs->set_value('aiowps_last_fcd_scan_time', current_time('mysql'));
                        $aio_wp_security->configs->save_config();
                        $aio_wp_security->debug_logger->log_debug_cron("Filescan - Scheduled filescan was successfully completed.");
//                    } 
//                    else
//                    {
//                        $aio_wp_security->debug_logger->log_debug_cron("Filescan - Scheduled filescan operation failed!",4);
//                    }
                }
            }
            else
            {
                //Set the last scan time to now so it can trigger for the next scheduled period
                $aio_wp_security->configs->set_value('aiowps_last_fcd_scan_time', current_time('mysql'));
                $aio_wp_security->configs->save_config();
            }
        }
    }
    
    /* Returns true if there is at least one previous scaned data in the DB. False otherwise */
    function has_scan_data()
    {
        global $wpdb;
        //For scanced data the meta_key1 column valu is 'file_change_detection', meta_value1 column value is 'file_scan_data'. Then the data is stored in meta_value4 column.
        $aiowps_global_meta_tbl_name = AIOWPSEC_TBL_GLOBAL_META_DATA;
        $resultset = $wpdb->get_row("SELECT * FROM $aiowps_global_meta_tbl_name WHERE meta_key1 = 'file_change_detection' AND meta_value1='file_scan_data'", OBJECT);
        if($resultset){
            $scan_data = maybe_unserialize($resultset->meta_value4);
            if(!empty($scan_data)){
                return true;
            }
        }
        return false;
    }
    
    function get_last_scan_data()
    {
        global $wpdb;
        //For scanned data the meta_key1 column valu is 'file_change_detection', meta_value1 column value is 'file_scan_data'. Then the data is stored in meta_value4 column.
        $aiowps_global_meta_tbl_name = AIOWPSEC_TBL_GLOBAL_META_DATA;
        $resultset = $wpdb->get_row("SELECT * FROM $aiowps_global_meta_tbl_name WHERE meta_key1 = 'file_change_detection' AND meta_value1='file_scan_data'", OBJECT);
        if($resultset){
            $scan_data = maybe_unserialize($resultset->meta_value4);
            return $scan_data;
        }
        return array(); //return empty array if no old scan data
    }
    
    function save_scan_data_to_db($scanned_data, $save_type = 'insert', $scan_result = array())
    {
        global $wpdb, $aio_wp_security;
        $result = '';
        //For scanned data the meta_key1 column value is 'file_change_detection', meta_value1 column value is 'file_scan_data'. Then the data is stored in meta_value4 column.
        $aiowps_global_meta_tbl_name = AIOWPSEC_TBL_GLOBAL_META_DATA;
        $payload = serialize($scanned_data);
        $scan_result = serialize($scan_result);
        $date_time = current_time('mysql');
        $data = array('date_time' => $date_time, 'meta_key1' => 'file_change_detection', 'meta_value1' => 'file_scan_data', 'meta_value4' => $payload, 'meta_key5' => 'last_scan_result', 'meta_value5' => $scan_result);
        if($save_type == 'insert'){
            $result = $wpdb->insert($aiowps_global_meta_tbl_name, $data);
        }
        else{
            $where = array('meta_key1' => 'file_change_detection', 'meta_value1' => 'file_scan_data');
            $result = $wpdb->update($aiowps_global_meta_tbl_name, $data, $where);
            
        }
        if ($result === false){
            $aio_wp_security->debug_logger->log_debug("save_scan_data_to_db() - Error inserting data to DB!",4);
            return false;
        }else{
            return true;
        }
    }
    
    function do_file_change_scan($start_dir=ABSPATH)
    {
        global $aio_wp_security;
        $filescan_data = array();
        $dit = new RecursiveDirectoryIterator($start_dir);
        $rit = new RecursiveIteratorIterator(
            $dit, RecursiveIteratorIterator::SELF_FIRST);
        
        $file_types_to_skip = $aio_wp_security->configs->get_value('aiowps_fcd_exclude_filetypes');

        foreach ($rit as $fileinfo) {
            if ($fileinfo->isDir()) continue; //skip directories
            if ($fileinfo->getFilename() == 'wp-security-log-cron-job.txt' || $fileinfo->getFilename() == 'wp-security-log.txt') continue; //skip aiowps log files
            //Let's omit any file types from the scan which were specified in the settings if necessary
            if (!empty($file_types_to_skip)){
                $file_types_to_skip = strtolower($file_types_to_skip);
                //$current_file_ext = strtolower($fileinfo->getExtension()); //getExtension() only available on PHP 5.3.6 or higher
                $ext = pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION);
                $current_file_ext = strtolower($ext);
                if (!empty($current_file_ext)){
                    if (strpos($file_types_to_skip, $current_file_ext) !== FALSE) continue;
                }
            }
            //Let's omit specific files or directories from the scan which were specified in the settings
            $filename = $fileinfo->getPathname();
            $files_to_skip = $aio_wp_security->configs->get_value('aiowps_fcd_exclude_files');
            if (!empty($files_to_skip))
            {
                $file_array = explode(PHP_EOL, $files_to_skip);
                $skip_this = FALSE;
                foreach ($file_array as $f_or_dir)
                {
                    if (strpos($filename, trim($f_or_dir)) !== FALSE){
                        $skip_this = TRUE;
                    } 
                }
                if ($skip_this) continue;
            }
            $filescan_data[$filename] = array();
            $filescan_data[$filename]['last_modified'] = $fileinfo->getMTime();
            $filescan_data[$filename]['filesize'] = $fileinfo->getSize();

        }
        return $filescan_data; 
    }
    
    function compare_scan_data($last_scan_data, $new_scanned_data)
    {
        $files_added = @array_diff_assoc( $new_scanned_data, $last_scan_data ); //Identify new files added: get all files which are in the new scan but not present in the old scan
        $files_removed = @array_diff_assoc( $last_scan_data, $new_scanned_data ); //Identify files deleted : get all files which are in the old scan but not present in the new scan
        $new_scan_minus_added = @array_diff_key( $new_scanned_data, $files_added ); //Get all files in current scan which were not newly added
        $old_scan_minus_deleted = @array_diff_key( $last_scan_data, $files_removed );  //Get all files in old scan which were not deleted
        $file_changes_detected = array();

        //compare file hashes and mod dates
        foreach ( $new_scan_minus_added as $entry => $key) {
            if ( array_key_exists( $entry, $old_scan_minus_deleted ) ) 
            {
                //check filesize and last_modified values
                if (strcmp($key['last_modified'], $old_scan_minus_deleted[$entry]['last_modified']) != 0 || 
                                strcmp($key['filesize'], $old_scan_minus_deleted[$entry]['filesize']) != 0) 
                {
                    $file_changes_detected[$entry]['filesize'] = $key['filesize'];
                    $file_changes_detected[$entry]['last_modified'] = $key['last_modified'];
                }
            }

        }

        //create single array of all changes
        $results = array(
                'files_added' => $files_added,
                'files_removed' => $files_removed,
                'files_changed' => $file_changes_detected
        );
        return $results;
    }
}