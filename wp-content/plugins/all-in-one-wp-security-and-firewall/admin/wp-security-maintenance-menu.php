<?php

class AIOWPSecurity_Maintenance_Menu extends AIOWPSecurity_Admin_Menu
{
    var $menu_page_slug = AIOWPSEC_MAINTENANCE_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs = array(
        'tab1' => 'Visitor Lockout', 
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
        global $aio_wp_security;
        $maint_msg = '';
        if(isset($_POST['aiowpsec_save_site_lockout']))
        {
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'aiowpsec-site-lockout'))
            {
                $aio_wp_security->debug_logger->log_debug("Nonce check failed on site lockout feature settings save!",4);
                die("Nonce check failed on site lockout feature settings save!");
            }
            
            //Save settings
            $aio_wp_security->configs->set_value('aiowps_site_lockout',isset($_POST["aiowps_site_lockout"])?'1':'');
            $maint_msg = htmlentities(stripslashes($_POST['aiowps_site_lockout_msg']), ENT_COMPAT, "UTF-8");
            $aio_wp_security->configs->set_value('aiowps_site_lockout_msg',$maint_msg);//Text area/msg box
            $aio_wp_security->configs->save_config();

            $this->show_msg_updated(__('Site lockout feature settings saved!', 'aiowpsecurity'));

        }
        ?>
        <div class="postbox">
        <h3><label for="title"><?php _e('General Visitor Lockout', 'aiowpsecurity'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('aiowpsec-site-lockout'); ?>
        <div class="aio_blue_box">
            <?php
            echo '<p>'.__('This feature allows you to put your site into "maintenance mode" by locking down the front-end to all visitors except logged in users with super admin privileges.', 'aiowpsecurity').'</p>';
            echo '<p>'.__('Locking your site down to general visitors can be useful if you are investigating some issues on your site or perhaps you might be doing some maintenance and wish to keep out all traffic for security reasons.', 'aiowpsecurity').'</p>';
            ?>
        </div>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Enable Front-end Lockout', 'aiowpsecurity')?>:</th>                
                <td>
                <input name="aiowps_site_lockout" type="checkbox"<?php if($aio_wp_security->configs->get_value('aiowps_site_lockout')=='1') echo ' checked="checked"'; ?> value="1"/>
                <span class="description"><?php _e('Check this if you want all visitors except those who are logged in as administrator to be locked out of the front-end of your site.', 'aiowpsecurity'); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Enter a Message:', 'aiowpsecurity')?></th>
                <td>
                    <?php
                    $aiowps_site_lockout_msg_raw = $aio_wp_security->configs->get_value('aiowps_site_lockout_msg');
                    if(empty($aiowps_site_lockout_msg_raw)){
                        $aiowps_site_lockout_msg_raw = 'This site is currently not available. Please try again later.';
                    }
                    $aiowps_site_lockout_msg = html_entity_decode($aiowps_site_lockout_msg_raw, ENT_COMPAT, "UTF-8");
                    $aiowps_site_lockout_msg_settings = array('textarea_name' => 'aiowps_site_lockout_msg', 'media_buttons' => false);
                    wp_editor($aiowps_site_lockout_msg, "aiowps_site_lockout_msg_editor_content", $aiowps_site_lockout_msg_settings);                    
                    ?>
                    <br />
                    <span class="description"><?php _e('Enter a message you wish to display to visitors when your site is in maintenance mode.','aiowpsecurity');?></span>
                </td>
            </tr>

        </table>
    
        <div class="submit">
            <input type="submit" class="button-primary" name="aiowpsec_save_site_lockout" value="<?php _e('Save Site Lockout Settings'); ?>" />
        </div>
        </form>   
        </div></div>
        <?php
    }
} //end class