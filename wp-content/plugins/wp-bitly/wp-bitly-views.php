<?php
/**
 * Plugin metabox functions
 */

/**
 * Globalize Plugin options
 */
global $wpbitly_options;
$wpbitly_options = wpbitly_get_options();

/**
 * Hook Plugin metabox into the edit
 * page for each post for which shortlinks
 * are enabled.
 */
foreach ( $wpbitly_options['post_types'] as $post_type ) {
	add_action( 'add_meta_boxes_' . $post_type, 'wpbitly_add_metaboxes' );
}

/**
 * Callback for add_meta_boxes-{posttype}
 */
function wpbitly_add_metaboxes( $post ) {
	
	global $post;
	$shortlink = wp_get_shortlink();

	if ( empty( $shortlink ) )
		return;

	add_meta_box( 'wpbitly-meta', 'WP Bit.ly', 'wpbitly_build_metabox', $post->post_type, 'side', 'default', array( $shortlink ) );

}

/**
 * Build Plugin Metabox
 */
function wpbitly_build_metabox( $post, $args )
{
	global $wpbitly;

	$shortlink = $args['args'][0];

	echo '<label class="screen-reader-text" for="new-tag-post_tag">WP Bit.ly</label>';
	echo '<p style="margin-top: 8px;"><input type="text" id="wpbitly-shortlink" name="_wpbitly" size="32" autocomplete="off" value="'.$shortlink.'" style="margin-right: 4px; color: #aaa;" /></p>';

	$url = sprintf( $wpbitly->url['clicks'], $shortlink, $wpbitly->options['bitly_username'], $wpbitly->options['bitly_api_key'] );
	$bitly_response = wpbitly_curl( $url );

	echo '<h4 style="margin-left: 4px; margin-right: 4px; padding-bottom: 3px; border-bottom: 4px solid #eee;">Shortlink Stats</h4>';

	if ( is_array( $bitly_response ) && $bitly_response['status_code'] == 200 )
	{
		echo "<p>Global Clicks: <strong>{$bitly_response['data']['clicks'][0]['global_clicks']}</strong><br/>";
		echo "<p>User Clicks: <strong>{$bitly_response['data']['clicks'][0]['user_clicks']}</strong></p>";
	}
	else
	{
		echo '<p class="error" style="padding: 4px;">There was a problem retrieving stats!</p>';
	}

}

?>