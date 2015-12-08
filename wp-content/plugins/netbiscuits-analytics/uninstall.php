<?php

// check for the WP global indicating the user has deactivated, and is now deleting, the plugin
if ( defined('WP_UNINSTALL_PLUGIN') ) {

    $nb_code_field_name = 'nb_analytics_code';

    // delete any existing cached version of the code
    delete_transient( $nb_code_field_name );

    // delete key/value from the database
    delete_option( $nb_code_field_name );

}

// end of file