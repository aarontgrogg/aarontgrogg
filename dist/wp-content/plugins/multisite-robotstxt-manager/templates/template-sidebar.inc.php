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
 *  ==================================== Sidebar Menu Template
 */

if( !defined( 'ABSPATH' ) ) { exit; } /* Wordpress check */ ?>

<!-- start sidebar -->
	<div class="inner-sidebar">
		<div class="postbox">
			<h3><span><?php _e('Helpful Goodies', 'ms_robotstxt_manager');?>!</span></h3>
		<div class="inside">
			<ul>
				<?php if( !$this->msrtm_version( $check = true ) ) {?>
					<li><strong><span style="color:#cc0000;font-size:16px;">Pro Automation Extension</span></strong> [<a href="http://msrtm.technerdia.com/" target="_blank"><?php _e('details', 'ms_robotstxt_manager');?></a>]<br /><?php _e('Fully automate the creation of all robots.txt files', 'ms_robotstxt_manager');?>!</li>
				<?php } ?>
				<li><strong>&raquo; <a href="http://wordpress.org/extend/plugins/multisite-robotstxt-manager/" target="_blank"><?php _e('Please Rate This Plugin', 'ms_robotstxt_manager');?>!</a></strong><br />
				<?php _e('It only takes a few seconds to', 'ms_robotstxt_manager');?> <a href="http://wordpress.org/extend/plugins/multisite-robotstxt-manager/" target="_blank"><?php _e('rate this plugin', 'ms_robotstxt_manager');?></a>! <?php _e('Your rating helps create motivation for future developments', 'ms_robotstxt_manager');?>!</li>
			</ul>
		</div></div> <!-- end inside & postbox -->

		<?php /** update notices */ if( $this->msrtm_version( $check = true ) ) { echo $this->msrtm_plugin( $type = 'updates' ); }?>
		<?php /** newsletter box */ if( !$this->msrtm_version( $check = true ) ) { echo $this->msrtm_optin(); }?>

		<div class="postbox">
			<h3><span><?php _e('The MS Robots.txt Manager', 'ms_robotstxt_manager');?></span></h3>
		<div class="inside">
			<ul>
				<li>&bull; <a href="http://msrtm.technerdia.com/" target="_blank"><?php _e('Plugin Home Page', 'ms_robotstxt_manager');?></a> : <?php _e('Project Details', 'ms_robotstxt_manager');?></li>
				<li>&bull; <a href="http://wordpress.org/extend/plugins/multisite-robotstxt-manager/" target="_blank"><?php _e('Plugin at Wordpress.org', 'ms_robotstxt_manager');?></a> : MS Robots.txt</li>
				<li>&bull; <a href="http://wordpress.org/support/plugin/multisite-robotstxt-manager" target="_blank"><?php _e('Support Forum', 'ms_robotstxt_manager');?></a> : <?php _e('Problems, Questions', 'ms_robotstxt_manager');?>?</li>
				<li>&bull; <a href="http://msrtm.technerdia.com/feedback.html" target="_blank"><?php _e('Submit Feedback', 'ms_robotstxt_manager');?></a> : <?php _e('I\'m Listening', 'ms_robotstxt_manager');?>!</li>
				<li>&bull; <a href="http://technerdia.com/projects.html" target="_blank"><?php _e('techNerdia Projects', 'ms_robotstxt_manager');?></a> : <?php _e('More Goodies!', 'ms_robotstxt_manager');?>!</li>
			</ul>
		</div></div> <!-- end inside & postbox -->

		<div class="postbox">
			<h3><span><?php _e('Robots.txt Documentation', 'ms_robotstxt_manager');?></span></h3>
		<div class="inside">
			<ul>
				<li>&bull; <a href="http://codex.wordpress.org/Search_Engine_Optimization_for_WordPress#Robots.txt_Optimization" target="_blank"><?php _e('Robots.txt Optimization Tips', 'ms_robotstxt_manager');?></a></li>
				<li>&bull; <a href="http://www.askapache.com/seo/updated-robotstxt-for-wordpress.html" target="_blank"><?php _e('AskAapche Robots.txt Example', 'ms_robotstxt_manager');?></a></li>
				<li>&bull; <a href="https://developers.google.com/webmasters/control-crawl-index/docs/faq" target="_blank"><?php _e('Google Robots.txt F.A.Q.', 'ms_robotstxt_manager');?></a></li>
				<li>&bull; <a href="https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt" target="_blank"><?php _e('Robots.txt Specifications', 'ms_robotstxt_manager');?></a></li>
				<li>&bull; <a href="http://www.robotstxt.org/db.html" target="_blank"><?php _e('Web Robots Database', 'ms_robotstxt_manager');?></a></li>
				<?php if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {?>
					<li>&bull; <a href="settings.php?tab=help&amp;page=msrtm-network.php"><?php _e('How To Use This Plugin', 'ms_robotstxt_manager');?></a></li>
				<?php } else {?>
					<li>&bull; <a href="options-general.php?tab=help&amp;page=msrtm-website.php"><?php _e('How To Use This Plugin', 'ms_robotstxt_manager');?></a></li>
				<?php }?>
			</ul>
		</div></div> <!-- end inside & postbox -->
	</div> <!-- end inner-sidebar -->
<!-- end sidebar -->