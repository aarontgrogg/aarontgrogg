<?php

global $wpbitly_options;
$wpbitly_options = wpbitly_get_options();

function wpbitly_get_options() {
	$defaults = wpbitly_get_option_defaults();
	global $wpbitly_options;
	$wpbitly_options = wp_parse_args( get_option( 'wpbitly_options', array() ), $defaults );
	return $wpbitly_options;
}

function wpbitly_get_option_defaults() {
	return apply_filters( 'wpbitly_option_defaults', array(		
		'bitly_username' => '',
		'bitly_api_key'  => '',
		'post_types'     => array( 'post', 'page' ),
		'enable_admin_toolbar_shortlink' => true,
		'wpbitly_invalid' => false,
		'wpbitly_version' => '1.0'
	) );
}

function wpbitly_get_option_parameters() {
	$defaults = wpbitly_get_option_defaults();
	return apply_filters( 'wpbitly_option_parameters', array(
		'bitly_username' => array(
			'name' => 'bitly_username',
			'title' => __( 'Bit.ly Username', 'wpbitly' ),
			'description' => __( 'The username you use to log in to your Bit.ly account.', 'wpbitly' ),
			'type' => 'text',
			'sanitize' => 'nohtml',
			'default' => $defaults['bitly_username']
		),
		'bitly_api_key' => array(
			'name' => 'bitly_api_key',
			'title' => __( 'Bit.ly API Key', 'wpbitly' ),
			'description' => sprintf( __( 'Your API key can be found on your %1$s', 'wpbitly' ), '<a href="http://bit.ly/account/" target="_blank">' . __( 'Bit.ly account page', 'wpbitly' ) . '</a>' ),
			'type' => 'text',
			'sanitize' => 'nohtml',
			'default' => $defaults['bitly_api_key']
		),
		'post_types' => array(
			'name' => 'post_types',
			'title' => __( 'Post Types', 'wpbitly' ),
			'description' => __( 'What kind of posts should short links be generated for?', 'wpbitly' ),
			'type' => 'checkboxarray',
			'valid_options' => wpbitly_get_valid_post_types(),
			'default' => $defaults['post_types']
		),
		'enable_admin_toolbar_shortlink' => array(
			'name' => 'enable_admin_toolbar_shortlink',
			'title' => __( 'Admin Toolbar Link', 'wpbitly' ),
			'description' => __( 'Enable the "Shortlink" link in the Admin Toolbar', 'wpbitly' ),
			'type' => 'checkbox',
			'valid_options' => wpbitly_get_valid_post_types(),
			'default' => $defaults['enable_admin_toolbar_shortlink']
		),
	) );
}

function wpbitly_get_valid_post_types() {
	return apply_filters( 'wpbitly_valid_post_types', get_post_types( array( 'public' => true ) ) );
}

/**
 * Register Plugin settings
 */
