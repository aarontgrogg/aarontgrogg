<?php
/**
 * Plugin Name: Multisite Robots.txt Manager | MS Robots.txt
 * Plugin URI: http://msrtm.technerdia.com/
 * Description: A Multisite Network Robots.txt Manager. Quickly manage your Network Websites robots.txt files from a single administration area.
 * Tags: robotstxt, robots.txt, robots, robot, spiders, virtual, search, google, seo, plugin, network, wpmu, multisite, technerdia, tribalnerd
 * Version: 0.4.0
 * License: GPL
 * Author: tribalNerd
 * Author URI: http://techNerdia.com/
 *
 ***************************************************************************************
 * This program is free software; you can redistribute it and/or modify it under			*
 * the terms of the GNU General Public License as published by the Free Software			*
 * Foundation; either version 2 of the License, or (at your option) any later version.	*
 * 																												*
 * This program is distributed in the hope that it will be useful, but WITHOUT			*
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS			*
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.		*
 * 																												*
 * You should have received a copy of the GNU General Public License along with			*
 * this program; if not, please visit: http://www.gnu.org/licenses/gpl.html				*
 ***************************************************************************************
 * @author tribalNerd (tribalnerd@technerdia.com)													*
 * @copyright Copyright (c) 2012-2013, techNerdia LLC.											*
 * @link http://msrtm.technerdia.com/																	*
 * @license http://www.gnu.org/licenses/gpl.html													*
 * @version 0.4.0																								*
 ***************************************************************************************
 */

if( !defined( 'ABSPATH' ) ) { exit; } /** Wordpress check */

/**
 * ===================================================== Call, Build and Display robots.txt file
 */
class msrtm_robots_txt {
	/** ===================================================== Load proper robots.txt file */
	function __construct() {
		$check_public_blog = get_option( "blog_public" );
		if( $check_public_blog == "0" ) { return; }

		/** ===================================================== if robots.txt file, continue */
		if( substr( strrchr( $_SERVER['REQUEST_URI'], "/" ), 1 ) == "robots.txt" || $_SERVER['REQUEST_URI'] == "/robots.txt" || $_ENV['REQUEST_URI'] == "/robots.txt" ) {
			$this->msrtm_plugin();

			/** ============ show wp default robots.txt if plugin robots.txt is not set */
			if( !get_option( "ms_robotstxt" ) ) { return; }

			/** parse possible paths */
			global $current_blog;
			$blog_path 	= $current_blog->path;
			$check_path = parse_url( get_option( "siteurl" ), PHP_URL_PATH );
			$get_parts 	= explode( "/", $_SERVER['REQUEST_URI'] );

			/** if within path, init fake display */
			if( $blog_path != "/" ) {
				$fake_file = "1";
			} elseif( !empty( $check_path ) ) {
				$fake_file = "1";
			} elseif( $get_parts[1] != "robots.txt" ) {
				$fake_file = "1";
			} else {
				$fake_file = "";
			}

			/** ============ call robots.txt file */
			if( isset( $fake_file ) && $fake_file == "1" ) {
				add_action( 'init', array( &$this, 'msrtm_show_robots_txt' ) );
			}else{
			/** real robots.txt display for proper network websites */
				add_filter( 'robots_txt', array( &$this, 'msrtm_show_robots_txt' ), 10000, 0 );
			}
		} /** end robots.txt check */
	} /** end function __construct() */


	/** ===================================================== Display robots.txt file */
	function msrtm_show_robots_txt() {
		global $blog_id;
		switch_to_blog( $blog_id );

		/** get sitemap data */
		if( get_option( "ms_robotstxt_sitemap" ) ) { 			$sitemap_data = maybe_unserialize( get_option( "ms_robotstxt_sitemap" ) ); }
		if( isset( $sitemap_data['sitemap_structure'] ) ) { 	$sitemap_structure = $sitemap_data['sitemap_structure']; }
		if( isset( $sitemap_data['sitemap_show'] ) ) { 			$sitemap_show = "yes"; }

		/** build sitemap url */
		if( isset( $sitemap_structure ) && isset( $sitemap_show ) && $sitemap_show == "yes" ) {
			$temp_url = $this->msrtm_sitemap_url( $sitemap_structure );
			$sitemap_url = "\r\nSitemap: " . $this->msrtm_sitemap_url( $sitemap_structure );
		}

		/** display robots.txt */
		header( 'Status: 200 OK', true, 200 );
		header( 'Content-type: text/plain; charset='. get_bloginfo('charset') );
		do_action( 'do_robotstxt' );
			echo get_option( "ms_robotstxt" );
			if( !empty( $temp_url ) ) { echo $sitemap_url; }
			exit;
	} /** end msrtm_show_robots_txt() */


	/** ===================================================== Build Sitemap URL */
	function msrtm_sitemap_url( $sitemap_structure ) {
		if( !$sitemap_structure ) { return; }

		/** domain extensions */
		$tlds = array( 'aero', 'asia', 'biz', 'cat', 'com', 'coop', 'edu', 'gov', 'info', 'int', 'jobs', 
			'mil', 'mobi', 'museum', 'name', 'net', 'org', 'post', 'pro', 'tel', 'travel', 'xxx', 
			'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'as', 'at', 'au', 'aw', 'ax', 'az', 
			'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 
			'bz', 'ca', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'cr', 'cs', 'cu', 'cv', 'cx', 
			'cy', 'cz', 'dd', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'eh', 'er', 'es', 'et', 'eu', 'fi', 
			'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 
			'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'io', 
			'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 
			'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 
			'mk', 'ml', 'mm', 'mn', 'mo', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nc', 
			'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 
			'pm', 'pn', 'pr', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 
			'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'ss', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tf', 
			'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'um', 
			'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu', 'za', 'zm', 'zw'
		);

		/** get website url parts */
		$domain = parse_url( site_url('/') );
		$domain_scheme = $domain['scheme'];
		$domain_host = $domain['host'];

		/** get domain parts and count parts */
		$the_domain_parts = explode( ".", $domain_host );
		$count = count( $the_domain_parts );

		/** loop through each part */
		for ( $i = 0; $i < $count; $i++ ) {
			/** go to end of array */
			$end_of_array = array_pop( $the_domain_parts );

			/** reduce parts down to tlds and domain */
			if( !isset( $tld_check ) ) {
				if( in_array( $end_of_array, $tlds ) ) {
					if( isset( $extension ) ) { $extension = $extension; } else { $extension = ""; }
					$extension = '.'. $end_of_array . $extension;
				} else {
					$domain_name = $end_of_array;
					$tld_check = 1;
				}
			}
		} /** end for */

		/** define vars */
		$domain_host = strtolower( $domain_name . $extension );
		$domain_ext = preg_replace( "/^./", "", strtolower( $extension ), 1 );

		/** replace sitemap structure parts */
		$sitemap_string 	= str_replace( "[WEBSITE_URL]", $domain_host, $sitemap_structure ); 	/** domain.com */
		$sitemap_string 	= str_replace( "[DOMAIN]", $domain_name, $sitemap_string );				/** domain */
		$sitemap_url 		= str_replace( "[EXT]", $domain_ext, $sitemap_string );					/** com */

		/** return sitemap url */
		return $sitemap_url;
	} /** end function msrtm_sitemap_url() */


	/** ===================================================== Plugin extension */
	function msrtm_plugin() {
		if( is_file( WP_PLUGIN_DIR . '/msrtm-pro/ms-robotstxt.php' ) ) {
			require_once( WP_PLUGIN_DIR . '/msrtm-pro/ms-robotstxt.php' );
			$call_plugin = new msrtm_extension();
			$data = $call_plugin->msrtm_construct();
		}
	} /** end function msrtm_plugin() */
} /** end class msrtm_robots_txt */

/** call robots.txt display */
if( !is_admin() && !is_network_admin() ) { $display_robots = new msrtm_robots_txt(); }


/**
 * ========================================================================= Admin Areas
 * ========================================================================= Admin Areas
 * ========================================================================= Admin Areas
 */


/**
 * ===================================================== Setting Pages
 */
class msrtm_admin_areas {
	function __construct() {
		add_action( 'network_admin_menu', array( &$this, 'msrtm_submenu' ) ); 	/** Network Admin */
		add_action( 'admin_menu', array( &$this, 'msrtm_submenu' ) );				/** Website Admin */
		add_action( 'init', array( &$this, 'msrtm_includes' ), 1 );					/** Admin CSS File */
	}


