<?php
/*
	Plugin Name: First Wednesday Email Admin
	Plugin URI: http://aarontgrogg.com/
	Description: Provides form-based Admin of email list
	Version: 1.0
	Author: Aaron T. Grogg
	Author URI: http://aarontgrogg.com/
	License: GPLv2 or later
*/
	
	// Define plug-in URI */
	define('FW_PLUGIN_URL', WP_PLUGIN_URL.'/fw-email-admin');
	
	// global message variable
	$_SESSION['message'] = false;

	if ($_POST['saved']) {
		//																push into these fields...          using these placeholders... these values...
		// $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) VALUES ( %d, %s, %s )",  array(10, $metakey, $metavalue) ) );
		$i = -1;
		$len = count($_POST['name']);
		for (; ++$i < $len ;) {
			$name = $_POST['name'][$i];
			$email = $_POST['email'][$i];
			$referredby = $_POST['referredby'][$i];
			$id = $_POST['id'][$i];
			$query = "UPDATE `atg_email_addresses` SET name=%s, email=%s, referredby=%s WHERE id=%d";
			$values = array($name, $email, $referredby, $id);
			$wpdb->query( $wpdb->prepare( $query, $values ) );
		}
		$_SESSION['message'] = $len.' email record(s) have been updated.';
		if ($len = count($_POST['delete'])) {
			$i = -1;
			for (; ++$i < $len ;) {
				$id = $_POST['delete'][$i];
				$query = "DELETE FROM `atg_email_addresses` WHERE id=%d";
				$values = array($id);
				$wpdb->query( $wpdb->prepare( $query, $values ) );
			}
			$_SESSION['message'] .= '<br />'.$len.' email record(s) have been deleted.';
		}
	}

	if ($_POST['added']) {
		$i = -1;
		$len = count($_POST['name']);
		for (; ++$i < $len ;) {
			$name = $_POST['name'][$i];
			$email = $_POST['email'][$i];
			$referredby = $_POST['referredby'][$i];
			$query = "INSERT INTO `atg_email_addresses` ( name, email, referredby ) VALUES ( %s, %s, %s )";
			$values = array($name, $email, $referredby);
			$wpdb->query( $wpdb->prepare( $query, $values ) );
		//  $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) VALUES ( %d, %s, %s )",  array(10, $metakey, $metavalue) ) );
		}
		$_SESSION['message'] = 'New email has been added.';
	}

/*
 	Begin Admin panel.

	There are essentially 5 sections to this:
	1)	Add "'FW Email Admin" link to left-nav Admin Menu & callback function for clicking that menu link
	2)	Add Admin Page CSS if on the Admin Page
	3)	Add "'FW Email Admin" Page options
	4)	Create functions to add above elements to pages
	5)	Add HTML5 Boilerplate options to page as requested
*/

/*	1)	Add "FW Email Admin" link to left-nav Admin Menu */

	//	Add option if in Admin Page
		function fw_create_email_admin_page() {
			add_submenu_page('tools.php', 'Email Admin', 'FW Email Admin', 'administrator', 'fw-email-admin', 'fw_build_email_admin_page');
		}
		add_action('admin_menu', 'fw_create_email_admin_page');

	//	You get this if you click the left-column "Email Admin" (added above)
		function fw_build_email_admin_page() {
		?>
			<div id="fw-email-admin-wrapper">
				<div class="icon32" id="icon-tools"><br /></div>
				<h2>Email Admin</h2>
				<?php
					if ($_SESSION['message']) {
						echo '<div class="updated" id="message"><p>'.$_SESSION['message'].'</p></div>';
					}
				?>
				<form method="post" action="<?php echo $PHP_SELF;?>">
					<p>Add new email info below and click Save Changes.</p>
					<input type="hidden" name="added" value="true" />
					<?php fw_add_new_email(); ?>
					<p class="submit"><input name="Add" type="submit" class="button-primary" value="<?php esc_attr_e('Add New'); ?>" /></p>
				</form>
				<form method="post" action="<?php echo $PHP_SELF;?>">
					<p>Update email info below and click Save Changes.</p>
					<input type="hidden" name="saved" value="true" />
					<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" /></p>
					<?php fw_updates_emails(); ?>
					<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" /></p>
				</form>
			</div>
		<?php
		}

/*	2)	Add plugin CSS/JS if on the Email Admin Page */

		function fw_admin_register_head() {
			echo '<link rel="stylesheet" href="' .FW_PLUGIN_URL. '/fw-email-admin.css" />'.PHP_EOL;
			echo '<script src="' .FW_PLUGIN_URL. '/fw-email-admin.js"></script>'.PHP_EOL;
		}
		add_action('admin_head', 'fw_admin_register_head');

