<?php
/**
 * Plugin Name: TinyMCE Valid Elements
 * Plugin URI: http://www.engfers.com
 * Description: Allows one to add "invalid" and custom HTML elements to the TinyMCE editor.
 * Version: 0.3
 * Author: David Engfer
 * Author URI: http://www.engfers.com/
 * 
 * TinyMCE Valid Elements - Wordpress plugin that will allow you to add
 * custom or "unallowed" elements to TinyMCE so that they will not be
 * replaced in your posts and pages when you edit in graphical mode.
 * 
 * Copyright (C) 2008-2009 David Engfer
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$tmve_db_version = 1.0;
$tmve_db_tablename = 'tmve_allowed_elements';
$tmve_default_attribte = '';

/**
 * Activation hook
 */
function tmve_activate() {
    global $tmve_db_version;
    
    // Test to see if this plugin has been installed before
    $installed = get_option( 'tmve_db_version' );
    if ( !$installed ){
        // Never been installed, install db
        tmve_install();
    } else if ( $installed != $tmve_db_version ){
        // Install exists and db versions differ, upgrade db
        tmve_update();
    } else {
        // Install exists, do nothing
    }
}

/**
 * Deactivation hook
 */
function tmve_deactivate() {
    global $wpdb;
    $tablename = $wpdb->prefix . 'tmve_allowed_elements';
    if ( get_option('tmve_cascade_on_deactivate') == 'true' ) {
        delete_option('tmve_db_version');
        
        $wpdb->query("DROP TABLE $tablename");
        delete_option('tmve_cascade_on_deactivate');
    }
}

/**
 * Install db tables
 */
function tmve_install(){
    global $wpdb;
    $tmve_db_version = '1.0';
    $tmve_db_tablename = 'tmve_allowed_elements';
    
    $tablename = $wpdb->prefix . $tmve_db_tablename;

    $sql = "CREATE TABLE " . $tablename . " ( 
        name varchar(30) NOT NULL,
        attribute varchar(30) NOT NULL,
        primary key (name,attribute)
    )";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if ( ! get_option('tmve_cascade_on_deactivate') ) {
        add_option('tmve_cascade_on_deactivate', "false");
    }
    
    update_option('tmve_db_version', $tmve_db_version);
}

/**
 * Future db migrations/upgrades
 */
function tmve_update(){
}

function tmve_get_element_map() {
    global $wpdb, $tmve_db_tablename, $tmve_default_attribte;
    $elements = array();
    
    $tablename = $wpdb->prefix . $tmve_db_tablename;
    
    $results = $wpdb->get_results("SELECT * FROM " . $tablename);
    
    foreach ( $results as $ele ) {
        if ( ! isset( $elements[ $ele->name ] ) ) {
            $elements[ $ele->name ] = array();
        }
        if ( $ele->attribute != $tmve_default_attribte ){
            $elements[ $ele->name ][] = $ele->attribute;
        }
    }
    
    ksort( $elements );

    foreach ( $elements as $element => $attributes ) {
        sort( $elements[ $element ] );
    }
    
    return $elements;
}

function tmve_add_admin_pages() {
    add_management_page('TinyMCE Valid Elements', 'TinyMCE Valid Elements', 10, __FILE__, 'tmve_admin_menu');
}

