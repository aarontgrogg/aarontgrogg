<?php
/*
   Plugin Name: Netbiscuits Analytics Plugin
   Description: Add Netbiscuits' Mobile Analytics Code to your site with just one copy, paste and click.
   Version: 1.5
   Author: Netbiscuits, GmbH
   Text Domain: nb_analytics_code
*/


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )  {
    exit;
};


// For testing, prior to deployment; delete before deploying
//define('WP_DEBUG', TRUE);


/**
 * Netbiscuits Analytics Page class
 */
class nb_analytics_options_page {


    // cache these for later
    protected $hidden_field_name  = 'nb_analytics_submit_hidden';
    protected $nb_code_field_name = 'nb_analytics_code';


    /**
     * Add Action to add the Admin Menu links
     */
    function __construct() {
        if ( !is_admin() ) {
            // also do not include this for Visitor ID Demo
            if ( strpos( $_SERVER['REQUEST_URI'], 'visitor-id-demo' ) === false ) {
                add_action( 'wp_footer', array( $this, 'nb_analytics_add_code_to_page' ) );
            }
        } else {
            add_action( 'admin_menu', array( $this, 'nb_add_menu_items' ) );
        }
    } // __construct


    /**
     * Add Admin Menu links
     */
    public function nb_add_menu_items() {

        // set-up localitzation
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain( $this->nb_code_field_name, false, $plugin_dir );

        // init admin page
        add_options_page( 'Netbiscuits Analytics Settings', 'NB Analytics', 'manage_options', 'netbiscuits-analytics', array( $this, 'nb_add_admin_page' ) );

    } // nb_add_menu_items


    /**
     * Add Admin Settings page
     */
    public function nb_add_admin_page () {

        // cache field name values locally
        $hidden_field_name  = $this->hidden_field_name;
        $nb_code_field_name = $this->nb_code_field_name;

        // verify the logged-in user has the proper permission to see this page
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __('You do not have sufficient permissions to access this page.', $nb_code_field_name ) );
        }

        // placeholder for "Saved" message
        $msg = '';

        // if the form has been submitted...
        if ( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

            // verify the existence of the nonce field
            if ( !isset( $_POST[$nb_code_field_name.'_nonce'] ) || !wp_verify_nonce( $_POST[$nb_code_field_name.'_nonce'], $nb_code_field_name.'_action' ) ) {
                wp_die( __('You do not have sufficient permissions to access this page.', $nb_code_field_name ) );
            }

            // update the the database...
            update_option( $nb_code_field_name, $_POST[ $nb_code_field_name ] );

            // delete any existing cached version
            delete_transient( $this->nb_code_field_name );

            // and notify the user
            $msg = '<div class="updated"><p>' . __('The code has been saved.', $nb_code_field_name ) . '</p></div>'.PHP_EOL;

        }

        // get the current code from the database
        $nb_analytics_code = stripslashes( esc_textarea( get_option( $nb_code_field_name ) ) );

        // generate Settings page to collect and save the code
    ?>
        <div class="wrap">
            <style>
                #<?php echo $nb_code_field_name; ?> {
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;
                    display: block;
                    margin: 0 0 10px;
                    padding: .5em;
                    width: 100%;
                    height: 300px;
                    overflow: auto;
                    font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
                    color: #68615e;
                    font-size: 13px;
                    line-height: 1.4;
                    white-space: pre-wrap;
                    -webkit-text-size-adjust: none;
                    word-break: break-all;
                    word-wrap: break-word;
                    background: #f1efee;
                    border: 1px solid #cccccc;
                    border-radius: 4px;
                }
            </style>
            <div class="metabox-holder">
                <?php echo $msg; ?>
                <form name="configurable_urls_options" method="post" action="">
                    <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
                    <div id="general" class="postbox">
                        <h3 class="hndle">
                            <p>Netbiscuits Analytics Settings</p>
                        </h3>
                        <div class="inside">
                            <p>
                                <?php echo __('To add your Netbiscuits Analytics tracking code to this website,
                                please <a href="https://my.netbiscuits.com/account-mgmt/detectioncode" title="This link will open your Netbiscuits Detection Code page, in a new window" target="_blank">copy the code from your account</a>
                                and paste it below, then click the <strong>Save Changes</strong> buttons.', $nb_code_field_name ); ?>
                            </p>
                            <label for="<?php echo $nb_code_field_name; ?>"><?php echo __('Paste Code Here', $nb_code_field_name ); ?>:</label>
                            <textarea id="<?php echo $nb_code_field_name; ?>" name="<?php echo $nb_code_field_name; ?>"><?php echo $nb_analytics_code; ?></textarea>
                            <p>
                                <?php wp_nonce_field( $nb_code_field_name.'_action', $nb_code_field_name.'_nonce'); ?>
                                <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
                            </p>
                        </div>
                    </div>
                    <script>
                        if (jQuery) {
                            jQuery('#<?php echo $nb_code_field_name; ?>').on('focus', function(){
                                jQuery(this).select();
                            });
                        }
                    </script>
                </form>
            </div>
        </div>
        <?php
    } // nb_add_admin_page


    /**
     * Add code to footer of page
     */
    public function nb_analytics_add_code_to_page() {

        // cache field name value locally
        $nb_code_field_name = $this->nb_code_field_name;

        // check if we already have the code cached
        $nb_analytics_code = get_transient( $nb_code_field_name );

        // if not, fetch it and create cache
        if ( $nb_analytics_code === false ) {

            // get the current code from the database
            $nb_analytics_code = stripslashes( get_option( $nb_code_field_name ) );

            // now cache the code for a faster next page view (YEAR_IN_SECONDS is a WP global value)
            set_transient( $nb_code_field_name, $nb_analytics_code, YEAR_IN_SECONDS );

        }

        // write the code to the page
        echo $nb_analytics_code . PHP_EOL;

    } // nb_analytics_add_code_to_page


} // nb_analytics_options_page


new nb_analytics_options_page;
// end of file