	/** ===================================================== Add menu for proper users only */
	function msrtm_submenu() {
		if( !is_user_logged_in() ) { return; }

		/** ============ network admin area */
		if( is_super_admin() && is_network_admin() ) {
			add_submenu_page( 'settings.php', 'MS Robots.txt', 'MS Robots.txt', 'manage_options', 'msrtm-network.php', array( &$this, 'msrtm_display_admin' ) );
		}

		/** ============ website admin area */
		if( is_user_member_of_blog() && !is_network_admin() ) {
			add_options_page( 'MS Robots.txt', 'MS Robots.txt', 'manage_options', 'msrtm-website.php', array( &$this, 'msrtm_display_admin' ) );
		}
	}


	/** ===================================================== contents of both admin areas */
	/** ===================================================== default displays & process posts */
	function msrtm_display_admin() {
		if( isset( $_GET['page'] ) && !$_GET['page'] == "msrtm-website.php" || isset( $_GET['page'] ) && !$_GET['page'] == "msrtm-network.php" ) { return; }


	/**
	 * ===================================================== Defaults
	 * ===================================================== Defaults
	 * ===================================================== Defaults
	 */

		/** ===================================================== define $show_site and selected blog */
		if( isset( $_POST['show_site'] ) ) {
			$show_site = absint( $_POST['show_site'] );
			switch_to_blog( $show_site );
		}else{
			restore_current_blog();
		}


		/** ===================================================== get sitemap structure or default robots.txt depending on location */
		if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" || isset( $_POST['show_site'] ) ) {
			/** unique robots.txt file */
			if( get_option( "ms_robotstxt" ) ) { $robots_txt_file = get_option( "ms_robotstxt" ); }

			/** sitemap data */
			if( get_option( "ms_robotstxt_sitemap" ) ) {
				$get_sitemap = maybe_unserialize( get_option( "ms_robotstxt_sitemap" ) );

				if( isset( $get_sitemap['sitemap_structure'] ) ){ 	$sitemap_structure = $get_sitemap['sitemap_structure']; }
				if( isset( $get_sitemap['sitemap_show'] ) ){ 		$sitemap_show = $get_sitemap['sitemap_show']; }

				/** build sitemap url - for display in admin area */
				if( isset( $sitemap_show ) && $sitemap_show == "yes" ) {
					$msrtm_robots_txt = new msrtm_robots_txt();
					$sitemap_url 		= $msrtm_robots_txt->msrtm_sitemap_url( $sitemap_structure );
				}
			}
		}else{
			/** default network robots.txt file */
			$default_option = maybe_unserialize( get_option( "ms_robotstxt_default" ) );
			if( isset( $default_option['default_robotstxt'] ) ){ 	$robots_txt_file = $default_option['default_robotstxt']; }
			if( isset( $default_option['sitemap_show'] ) ){ 		$sitemap_show = $default_option['sitemap_show']; }
			/** sitemap structure set below */
		}


		/** ===================================================== display sitemap structure url */
		if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {
			if( !isset( $show_site ) ) { $sitemap_structure = ""; }																						/** reset structure url */
			if( !isset( $_POST['sitemap_show'] ) && !isset( $_POST['sitemap_structure'] ) && !isset( $show_site ) ) {					/** default structure */
				if( isset( $default_option['sitemap_structure'] ) ) { $sitemap_structure = $default_option['sitemap_structure']; }
			}
			if( !isset( $_POST['sitemap_show'] ) && isset( $_POST['default_ms_robotstxt'] ) ) { $sitemap_structure = ""; }				/** default structure */
			if( isset( $_POST['sitemap_structure'] ) ) { $sitemap_structure = $_POST['sitemap_structure']; }								/** post carry over */
			if( !isset( $_POST['sitemap_show'] ) && isset( $_POST['sitemap_structure'] ) ) {														/** set structure url */
				if( isset( $default_option['sitemap_structure'] ) ) { $sitemap_structure = $_POST['sitemap_structure']; }
			}
			if( !isset( $sitemap_structure ) ) { $sitemap_structure = ""; }																			/** clear structure url */
		}


		/** ===================================================== add checked if show sitemap is checked */
		$checked = "";
		if( isset( $_POST['sitemap_show'] ) && $_POST['sitemap_show'] == "yes" ) {
			$checked = "checked";
		}elseif( !isset( $_POST['sitemap_show'] ) && isset( $_POST['sitemap_hidden'] ) && $_POST['sitemap_hidden'] == "1" ) {
			$checked = "";
		}else{
			if( isset( $sitemap_show ) && $sitemap_show == "yes" ) {
				$checked = "checked";
			}
		}



	/**
	 * ===================================================== Posts For Both Templates
	 * ===================================================== Posts For Both Templates
	 * ===================================================== Posts For Both Templates
	 */


		/** =====================================================  reset website option to default saved robots.txt file */
		if( isset( $_POST['reset_this_website'] ) ) {
			if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }

			/** get proper blog id */
			if( !isset( $show_site ) ) {
				global $blog_id;
				$show_site = $blog_id;
			}

			/** call reset function */
			$status = $this->msrtm_reset_website( $show_site );

			/** display presets */
			switch_to_blog(1);
				$get_default_option 	= maybe_unserialize( get_option( "ms_robotstxt_default" ) );
				$robots_txt_file 		= $get_default_option['default_robotstxt'];
				if( isset( $get_default_option['sitemap_structure'] ) ) { $sitemap_structure = $get_default_option['sitemap_structure']; }
				if( isset( $get_default_option['sitemap_show'] ) ) { $sitemap_show = $get_default_option['sitemap_show']; }
				if( isset( $sitemap_show ) && $sitemap_show == "yes" ) { $checked = "checked"; } else { $checked = ""; }
			restore_current_blog();