function wpbitly_options_init() {

	/**
	 * Register Plugin setting
	 * 
	 * @todo	move to 'permalink' once this bug is closed: http://core.trac.wordpress.org/ticket/9296
	 */
	register_setting( 'writing', 'wpbitly_options', 'wpbitly_options_validate' );

	/**
	 * Add settings section to Settings -> Permalinks
	 */
	add_settings_section( 'wpbitly_settings', 'WP Bit.ly Options', 'wpbitly_settings_section', 'writing' );	

	/**
	 * Permalinks settings section callback
	 */
	function wpbitly_settings_section() {
		echo '<p>Configure WP Bit.ly settings here.</p>';
	}

	/**
	* Add Bit.ly Username setting field
	* 
	* Adds setting fields to 
	* Settings -> Permalinks
	*/
	add_settings_field( 'bitly_username', '<label for="bitly_username">' . __( 'Bit.ly Username' , 'wpbitly' ) . '</label>', 'wpbitly_settings_field_username', 'writing', 'wpbitly_settings' );

	/**
	 * Bit.ly Username setting field callback
	 */
	function wpbitly_settings_field_username() {
		global $wpbitly_options;
		$option_parameters = wpbitly_get_option_parameters();
		?>
		<p>
			<input type="text" size="80" name="wpbitly_options[bitly_username]" value="<?php echo esc_attr( $wpbitly_options['bitly_username'] ); ?>" />
			<br />
			<?php echo $option_parameters['bitly_username']['description']; ?>
		</p>
		<?php
	}

	/**
	* Add Bit.ly API Key setting field
	* 
	* Adds setting fields to 
	* Settings -> Permalinks
	*/
	add_settings_field( 'bitly_api_key', '<label for="bitly_api_key">' . __( 'Bit.ly API Key' , 'wpbitly' ) . '</label>', 'wpbitly_settings_field_api_key', 'writing', 'wpbitly_settings' );

	/**
	 * Bit.ly API Key setting field callback
	 */
	function wpbitly_settings_field_api_key() {
		global $wpbitly_options;
		$option_parameters = wpbitly_get_option_parameters();
		?>
		<p>
			<input type="text" size="80" name="wpbitly_options[bitly_api_key]" value="<?php echo esc_attr( $wpbitly_options['bitly_api_key'] ); ?>" />
			<br />
			<?php echo $option_parameters['bitly_api_key']['description']; ?>
		</p>
		<?php
	}

	/**
	* Add Bit.ly Post Types setting field
	* 
	* Adds setting fields to 
	* Settings -> Permalinks
	*/
	add_settings_field( 'bitly_post_types', '<label for="bitly_post_types">' . __( 'Post Types' , 'wpbitly' ) . '</label>', 'wpbitly_settings_field_post_types', 'writing', 'wpbitly_settings' );

	/**
	 * Bit.ly Post Types setting field callback
	 */
	function wpbitly_settings_field_post_types() {
		global $wpbitly_options;
		$option_parameters = wpbitly_get_option_parameters();
		?>
		<p>
			<?php foreach ( $option_parameters['post_types']['valid_options'] as $post_type ) { ?>
			<input type="checkbox" name="wpbitly_options[post_types][]" value="<?php echo $post_type; ?>" <?php checked( true == in_array( $post_type, $wpbitly_options['post_types'] ) ); ?>>
			<?php echo $post_type; ?>
			<br />
			<?php } ?>
			<?php echo $option_parameters['post_types']['description']; ?>
		</p>
		<?php
	}

	/**
	* Add Bit.ly Admin Toolbar setting field
	* 
	* Adds setting fields to 
	* Settings -> Permalinks
	*/
	add_settings_field( 'bitly_admin_toolbar', '<label for="bitly_post_types">' . __( 'Admin Toolbar Link' , 'wpbitly' ) . '</label>', 'wpbitly_settings_field_admin_toolbar', 'writing', 'wpbitly_settings' );

	/**
	 * Bit.ly Post Types setting field callback
	 */
	function wpbitly_settings_field_admin_toolbar() {
		global $wpbitly_options;
		$option_parameters = wpbitly_get_option_parameters();
		?>
		<p>
			<input type="checkbox" name="wpbitly_options[enable_admin_toolbar_shortlink]" value="true" <?php checked( true == $wpbitly_options['enable_admin_toolbar_shortlink'] ); ?>>
			<br />
			<?php echo $option_parameters['post_types']['description']; ?>
		</p>
		<?php
	}

	/**
	* Add Bit.ly Plugin support links
	* 
	* Adds setting fields to 
	* Settings -> Permalinks
	*/
	add_settings_field( 'bitly_support_links', '<label for="bitly_support_links">' . __( 'Plugin Support' , 'wpbitly' ) . '</label>', 'wpbitly_settings_field_support_links', 'writing', 'wpbitly_settings' );

	/**
	 * Bit.ly Support Links setting field callback
	 */
	function wpbitly_settings_field_support_links() {
		?>
		<p><?php _e( 'If you require support, or would like to contribute to the further development of this plugin, please choose one of the following;', 'wpbitly' ); ?></p>
		<ul class="links">
			<li><a href="http://wordpress.org/support/plugin/wp-bitly"><?php _e( 'Plugin Support', 'wpbitly' ); ?></a></li>
			<li><a href="http://github.com/chipbennett/wp-bitly/issues"><?php _e( 'Submit Bug Reports via GitHub', 'wpbitly' ); ?></a></li>
			<li><a href="http://github.com/chipbennett/wp-bitly/"><?php _e( 'Contribute to the Plugin via GitHub', 'wpbitly' ); ?></a></li>
			<li><a href="http://wordpress.org/extend/plugins/wp-bitly/"><?php _e( 'Rate This Plugin', 'wpbitly' ); ?></a></li>
			<li><a href="http://www.chipbennett.net/"><?php _e( 'Developer Homepage', 'wpbitly' ); ?></a></li>
		</ul>
		<?php
	}
}
add_action( 'admin_init', 'wpbitly_options_init' );


function wpbitly_options_validate( $input ) {
	
	$option_parameters = wpbitly_get_option_parameters();
	
	global $wpbitly_options;
	$valid_input = $wpbitly_options;
	
	// Bit.ly Username
	$valid_input['bitly_username'] = wp_filter_nohtml_kses( $input['bitly_username'] );
	
	// Bit.ly API Key
	$valid_input['bitly_api_key'] = wp_filter_nohtml_kses( $input['bitly_api_key'] );
	
	// Post Types
	if ( ! isset( $input['post_types'] ) ) {
		$input['post_types'] = array();
	} else {
		foreach ( $input['post_types'] as $post_type ) {
			if ( ! in_array( $post_type, $option_parameters['post_types']['valid_options'] ) ) {
				unset( $input[$post_type] );
			}
		}
	}
	$valid_input['post_types'] = $input['post_types'];
	
	// Admin Toolbar Link
	$valid_input['enable_admin_toolbar_shortlink'] = ( isset ( $input['enable_admin_toolbar_shortlink'] ) ? true : false );
	
	// Validate Bit.ly API handshake
	if ( ! empty( $valid_input['bitly_username'] ) && ! empty( $valid_input['bitly_api_key'] ) ) {
	
		global $wpbitly;

		$url = sprintf( $wpbitly->url['validate'], $valid_input['bitly_username'], $valid_input['bitly_api_key'] );

		$wpbitly_validate = wpbitly_curl( $url );

		if ( is_array( $wpbitly_validate ) && $wpbitly_validate['data']['valid'] == 1 )
			$valid = true;

	}
		
	$valid_input['wpbitly_invalid'] = ( true === $valid ? false : true );

	return $valid_input;

}


