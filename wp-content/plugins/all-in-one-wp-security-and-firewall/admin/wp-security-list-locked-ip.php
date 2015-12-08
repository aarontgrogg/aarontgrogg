<?php
class AIOWPSecurity_List_Locked_IP extends AIOWPSecurity_List_Table {
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'item',     //singular name of the listed records
            'plural'    => 'items',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }

    function column_default($item, $column_name){
    	return $item[$column_name];
    }
        
    function column_failed_login_ip($item){
        //$tab = strip_tags($_REQUEST['tab']);
        //Build row actions
        $actions = array(
            'unlock' => sprintf('<a href="admin.php?page=%s&action=%s&lockdown_id=%s" onclick="return confirm(\'Are you sure you want to unlock this address range?\')">Unlock</a>',AIOWPSEC_USER_LOGIN_MENU_SLUG,'unlock_ip',$item['id']),
            'delete' => sprintf('<a href="admin.php?page=%s&action=%s&lockdown_id=%s" onclick="return confirm(\'Are you sure you want to delete this item?\')">Delete</a>',AIOWPSEC_USER_LOGIN_MENU_SLUG,'delete_blocked_ip',$item['id']),
        );
        
        //Return the user_login contents
        return sprintf('%1$s <span style="color:silver"></span>%2$s',
            /*$1%s*/ $item['failed_login_ip'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
       );
    }
    
    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox
            'failed_login_ip' => 'Locked IP Range',
            'user_id' => 'User ID',
            'user_login' => 'Username',
            'lockdown_date' => 'Date Locked',
            'release_date' => 'Release Date'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'failed_login_ip' => array('failed_login_ip',false),
            'user_id' => array('user_id',false),
            'user_login' => array('user_login',false),
            'lockdown_date' => array('lockdown_date',false),
            'release_date' => array('release_date',false)
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'unlock' => 'Unlock',
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {
        if('delete'===$this->current_action()) 
        {//Process delete bulk actions
            if(!isset($_REQUEST['item']))
            {
                AIOWPSecurity_Admin_Menu::show_msg_error_st(__('Please select some records using the checkboxes','aiowpsecurity'));
            }else 
            {            
                $this->delete_lockdown_records(($_REQUEST['item']));
            }
        }

        if('unlock'===$this->current_action()) 
        {//Process unlock bulk actions
            if(!isset($_REQUEST['item']))
            {
                AIOWPSecurity_Admin_Menu::show_msg_error_st(__('Please select some records using the checkboxes','aiowpsecurity'));
            }else 
            {            
                $this->unlock_ip_range(($_REQUEST['item']));
            }
        }
    }
    
    
    /*
     * This function will unlock an IP range by modifying the "release_date" column of a record in the "login_lockdown" table
     */
    function unlock_ip_range($entries)
    {
        global $wpdb;
        $lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
        if (is_array($entries))
        {
            //Unlock multiple records
            $id_list = "(" .implode(",",$entries) .")"; //Create comma separate list for DB operation
            $unlock_command = "UPDATE ".$lockdown_table." SET release_date = now() WHERE id IN ".$id_list;
            $result = $wpdb->query($unlock_command);
            if($result != NULL)
            {
                AIOWPSecurity_Admin_Menu::show_msg_updated_st(__('The selected IP ranges were unlocked successfully!','aiowpsecurity'));
            }
        } elseif ($entries != NULL)
        {
            //Delete single record
            $unlock_command = "UPDATE ".$lockdown_table." SET release_date = now() WHERE id = '".absint($entries)."'";
            $result = $wpdb->query($unlock_command);
            if($result != NULL)
            {
                AIOWPSecurity_Admin_Menu::show_msg_updated_st(__('The selected IP range was unlocked successfully!','aiowpsecurity'));
            }
        }
    }
    
    /*
     * This function will delete selected records from the "login_lockdown" table.
     * The function accepts either an array of IDs or a single ID
     */
    function delete_lockdown_records($entries)
    {
        global $wpdb;
        $lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
        if (is_array($entries))
        {
            //Delete multiple records
            $id_list = "(" .implode(",",$entries) .")"; //Create comma separate list for DB operation
            $delete_command = "DELETE FROM ".$lockdown_table." WHERE id IN ".$id_list;
            $result = $wpdb->query($delete_command);
            if($result != NULL)
            {
                AIOWPSecurity_Admin_Menu::show_msg_record_deleted_st();
            }
        } 
        elseif ($entries != NULL)
        {
            //Delete single record
            $delete_command = "DELETE FROM ".$lockdown_table." WHERE id = '".absint($entries)."'";
            $result = $wpdb->query($delete_command);
            if($result != NULL)
            {
                AIOWPSecurity_Admin_Menu::show_msg_record_deleted_st();
            }
        }
    }
    
    function prepare_items() {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
    	
    	global $wpdb;
        $lockdown_table_name = AIOWPSEC_TBL_LOGIN_LOCKDOWN;

	/* -- Ordering parameters -- */
	    //Parameters that are going to be used to order the result
	$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'lockdown_date';
	$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'DESC';

	$data = $wpdb->get_results("SELECT * FROM $lockdown_table_name WHERE release_date > now() ORDER BY $orderby $order", ARRAY_A);
        //$data = $wpdb->get_results("SELECT ID, floor((UNIX_TIMESTAMP(release_date)-UNIX_TIMESTAMP(now()))/60) AS minutes_left, ".
	//				"failed_login_IP FROM $lockdown_table_name WHERE release_date > now()", ARRAY_A);
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}