			/** return notices */
			if( in_array( "notice", $status ) ) { $notice = $status['1']; }
			if( !empty( $notice ) ) { $notice = __( $notice, 'ms_robotstxt_manager' ); }
		}


		/** ===================================================== delete options from this website */
		if( isset( $_POST['disable_this_website'] ) ) {
			if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }

			/** define show_site if not set */
			if( !isset( $show_site ) ) {
				global $blog_id;
				$show_site = $blog_id;		
			}

			/** clear options */
			switch_to_blog($show_site);
				delete_option( "ms_robotstxt" );
				delete_option( "ms_robotstxt_sitemap" );
			restore_current_blog();

			/** clear display after post */
			$sitemap_structure = "";
			$checked = "";

			/** update notice */
			$notice = __( 'The Multisite Robots.txt Manager Is No Longer Active On This Website.', 'ms_robotstxt_manager' );
		}


		/** ===================================================== update a websites robots.txt file and sitemap structure */
		if( isset( $_POST['update_ms_robotstxt'] ) ) {
			/** ============ error checks */
			if( empty( $_POST['robotstxt_option'] ) ) { wp_die( __('Sorry, you can not save a blank default robots.txt file. You can however, press the "disable this website" button to remove the robots.txt file managed by this plugin. Press your browsers back button to try again.', 'ms_robotstxt_manager') ); }
			if( !empty( $_POST['sitemap_show'] ) && empty( $_POST['sitemap_structure'] ) ) { wp_die( __('To use the Sitemap Feature you must enter a Sitemap URL.', 'ms_robotstxt_manager') ); }
			if( !empty( $_POST['sitemap_show'] ) && !preg_match( "/http/i", $_POST['sitemap_structure'] ) ) { wp_die( __('Error: You must include http:// or https:// with the sitemap structure url.', 'ms_robotstxt_manager') ); }

			/** ============ presets */
			if( empty( $_POST['sitemap_structure'] ) ) { $sitemap_structure = ""; }
			if( isset( $_POST['sitemap_show'] ) && $_POST['sitemap_show'] == "yes" ) { $sitemap_show = "yes"; }

			/** get post data */
			$robots_txt_file = strip_tags( $_POST['robotstxt_option'] );

			/** ============ clean build option */
			delete_option( "ms_robotstxt" );
			add_option( "ms_robotstxt", $robots_txt_file, '', "no" );

			/** get blog id */
			if( isset( $_POST['show_site'] ) ) {
				$this_site = absint( $_POST['show_site'] );
			} else {
				global $blog_id;
				$this_site = $blog_id;
			}

			/** build sitemap option */
			$this->msrtm_sitemap_option( $this_site );

			/** update notice */
			$notice = __('Robots.txt File Updated.', 'ms_robotstxt_manager') . ' [ <a href="'. get_site_url( $this_site, '/robots.txt' ) .'" target="_blank">'. __('view', 'ms_robotstxt_manager') .' changes</a> ]';
		} /** end if $_POST['update_ms_robotstxt'] */


		/** ===================================================== presets and examples */
		if( isset( $_GET['tab'] ) && $_GET['tab'] == "presets" ) {
			/** display default presets */
			$presets 					= new msrtm_defaults_presets();
			$default_robotstxt 		= $presets->msrtm_default_robotstxt();
			$google_robotstxt 		= $presets->msrtm_google_robotstxt();
			$default_robotstxt_old 	= $presets->msrtm_old_robotstxt();
			$mini_robotstxt 			= $presets->msrtm_mini_robotstxt();
			$blogger_robotstxt 		= $presets->msrtm_blogger_robotstxt();
			$blocked_robotstxt 		= $presets->msrtm_blocked_robotstxt();

			/** preset sitemap structure and show sitemap */
			switch_to_blog(1);
				$get_sitemap = maybe_unserialize( get_option( "ms_robotstxt_sitemap" ) );
				if( isset( $get_sitemap['sitemap_structure'] ) ){ 	$sitemap_structure = $get_sitemap['sitemap_structure']; }
				if( isset( $get_sitemap['sitemap_show'] ) ){ 		$sitemap_show = $get_sitemap['sitemap_show']; }
			restore_current_blog();

			/** ============ if preset post */
			if( isset( $_POST['preset_default'] ) || isset( $_POST['preset_google'] ) || isset( $_POST['preset_default_old'] ) || isset( $_POST['preset_open'] ) || isset( $_POST['preset_blog'] ) || isset( $_POST['preset_kill'] ) ) {
				if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }

				/** ============ error checks */
				if( !empty( $_POST['sitemap_show'] ) && empty( $_POST['sitemap_structure'] ) ) { wp_die( __('To use the Sitemap Feature you must enter a Sitemap URL.', 'ms_robotstxt_manager') ); }
				if( !empty( $_POST['sitemap_show'] ) && !preg_match( "/http/i", $_POST['sitemap_structure'] ) ) { wp_die( __('Error: You must include http:// or https:// with the sitemap structure url.', 'ms_robotstxt_manager') ); }

				/** ============ preset robots.txt value if selected */
				$preset_robotstxt = "";
				if( isset( $_POST['preset_default'] ) ) { 		$preset_robotstxt = $default_robotstxt; }
				if( isset( $_POST['preset_google'] ) ) { 			$preset_robotstxt = $google_robotstxt; }
				if( isset( $_POST['preset_default_old'] ) ) { 	$preset_robotstxt = $default_robotstxt_old; }
				if( isset( $_POST['preset_open'] ) ) { 			$preset_robotstxt = $mini_robotstxt; }
				if( isset( $_POST['preset_blog'] ) ) { 			$preset_robotstxt = $blogger_robotstxt; }
				if( isset( $_POST['preset_kill'] ) ) { 			$preset_robotstxt = $blocked_robotstxt; }

				/** preset sitemap structure */
				if( isset( $_POST['sitemap_structure'] ) ) { $sitemap_structure = $_POST['sitemap_structure']; }
				
				/** ===================================================== add checked if show sitemap is checked */
				$checked = "";
				if( isset( $_POST['sitemap_show'] ) && $_POST['sitemap_show'] == "yes" ) {
					$checked = "checked";
				}elseif( !isset( $_POST['sitemap_show'] ) && isset( $_POST['sitemap_hidden'] ) && $_POST['sitemap_hidden'] == "1" ) {
					$checked = "";
				}else{
					if( isset( $sitemap_show ) && $sitemap_show == "yes" ) {
						$checked = "checked";
					}
				}

				/** call reset function */
				$status = $this->msrtm_presets( $preset_robotstxt );

				/** ============ return notices */
				if( in_array( "notice", $status ) ) { $notice = $status['1']; }
				if( !empty( $notice ) ) { $notice = __( $notice, 'ms_robotstxt_manager' ); }
			} /** end if preset posts */
		} /** end if tab = presets */



	/**
	 * ===================================================== Error Correction Posts
	 * ===================================================== Error Correction Posts
	 * ===================================================== Error Correction Posts
	 */


		/** ===================================================== checks for missing robots.txt rewrite rules */
		if( isset( $_POST['msrtm_rules_check'] ) || !get_option( 'msrtm_rules_check' ) || get_option( 'msrtm_rules_check' ) == "1" ) {
			if( isset( $_POST['msrtm_rules_check'] ) ) { delete_option( 'msrtm_rules_check' ); }			
			$msrtm_rules = $this->msrtm_rules_check();
			if( !empty( $msrtm_rules ) ) { $msrtm_rules = "1"; } /** for template display */
			if( isset( $_POST['msrtm_rules_check'] ) ) { $notice = __( 'Rules Check Completed.', 'ms_robotstxt_manager' ); }
		} /** end if !get_option( 'msrtm_rules_check' ) */


		/** ===================================================== checks for old robots.txt plugin data */
		if( isset( $_POST['msrtm_old_check'] ) || !get_option( 'msrtm_plugin_check' ) || get_option( 'msrtm_plugin_check' ) == "1" ) {
			if( isset( $_POST['msrtm_old_check'] ) ) { delete_option( 'msrtm_plugin_check' ); }
			$msrtm_warning = $this->msrtm_old_check();
			if( !empty( $msrtm_warning ) ) { $msrtm_warning = "1"; } /** for template display */
			if( isset( $_POST['msrtm_old_check'] ) ) { $notice = __( 'Old Plugin Data Check Completed.', 'ms_robotstxt_manager' ); }
		} /** end if !get_option( 'msrtm_rules_check' ) */


		/** ===================================================== removes other plugins left over robots.txt file data */
		if( isset( $_POST['update_old'] ) ) {
			if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }

			/** query db */
			global $wpdb;
			$blog_ids = $wpdb->get_results( 'SELECT blog_id FROM '. $wpdb->blogs .' ORDER BY blog_id' );

			/** remove from each blog */
			foreach ( $blog_ids as $value ) {
				$id = $value->blog_id;
				switch_to_blog($id);
					delete_option( 'kb_robotstxt' );
					delete_option( 'cd_rdte_content' );
					delete_option( 'pc_robotstxt' );
					remove_action( 'do_robots', 'do_robots' );
					remove_filter( 'robots_txt', 'cd_rdte_filter_robots' );
					remove_filter( 'robots_txt', 'ljpl_filter_robots_txt' );
					remove_filter( 'robots_txt', 'robots_txt_filter' );
			} /** end foreach */

			switch_to_blog(1);

			delete_option( "msrtm_plugin_check" );
			add_option( "msrtm_plugin_check", "0", '', "no" );

			/** update notice */
			$notice = __( 'Old Plugin Data Has Been Removed.', 'ms_robotstxt_manager' );
		} /** end isset( $_POST['update_old'] ) */


		/** ===================================================== removes other plugins left over robots.txt file data */
		if( isset( $_POST['update_rules'] ) ) {
			if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }

			/** get the current user id of the current user */
			global $current_user, $wp_rewrite;
			get_currentuserinfo();
			$this_admin_user = $current_user->ID;

			/** get blog id's allowed by this user user id */
			$users_blogs = get_blogs_of_user( $this_admin_user );

			/** for each allowed blog */
			foreach( $users_blogs as $user_blog ) {
				/** switch to each blog */
				switch_to_blog( $user_blog->userblog_id );

				$check_rules = get_option( "rewrite_rules" );

				if( empty( $check_rules ) ) {
					$wp_rewrite->flush_rules();
				}

				$get_rules = get_option( "rewrite_rules" );

				if( !in_array( "index.php?robots=1", $get_rules ) ) {
					$rule_key = "robots\.txt$";
					$get_rules[ $rule_key ] = 'index.php?robots=1';
					update_option( "rewrite_rules", $get_rules );
				}
			} /** end foreach */

			switch_to_blog(1);

			/** update notice */
			$notice = __( 'The Missing Rewrite Rules Have Been Added.', 'ms_robotstxt_manager' );
		} /** end isset( $_POST['update_rules'] ) */



	/**
	 * ===================================================== Posts For Network Template Only
	 * ===================================================== Posts For Network Template Only
	 * ===================================================== Posts For Network Template Only
	 */


		/** ===================================================== update the network default robots.txt file and sitemap structure */
		if( isset( $_POST['default_ms_robotstxt'] ) ) {
			if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }

			/** ============ error checks */
			if( empty( $_POST['robotstxt_option'] ) ) { wp_die( __('Sorry, you can not save a blank default robots.txt file. You can however, clear the textarea (do not save) and publish to network a blank robots.txt file that all Websites will use. Press your browsers back button to try again.', 'ms_robotstxt_manager') ); }
			if( !empty( $_POST['sitemap_show'] ) && empty( $_POST['sitemap_structure'] ) ) { wp_die( __('To use the Sitemap Feature you must enter a Sitemap URL.', 'ms_robotstxt_manager') ); }
			if( !empty( $_POST['sitemap_show'] ) && !preg_match( "/http/i", $_POST['sitemap_structure'] ) ) { wp_die( __('Error: You must include http:// or https:// with the sitemap structure url.', 'ms_robotstxt_manager') ); }

			/** ============ get robots.txt data */
			if( isset( $_POST['robotstxt_option'] ) ) { $robots_txt_file = strip_tags( $_POST['robotstxt_option'] ); }

			/** get show sitemap checkbox */
			if( isset( $_POST['sitemap_show'] ) && $_POST['sitemap_show'] == "yes" ) { $sitemap_show = "yes"; }
			if( empty( $_POST['sitemap_show'] ) ) { $sitemap_show = ""; }

			/** ============ get sitemap structure */
			if( isset( $_POST['sitemap_structure'] ) ) { $sitemap_structure = sanitize_text_field( $_POST['sitemap_structure'] ); }
			if( empty( $_POST['sitemap_structure'] ) ) { $sitemap_structure = ""; }

			/** ============ default option array */
			if( isset( $sitemap_show ) && isset( $sitemap_structure ) ) {
				$default_array = array( 'sitemap_show' => 'yes', 'sitemap_structure' => $sitemap_structure, 'default_robotstxt' => $robots_txt_file );
			}

			/** ============ default option array */
			if( empty( $sitemap_show ) && isset( $sitemap_structure ) ) {
				$default_array = array( 'sitemap_structure' => $sitemap_structure, 'default_robotstxt' => $robots_txt_file );
			}
				
			/** ============ default option array */
			if( empty( $sitemap_show ) && empty( $sitemap_structure ) ) {
				$default_array = array( 'default_robotstxt' => $robots_txt_file );
			}


			/** ============ clean and build option */
			delete_option( "ms_robotstxt_default" );
			add_option( "ms_robotstxt_default", $default_array, '', "no" );

			/** update notice */
			$notice = __('Default Robots.txt Data Saved. Click the "publish to network" button to commit the update to all Websites within the Network.', 'ms_robotstxt_manager');
		} /** if isset( $_POST['default_ms_robotstxt'] ) */


		/** ===================================================== publish robots.txt file to network */
		if( isset( $_POST['publish_ms_robotstxt'] ) ) {
			if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }

			/** ============ get sitemap structure */
			if( isset( $_POST['sitemap_structure'] ) ) { $sitemap_structure = sanitize_text_field( $_POST['sitemap_structure'] ); }
			if( empty( $_POST['sitemap_structure'] ) ) { $sitemap_structure = ""; }

			/** presets */
			if( isset( $_POST['sitemap_show'] ) && $_POST['sitemap_show'] == "yes" ) { $sitemap_show = "yes"; }

			/** ============ error check */
			if( isset( $sitemap_show ) && $sitemap_show == "yes" && empty( $sitemap_structure ) ) { wp_die( __('To use the Sitemap Feature you must enter a Sitemap URL.', 'ms_robotstxt_manager') ); }
			if( !empty( $sitemap_structure ) && !preg_match( "/http/i", $sitemap_structure ) ) { wp_die( __('Error: You must include http:// or https:// with the sitemap structure url.', 'ms_robotstxt_manager') ); }

			/** get blog ids for blog switch and path */
			global $wpdb, $current_user;
			get_currentuserinfo();
			$this_admin_user = $current_user->ID;

			/** ============ get robots.txt file */
			if( isset( $_POST['robotstxt_option'] ) ) { $robots_txt_file = strip_tags( $_POST['robotstxt_option'] ); }

			/** get blog id's allowed by this user user id */
			$users_blogs = get_blogs_of_user( $this_admin_user );

			/** ============ allowed blogs */
			foreach ( $users_blogs as $users_blog_id ) {
				/** switch to each blog */
				switch_to_blog( $users_blog_id->userblog_id );

				/** clean and build option */
				delete_option( "ms_robotstxt" );
				add_option( "ms_robotstxt", $robots_txt_file, '', "no" );

				/** build sitemap option */
				$this->msrtm_sitemap_option( $users_blog_id->userblog_id );
			} /** end foreach */

			/** update notice */
			$notice = __('Robots.txt File Published To All Network Websites.', 'ms_robotstxt_manager');
		} /** end if $_POST['publish_ms_robotstxt'] */


		/** ===================================================== reset the default network robots.txt file */
		if( isset( $_POST['reset_this_default'] ) ) {
			if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }

			$get_defaults = new msrtm_defaults_presets();
			$robots_txt_file = $get_defaults->msrtm_default_robotstxt();

			/** display presets */
			if( isset( $default_option_value ) ) { $default_option_value = $default_option_value; }
			if( isset( $sitemap_structure ) ) { $sitemap_structure = ""; }
			if( isset( $checked ) ) { $checked = ""; }

			/** clear and build option */
			delete_option( "ms_robotstxt_default" );
			add_option( "ms_robotstxt_default", array( 'default_robotstxt' => $robots_txt_file ), '', "no" );

			$notice = __('Default Settings Have Been Restored.', 'ms_robotstxt_manager');
		}


		/** ============ display templates */
		ob_start();
			require_once( dirname( __FILE__ ) . '/templates/template-admin.inc.php' );
		ob_end_flush();
	} /** end msrtm_display_admin() */


	/**
 	 * ===================================================== Class Functions
 	 * ===================================================== Class Functions
 	 * ===================================================== Class Functions
 	 */

	/** ===================================================== Checks for old robots.txt plugin data */
	function msrtm_old_check() {
		$msrtm_warning = "";

		if( !get_option( 'msrtm_plugin_check' ) || get_option( 'msrtm_plugin_check' ) == "1" ) {

			/** get blog ids for blog switch and path */
			global $current_user;
			get_currentuserinfo();
			$this_admin_user = $current_user->ID;
			$users_blogs = get_blogs_of_user( $this_admin_user );

			/** ============ allowed blogs */
			foreach ( $users_blogs as $users_blog_id ) {
				/** switch to each blog */
				switch_to_blog( $users_blog_id->userblog_id );
					if( get_option( 'pc_robotstxt' ) ) { $msrtm_warning = "1"; }
					if( get_option( 'kb_robotstxt' ) ) { $msrtm_warning = "1"; }
					if( get_option( 'cd_rdte_content' ) ) { $msrtm_warning = "1"; }
			} /** end foreach */

			switch_to_blog(1);

			/** if plugin data detected, set option to 1, else set to 0 to stop checks */
			if( !empty( $msrtm_warning ) && $msrtm_warning == "1" ) {
				delete_option( "msrtm_plugin_check" );
				add_option( "msrtm_plugin_check", "1", '', "no" );
			} else {
				delete_option( "msrtm_plugin_check" );
				add_option( "msrtm_plugin_check", "0", '', "no" );
			}
		} /** end if !get_option( 'msrtm_plugin_check' ) */
			
		if( get_option( 'msrtm_plugin_check' ) == "1" ) { $msrtm_warning = "1"; }
			
		return $msrtm_warning;
	} /** end function msrtm_old_check() */
			
			
			
			
	/** ===================================================== Check if rewrite_rules option and robots.txt key is set */
	function msrtm_rules_check() {
		$msrtm_rules = "";

		/** query db */
		global $wpdb, $wp_rewrite;
		$blog_ids = $wpdb->get_results( 'SELECT blog_id FROM '. $wpdb->blogs .' ORDER BY blog_id' );

		/** get rules for each site, return 1 if robots value not found */
		foreach ( $blog_ids as $value ) {
			$id = $value->blog_id;
			switch_to_blog($id);

			$get_rules = get_option( "rewrite_rules" );

			if( !in_array( "index.php?robots=1", $get_rules ) ) { $msrtm_rules = "1"; }
		} /** end foreach */

		switch_to_blog(1);

		/** if no rule detected, set option to 1, else set to 0 to stop checks */
		if( !empty( $msrtm_rules ) && $msrtm_rules == "1" ) {
			delete_option( "msrtm_rules_check" );
			add_option( "msrtm_rules_check", "1", '', "no" );
		} else {
			delete_option( "msrtm_rules_check" );
			add_option( "msrtm_rules_check", "0", '', "no" );
		}

		if( get_option( 'msrtm_rules_check' ) == "1" ) { $msrtm_rules = "1"; }
			
		return $msrtm_rules;
	} /** end function msrtm_rules_check() */


	/** ===================================================== Update Default or Websites Robots.txt With Presets */
	function msrtm_presets( $preset_robotstxt ) {

		/** ============ update the default robotst.txt or a selected websites robots.txt */
		if( isset( $_POST['selected_site'] ) && $_POST['selected_site'] == "robotstxt_network_set" ) {

			/** get show sitemap checkbox */
			if( isset( $_POST['sitemap_show'] ) && $_POST['sitemap_show'] == "yes" ) { $sitemap_show = "yes"; }
			if( empty( $_POST['sitemap_show'] ) ) { $sitemap_show = ""; }

			/** ============ get sitemap structure */
			if( isset( $_POST['sitemap_structure'] ) ) { $sitemap_structure = sanitize_text_field( $_POST['sitemap_structure'] ); }
			if( empty( $_POST['sitemap_structure'] ) ) { $sitemap_structure = ""; }

			/** ============ default option array */
			if( isset( $sitemap_show ) && isset( $sitemap_structure ) ) {
				$default_array = array( 'sitemap_show' => 'yes', 'sitemap_structure' => $sitemap_structure, 'default_robotstxt' => $preset_robotstxt );
			}

			/** ============ default option array */
			if( empty( $sitemap_show ) && isset( $sitemap_structure ) ) {
				$default_array = array( 'sitemap_structure' => $sitemap_structure, 'default_robotstxt' => $preset_robotstxt );
			}
				
			/** ============ default option array */
			if( empty( $sitemap_show ) && empty( $sitemap_structure ) ) {
				$default_array = array( 'default_robotstxt' => $preset_robotstxt );
			}

			/** clear and add network option */
			switch_to_blog(1);
				delete_option( "ms_robotstxt_default" );
				add_option( "ms_robotstxt_default", $default_array, '', "no" );
			restore_current_blog();
		}else{
			/** define selected site */
			if( isset( $_POST['selected_site'] ) ) {
				$selected_site = absint( $_POST['selected_site'] );
			} else {
				global $blog_id;
				$selected_site = $blog_id;
			}

			/** clear and add website option */
			switch_to_blog($selected_site);
				delete_option( "ms_robotstxt" );
				add_option( "ms_robotstxt", $preset_robotstxt, '', "no" );
			restore_current_blog();

			/** build sitemap option */
			$this->msrtm_sitemap_option( $show_site = $selected_site );
		} /** end if $_POST['selected_site'] == "robotstxt_network_set" */

		/** site id for view changes link */
		if( isset( $_POST['selected_site'] ) && $_POST['selected_site'] == "robotstxt_network_set" ) {
			$site_id = "1";
		} else {
			if( isset( $_POST['selected_site'] ) ) { $selected_site = $_POST['selected_site']; }
			$siteid = absint( $selected_site );
		}

		if( isset( $_GET['tab'] ) && $_GET['tab'] == "presets" ) {
			if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {
				$notice = __('Default Robots.txt For The Network Has Been Updated.', 'ms_robotstxt_manager');
			} else {
				$notice = __('The Robots.txt For This Website Has Been Updated.', 'ms_robotstxt_manager');
			}
		}else{
			$notice = __('The Selected Robots.txt File Has Been Published.', 'ms_robotstxt_manager') . ' [ <a href="'. get_site_url( $siteid, '/robots.txt' ) .'" target="_blank">'. __('view changes', 'ms_robotstxt_manager') .'</a> ]';
		}
		
		$notice = array( 'notice', $notice );
		return $notice;
	} /** end function msrtm_presets() */


	/** ===================================================== Reset A Websites Robots.txt Back To Default */
	function msrtm_reset_website( $show_site ) {
		/** sanatize */
		$show_site = absint( $show_site );

		/** get defaults */
		switch_to_blog(1);
		$get_default_option = maybe_unserialize( get_option( "ms_robotstxt_default" ) );
		if( isset( $get_default_option['default_robotstxt'] ) ) { $robots_txt_file = $get_default_option['default_robotstxt']; }
		if( isset( $get_default_option['sitemap_structure'] ) ) { $sitemap_structure = $get_default_option['sitemap_structure']; }
		if( isset( $get_default_option['sitemap_show'] ) ) { $sitemap_show = $get_default_option['sitemap_show']; }

		switch_to_blog( $show_site );

		/** clear and build sitemap option */
		$this->msrtm_sitemap_option( $show_site );

		/** clear and update robots.txt file */
		delete_option( "ms_robotstxt" );
		add_option( "ms_robotstxt", $robots_txt_file, '', "no" );

		restore_current_blog();

		/** return data */
		$notice = __('Robots.txt File Updated To The Default Version.', 'ms_robotstxt_manager') . ' [ <a href="'. get_site_url( $show_site, '/robots.txt' ) .'" target="_blank">'. __('view', 'ms_robotstxt_manager') .' changes</a> ]';
		$notice = array( 'notice', $notice );
		return $notice;
	}


	/** ===================================================== Build Sitemap Option */
	function msrtm_sitemap_option( $show_site ) {
		if( !$show_site ) { return; }

		/** sanatize */
		$show_site = absint( $show_site );

		/** set vars */
		if( !isset( $_POST['sitemap_structure'] ) || isset( $_POST['reset_this_website'] ) ) {
			switch_to_blog(1);
				$get_default_option 	= maybe_unserialize( get_option( "ms_robotstxt_default" ) );
				if( isset( $get_default_option['sitemap_structure'] ) ) { 	$sitemap_structure = $get_default_option['sitemap_structure']; }
				if( isset( $get_default_option['sitemap_show'] ) ) { 			$sitemap_show = $get_default_option['sitemap_show']; }
			restore_current_blog();
		} else {
			$sitemap_structure = sanitize_text_field( $_POST['sitemap_structure'] );
			if( isset( $_POST['sitemap_show'] ) && $_POST['sitemap_show'] == "yes" ) { $sitemap_show = "yes"; }
			if( empty( $_POST['sitemap_show'] ) ) { $sitemap_show = ""; }
		}

		switch_to_blog($show_site);

		/** set sitemap and structure */
		if( isset( $sitemap_structure ) && isset( $sitemap_show ) && $sitemap_show == "yes" ) {
			$sitemap_array = array( 'sitemap_show' => 'yes', 'sitemap_structure' => $sitemap_structure );
		}

		/** set only the structure */
		if( isset( $sitemap_structure ) && isset( $sitemap_show ) && $sitemap_show != "yes" ) {
			$sitemap_array = array( 'sitemap_structure' => $sitemap_structure );
		}

		/** clean and build option */
		delete_option( "ms_robotstxt_sitemap" );

		if( isset( $_POST['disable_this_website'] ) || empty( $sitemap_structure ) ) {
			delete_option( "ms_robotstxt_sitemap" );
		} else {
			delete_option( "ms_robotstxt_sitemap" );
			add_option( "ms_robotstxt_sitemap", $sitemap_array, '', "no" );
		}
		
		restore_current_blog();
	}


	/** ===================================================== Gets Site ID's and Domain Name for Dropdown */
	function msrtm_select_site() {
		/** get the current user id of the current user */
		global $current_user;
		get_currentuserinfo();
		$this_admin_user = $current_user->ID;

		/** get blog id's allowed by this user user id */
		$users_blogs = get_blogs_of_user( $this_admin_user );

		/** create dropdown option list */
		foreach( $users_blogs as $user_data ) {
			$selected = "";
			if( isset( $_POST['show_site'] ) && $user_data->userblog_id == $_POST['show_site'] ) { $selected = "selected"; }
			if( isset( $_POST['selected_site'] ) && $user_data->userblog_id == $_POST['selected_site'] ) { $selected = "selected"; }
			if( isset( $_GET['open'] ) && $user_data->userblog_id == $_GET['open'] ) { $selected = "selected"; }
			
			if( $user_data->blogname ) { $blog_name = $user_data->blogname; } else { $blog_name = $user_data->domain; }
			
			echo '<option value="'. $user_data->userblog_id .'" '. $selected .'>('. $user_data->userblog_id .') '. $blog_name .'</option>';
		}
	}


	/** ===================================================== Tabs */
	function msrtm_tabs() {
		/** default */
		if( isset( $_GET['tab'] ) ){ $current = $_GET['tab']; } else { $current = "settings"; }

		/** network admin tabs */
		if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {
			$more_tabs = $this->msrtm_plugin( $type = 'tabs' );
			if( !empty( $more_tabs ) ) {
				$tabs = $this->msrtm_plugin( $type = 'tabs' );
			} else {
				$tabs = array( 'settings' => __('Defaults', 'ms_robotstxt_manager'), 'presets' => __('Presets', 'ms_robotstxt_manager'), 'help' => __('Help', 'ms_robotstxt_manager') );
			}
		}

		/** website admin tabs */
		if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {
			$tabs = array( 'settings' => __('Home', 'ms_robotstxt_manager'), 'presets' => __('Presets', 'ms_robotstxt_manager'), 'help' => __('Help', 'ms_robotstxt_manager') );
		}

		/** tab wrap and link */
		$page = sanitize_file_name( $_GET['page'] );
		$tab_menu = '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				$tab_menu .= '<a class="nav-tab'. $class .'" href="?tab='. $tab .'&amp;page='. $page .'">'. $name .'</a>';
			}

			/** network home link */
			if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {
				$tab_menu .= '<a class="nav-tab" href="'. network_home_url() .'wp-admin/network/settings.php?page=msrtm-network.php">Network Admin</a>';
			}
		$tab_menu .= '</h2><br />';

		return $tab_menu;
	}


	/** ===================================================== Plugin extension */
	function msrtm_plugin( $type ) {
		if( is_file( WP_PLUGIN_DIR . '/msrtm-pro/ms-robotstxt.php' ) ) {
			require_once( WP_PLUGIN_DIR . '/msrtm-pro/ms-robotstxt.php' );
			$call_plugin = new msrtm_extension();

				if( isset( $type ) && $type == "admin" ) {
					if( is_plugin_active_for_network( 'msrtm-pro/msrtm-pro.php' ) ) {
						$data = $call_plugin->msrtm_auto_admin();															/** extended admin area */
					} else {
						$data = $call_plugin->msrtm_activate_me();														/** inactive plugin message */
					}
				}

				if( isset( $type ) && $type == "updates" ) { $data = $call_plugin->msrtm_newsletter(); } 	/** newsletter */
				if( isset( $type ) && $type == "tabs" ) { $data = $call_plugin->msrtm_more_tabs(); } 		/** extra tabs */
			return $data;
		}
	} /** end function msrtm_plugin() */


	/** ===================================================== Tabs as text links - used at bottom of admin pages */
	function msrtm_tab_links() {
		$page = sanitize_file_name( $_GET['page'] );

		/** displayed page change style */
		$style = "font-weight:normal;text-decoration:none;";
		$footer_links = "";
		$style1 = "";
		$style2 = "";
		$style3 = "";
		$style4 = "";

		if( isset ( $_GET['page'] ) && !isset ( $_GET['tab'] ) ) { 		$style1 = $style; }
		if( isset ( $_GET['tab'] ) && $_GET['tab'] == "settings" ) { 	$style1 = $style; }
		if( isset ( $_GET['tab'] ) && $_GET['tab'] == "auto" ) { 		$style2 = $style; }
		if( isset ( $_GET['tab'] ) && $_GET['tab'] == "presets" ) { 	$style3 = $style; }
		if( isset ( $_GET['tab'] ) && $_GET['tab'] == "help" ) { 		$style4 = $style; }
		
		$footer_links = '<p align="right" style="font-size:10px;font-weight:bold;margin:45px 25px 10px 0;">&bull; ';
		
		/** link - network */
		if( is_super_admin() && !is_network_admin() ) {
			$footer_links .= '<a href="'. network_home_url() .'wp-admin/network/settings.php?page='. $page .'">'. __('Network Admin', 'ms_robotstxt_manager') .'</a> | ';
		}

		/** link - create manage */
		$footer_links .= '<a style="'. $style1 .'" href="?tab=settings&amp;page='. $page .'">'. __('Default', 'ms_robotstxt_manager') .'</a> | ';

		/** link - automate */
		if( is_network_admin() && $this->msrtm_version( $check = true ) ) {
			$footer_links .= '<a style="'. $style2 .'" href="?tab=auto&amp;page='. $page .'">'. __('Automate', 'ms_robotstxt_manager') .'</a> | ';
		}

		/** link - presets examples */
		$footer_links .= '<a style="'. $style3 .'" href="?tab=presets&amp;page='. $page .'">'. __('Presets', 'ms_robotstxt_manager') .'</a> | ';

		/** link - how to use */
		$footer_links .= '<a style="'. $style4 .'" href="?tab=help&amp;page='. $page .'">'. __('Help', 'ms_robotstxt_manager') .'</a> | ';

		/** link - top of page */
		$footer_links .= '<a href="'. esc_attr( $_SERVER['REQUEST_URI'] ) .'#top" style="color:#cc0000">'. __('Top', 'ms_robotstxt_manager') .'</a> ^';

		$footer_links .= '</p>';
		return $footer_links;
	} /** end function msrtm_tab_links() */


	/** ===================================================== Include CSS */
	function msrtm_optin() {
		ob_start();?>
		<div class="postbox">
			<h3><span><?php _e('Newsletter', 'ms_robotstxt_manager');?></span></h3>
		<div class="inside">
			<ul>
				<li><p><strong><?php _e('Subscribe to the MS Robots.txt Manager Wordpress Plugin Newsletter', 'ms_robotstxt_manager');?></strong><br />
					<small>Product News, Updates, Bug Fixes, Specials &amp; More...</small></p>
				 <form method="post" class="af-form-wrapper" action="http://www.aweber.com/scripts/addlead.pl">
					<input type="hidden" name="redirect" value="http://msrtm.technerdia.com/news/thanks.html" id="redirect_d9a087264565f2c613fd5ce16d490c53" />
					<input type="hidden" name="meta_web_form_id" value="674163288" />
					<input type="hidden" name="meta_adtracking" value="Main_Form" />
					<input type="hidden" name="meta_required" value="email,name" />
					<input type="hidden" name="listname" value="msrtm-leads" />
					<input type="hidden" name="meta_message" value="1" />
					<input type="hidden" name="meta_split_id" value="" />
					<input type="hidden" name="meta_tooltip" value="" />
						<p><input type="text" name="email" value="" tabindex="500" placeholder="First Name" size="40" /><br />
						<input type="text" name="name" value="" tabindex="501" placeholder="Email Address" size="40" /></p>
						<p class="center"><input name="submit" type="submit" value=" Subscribe Today " tabindex="502" class="button button-primary" /><br style="clear:both;" /></p>
				 </form>				
				</li>
			</ul>
		</div></div> <!-- end inside & postbox -->
	<?php $msrtm_optin = ob_get_clean();
		echo $msrtm_optin;
	} /** end function msrtm_opt() */


	/** ===================================================== Include CSS */
	function msrtm_includes() {
		wp_register_style( 'msrtm-admin', plugins_url( 'templates/style.css' , __FILE__ ), '', '', 'all' );

		if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" ) {
			wp_enqueue_style( 'msrtm-admin' );				/** admin css */
		}
	} /** end function msrtm_includes() */


	/** ===================================================== Check if Pro Extension Has Been Uploaded */
	function msrtm_version( $check ) {
		/** file check */
		if( is_file( WP_PLUGIN_DIR . '/msrtm-pro/ms-robotstxt.php' ) ) {
			return true;
		} else {
			return false;
		}
	} /** end function msrtm_version() */


	/**
	 * Check For Old Version
	 */
	function robotstxt_ms_version() {
		/** file check */
		if( is_file( WP_PLUGIN_DIR . '/ms-robotstxt.php' ) ) {
			return WP_PLUGIN_DIR;
		}elseif( is_file( WP_CONTENT_DIR . '/ms-robotstxt.php' ) ) {
			return WP_CONTENT_DIR;
		}else{
			return false;
		}
	}

	/** ===================================================== Network Admin Notice on Robots.txt Creation */
	function msrtm_network_notices() {
		echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)"><p style="margin:1px 0;padding:0;"><small><em>Robots.txt Created...</em></small></p></div>';
		remove_action( 'network_admin_notices', array( 'msrtm_admin_areas', 'msrtm_network_notices' ) );
	}

} /** end class msrtm_admin_areas */