class wpbitly_options
{

	public $version;

	public $options;

	public $url = array(
		'shorten'  => 'http://api.bit.ly/v3/shorten?login=%s&apiKey=%s&uri=%s&format=json',
		'expand'   => 'http://api.bit.ly/v3/expand?shortUrl=%s&login=%s&apiKey=%s&format=json',
		'validate' => 'http://api.bit.ly/v3/validate?x_login=%s&x_apiKey=%s&login=wpbitly&apiKey=R_bfef36d10128e7a2de09637a852c06c3&format=json',
		'clicks'   => 'http://api.bit.ly/v3/clicks?shortUrl=%s&login=%s&apiKey=%s&format=json',
	);


	public function __construct( array $defaults )
	{

		$this->_get_version();
		$this->_refresh_options( $defaults );

		add_action( 'init', array( $this, 'check_options' ) );

	}


	private function _get_version()
	{
		global $wpbitly_options;

		$this->version = $wpbitly_options['wpbitly_version'];
	}


	private function _refresh_options( $defaults )
	{

		$this->options = wpbitly_get_options();

	}


	public function check_options()
	{

		// Display any necessary administrative notices
		if ( current_user_can( 'edit_posts' ) )
		{
			if ( empty( $this->options['bitly_username'] ) || empty( $this->options['bitly_api_key'] ) )
			{
				if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'wpbitly' )
				{
				add_action( 'admin_notices', array( $this, 'notice_setup' ) );
				}
			}

			if ( get_option( 'wpbitly_invalid' ) !== false && isset( $_GET['page'] ) && $_GET['page'] == 'wpbitly' )
			{
				add_action( 'admin_notices', array( $this, 'notice_invalid' ) );
			}
		}

	}


	public function notice_setup()
	{

		$title = __( 'WP Bit.Ly is almost ready!', 'wpbitly' );
		$settings_link = '<a href="options-writing.php">'.__( 'settings page', 'wpbitly' ).'</a>';
		$message = sprintf( __( 'Please visit the %s to configure WP Bit.ly', 'wpbitly' ), $settings_link );

		return $this->display_notice( "<strong>{$title}</strong> {$message}", 'error' );

	}


	public function notice_invalid()
	{

		$title = __( 'Invalid API Key!', 'wpbitly' );
		$message = __( "Your username and API key for bit.ly can't be validated. All features are temporarily disabled.", 'wpbitly' );

		return $this->display_notice( "<strong>{$title}</strong> {$message}", 'error' );

	}


	public function display_notice( $string, $type = 'updated', $echo = true )
	{

		if ( $type != 'updated' )
			$type == 'error';

		$string = '<div id="message" class="' . $type . ' fade"><p>' . $string . '</p></div>';

		if ( $echo != true )
			return $string;

		echo $string;

	}

}

abstract class wpbitly_post
{

	private static $post_id;

	private static $permalink = array();

	private static $shortlink;


	public static function id()
	{

		if ( ! self::$post_id )
		{
			self::_get_post_id();
		}

		return self::$post_id;

	}


	public static function permalink( $key = 'raw' )
	{

		if ( empty( self::$permalink ) )
		{
			self::_get_permalink();
		}

		switch ( $key )
		{
			case 'raw':     return self::$permalink['raw'];
			case 'encoded': return self::$permalink['encoded'];
			default:        return self::$permalink;
		}

	}


	public static function shortlink()
	{

		if ( ! self::$shortlink )
		{
			self::_get_shortlink();
		}

		return self::$shortlink;

	}


	private static function _get_post_id()
	{
		global $post;

		if ( is_null( $post ) )
		{
			trigger_error( 'wpbitly::id() cannot be called before $post is set in the global namespace.', E_USER_ERROR );
		}

		self::$post_id = $post->ID;

		if ( $parent = wp_is_post_revision( self::$post_id ) )
		{
			self::$post_id = $parent;
		}

	}


	private static function _get_permalink()
	{

		if ( ! is_array( self::$permalink ) )
		{
			self::$permalink = array();
		}

		self::$permalink['raw']     = get_permalink( self::$post_id );
		self::$permalink['encoded'] = urlencode( self::$permalink['raw'] );

	}


	private static function _get_shortlink()
	{
		self::$shortlink = get_post_meta( self::$post_id, '_wpbitly', true );
	}

}