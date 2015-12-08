<?php if(! WsdUtil::canLoad()) { return; } ?>
<?php
/*
* =====================================================================================
* 	Because this feature is error prone and the blog can go "bye-bye" in a second,
* 	ensure we've checked EVERYTHING before displaying the form
* =====================================================================================
*/
	global $table_prefix;

	//## Assume FALSE
	$showPage = false;            // whether or not to display this page
	$isWPConfigWritable = false;  // whether or not the wp-config file is writable
	$cdtpIsPostBack = false;      // whether or not the form was posted back.
    $acxInfoMessage = $wsd_Message = ''; // Hold the error/info messages generated on form postback
	$old_prefix = $table_prefix;
	$new_prefix = ''; // leave empty. it will be populated at runtime
    $canAlter = false;  // Assume the user doesn't have ALTER rights

	$wpConfigFile = trailingslashit(ABSPATH).'wp-config.php';
    $acx_dbRights = WsdInfoServer::getDatabaseUserAccessRights();

	if (is_writable($wpConfigFile)){
		$isWPConfigWritable = true;
	}

    if ($acx_dbRights['rightsEnough']) {
        $canAlter = true;
    }

	//!! Check wp-config.php file and rights first
	if ($isWPConfigWritable && $canAlter){
		$showPage = true;
	}

    // Check if user has enough rights to alter the Table structure
    if ($acx_dbRights['rightsEnough'])
	{
        $_canAlter = '<span style="color: #060; font-weight: 900;">('.__('Yes').')</span>';
    }
    else { $_canAlter = '<span style="color: #f00; font-weight: 900;">('.__('No').')</span>'; }

	if ($isWPConfigWritable)
	{
		$wpConfigFileinfo = '<span style="color: #060; font-weight: 900;">('.__('Yes').')</span>';
	}
	else { $wpConfigFileinfo = '<span style="color: #f00; font-weight: 900;">('.__('No').')</span>'; }
?>

<div class="acx-section-box">
    <p><?php echo __('Change your database table prefix to avoid zero-day SQL Injection attacks.');?></p>
    <h4 style="margin-top: 15px;"><?php echo __('Before running this script');?>:</h4>
    <ul class="acx-common-list" style="margin-top: 20px;">
        <li><?php echo __('The <code>wp-config.php</code> file must be <strong>writable</strong>.').' '.$wpConfigFileinfo;?></li>
        <li><?php echo __("The database user you're using to connect to database must have <strong>ALTER</strong> rights.").' '.$_canAlter;?></li>
    </ul>
</div>

<?php
/*
 * If the user doesn't have ALTER rights or the wp-config file is not writable
 *============================================================================
 */
//@ if all good but we cannot use the "file' function, stop here
if ($showPage && !function_exists('file'))
{
	echo '<p class="acx-info-box">';
        echo '<span class="acx-icon-alert-info">';
            echo __('In order to alter the <code>wp-config.php</code> file we need the <strong>file</strong> function which seems to be blacklisted by your server administrator!');
        echo '</span>';
	echo '</p>';

    return;
}

//@ If we cannot load the page
if ( ! $showPage )
{
	echo '<p class="acx-info-box">';
		if (!$canAlter) {
            echo '<span class="acx-icon-alert-critical">';
                echo __('The User used to access the database must have <strong>ALTER</strong> rights in order to perform this action!');
            echo '</span><br/>';
		}
		if (!$isWPConfigWritable) {
            echo '<span>';
            	echo __('The <strong>wp-config</strong> file <strong>MUST</strong> be writable!');
            echo '</span>';
		}
	echo '</p>';

    //!! Stop here, no need to load the rest of the page
    return;
}
?>

<?php
/*
 * Issue the file permissions warning ONLY IF wp-config IS WRITABLE,
 * otherwise the form gets confusing displaying so many info messages...
 *======================================================================
 */
 	if ($isWPConfigWritable)
	{
		echo '<p class="acx-info-box">';
            echo '<span class="acx-icon-alert-info">';
                echo __('It is a security risk to have your files <strong>writable</strong>!
                    Please make sure that <strong>after</strong> running this script, the <code>wp-config.php</code> file\'s permissions are set to 0644 or to a more restrictive one. See: <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">http://codex.wordpress.org/Changing_File_Permissions</a> for more information.');
            echo '</span>';
		echo '</p>';
	}
	//@ If the user has too many rights
    if (!empty($acx_dbRights['rightsTooMuch'])) {
		echo '<p class="acx-info-box">';
            echo '<span class="acx-icon-alert-info">'. __("Your currently used User to access the Wordpress Database <code>holds too many rights</code>. We suggest that you limit his rights or to use another User with more limited rights instead, to increase your website's Security.").'</span>';
		echo '</p>';
    }
?>