/** display admin area */
if( is_network_admin() || is_admin() ) {
	$display_network = new msrtm_admin_areas();
}



/**
 * ========================================================================= Shared Functions
 * ========================================================================= Shared Functions
 * ========================================================================= Shared Functions
 */



/**
 * =====================================================Functions Class
 */
class msrtm_functions {
	/** ===================================================== Used when Network Wide is selected in the dropdown. */
	function msrtm_redirect() {
		if( !check_admin_referer( 'robotstxt_action', 'robotstxt_nonce' ) ) { return; }
		wp_safe_redirect( network_admin_url( '/settings.php?page=msrtm-network.php' ) ); 
	}


	/** ===================================================== Define plugin textdomain for translations */
	function msrtm_lang() {
		load_plugin_textdomain( 'ms_robotstxt_manager', false, dirname( plugin_basename( __FILE__ ) ) . '/langs' );

		$locale_lang = get_locale();
			if( !empty( $locale_lang ) && ( dirname( plugin_basename( __FILE__ ) ) . '/langs/'. $locale_lang .'.mo' ) ) {
				load_textdomain( 'ms_robotstxt_manager', dirname( plugin_basename( __FILE__ ) ) . '/langs/'. $locale_lang .'.mo' );
			}
	}


	/** ===================================================== Extra Links */
	function msrtm_links( $links, $file ) {
		$plugin = plugin_basename( __FILE__ );
		if( $file == $plugin ) {
			$links[] = '<a href="settings.php?page=msrtm-network.php">'. __('Settings', 'ms_robotstxt_manager') .'</a>';
			$links[] = '<a href="http://msrtm.technerdia.com/#faq">'. __('F.A.Q.', 'ms_robotstxt_manager') .'</a>';
			$links[] = '<a href="http://msrtm.technerdia.com/help.html">'. __('Support', 'ms_robotstxt_manager') .'</a>';
			$links[] = '<a href="http://msrtm.technerdia.com/feedback.html">'. __('Feedback', 'ms_robotstxt_manager') .'</a>';
			$links[] = '<a href="http://msrtm.technerdia.com/donate.html">'. __('Donations', 'ms_robotstxt_manager') .'</a>';
			$links[] = '<a href="http://msrtm.technerdia.com/" target="_blank">'. __('PRO Details', 'ms_robotstxt_manager') .'</a>';
		}
		return $links;
	}
} /** end class msrtm_functions */