function is_alnum( $str ) {
    return strspn($str, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") == strlen($str);
}

function is_alpha( $str ) {
    return strspn($str, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") == strlen($str);
}

function tmve_add_element( $element = null ) {
    global $wpdb, $tmve_db_tablename, $tmve_default_attribte;
    $tablename = $wpdb->prefix . $tmve_db_tablename;
    if ( count( $wpdb->get_results( "SELECT * FROM $tablename WHERE lower(name) = lower('$element')" ) ) > 0 ) {
        return false;
    }
    
    return $wpdb->query("INSERT INTO $tablename (name, attribute) VALUES ('$element','$tmve_default_attribte')");
}

function tmve_remove_element( $element = null ) {
    global $wpdb, $tmve_db_tablename;
    $tablename = $wpdb->prefix . $tmve_db_tablename;
    return $wpdb->query("DELETE FROM $tablename WHERE lower(name) = lower('$element')");
}

function tmve_add_attribute( $element = null, $attribute = null ) {
    global $wpdb, $tmve_db_tablename, $tmve_default_attribte;
    $tablename = $wpdb->prefix . $tmve_db_tablename;
    if ( count( $wpdb->get_results( "SELECT * FROM $tablename WHERE lower(name) = lower('$element') AND lower(attribute) = lower('$attribute')" ) ) > 0 ) {
        return false;
    }
    
    return $wpdb->query("INSERT INTO $tablename (name, attribute) VALUES ('$element','$attribute')");
}

function tmve_remove_attribute( $element = null, $attribute = null ) {
    global $wpdb, $tmve_db_tablename;
    $tablename = $wpdb->prefix . $tmve_db_tablename;
    return $wpdb->query("DELETE FROM $tablename WHERE lower(name) = lower('$element') AND lower(attribute) = lower('$attribute')");
}

function tmve_do_actions() {
    $mesg = '';
    $emesg = '';
    if ( isset( $_GET[ 'add_ele' ] ) ) {
        if ( ! empty( $_GET[ 'add_ele' ] ) ) {
            if ( is_alpha( $_GET[ 'add_ele' ] ) ) {
                if ( tmve_add_element( $_GET[ 'add_ele' ] ) ) {
                    $mesg = 'Element \'' . $_GET[ 'add_ele' ] . '\' added. Refresh TinyMCE (ctrl + F5).';
                } else {
                    $emesg = 'Element \'' . $_GET[ 'add_ele' ] . '\' already exists.';
                }
            } else {
                $emesg = "New element must contain letters only.";
            }
        } else {
            $emesg = "New element can't be empty.";
        }
    } else if ( isset( $_GET[ 'rm_ele' ] ) ) {
        if ( ! empty( $_GET[ 'rm_ele' ] ) ) {
            if ( tmve_remove_element( $_GET[ 'rm_ele' ] ) ) {
                $mesg = 'Element \'' . $_GET[ 'rm_ele' ] . '\' removed.';
            } else {
                $emesg = 'Error. Element \'' . $_GET[ 'rm_ele' ] . '\' not removed.';
            }
        } else {
            $emesg = "Element can't be empty.";
        }
    } else if ( isset( $_GET[ 'add_attr' ] ) ) {
        if ( ! empty( $_GET[ 'add_attr' ] ) ) {
            if ( is_alpha( $_GET[ 'add_attr' ] ) ) {
                if ( tmve_add_attribute( $_GET[ 'ele' ], $_GET[ 'add_attr' ] ) ) {
                    $mesg = 'Attribute \'' . $_GET[ 'add_attr' ] . '\' added to element \''. $_GET[ 'ele' ] .'\'. Refresh TinyMCE (ctrl + F5)';
                } else {
                    $emesg = 'Attribute \'' . $_GET[ 'add_attr' ] . '\' already exists.';
                }
            } else {
                $emesg = "New attribute must contain letters only.";
            }
        } else {
            $emesg = "New attribute can't be empty.";
        }
    } else if ( isset( $_GET[ 'rm_attr' ] ) ) {
        if ( ! empty( $_GET[ 'rm_attr' ] ) ) {
            if ( tmve_remove_attribute( $_GET[ 'ele' ], $_GET[ 'rm_attr' ] ) ) {
                $mesg = 'Attribute \'' . $_GET[ 'rm_attr' ] . '\' removed from element \''. $_GET[ 'ele' ] .'\'.';
            } else {
                $emesg = 'Error. Attribute \'' . $_GET[ 'rm_ele' ] . '\' not removed.';
            }
        } else {
            $emesg = "Attribute can't be empty.";
        }
    } else if ( isset( $_GET[ 'delete_on_deactivate' ] ) ) {
        if ( $_GET[ 'delete_on_deactivate' ] == "yes" ) {
            update_option( 'tmve_cascade_on_deactivate', "true" );
            $mesg = 'Deactivation settings changed. Plugin tables and settings deleted on deactivation.';
        } else if ( $_GET[ 'delete_on_deactivate' ] == "no" ) {
            update_option( 'tmve_cascade_on_deactivate', "false" );
            $mesg = 'Deactivation settings changed. Plugin tables and settings saved on deactivation.';
        } else {
            $emesg = 'Invalid deactivation settings used.';
        }
    }
    
    if ( ! empty( $mesg ) ) {
        echo '<div id="message" class="updated fade"><p>'. $mesg . '</p></div>';
    }
    if ( ! empty( $emesg ) ) {
        echo '<div id="message" class="error fade" style="background-color: red"><p>'. $emesg . '</p></div>';
    }
}

function tmve_admin_menu() {
    tmve_do_actions();
    tmve_list_elements();
}

function tmve_list_elements() {
    $elements = tmve_get_element_map();
?>
  <div class="wrap">
    <h2>Editing TinyMCE Valid Elements</h2>
    <h3>Current Elements: (<?php print count( $elements ); ?> total)</h3>

    <?php if ( ! empty( $elements ) ) { ?>
      <div style="background-color: #E4F2FD; border: 1px solid #bcc">
        <ul>
          <?php foreach ( $elements as $element => $attributes ) { ?>
            <li>
              <?php print $element; ?> 
              <a style="text-decoration: none; font-weight: bold; color: red" 
                 href="<?php echo $_SERVER['REQUEST_URI'] . '?' . http_build_query( array( 
                             "page" => $_GET[ 'page' ],
                             "rm_ele" => $element ) ); ?>"
                 onclick="return confirm('Are you sure you want to delete the element \''+'<?php echo $element; ?>'+'\'?');">
                [-]</a>
                  
              <ul><li>
                <?php if ( ! empty( $attributes ) ) { ?>
                  <?php for ( $i = 0; $i < count( $attributes ); $i++ ) { ?>
                    <?php echo $attributes[ $i ]; ?>
                    <a style="text-decoration: none; font-weight: bold; color: red" 
                       href="<?php echo $_SERVER['REQUEST_URI'] . '?' . http_build_query( array( 
                                   "page" => $_GET[ 'page' ],
                                   "rm_attr" => $attributes[ $i ],
                                   "ele" => $element ) ); ?>"
                       onclick="return confirm('Are you sure you want to delete the attribute \''+'<?php echo $attributes[ $i ]; ?>'+'\'?');">
                      [-]</a>
                    <?php echo $i < count( $attributes ) - 1 ? ',' : ''; ?>
                  <?php } ?>
                <?php } ?>
                <form name="add_attribute_form" style="display: inline" method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                  <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
                  <input type="hidden" name="ele" value="<?php echo $element; ?>"/>
                  <nobr>
                    <input type="text" name="add_attr" size="5" maxlength="30"/>
                    <input type="submit" name="submit" value="Add Attribute"/>
                  </nobr>
                </form>
              </li></ul>
            </li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
    
    <h4>Add New Element:</h4>
    <form name="add_element_form" method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
      <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
      <input type="text" name="add_ele" size="14" maxlength="30"/>
      <input type="submit" name="Submit" value="Add Element" />
    </form>
    
    <h2>Help</h2>
    <h3>Description:</h3>
    <p>By default, <a href="http://www.wordpress.org" title="WordPress">WordPress'</a> <abbr title="What You See Is What You Get">WYSIWYG</abbr> 
    editor, <a href="http://tinymce.moxiecode.com/" title="TinyMCE">TinyMCE</a>, will strip out of your Article and Page HTML code any 
    elements that are not defined as <b>"valid elements"</b>; this can be extremely annoying (especially if you want to include iframes).</p>
    <p>This plugin will allow you to <b>extend</b> what TinyMCE defines as <b>"valid elements"</b>. By doing so, TinyMCE will no longer remove,
    delete, or strip-out the additional elements and attributes that you specify.</p>
    <p style="color:red"><b>** NOTE: Make sure after you add elements or attributes you do a <u>hard refresh</u> (ctrl + F5) of your browser 
    <i>on a TinyMCE screen (editing or creating a page/post)</i> so that the TinyMCE cache will be refreshed!</b> You will not see your changes 
    until you do this!</p>

    <h3>Operation:</h3>
    <p>To <b>add</b> an <b>element</b>, enter the element name click "Add Element".</p>
    <p>To <b>delete</b> an <b>element</b>, click the minus icon <span style="color: red; font-weight: bold;">[-]</span> next to the element name. 
    A confirmation box will appear; on confirmation, the element and all of it's child attributes will be deleted.</p>
    <p>To <b>add</b> an <b>attribute</b>, enter the attribute name on the desired elements' add attribute box and click "Add Attribute".</p>
    <p>To <b>delete</b> an <b>attribute</b>, click the minus icon <span style="color: red; font-weight: bold;">[-]</span> next to the attribute name. 
    A confirmation box will appear; on confirmation, the attribute will be deleted.</p>

    <h3>Uninstall:</h3>
    <p>By default, when you deactivate the plugin, your elements and attributes remain stored in the WordPress database; this was done to guard
    against accidental deactivation and the event where you want to re-activate the plugin</p>
    <p>If you would like to change what happens to the database tables and options once this plugin is deactivated, change the options below:</p>
    <p>
      <div style="background-color: #eef; border: 1px solid #aaf; display: table; padding: 10px;">
        <b>Delete database tables and options upon deactivation?</b>
        <form name="change_deactivate_form" method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
          <input type="hidden" name="page" value="<?php echo $_GET[ 'page' ]; ?>"/>
          Yes: <input type="radio" name="delete_on_deactivate"  value="yes"
                      <?php echo get_option('tmve_cascade_on_deactivate') == "true" ? 'checked="checked"' : ''; ?>/>
          No: <input type="radio" name="delete_on_deactivate" value="no"
                     <?php echo get_option('tmve_cascade_on_deactivate') == "false" ? 'checked="checked"' : ''; ?>/>
          <input type="submit" name="submit" value="Change"/>
        </form>
      </div>
    </p>
  </div>
<?php
}