<?php
/*
 * VALIDATE FORM
 *======================================================
 */
	 if ($_SERVER['REQUEST_METHOD'] == 'POST')
	 {
		 if (!empty($_POST['newPrefixInput']) && isset($_POST['changePrefixButton']))
		 {
			$cdtpIsPostBack = true;

			if (function_exists('wp_nonce_field')) {
				check_admin_referer('prefix-changer-change_prefix');
			}

			 //@@ Double check the request
			 if (!$isWPConfigWritable || !$canAlter)
			 {

				 $e  = __('Please correct the following errors').':<br/>';
	    		 $e .= '<br/>'.__('The User used to access the database must have <strong>ALTER</strong> rights in order to perform this action!');
				 $e .= '<br/>'.__('The <strong>wp-config</strong> file <strong>MUST</strong> be writable!');

				 wp_die($e);
			 }

             $wpdb = $GLOBALS['wpdb'];
             if (empty($wpdb))
             {
                 wp_die(__('An internal error has occurred (empty $wpdb). Please inform the plug-in author about this error. Thank you!'));
             }

             $new_prefix = $_POST['newPrefixInput'];

             // validate prefix
             if(! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]+$/', $new_prefix)){
                 $acxInfoMessage .=  sprintf('Invalid table name prefix: %s', htmlentities($new_prefix));
                 $new_prefix = $old_prefix;
             }
             else
             {
                 if (empty($acx_dbRights['rightsEnough']))
                 {
                     $wsd_Message .= '<span class="acx-icon-alert-critical">'. __('The User which is used to access your Wordpress Database, hasn\'t enough rights (is missing the <code>ALTER</code> right) to alter the Table structure.
                            If the user <code>has ALTER</code> rights and the tool is still not working, please <a href="http://wordpress.org/support/plugin/wp-security-scan" target="_blank">contact us</a> for assistance!').'</span>';
                 }

                 if (strlen($new_prefix) < strlen($_POST['newPrefixInput'])){
                     $acxInfoMessage .= __('You used some characters disallowed in Table names. The sanitized prefix will be used instead').': '.$new_prefix;
                 }
                 if ($new_prefix == $old_prefix) {
                     if (!empty($acxInfoMessage)) { $acxInfoMessage .= '<br/>'; }
                     $acxInfoMessage .= __('No change! Please select a different table prefix value.');
                 }
                 else
                 {
                     // Get the list of tables to modify
                     $tables = WsdUtil::getTablesToAlter();
                     if (empty($tables))
                     {
                         if (!empty($acxInfoMessage)) { $acxInfoMessage .= '<br/>'; }
                         $acxInfoMessage .= __("Internal Error: We couldn't retrieve the list of tables from the database! Please inform the plug-in author about this error! Thank you!");
                     }
                     else
                     {
                         $result = WsdUtil::renameTables($tables, $old_prefix, $new_prefix);

                         // check for errors
                         if (!empty($result))
                         {
                             if (!empty($acxInfoMessage)) { $acxInfoMessage .= '<br/>'; }
                             $acxInfoMessage .= '<span class="acx-notice-success acx-icon-alert-success">'.__('All tables have been successfully updated!').'</span>';

                             // try to rename the fields
                             $acxInfoMessage .= WsdUtil::renameDbFields($old_prefix, $new_prefix);

                             if (0 < WsdUtil::updateWpConfigTablePrefix($wpConfigFile, $new_prefix))
                             {
                                 $acxInfoMessage .= '<br/><span class="acx-notice-success acx-icon-alert-success">'.__('The <strong>wp-config</strong> file has been successfully updated!').'</span>';
                             }
                             else {
                                 $acxInfoMessage .= '<br/>'.__('The <strong>wp-config</strong> file could not be updated! You have to manually update the <strong>$table_prefix</strong> variable to the one you have specified').': '.$new_prefix;
                             }
                         }// End if tables successfully renamed
                         else { $acxInfoMessage .= '<br/><strong>'.__('An error has occurred and the tables could not be updated!').'</strong>'; }
                     }// End if there are tables to rename
                 }// End checks
             }
		}// End if (!empty($_POST['newPrefixInput']))
	}// End if postback
    else { $new_prefix = $old_prefix; }

if(empty($new_prefix)){
    $new_prefix = $old_prefix;
}
?>

<?php
/*
* Dsplay the form
*=======================================================
*/
?>
<div class="acx-section-box">
    <form action="#cdtp" method="post" name="prefixchanging">
        <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('prefix-changer-change_prefix'); } ?>
        <p><?php echo sprintf(__('Change the current: <input type="text" name="newPrefixInput" value="%s" size="20" maxlength="15"/> table prefix to something different.'), $new_prefix); ?></p>
        <p><?php echo __('Allowed characters: all latin alphanumeric as well as the <strong>_</strong> (underscore).');?></p>
        <input type="submit" class="button-primary" name="changePrefixButton" value="<?php echo __('Start Renaming');?>" />
    </form>
</div>
<div id="cdtp">
    <?php
        // Display status information
        if ($cdtpIsPostBack){
            if (!empty($acxInfoMessage)){ echo '<p class="acx-info-box">',$acxInfoMessage,'</p>'; }
            if (!empty($wsd_Message)) { echo '<p class="acx-info-box">',$wsd_Message,'</p>'; }
        }
        else {
            if (!empty($wsd_Message)) { echo '<p class="acx-info-box">',$wsd_Message,'</p>'; }
        }
    ?>
</div>