/** Network Wide redirect */
if( isset( $_POST['show_site'] ) && $_POST['show_site'] == "msrtm_redirect" ) {
	add_action( 'init', array( 'msrtm_functions', 'msrtm_redirect' ) );
}

/** Load textdomain */
if( isset( $_GET['page'] ) && $_GET['page'] == "msrtm-website.php" || isset( $_GET['page'] ) && $_GET['page'] == "msrtm-network.php" ) {
	add_action( 'init', array( 'msrtm_functions', 'msrtm_lang' ) );
}

/** Display extra links within plugins section */
if( is_network_admin() ) {
	if( $_SERVER['PHP_SELF'] == "/wp-admin/network/plugins.php" || $_SERVER['SCRIPT_NAME'] == "/wp-admin/network/plugins.php" || parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) == "/wp-admin/network/plugins.php" ) {
		add_filter( 'plugin_row_meta', array( 'msrtm_functions', 'msrtm_links' ), 10, 2 );
	}
}




/**
 * ========================================================================= Other Classes
 * ========================================================================= Other Classes
 * ========================================================================= Other Classes
 */



/**
 * ===================================================== Default Robots.txt Files
 */
class msrtm_defaults_presets {
/** Default Robots.txt File Build */
	public function msrtm_default_robotstxt() {
			if( isset( $_POST['selected_site'] ) ) {
				$selected_site = $_POST['selected_site'];
				$site_url = parse_url( get_site_url( $selected_site ) );
				$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';
			}
			if( !isset( $path ) ) { $path = ""; }
		$robotstxt_mu = "# robots.txt\n";
		$robotstxt_mu .= "User-agent: *\n";
		$robotstxt_mu .= "Disallow: $path/feed\n";
		$robotstxt_mu .= "Disallow: $path/feed/\n";
		$robotstxt_mu .= "Disallow: $path/cgi-bin/\n";
		$robotstxt_mu .= "Disallow: $path/comment\n";
		$robotstxt_mu .= "Disallow: $path/comments\n";
		$robotstxt_mu .= "Disallow: $path/trackback\n";
		$robotstxt_mu .= "Disallow: $path/wp-admin/\n";
		$robotstxt_mu .= "Disallow: $path/wp-content\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/\n";
		$robotstxt_mu .= "Disallow: $path/wp-includes\n";
		$robotstxt_mu .= "Disallow: $path/wp-includes/\n";
		$robotstxt_mu .= "Disallow: $path/wp-login.php";
		return $robotstxt_mu;
	}

/** Default Robots.txt File Build */
	public function msrtm_old_robotstxt() {
			if( isset( $_POST['selected_site'] ) ) {
				$selected_site = $_POST['selected_site'];
				$site_url = parse_url( get_site_url( $selected_site ) );
				$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';
			}
			if( !isset( $path ) ) { $path = ""; }
		$robotstxt_mu = "# robots.txt\n";
		$robotstxt_mu .= "User-agent: *\n";
		$robotstxt_mu .= "Disallow: */feed\n";
		$robotstxt_mu .= "Disallow: */feed/\n";
		$robotstxt_mu .= "Disallow: */comment/\n";
		$robotstxt_mu .= "Disallow: */comments/\n";
		$robotstxt_mu .= "Disallow: */trackback/\n";
		$robotstxt_mu .= "Disallow: $path/feed\n";
		$robotstxt_mu .= "Disallow: $path/feed/\n";
		$robotstxt_mu .= "Disallow: $path/cgi-bin/\n";
		$robotstxt_mu .= "Disallow: $path/comment\n";
		$robotstxt_mu .= "Disallow: $path/comment/\n";
		$robotstxt_mu .= "Disallow: $path/comments\n";
		$robotstxt_mu .= "Disallow: $path/comments/\n";
		$robotstxt_mu .= "Disallow: $path/wp-admin/\n";
		$robotstxt_mu .= "Disallow: $path/trackback\n";
		$robotstxt_mu .= "Disallow: $path/trackback/\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/\n";
		$robotstxt_mu .= "Disallow: $path/wp-includes/\n";
		$robotstxt_mu .= "Disallow: $path/wp-login.php";
		return $robotstxt_mu;
	}

