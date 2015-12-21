<?php
/**
 * Multisite Robots.txt Manager
 * @package Multisite Robots.txt Manager
 * @author tribalNerd (tribalnerd@technerdia.com)
 * @copyright Copyright (c) 2012-2013, techNerdia LLC.
 * @link http://msrtm.technerdia.com/
 * @license http://www.gnu.org/licenses/gpl.html
 * @version 0.4.0
 */

/**
 * ==================================== Network Admin Area Template
 */

if( !defined( 'ABSPATH' ) ) { exit; } /* Wordpress check */
if( isset( $robots_txt_file ) ) { $robots_txt_file = $robots_txt_file; } else { $robots_txt_file = ""; }?>

<div class="wrap">
	<div id="icon-themes" class="icon32"><br /></div><h2><?php _e( 'Multisite Robots.txt Manager - Network Settings', 'technerdia_theme' );?></h2>
	<p><?php _e( 'Quickly and easily manage all of your Network Websites robots.txt files.', 'technerdia_theme' );?></p>

<?php /* page tabs */ echo $this->msrtm_tabs();?>
<?php /* notices */ if( isset( $notice ) ) {?><div class="updated" id="message" onclick="this.parentNode.removeChild(this)"><p><strong><em><?php echo $notice;?></em></strong></p></div><?php }?>
<?php /* notices */ if( !$this->robotstxt_ms_version() == false ) {?><div class="updated" id="message" onclick="this.parentNode.removeChild(this)"><p><strong>UPDATE: <em>Update Available For The MS Robots.txt Manager Pro Extension Plugin. Version 2 has been emailed to you, please check your email for more details.  Please <a href="http://msrtm.technerdia.com/help.html" target="_blank">contact support</a> if you have any questions or problems.</em></strong></p><p><strong>NOTICE: <em>Version 1 of the Pro Extension Plugin was found on your server. Please Delete The Old Plugin To Avoid Conflicts! FTP to your Wordpress Install, location: "<?php echo $this->robotstxt_ms_version();?>" and delete the ms-robotstxt.php file. If you have any questions please <a href="http://msrtm.technerdia.com/help.html" target="_blank">contact support</a>.</em></strong></p></div><?php }?>

	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2"><div id="post-body-content">

