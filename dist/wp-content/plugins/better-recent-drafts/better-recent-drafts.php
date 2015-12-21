<?php
/* 
Plugin Name: Better Recent Drafts
Plugin URI: http://www.jonlynch.co.uk/wordpress-plugins/better-recent-drafts/ 
Description: Displays an improved recent drafts widget on the dashboard
Author: Jon Lynch 
Version: 0.1
Author URI: http://www.jonlynch.co.uk 

original snippet taken from Marco at 
http://wordpress.org/extend/ideas/topic/recent-drafts-on-dashboard-see-drafts-and-pendings-of-all-users

function prefix jl_brd_
*/
 
add_action ('wp_dashboard_setup', 'jl_brd_recent_drafts');
function jl_brd_recent_drafts () {
  if (!( is_blog_admin() && current_user_can('edit_posts') ) ) return; 
  wp_add_dashboard_widget( 'jl_brd_dashboard_recent_drafts', __('Recent Drafts'), 'jl_brd_dashboard_recent_drafts' );
  remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');  // recent drafts
  remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');  // recent drafts
};

function jl_brd_dashboard_recent_drafts( $drafts = false ) {
	if ( ! $drafts ) {
		$drafts_query = new WP_Query( array(
			'post_type' => 'any',
			'post_status' => array('draft', 'pending'),
			'posts_per_page' => 150,
			'orderby' => 'modified',
			'order' => 'DESC'
		) );
		$drafts =& $drafts_query->posts;
	}

	if ( $drafts && is_array( $drafts ) ) {
		$list = array();		
		foreach ( $drafts as $draft ) {
			$url = get_edit_post_link( $draft->ID );
			$title = _draft_or_post_title( $draft->ID );
			$last_id = get_post_meta( $draft->ID, '_edit_last', true);
			$last_user = get_userdata($last_id);
			$last_modified = '<i>' . esc_html( $last_user->display_name ) . '</i>';
			$item = '<h4><a href="' . $url . '" title="' . sprintf( __( 'Edit ?%s?' ), esc_attr( $title ) ) . '">' . esc_html($title) . '</a>' . '<abbr> ' . $draft->post_status . ' ' . $draft->post_type . '</abbr>' . '<abbr style="display:block;margin-left:0;">' . sprintf(__('Last edited by %1$s on %2$s at %3$s'), $last_modified, mysql2date(get_option('date_format'), $draft->post_modified), mysql2date(get_option('time_format'), $draft->post_modified)) . '</abbr></h4>';
			if ( $the_content = preg_split( '#\s#', strip_shortcodes(strip_tags( $draft->post_content ), 11, PREG_SPLIT_NO_EMPTY )) )
				$item .= '<p>' . join( ' ', array_slice( $the_content, 0, 10 ) ) . ( 10 < count( $the_content ) ? '&hellip;' : '' ) . '</p>';
			$list[] = $item;
		}
?>
	<ul>
		<li><?php echo join( "</li>\n<li>", $list ); ?></li>
	</ul>
<?php
	} else {
		_e('There are no drafts at the moment');
	}
}