	public function msrtm_wp_robotstxt() {
			if( isset( $_POST['selected_site'] ) ) {
				$selected_site = $_POST['selected_site'];
				$site_url = parse_url( get_site_url( $selected_site ) );
				$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';
			}
			if( !isset( $path ) ) { $path = ""; }
		$robotstxt_mu = "# robots.txt\n";
		$robotstxt_mu .= "User-agent: *\n";
		$robotstxt_mu .= "Disallow: $path/wp-admin/\n";
		$robotstxt_mu .= "Disallow: $path/wp-includes/";
		return $robotstxt_mu;
	}

	public function msrtm_mini_robotstxt() {
		$robotstxt_mu = "# robots.txt\n";
		$robotstxt_mu .= "User-agent: *\n"; 
		$robotstxt_mu .= "Disallow:";
		return $robotstxt_mu;
	}

	public function msrtm_blogger_robotstxt() {
			if( isset( $_POST['selected_site'] ) ) {
				$selected_site = $_POST['selected_site'];
				$site_url = parse_url( get_site_url( $selected_site ) );
				$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';
			}
			if( !isset( $path ) ) { $path = ""; }
		$robotstxt_mu = "# robots.txt\n";
		$robotstxt_mu .= "User-agent: *\n";
		$robotstxt_mu .= "Disallow: *?\n";
		$robotstxt_mu .= "Disallow: *.js$\n";
		$robotstxt_mu .= "Disallow: *.inc$\n";
		$robotstxt_mu .= "Disallow: *.css$\n";
		$robotstxt_mu .= "Disallow: *.php$\n";
		$robotstxt_mu .= "Disallow: */feed\n";
		$robotstxt_mu .= "Disallow: */feed/\n";
		$robotstxt_mu .= "Disallow: */author\n";
		$robotstxt_mu .= "Disallow: */comment/\n";
		$robotstxt_mu .= "Disallow: */comments/\n";
		$robotstxt_mu .= "Disallow: */trackback/\n";
		$robotstxt_mu .= "Disallow: $path/wp-\n";
		$robotstxt_mu .= "Disallow: $path/wp-*\n";
		$robotstxt_mu .= "Disallow: $path/feed\n";
		$robotstxt_mu .= "Disallow: $path/feed/\n";
		$robotstxt_mu .= "Disallow: $path/author\n";
		$robotstxt_mu .= "Disallow: $path/cgi-bin/\n";
		$robotstxt_mu .= "Disallow: $path/wp-admin/\n";
		$robotstxt_mu .= "Disallow: $path/comment/\n";
		$robotstxt_mu .= "Disallow: $path/comments/\n";
		$robotstxt_mu .= "Disallow: $path/trackback/\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/\n";
		$robotstxt_mu .= "Disallow: $path/wp-includes/\n";
		$robotstxt_mu .= "Disallow: $path/wp-login.php\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/cache/\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/themes/\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/plugins/";
		return $robotstxt_mu;
	}