<?php if( isset( $_GET['tab'] ) && $_GET['tab'] == "presets" ) {?>
<!-- tab presets and examples -->
		<div class="postbox">
			<h3><span><?php _e('Making Life Easy', 'ms_robotstxt_manager');?></span></h3>
		<div class="inside">
		<div class="inside-box"><div class="inside-pad para">
		<?php if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?>
			<p><?php _e('The preset feature allows you to copy a premade robots.txt file to either the default robots.txt file or a selected websites robots.txt file.', 'ms_robotstxt_manager');?></p>
		<?php }
		if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {?>
			<p><?php _e('The preset feature allows you to copy a premade robots.txt file to this websites robots.txt file.', 'ms_robotstxt_manager');?></p>
		<?php }?>
		
			<p><strong><?php _e('To use', 'ms_robotstxt_manager');?></strong>: <?php _e('Select where you would like to post the premade robots.txt file; from the drop down menu, select either the Default robots.txt file or a Website to update. Then select the checkbox and enter a sitemap structure to render sitemap urls. Finally, click the "set as default" button above the robots.txt preset robots.txt example you want to use.', 'ms_robotstxt_manager');?></p>

			<?php if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?>
				<hr />
				<h2><?php _e('Publish To', 'ms_robotstxt_manager');?>?</h2>
				<strong><?php _e('Network', 'ms_robotstxt_manager');?></strong>: <?php _e('Publishing to the Network Robots.txt file without a sitemap structure will remove the default sitemap structure - if one has already been set. It is recommended that you include the sitemap structure if publishing to the Network robots.txt file.', 'ms_robotstxt_manager');?><br />
				<p><strong><?php _e('Website', 'ms_robotstxt_manager');?></strong>: <?php _e('Publishing to a Website without a sitemap structure, only updates the robots.txt file - no sitemap data is changed. However, including a sitemap structure, will update the sitemap data for the selected Website.', 'ms_robotstxt_manager');?></p>
			<?php }?>

				<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
				<?php wp_nonce_field( 'robotstxt_action', 'robotstxt_nonce' );?>
				<?php if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?>
					<p><select name="selected_site"><option value="robotstxt_network_set"><?php _e('Default Robots.txt File', 'ms_robotstxt_manager');?></option><?php $this->msrtm_select_site();?></select></p>
				<?php }?>

					<hr />

					<h2><?php _e('Sitemap Structure', 'ms_robotstxt_manager');?></h2>
					<?php _e('The Sitemap Structure below is based on the default Sitemap Structure defined under the Defaults tab.', 'ms_robotstxt_manager');?>
					<p><input type="checkbox" name="sitemap_show" value="yes" <?php echo $checked;?> /> <?php _e('Check To Add Sitemap URL To The Robots.txt File', 'ms_robotstxt_manager');?><br />
					<input type="hidden" name="sitemap_hidden" value="1" />
					<p><?php _e('Sitemap URL Structure', 'ms_robotstxt_manager');?>: <input type="text" name="sitemap_structure" value="<?php if( isset( $sitemap_structure ) ) { echo $sitemap_structure; }?>" size="70" placeholder="click help for instructions" class="msrtm-input" /> [<a href="settings.php?tab=help&amp;page=ms_robotstxt.php#sitemap" target="_blank"><?php _e('help', 'ms_robotstxt_manager');?></a>]</p>

					<hr />

					<h2><?php _e('Select a Robots.txt File', 'ms_robotstxt_manager');?></h2>
					<?php _e('Click the "set as default" button to update the selected robots.txt file.', 'ms_robotstxt_manager');?>
					<br /><br /><p><strong><?php _e('Default robots.txt File', 'ms_robotstxt_manager');?></strong>: <input type="submit" name="preset_default" value=" <?php _e('set as default', 'ms_robotstxt_manager');?> " class="button button-large" /><br />
					<textarea name="value_default" cols="65" rows="12" class="msrtm-textarea"><?php if( isset( $default_robotstxt ) ) { echo $default_robotstxt; }?></textarea></p>

					<br /><br /><p><strong><?php _e('Google Heavy Robots.txt File', 'ms_robotstxt_manager');?></strong>: <input type="submit" name="preset_google" value=" <?php _e('set as default', 'ms_robotstxt_manager');?> " class="button button-large" /><br />
					<textarea name="value_google" cols="65" rows="12" class="msrtm-textarea"><?php if( isset( $google_robotstxt ) ) { echo $google_robotstxt; }?></textarea></p>

					<br /><br /><p><strong><?php _e('Old Default Robots.txt File', 'ms_robotstxt_manager');?></strong>: <input type="submit" name="preset_default_old" value=" <?php _e('set as default', 'ms_robotstxt_manager');?> " class="button button-large" /><br />
					<textarea name="value_default_old" cols="65" rows="12" class="msrtm-textarea"><?php if( isset( $default_robotstxt_old ) ) { echo $default_robotstxt_old; }?></textarea></p>

					<br /><p><strong><?php _e('Open 24/7', 'ms_robotstxt_manager');?></strong>: <input type="submit" name="preset_open" value=" <?php _e('set as default', 'ms_robotstxt_manager');?> " class="button button-large" /><br />
					<textarea name="value_open" cols="65" rows="5" class="msrtm-textarea"><?php if( isset( $mini_robotstxt ) ) { echo $mini_robotstxt; }?></textarea></p>

					<br /><p><strong><?php _e('Blogger Goes Crazy', 'ms_robotstxt_manager');?></strong>: <input type="submit" name="preset_blog" value=" <?php _e('set as default', 'ms_robotstxt_manager');?> " class="button button-large" /><br />
					<textarea name="value_blog" cols="65" rows="15" class="msrtm-textarea"><?php if( isset( $blogger_robotstxt ) ) { echo $blogger_robotstxt; }?></textarea></p>

					<br /><p><strong><?php _e('Kill\'em All', 'ms_robotstxt_manager');?></strong>: <input type="submit" name="preset_kill" value=" <?php _e('set as default', 'ms_robotstxt_manager');?> " class="button button-large" /><br />
					<textarea name="value_kill" cols="65" rows="5" class="msrtm-textarea"><?php if( isset( $blocked_robotstxt ) ) { echo $blocked_robotstxt; }?></textarea></p>
				</form>
			</div></div> <!-- end inside-box and inside-pad para -->
		</div> <!-- end inside -->
			<?php /* page tabs */ echo $this->msrtm_tab_links();?>
		</div> <!-- end  postbox-->
