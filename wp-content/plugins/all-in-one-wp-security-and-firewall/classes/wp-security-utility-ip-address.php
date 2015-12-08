<?php

class AIOWPSecurity_Utility_IP
{
    function __construct(){
        //NOP
    }
    
    static function get_user_ip_address()
    {
        //Let's try getting the headers if possible
        if ( function_exists( 'apache_request_headers' ) ) {
            $headers = apache_request_headers(); 
        } else { 
            $headers = $_SERVER;
        }

        //Get the forwarded IP if it exists
        if (array_key_exists( 'X-Forwarded-For', $headers) ) {
            $userIP = $headers['X-Forwarded-For'];
        } else {
            $userIP = $_SERVER['REMOTE_ADDR'];
        }
        return $userIP;
    }
    
     /*
     * Returns the first three octets of a sanitized IP address so it can used as an IP address range
     */
    static function get_sanitized_ip_range($ip)
    {
        global $aio_wp_security;
        //$ip = AIOWPSecurity_Utility_IP::get_user_ip_address(); //Get the IP address of user
        $ip_range = '';
        $valid_ip = filter_var($ip, FILTER_VALIDATE_IP); //Sanitize the IP address
        if ($valid_ip)
        {
            $ip_range = substr($valid_ip, 0 , strrpos ($valid_ip, ".")); //strip last portion of address to leave an IP range
        }
        else
        {
            //Write log if the 'REMOTE_ADDR' contains something which is not an IP
            $aio_wp_security->debug_logger->log_debug("AIOWPSecurity_Utility_IP - Invalid IP received ".$ip,4);
        }
        return $ip_range;
    }

    
    static function create_ip_list_array_from_string_with_newline($ip_addresses)
    {
        $ip_list_array = explode(PHP_EOL, $ip_addresses);
        return $ip_list_array;
    }
    
    static function validate_ip_list($ip_list_array, $list='blacklist')
    {
        @ini_set('auto_detect_line_endings', true);
        $errors = '';

        //validate list
        $submitted_ips = $ip_list_array;
        $list = array();

        if(!empty($submitted_ips))
        {
            foreach($submitted_ips as $item) 
            {
                $item = filter_var($item, FILTER_SANITIZE_STRING);
                if (strlen( $item ) > 0) 
                {
                    $ipParts = explode('.', $item);
                    $isIP = 0;
                    $partcount = 1;
                    $goodip = true;
                    $foundwild = false;
                    
                    if (count($ipParts) < 2)
                    {
                        $errors .= '<p>'.$item.__(' is not a valid ip address format.', 'aiowpsecurity').'</p>';
                        continue;
                    }

                    foreach ($ipParts as $part) 
                    {
                        if ($goodip == true) 
                        {
                            if ((is_numeric(trim($part)) && trim($part) <= 255 && trim($part) >= 0) || trim($part) == '*') 
                            {
                                $isIP++;
                            }

                            switch ($partcount) 
                            {
                                case 1:
                                    if (trim($part) == '*') 
                                    {
                                        $goodip = false;
                                        $errors .= '<p>'.$item.__(' is not a valid ip address format.', 'aiowpsecurity').'</p>';
                                    }
                                    break;
                                case 2:
                                    if (trim($part) == '*')
                                    {
                                        $foundwild = true;
                                    }
                                    break;
                                default:
                                    if (trim($part) != '*') 
                                    {
                                        if ($foundwild == true) 
                                        {
                                            $goodip = false;
                                            $errors .= '<p>'.$item.__(' is not a valid ip address format.', 'aiowpsecurity').'</p>';
                                        }
                                    }
                                    else 
                                    {
                                        $foundwild = true;	
                                    }
                                    break;
                            }

                            $partcount++;
                        }
                    }
                    if (ip2long(trim(str_replace('*', '0', $item))) == false) 
                    { //invalid ip 
                        $errors .= '<p>'.$item.__(' is not a valid ip address format.', 'aiowpsecurity').'</p>';
                    } 
                    elseif (strlen($item > 4 && !in_array($item, $list))) 
                    {
                        $current_user_ip = AIOWPSecurity_Utility_IP::get_user_ip_address();
                        if ($current_user_ip == $item && $list == 'blacklist')
                        {
                            //You can't ban your own IP
                            $errors .= '<p>'.__('You cannot ban your own IP address: ', 'aiowpsecurity').$item.'</p>';
                        }
                        else
                        {
                            $list[] = trim($item);
                        }
                    }
                }
            }
        }
        else{
            //This function was called with an empty IP address array list
        }

        if (strlen($errors)> 0)
        {
            $return_payload = array(-1, array($errors));
            return $return_payload;
        }
        
        if (sizeof($list) >= 1) 
        {
            sort($list);
            $list = array_unique($list, SORT_STRING);
            
            $return_payload = array(1, $list);
            return $return_payload;
	}

        $return_payload = array(1, array());
        return $return_payload;
    }    
}