	public function msrtm_blocked_robotstxt() {
			if( isset( $_POST['selected_site'] ) ) {
				$selected_site = $_POST['selected_site'];
				$site_url = parse_url( get_site_url( $selected_site ) );
				$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';
			}
			if( !isset( $path ) ) { $path = ""; }
		$robotstxt_mu = "# robots.txt\n";
		$robotstxt_mu .= "User-agent: *\n";
		$robotstxt_mu .= "Disallow: $path/";
		return $robotstxt_mu;
	}

	public function msrtm_google_robotstxt() {
			if( isset( $_POST['selected_site'] ) ) {
				$selected_site = $_POST['selected_site'];
				$site_url = parse_url( get_site_url( $selected_site ) );
				$path = ( !empty( $site_url['path'] ) ) ? $site_url['path'] : '';
			}
			if( !isset( $path ) ) { $path = ""; }
		$robotstxt_mu = "# robots.txt\n";
		$robotstxt_mu .= "User-agent: *\n";
		$robotstxt_mu .= "Disallow: $path/wp-\n";
		$robotstxt_mu .= "Disallow: $path/feed\n";
		$robotstxt_mu .= "Disallow: $path/feed/\n";
		$robotstxt_mu .= "Disallow: $path/author\n";
		$robotstxt_mu .= "Disallow: $path/cgi-bin/\n";
		$robotstxt_mu .= "Disallow: $path/wp-admin/\n";
		$robotstxt_mu .= "Disallow: $path/comment/\n";
		$robotstxt_mu .= "Disallow: $path/comments/\n";
		$robotstxt_mu .= "Disallow: $path/trackback/\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/\n";
		$robotstxt_mu .= "Disallow: $path/wp-includes/\n";
		$robotstxt_mu .= "Disallow: $path/wp-login.php\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/cache/\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/themes/\n";
		$robotstxt_mu .= "Disallow: $path/wp-content/plugins/\n";
		$robotstxt_mu .= "\n";
		$robotstxt_mu .= "# google bot\n";
		$robotstxt_mu .= "User-agent: Googlebot\n";
		$robotstxt_mu .= "Disallow: $path/wp-*\n";
		$robotstxt_mu .= "Disallow: *?\n";
		$robotstxt_mu .= "Disallow: *.js$\n";
		$robotstxt_mu .= "Disallow: *.inc$\n";
		$robotstxt_mu .= "Disallow: *.css$\n";
		$robotstxt_mu .= "Disallow: *.php$\n";
		$robotstxt_mu .= "Disallow: */feed\n";
		$robotstxt_mu .= "Disallow: */feed/\n";
		$robotstxt_mu .= "Disallow: */author\n";
		$robotstxt_mu .= "Disallow: */comment/\n";
		$robotstxt_mu .= "Disallow: */comments/\n";
		$robotstxt_mu .= "Disallow: */trackback/\n";
		$robotstxt_mu .= "\n";
		$robotstxt_mu .= "# google image bot\n";
		$robotstxt_mu .= "User-agent: Googlebot-Image\n";
		$robotstxt_mu .= "Allow: /*\n";
		return $robotstxt_mu;
	}
} /** end class msrtm_defaults_presets */