<?php
	/*
	 * Tab - How to Use
	 */
	}elseif( isset( $_GET['tab'] ) && $_GET['tab'] == "help" ) {?>

<?php if( isset( $msrtm_warning ) && $msrtm_warning == "1" ) { /** old robots.txt file data check */?>
			<div class="postbox">
					<h3><span><?php _e('Remove Garbage Robots.txt File Data', 'ms_robotstxt_manager');?></span></h3>
			<div class="inside">
				<div class="inside-box"><div class="inside-pad para">
					<p><strong><?php _e( 'Warning', 'ms_robotstxt_manager' );?></strong>: <?php _e( 'Old robots.txt file data from other robots.txt file plugins detected. This left over option data may make this plugin fail to work correctly. To correct: Disable other robots.txt file plugin(s) first, then click the button below to mass remove old option data left over by other robots.txt file plugins.', 'ms_robotstxt_manager' );?></p>
					<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
					<?php wp_nonce_field( "robotstxt_action", "robotstxt_nonce" );?>
						<p><input type="submit" name="update_old" value=" mass clean " class="button button-large" /></p>
					</form>
				</div></div> <!-- end inside-box and inside-pad para -->
			</div></div> <!-- end inside and postbox -->
<?php }?>

<?php if( isset( $msrtm_rules ) && $msrtm_rules == "1" ) { /** missing robots.txt rewrite rules */?>
			<div class="postbox">
					<h3><span><?php _e('Add Mising Robots.txt Rewrite Rules', 'ms_robotstxt_manager');?></span></h3>
			<div class="inside">
				<div class="inside-box"><div class="inside-pad para">
					<p><strong><?php _e( 'Warning', 'ms_robotstxt_manager' );?></strong>: <?php _e( 'It appears the Robots.txt Rule is missing from a Websites rewrite_rules option. This can cause robots.txt files to return 404 errors. To correct: Click the button below to mass update any Websites missing the robots.txt file rule.', 'ms_robotstxt_manager' );?></p>
					<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
					<?php wp_nonce_field( "robotstxt_action", "robotstxt_nonce" );?>
						<p><input type="submit" name="update_rules" value=" mass clean " class="button button-large" /></p>
					</form>
				</div></div> <!-- end inside-box and inside-pad para -->
			</div></div> <!-- end inside and postbox -->
<?php }?>

		<div class="postbox">
			<h3><span><?php _e('Check For Errors', 'ms_robotstxt_manager');?></span></h3>
		<div class="inside">
			<div class="inside-box"><div class="inside-pad para">
				<p><?php _e( 'The features below checks for common errors that make some robots.txt files return 404 or blank. Only use this feature if your robots.txt files are not working correctly.', 'ms_robotstxt_manager' );?></p>
				<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
					<?php wp_nonce_field( "robotstxt_action", "robotstxt_nonce" );?>
					<p style="float:left;"><input type="submit" name="msrtm_rules_check" value=" check for missing rewrite rules " class="button button-large" /></p>
				</form>

				<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
					<?php wp_nonce_field( "robotstxt_action", "robotstxt_nonce" );?>
					<p style="float:right;"><input type="submit" name="msrtm_old_check" value=" check for old robots.txt plugin data " class="button button-large" /></p>
				</form>
				<div class="clear"></div>
			</div></div> <!-- end inside-box and inside-pad para -->
		</div></div> <!-- end inside and postbox -->

<?php require_once( dirname( __FILE__ ) . '/template-help.inc.php' );

	/*
	 * Tab - Extended Admin
	 */
	}elseif( isset( $_GET['tab'] ) && $_GET['tab'] == "auto" ) {
		$this->msrtm_plugin( $type = 'admin' );
	}else{?>

<?php if( isset( $msrtm_warning ) && $msrtm_warning == "1" ) { /** old robots.txt file data check */?>
			<div class="postbox">
					<h3><span><?php _e('Remove Garbage Robots.txt File Data', 'ms_robotstxt_manager');?></span></h3>
			<div class="inside">
				<div class="inside-box"><div class="inside-pad para">
					<p><strong><?php _e( 'Warning', 'ms_robotstxt_manager' );?></strong>: <?php _e( 'Old robots.txt file data from other robots.txt file plugins detected. This left over option data may make this plugin fail to work correctly. To correct: Disable other robots.txt file plugin(s) first, then click the button below to mass remove old option data left over by other robots.txt file plugins.', 'ms_robotstxt_manager' );?></p>
					<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
					<?php wp_nonce_field( "robotstxt_action", "robotstxt_nonce" );?>
						<p><input type="submit" name="update_old" value=" mass clean " class="button button-large" /></p>
					</form>
				</div></div> <!-- end inside-box and inside-pad para -->
			</div></div> <!-- end inside and postbox -->
<?php }?>

<?php if( isset( $msrtm_rules ) && $msrtm_rules == "1" ) { /** missing robots.txt rewrite rules */?>
			<div class="postbox">
					<h3><span><?php _e('Add Mising Robots.txt Rewrite Rules', 'ms_robotstxt_manager');?></span></h3>
			<div class="inside">
				<div class="inside-box"><div class="inside-pad para">
					<p><strong><?php _e( 'Warning', 'ms_robotstxt_manager' );?></strong>: <?php _e( 'It appears the Robots.txt Rule is missing from a Websites rewrite_rules option. This can cause robots.txt files to return 404 errors. To correct: Click the button below to mass update any Websites missing the robots.txt file rule.', 'ms_robotstxt_manager' );?></p>
					<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
					<?php wp_nonce_field( "robotstxt_action", "robotstxt_nonce" );?>
						<p><input type="submit" name="update_rules" value=" mass clean " class="button button-large" /></p>
					</form>
				</div></div> <!-- end inside-box and inside-pad para -->
			</div></div> <!-- end inside and postbox -->
<?php }?>

<!-- front page of settings -->
		<div class="postbox">
			<?php if( !isset( $show_site ) && isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?>
				<h3><span><?php _e('Default Settings', 'ms_robotstxt_manager');?></span></h3>
			<?php }
			if( isset( $show_site ) || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {?>
				<h3><span><?php _e('Settings For This Website', 'ms_robotstxt_manager');?></span></h3>
			<?php }?>
		<div class="inside">
		<div class="inside-box"><div class="inside-pad para">
			<?php if( !isset( $show_site ) && isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?>
				<p><?php _e('The default robots.txt file and default sitemap structure below is not live or public data. Defaults are used when publishing the settings across the entire network or resetting a unique website back to the default settings.', 'ms_robotstxt_manager');?></p>
			<?php }
			if( isset( $show_site ) && !$this->msrtm_version( $check = true ) ) {?>
				<?php if( !get_option( "ms_robotstxt" ) ) { echo '<p><strong>'. __('The MS Robots.txt Manager is DISABLED on this Website.', 'ms_robotstxt_manager') .'</strong></p>'; }?>
				<?php if( get_option( "ms_robotstxt" ) ) { echo '<p><strong>'. __('The MS Robots.txt Manager is ACTIVE on this Website.', 'ms_robotstxt_manager') .'</strong></p>'; }?>
			<?php }
			if( isset( $show_site ) && isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {?>
				<p><?php _e('Modify the robots.txt file and sitemap structure below, once done click the "update this website" button to save your changes.', 'ms_robotstxt_manager');?></p>
			<?php }?>

			<hr />

		<?php if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?>
			<h2><?php _e('Unique Robots.txt Files', 'ms_robotstxt_manager');?>:</h2>
			<?php if( !isset( $show_site ) ) {?><?php _e('To modify a Websites unique robots.txt file and sitemap structure; select the Website from the drop down, then click the "change sites" button to open the selected Website robots.txt file and sitemap data.', 'ms_robotstxt_manager');?><br /><br /><?php }?>
			<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
			<?php wp_nonce_field( 'robotstxt_action', 'robotstxt_nonce' );?>
				<select name="show_site"><option value="msrtm_redirect"><?php _e('Network Home', 'ms_robotstxt_manager');?></option><?php $this->msrtm_select_site();?></select>
				<input type="submit" name="submit" value=" change sites " class="button button-large" /><?php if( isset( $show_site ) ) {?> [ <a href="<?php echo get_site_url( $show_site, '/robots.txt' );?>" target="_blank"><?php _e('view', 'ms_robotstxt_manager');?> robots.txt</a> ]<?php }?><?php if( isset ( $_GET['open'] ) ) {?> [ <a href="<?php echo get_site_url( $_GET['open'], '/wp-admin/index.php' );?>"><?php _e('Return to Site', 'ms_robotstxt_manager');?></a> ]<?php }?>
			</form>
			<br />
		<?php }?>

		<h2><?php if( !isset( $show_site ) && isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?><?php _e('Default', 'ms_robotstxt_manager');?> <?php }?><?php _e('Robots.txt File', 'ms_robotstxt_manager');?>:</h2>
		<?php if( !isset( $show_site ) && isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) { _e('Modify the robots.txt file and sitemap structure to match your needs, then click the “save default settings” button to save your changes. Once ready, click the “publish to network” button to commit the saved changes to all websites within your network.', 'ms_robotstxt_manager'); }?>
		<?php if( isset( $show_site ) && isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) { _e('This websites robots.txt file.', 'ms_robotstxt_manager'); }?>

			<form action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] );?>" method="post">
			<?php wp_nonce_field( 'robotstxt_action', 'robotstxt_nonce' ); ?>
				<?php if( isset( $show_site ) ) {?><input type="hidden" name="show_site" value="<?php echo $show_site;?>" /><?php }?>
				<?php if( isset( $_POST['reset_this_default'] ) ) {?>
					<p><textarea name="robotstxt_option" cols="85" rows="20" class="msrtm-textarea"><?php echo esc_attr( $robots_txt_file );?></textarea></p>
				<?php }elseif( isset( $_POST['publish_ms_robotstxt'] ) ) { switch_to_blog(1);?>
					<p><textarea name="robotstxt_option" cols="85" rows="20" class="msrtm-textarea"><?php if( isset( $_POST['robotstxt_option'] ) ) { echo esc_attr( $_POST['robotstxt_option'] ); }else{ echo esc_attr( $robots_txt_file ); }?></textarea></p>
				<?php }elseif( isset( $_POST['disable_this_website'] ) ) {?>
					<p><textarea name="robotstxt_option" cols="85" rows="20" class="msrtm-textarea">/* <?php _e('Robots.txt Disabled', 'ms_robotstxt_manager');?> */</textarea></p>
				<?php }elseif( isset( $_POST['reset_this_website'] ) ) {?>
					<p><textarea name="robotstxt_option" cols="85" rows="20" class="msrtm-textarea"><?php echo esc_attr( $robots_txt_file );?></textarea></p>
				<?php }elseif( isset( $show_site ) ) {?>
					<p><textarea name="robotstxt_option" cols="85" rows="20" class="msrtm-textarea"><?php if( isset( $_POST['robotstxt_option'] ) ) { echo esc_attr( $_POST['robotstxt_option'] ); } else { echo esc_attr( $robots_txt_file ); }?></textarea></p>
				<?php }else{?>
					<p><textarea name="robotstxt_option" cols="85" rows="20" class="msrtm-textarea"><?php if( isset( $_POST['robotstxt_option'] ) ) { echo esc_attr( $_POST['robotstxt_option'] ); } else { echo esc_attr( $robots_txt_file ); }?></textarea></p>
				<?php }?>

				<?php if( isset( $show_site ) || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {?>
					<p><strong><?php _e('Add Sitemap URL To This Websites Robots.txt Files', 'ms_robotstxt_manager');?></strong><br />
				<?php }else{?>
					<p><strong><?php _e('Add Sitemap URL Structure To ALL Robots.txt Files', 'ms_robotstxt_manager');?></strong><br />
				<?php }?>
					<input type="checkbox" name="sitemap_show" value="yes" <?php if( isset( $checked ) ) { echo $checked; }?> /> <?php _e('Check To Add Sitemap URLs To Robots.txt Files', 'ms_robotstxt_manager');?> <span class="description">( <?php _e('If selected, a sitemap url structure is required.', 'ms_robotstxt_manager');?> )</span></p>
					<input type="hidden" name="sitemap_hidden" value="1" />
				<p><strong><?php _e('Sitemap URL Structure', 'ms_robotstxt_manager');?><?php if( isset( $show_site ) ) {?> (<?php _e('or direct URL', 'ms_robotstxt_manager');?>)<?php }?></strong>: <input type="text" name="sitemap_structure" value="<?php if( isset( $sitemap_structure ) ) { echo $sitemap_structure; }?>" size="80" placeholder="instructions below" class="msrtm-input" /><br />
					<?php _e('Entering a sitemap url structure, saves the structure, even if the check box to include the sitemap url is not selected. ** Help on this feature can be found below.', 'ms_robotstxt_manager');?></p>

				<?php if( isset( $show_site ) && isset( $sitemap_url ) || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" && isset( $sitemap_url ) ) {?>
					<p><strong><?php _e('Current Sitemap URL', 'ms_robotstxt_manager');?></strong>: <?php echo $sitemap_url;?></p>
				<?php }?>

				<br />

				<?php if( isset( $show_site ) || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {?>
					<p><input type="submit" name="update_ms_robotstxt" value=" <?php _e('update this website', 'ms_robotstxt_manager');?> " class="button button-large" /></p>
					<br /><p><input type="submit" name="reset_this_website" value=" <?php _e('reset this website', 'ms_robotstxt_manager');?> " class="button button-large" /><?php if( !$this->msrtm_version( $check = true ) ) {?> <input type="submit" name="disable_this_website" value=" <?php _e('disable this website', 'ms_robotstxt_manager');?> " class="button button-large" /></p><?php }?>
					<p class="description">* <?php _e('Resetting this website duplicates the default network wide robots.txt file and sitemap structure to this website.', 'ms_robotstxt_manager');?><br />
					<?php if( !$this->msrtm_version( $check = true ) ) {?>* <?php _e('Disabling this website restores the default Wordpress robots.txt file.', 'ms_robotstxt_manager');?></p><?php }?>
				<?php }else{?>
					<p><input type="submit" name="default_ms_robotstxt" value=" <?php _e('save default settings', 'ms_robotstxt_manager');?> " class="button button-large" /> <small>then</small> <input type="submit" name="publish_ms_robotstxt" value=" <?php _e('publish to network', 'ms_robotstxt_manager');?> " class="button button-large" /></p>
					<br />
					<p><input type="submit" name="reset_this_default" value="<?php _e(' reset to default', 'ms_robotstxt_manager');?> " class="button button-large" /></p>
					<p class="description">* <?php _e('Resetting clears the above defaults only. The sitemap url structure will be cleared and the robots.txt file will be restored with the default coded-in version.', 'ms_robotstxt_manager');?></p>
				<?php }?>
			</form>
			</div></div> <!-- end inside-box and inside-pad para -->
		</div></div> <!-- end inside and postbox-->

		<div class="postbox">
			<h3><span><?php _e('Sitemap URL Structure', 'ms_robotstxt_manager');?></span></h3>
		<div class="inside">
		<div class="inside-box"><div class="inside-pad para">
			<p><?php _e('The Sitemap URL Structure feature uses 3 different [shortcodes] to customize how sitemap urls are rendered on a robots.txt file. The shortcodes get a websites url and then break the url apart, separating the domain name from the domain extension.', 'ms_robotstxt_manager');?></p>
			<p><strong><?php _e('Example Structure', 'ms_robotstxt_manager');?></strong>: http://[WEBSITE_URL]/sitemap.xml</p>
			<p><?php _e('The [bracket] within the url automatically gets replaced by the plugin (You Will Use The Brackets).', 'ms_robotstxt_manager');?></p>

			<?php if( !isset( $show_site ) && isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?><p><?php _e('The default sitemap structure (on this page), "must" use the [bracket] structure, otherwise when you publish to the network, the sitemap urls will not render correctly. You can directly modify a Websites sitemap structure or enter a full sitemap url, by selected the site from the drop down menu above, then click the "change sites" button.', 'ms_robotstxt_manager');?></p><?php }?>
			<?php if( isset( $show_site ) && isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {?><p><?php _e('The sitemap structure (on this page), can either use the [bracket] structure or the actual-real sitemap url for this Website.', 'ms_robotstxt_manager');?></p><?php }?>
 
			<h2><?php _e('Sitemap URL Structures', 'ms_robotstxt_manager');?></h2>
			<p>Example sitemap structures you can use.</p>

			<p><strong><?php _e('Wordpress Structure', 'ms_robotstxt_manager');?>:</strong> http://[WEBSITE_URL]/sitemap.xml<br />
			<strong><?php _e('Sitemap URL', 'ms_robotstxt_manager');?></strong>: http://domain.com/sitemap.xml</p>

			<p><strong><?php _e('GoDaddy Structure', 'ms_robotstxt_manager');?>:</strong> http://[WEBSITE_URL]/sitemaps/[DOMAIN]-[EXT].xml<br />
			<strong><?php _e('Sitemap URL', 'ms_robotstxt_manager');?></strong>: http://domain.com/sitemaps/domain-com.xml</p>

			<p><strong><?php _e('Random Structure', 'ms_robotstxt_manager');?>:</strong> http://[WEBSITE_URL]/[DOMAIN]-[EXT]-sitemap.xml.gz<br />
			<strong><?php _e('Sitemap URL', 'ms_robotstxt_manager');?></strong>: http://domain.com/domain-com-sitemap.xml.gz</p>

			<h2><?php _e('Structure Meaning', 'ms_robotstxt_manager');?></h2>
				<ol>
					<li>[WEBSITE_URL] = domain.com</li>
					<li>[DOMAIN] = domain</li>
					<li>[EXT] = .com/net, etc.</li>
				</ol>

				<hr />

				<p>&bull; <strong><em><?php _e('Always include the http:// with the Sitemap URL Structure.', 'ms_robotstxt_manager');?></em></strong><br />
				&bull; <strong><em><?php _e('If the sitemaps are within a directory, /include-the-path/ within the sitemap url.', 'ms_robotstxt_manager');?></em></strong></p>
			</div></div> <!-- end inside-box and inside-pad para -->
		</div> <!-- end inside -->
			<?php /* page tabs */ echo $this->msrtm_tab_links();?>
		</div> <!-- end  postbox-->
<?php } /* end if */?>
		</div> <!-- end post-body-content -->

<!-- start sidebar -->
		<div id="postbox-container-1" class="postbox-container"><?php require_once( dirname( __FILE__ ) . '/template-sidebar.inc.php' );?></div>
<!-- end sidebar -->
		<br class="clear" /></div></div> <!-- close poststuff and post-body -->
		<br style="clear:both;" /><br /><hr />
		<p style="text-align:right;"><small><b><?php _e('Created by', 'ms_robotstxt_manager');?></b>: <a href="http://technerdia.com/" target="_blank">techNerdia</a></small></p>
</div> <!-- end wrap -->