/*	3)	Add "Boilerplate Admin" Page options */

	//	Register form elements
		function fw_updates_emails() {
			// get emails from DB
			global $wpdb;
			$wpdb->show_errors();
			$fw_emails = $wpdb->get_results( "SELECT * FROM `atg_email_addresses` ORDER BY `id`, `name`;" );
			$i = -1;
			$len = count($fw_emails);
			$html = '<table>' . PHP_EOL;
			$html .= '	<thead>' . PHP_EOL;
			$html .= '		<tr><th>&nbsp;</th>' . PHP_EOL;
			$html .= '			<th>X</th>' . PHP_EOL;
			$html .= '			<th>Name</th>' . PHP_EOL;
			$html .= '			<th>Email</th>' . PHP_EOL;
			$html .= '			<th>Referred By</th>' . PHP_EOL;
			$html .= '			<th>Entered</th>' . PHP_EOL;
			$html .= '		</tr>' . PHP_EOL;
			$html .= '	</thead>' . PHP_EOL;
			$html .= '	<tbody>' . PHP_EOL;
			for (; ++$i < $len ;) {
				$html .= '		<tr><td>'.($i+1).'.<input type="hidden" name="id[]" value="'.$fw_emails[$i]->id.'" /></td>' . PHP_EOL;
				$html .= '			<td><input type="checkbox" name="delete[]" value="'.$fw_emails[$i]->id.'" /></td>' . PHP_EOL;
				$html .= '			<td><input type="text" name="name[]" value="'.$fw_emails[$i]->name.'" size="20" /></td>' . PHP_EOL;
				$html .= '			<td><input type="text" name="email[]" value="'.$fw_emails[$i]->email.'" size="20" /></td>' . PHP_EOL;
				$html .= '			<td><input type="text" name="referredby[]" value="'.$fw_emails[$i]->referredby.'" size="20" /></td>' . PHP_EOL;
				$html .= '			<td>'.$fw_emails[$i]->entered.'</td>' . PHP_EOL;
				$html .= '		</tr>' . PHP_EOL;
			}
			$html .= '	</tbody>' . PHP_EOL;
			$html .= '</table>' . PHP_EOL;
			echo $html;
		}

	//	Register form elements
		function fw_add_new_email() {
			$html = '<table>' . PHP_EOL;
			$html .= '	<thead>' . PHP_EOL;
			$html .= '		<tr><th>&nbsp;</th>' . PHP_EOL;
			$html .= '			<th>Name</th>' . PHP_EOL;
			$html .= '			<th>Email</th>' . PHP_EOL;
			$html .= '			<th>Referred By</th>' . PHP_EOL;
			$html .= '		</tr>' . PHP_EOL;
			$html .= '	</thead>' . PHP_EOL;
			$html .= '	<tbody>' . PHP_EOL;
			$html .= '		<tr><td>&nbsp;</td>' . PHP_EOL;
			$html .= '			<td><input type="text" name="name[]" value="" size="20" /></td>' . PHP_EOL;
			$html .= '			<td><input type="text" name="email[]" value="" size="20" /></td>' . PHP_EOL;
			$html .= '			<td><input type="text" name="referredby[]" value="" size="20" /></td>' . PHP_EOL;
			$html .= '		</tr>' . PHP_EOL;
			$html .= '	</tbody>' . PHP_EOL;
			$html .= '</table>' . PHP_EOL;
			echo $html;
		}


/*	4)	Update DB with values from page */

	//	Add Admin Page validation
		function fw_validate_setting($plugin_options) {
			// $wpdb->escape($user_entered_data_string);
			// $wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = '13'");
			// $wpdb->query("UPDATE $wpdb->posts SET post_parent = 7 WHERE ID = 15 AND post_status = 'static'");
			//																push into these fields...          using these placeholders... these values...
			// $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) VALUES ( %d, %s, %s )", 10, $metakey, $metavalue ) );
			// The FILTER_SANITIZE_EMAIL filter removes all illegal e-mail characters from a string
			// The FILTER_VALIDATE_EMAIL filter validates value as an e-mail address

			$keys = array_keys($_FILES);
			$i = 0;
			foreach ( $_FILES as $image ) {
				// if a files was upload
				if ($image['size']) {
					// if it is an image
					if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) {
						$override = array('test_form' => false);
						// save the file, and store an array, containing its location in $file
						$file = wp_handle_upload( $image, $override );
						$plugin_options[$keys[$i]] = $file['url'];
					} else {
						// Not an image.
						$options = get_option('plugin_options');
						$plugin_options[$keys[$i]] = $options[$logo];
						// Die and let the user know that they made a mistake.
						wp_die('No image was uploaded.');
					}
				} else { // else, the user didn't upload a file, retain the image that's already on file.
					$options = get_option('plugin_options');
					$plugin_options[$keys[$i]] = $options[$keys[$i]];
				}
				$i++;
			}
			return $plugin_options;
		}

/*	End customization for Boilerplate */

?>