/**
 * ===================================================== Activation, Setup and Deactivation
 */
class msrtm_hooks {
	/** ===================================================== Setup */
	static function msrtm_activate() {
		if( !is_user_logged_in() && !current_user_can('manage_options') ) { wp_die( __( 'Authorized Access Required', 'ms_robotstxt_manager' ) ); }

		/** networks only */
		if( !function_exists( 'switch_to_blog' ) ) { wp_die( __( 'Activation Failed: This plugin can only be activated on Network Enabled Wordpress installs.', 'ms_robotstxt_manager' ) ); }

		/** network admin only */
		if( !is_network_admin() ) {
			wp_die( __('This plugin must be Network Activated.', 'ms_robotstxt_manager') );
		}

		/** current wordpress only */
		global $wp_version;
		if( version_compare( $wp_version, "3.3", "<" ) ) {
			wp_die( __( 'This plugin requires WordPress 3.3 or higher. Please Upgrade Wordpress, then try activating this plugin again. Press the browser back button to return to the previous page.', 'ms_robotstxt_manager' ) );
		}

		/** get the plugins default robots.txt file */
		$get_defaults = new msrtm_defaults_presets();
		$robots_txt_file = $get_defaults->msrtm_default_robotstxt();
		
		/** build array for default option */
		$default_robotstxt = array( 'default_robotstxt' => $robots_txt_file );

		/** clean and build default robots.txt option */
		delete_option( "ms_robotstxt_default" );
		add_option( "ms_robotstxt_default", $default_robotstxt, '', "no" );
	}

	/** ===================================================== Remove Display Filter */
	static function msrtm_deactivate() {
		/** proper users only */
		if( !is_user_logged_in() && !current_user_can('manage_options') ) { wp_die( __( 'Authorized Access Required', 'ms_robotstxt_manager' ) ); }

		/** restore wordpress robots.txt file */
		remove_filter( 'robots_txt', array( 'robotstxt_ms', 'msrtm_show_robots_txt' ) );
	}
} /** end class msrtm_hooks */

/** calls add_action, activate_, plugin, msrtm_activate() */
if( isset( $_GET['action'] ) && $_GET['action'] == "activate" ) {
	if( is_network_admin() || is_admin() ) {
		register_activation_hook( __FILE__, array( 'msrtm_hooks', 'msrtm_activate' ) );
	}
}

/** calls add_action, deactivate_, plugin, msrtm_deactivate() */
if( isset( $_GET['action'] ) && $_GET['action'] == "deactivate" ) {
	if( is_network_admin() || is_admin() ) {
		register_deactivation_hook( __FILE__, array( 'msrtm_hooks', 'msrtm_deactivate' ) );
	}
}

/** old class and primary functions - delete at a later date */
class robotstxt_msAdmin {
	function robotstxt_tab_links() {
		$display = '<p align="center" style="font-size:16px;font-weight:bold;margin:45px 25px 10px 0;">&bull; '. __('Update Ready: Click The Check For Updates Link Above!!!', 'ms_robotstxt_manager') .'</p>';
		return $display;
	}
	function build_sitemap_option() { wp_die( __( 'Update Ready: Click The Check For Updates Link!.', 'ms_robotstxt_manager') ); }
	function network_notices() {
		echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)"><p style="margin:1px 0;padding:0;"><small><em>Robots.txt Created... NOTICE: An upgrade to the MS Robots.txt Manager Pro Plugin is available!</em></small></p></div>';
		remove_action( 'network_admin_notices', array( 'msrtm_admin_areas', 'msrtm_network_notices' ) );
	}
}
?>