/**
 * Add to extended_valid_elements for TinyMCE
 *
 * @param $init assoc. array of TinyMCE options
 * @return $init the changed assoc. array
 */
function tmve_mce_valid_elements( $init ) {
    $elements = tmve_get_element_map();

    $eleList = array();
    foreach ( $elements as $element => $attributes ) {
        if ( count( $attributes ) > 0 ) {
            $eleList[] = $element . '[' . implode( '|', $attributes ) . ']';
        } else {
            $eleList[] = $element;
        }
    }

    // Extended string to add to tinyMCE.init();
    $extStr = implode(',', $eleList);
    // Only add ext valid ele's if a correct string was made
    if ( $extStr != null && $extStr != '' ) {
        // Add to extended_valid_elements if it alreay exists
        if ( isset( $init['extended_valid_elements'] ) 
               && ! empty( $init['extended_valid_elements'] ) ) {
            $init['extended_valid_elements'] .= ',' . $extStr;
        } else {
            $init['extended_valid_elements'] = $extStr;
        }
    }

    // Super important: return $init!
    return $init;
}

register_activation_hook(__FILE__, 'tmve_activate');
register_deactivation_hook( __FILE__, 'tmve_deactivate');

add_action('admin_menu', 'tmve_add_admin_pages');

add_filter('tiny_mce_before_init', 'tmve_mce_valid_elements');
