<?php


/**
* Cachify
*/

final class Cachify {


	/**
	* Plugin-Optionen
	*
	* @since  2.0
	* @var    array
	*/

	private static $options;


	/**
	* Cache-Methode
	*
	* @since  2.0
	* @var    object
	*/

	private static $method;


	/**
	* Pseudo-Konstruktor der Klasse
	*
	* @since   2.0.5
	* @change  2.0.5
	*/

	public static function instance()
	{
		new self();
	}


	/**
	* Konstruktor der Klasse
	*
	* @since   1.0.0
	* @change  2.0.6
	*/

  	public function __construct()
  	{
  		/* Filter */
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return;
		}

		/* Variablen */
		self::_set_vars();

		/* Publish-Hooks */
		self::_publish_hooks();

		/* Flush Hook */
		add_action(
			'cachify_flush_cache',
			array(
				__CLASS__,
				'flush_cache'
			)
		);
		add_action(
			'_core_updated_successfully',
			array(
				__CLASS__,
				'flush_cache'
			)
		);

		/* Backend */
		if ( is_admin() ) {
			add_action(
				'wpmu_new_blog',
				array(
					__CLASS__,
					'install_later'
				)
			);
			add_action(
				'delete_blog',
				array(
					__CLASS__,
					'uninstall_later'
				)
			);

			add_action(
				'admin_init',
				array(
					__CLASS__,
					'register_settings'
				)
			);
			add_action(
				'admin_init',
				array(
					__CLASS__,
					'receive_flush'
				)
			);
			add_action(
				'admin_menu',
				array(
					__CLASS__,
					'add_page'
				)
			);
			add_action(
				'admin_print_styles',
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'transition_comment_status',
				array(
					__CLASS__,
					'touch_comment'
				),
				10,
				3
			);
			add_action(
				'edit_comment',
				array(
					__CLASS__,
					'edit_comment'
				)
			);
			add_action(
				'admin_bar_menu',
				array(
					__CLASS__,
					'add_menu'
				),
				90
			);
			add_action(
				'right_now_content_table_end',
				array(
					__CLASS__,
					'add_count'
				)
			);

			add_filter(
				'plugin_row_meta',
				array(
					__CLASS__,
					'row_meta'
				),
				10,
				2
			);
			add_filter(
				'plugin_action_links_' .CACHIFY_BASE,
				array(
					__CLASS__,
					'action_links'
				)
			);

		/* Frontend */
		} else {
			add_action(
				'pre_comment_approved',
				array(
					__CLASS__,
					'pre_comment'
				),
				99,
				2
			);
			add_action(
				'template_redirect',
				array(
					__CLASS__,
					'manage_cache'
				),
				0
			);
			add_action(
				'robots_txt',
				array(
					__CLASS__,
					'robots_txt'
				)
			);
		}
	}


	/**
	* Plugin-Installation für MU-Blogs
	*
	* @since   1.0
	* @change  1.0
	*/

	public static function install()
	{
		/* Multisite & Network */
		if ( is_multisite() && !empty($_GET['networkwide']) ) {
			/* Blog-IDs */
			$ids = self::_get_blog_ids();

			/* Loopen */
			foreach ($ids as $id) {
				switch_to_blog( (int)$id );
				self::_install_backend();
			}

			/* Wechsel zurück */
			restore_current_blog();

		} else {
			self::_install_backend();
		}
	}


	/**
	* Plugin-Installation bei neuen MU-Blogs
	*
	* @since   1.0
	* @change  1.0
	*/

	public static function install_later($id) {
		/* Kein Netzwerk-Plugin */
		if ( !is_plugin_active_for_network(CACHIFY_BASE) ) {
			return;
		}

		/* Wechsel */
		switch_to_blog( (int)$id );

		/* Installieren */
		self::_install_backend();

		/* Wechsel zurück */
		restore_current_blog();
	}


	/**
	* Eigentliche Installation der Optionen
	*
	* @since   1.0
	* @change  2.0
	*/

	private static function _install_backend()
	{
		add_option(
			'cachify',
			array()
		);

		/* Flush */
		self::flush_cache();
	}


	/**
	* Deinstallation des Plugins pro MU-Blog
	*
	* @since   1.0
	* @change  1.0
	*/

	public static function uninstall()
	{
		/* Global */
		global $wpdb;

		/* Multisite & Network */
		if ( is_multisite() && !empty($_GET['networkwide']) ) {
			/* Alter Blog */
			$old = $wpdb->blogid;

			/* Blog-IDs */
			$ids = self::_get_blog_ids();

			/* Loopen */
			foreach ($ids as $id) {
				switch_to_blog($id);
				self::_uninstall_backend();
			}

			/* Wechsel zurück */
			switch_to_blog($old);
		} else {
			self::_uninstall_backend();
		}
	}


	/**
	* Deinstallation des Plugins bei MU & Network
	*
	* @since   1.0
	* @change  1.0
	*/

	public static function uninstall_later($id)
	{
		/* Kein Netzwerk-Plugin */
		if ( !is_plugin_active_for_network(CACHIFY_BASE) ) {
			return;
		}

		/* Wechsel */
		switch_to_blog( (int)$id );

		/* Installieren */
		self::_uninstall_backend();

		/* Wechsel zurück */
		restore_current_blog();
	}


	/**
	* Eigentliche Deinstallation des Plugins
	*
	* @since   1.0
	* @change  1.0
	*/

	private static function _uninstall_backend()
	{
		/* Option */
		delete_option('cachify');

		/* Cache leeren */
		self::flush_cache();
	}


	/**
	* Rückgabe der IDs installierter Blogs
	*
	* @since   1.0
	* @change  1.0
	*
	* @return  array  Blog-IDs
	*/

	private static function _get_blog_ids()
	{
		/* Global */
		global $wpdb;

		return $wpdb->get_col(
			$wpdb->prepare("SELECT blog_id FROM `$wpdb->blogs`")
		);
	}


	/**
	* Eigenschaften des Objekts
	*
	* @since   2.0
	* @change  2.0
	*/

	private static function _set_vars()
	{
		/* Optionen */
		self::$options = self::_get_options();

		/* Methode */
		if ( self::$options['use_apc'] === 1 && extension_loaded('apc') ) {
			self::$method = new Cachify_APC;
		} else if ( self::$options['use_apc'] === 2 ) {
			self::$method = new Cachify_HDD;
		} else {
			self::$method = new Cachify_DB;
		}
	}


	/**
	* Generierung von Publish-Hooks für Custom Post Types
	*
	* @since   2.0.3
	* @change  2.0.3
	*/

	private static function _publish_hooks() {
		/* Verfügbare CPT */
		$available_cpt = get_post_types(
			array('public' => true)
		);

		/* Leer? */
		if ( empty($available_cpt) ) {
			return;
		}

		/* Loopen */
		foreach ( $available_cpt as $cpt ) {
			add_action(
				'publish_' .$cpt,
				array(
					__CLASS__,
					'publish_cpt'
				),
				10,
				2
			);
			add_action(
				'publish_future_' .$cpt,
				array(
					__CLASS__,
					'publish_cpt'
				)
			);
		}
	}


	/**
	* Rückgabe der Optionen
	*
	* @since   2.0
	* @change  2.0
	*
	* @return  array  $diff  Array mit Werten
	*/

	private static function _get_options()
	{
		return wp_parse_args(
			get_option('cachify'),
			array(
				'only_guests'	 => 1,
				'compress_html'	 => 0,
				'cache_expires'	 => 12,
				'without_ids'	 => '',
				'without_agents' => '',
				'use_apc'		 => 0
			)
		);
	}


	/**
	* Hinzufügen der Action-Links
	*
	* @since   1.0
	* @change  2.0.2
	*
	* @param   string  $data  Ursprungsinhalt der dynamischen robots.txt
	* @return  string  $data  Modifizierter Inhalt der robots.txt
	*/

	public static function robots_txt($data)
	{
		/* HDD only */
		if ( self::$options['use_apc'] !== 2 ) {
			return $data;
		}

		/* Pfad */
		$path = parse_url(site_url(), PHP_URL_PATH);

		/* Ausgabe */
		$data .= sprintf(
			'Disallow: %s/wp-content/cache/%s',
			( empty($path) ? '' : $path ),
			"\n"
		);

		return $data;
	}


	/**
	* Hinzufügen der Action-Links
	*
	* @since   1.0
	* @change  1.0
	*
	* @param   array  $data  Bereits existente Links
	* @return  array  $data  Erweitertes Array mit Links
	*/

	public static function action_links($data)
	{
		/* Rechte? */
		if ( !current_user_can('manage_options') ) {
			return $data;
		}

		return array_merge(
			$data,
			array(
				sprintf(
					'<a href="%s">%s</a>',
					add_query_arg(
						array(
							'page' => 'cachify'
						),
						admin_url('options-general.php')
					),
					__('Settings')
				)
			)
		);
	}


	/**
	* Meta-Links des Plugins
	*
	* @since   0.5
	* @change  2.0.5
	*
	* @param   array   $input  Bereits vorhandene Links
	* @param   string  $page   Aktuelle Seite
	* @return  array   $data   Modifizierte Links
	*/

	public static function row_meta($input, $page)
	{
		/* Rechte */
		if ( $page != CACHIFY_BASE ) {
			return $input;
		}

		return array_merge(
			$input,
			array(
				'<a href="https://flattr.com/donation/give/to/sergej.mueller" target="_blank">Flattr</a>',
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=5RDDW9FEHGLG6" target="_blank">PayPal</a>'
			)
		);
	}


	/**
	* Hinzufügen eines Admin-Bar-Menüs
	*
	* @since   1.2
	* @change  1.2.1
	*
	* @param   object  Objekt mit Menü-Eigenschaften
	*/

	public static function add_menu($wp_admin_bar)
	{
		/* Aussteigen */
		if ( !is_admin_bar_showing() or !is_super_admin() ) {
			return;
		}

		/* Hinzufügen */
		$wp_admin_bar->add_menu(
			array(
				'id' 	 => 'cachify',
				'title'  => '<span class="ab-icon" title="Cache leeren"></span>',
				'href'   => add_query_arg('_cachify', 'flush'),
				'parent' => 'top-secondary'
			)
		);
	}


	/**
	* Anzeige des Spam-Counters auf dem Dashboard
	*
	* @since   2.0.0
	* @change  2.0.6
	*/

	public static function add_count()
	{
		/* Größe */
		$size = self::get_cache_size();

		/* Formatierung */
		$format = ( empty($size) ? array(0, 'Bytes') : explode(' ', size_format($size)) );

		/* Ausgabe */
		echo sprintf(
			'<tr>
				<td colspan="2">
					<div class="table table_cachify">
						<p class="sub">Cache</p>
						<table>
							<tr>
								<td class="b">%s</td>
								<td class="last t">%s</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>',
			(int)$format[0],
			$format[1]
		);
	}


	/**
	* Rückgabe der Cache-Größe
	*
	* @since   2.0.6
	* @change  2.0.6
	*
	* @param   integer  $size  Cache-Größe in Bytes
	*/

	public static function get_cache_size()
	{
		if ( ! $size = get_transient('cachify_cache_size') ) {
			/* Auslesen */
			$size = (int) call_user_func(
				array(
					self::$method,
					'get_stats'
				)
			);

			/* Speichern */
			set_transient(
		      'cachify_cache_size',
		      $size,
		      60 * 15
		    );
		}

		return $size;
	}


	/**
	* Verarbeitung der Plugin-Meta-Aktionen
	*
	* @since   0.5
	* @change  1.2
	*
	* @param   array  $data  Metadaten der Plugins
	*/

	public static function receive_flush($data)
	{
		/* Leer? */
		if ( empty($_GET['_cachify']) or $_GET['_cachify'] !== 'flush' ) {
			return;
		}

		/* Global */
		global $wpdb;

		/* Multisite & Network */
		if ( is_multisite() && is_plugin_active_for_network(CACHIFY_BASE) ) {
			/* Alter Blog */
			$old = $wpdb->blogid;

			/* Blog-IDs */
			$ids = self::_get_blog_ids();

			/* Loopen */
			foreach ($ids as $id) {
				switch_to_blog($id);
				self::flush_cache();
			}

			/* Wechsel zurück */
			switch_to_blog($old);

			/* Notiz */
			add_action(
				'network_admin_notices',
				array(
					__CLASS__,
					'flush_notice'
				)
			);
		} else {
			/* Leeren */
			self::flush_cache();

			/* Notiz */
			add_action(
				'admin_notices',
				array(
					__CLASS__,
					'flush_notice'
				)
			);
		}
	}


	/**
	* Hinweis nach erfolgreichem Cache-Leeren
	*
	* @since   1.2
	* @change  1.2
	*/

	public static function flush_notice()
	{
		/* Kein Admin */
		if ( !is_super_admin() ) {
			return false;
		}

		echo '<div id="message" class="updated"><p><strong>Cachify-Cache geleert.</strong></p></div>';
	}


	/**
	* Löschung des Cache beim Kommentar-Editieren
	*
	* @since   0.1
	* @change  0.4
	*
	* @param   integer  $id  ID des Kommentars
	*/

	public static function edit_comment($id)
	{
		self::_delete_cache(
			get_permalink(
				get_comment($id)->comment_post_ID
			)
		);
	}


	/**
	* Löschung des Cache beim neuen Kommentar
	*
	* @since   0.1.0
	* @change  2.0.6
	*
	* @param   mixed  $approved  Kommentar-Status
	* @param   array  $comment   Array mit Eigenschaften
	* @return  mixed  $approved  Kommentar-Status
	*/

	public static function pre_comment($approved, $comment)
	{
		/* Approved comment? */
		if ( $approved === 1 ) {
			self::_delete_cache(
				get_permalink($comment['comment_post_ID'])
			);
		}

		return $approved;
	}


	/**
	* Löschung des Cache beim Editieren der Kommentare
	*
	* @since   0.1
	* @change  0.4
	*
	* @param   string  $new_status  Neuer Status
	* @param   string  $old_status  Alter Status
	* @param   object  $comment     Array mit Eigenschaften
	*/

	public static function touch_comment($new_status, $old_status, $comment)
	{
		if ( $new_status != $old_status ) {
			self::_delete_cache(
				get_permalink($comment->comment_post_ID)
			);
		}
	}


	/**
	* Leerung des Cache bei neuen CPTs
	*
	* @since   2.0.3
	* @change  2.0.3
	*
	* @param   integer  $id    PostID
	* @param   object   $post  Object mit CPT-Metadaten [optional]
	*/

	public static function publish_cpt($id, $post = false)
	{
		/* Leer? */
		if ( empty($post) ) {
			return;
		}

		/* Status */
		if ( in_array( $post->post_status, array('publish', 'future') ) ) {
			self::flush_cache();
		}
	}


	/**
	* Rückgabe der Cache-Gültigkeit
	*
	* @since   2.0
	* @change  2.0
	*
	* @return  intval    Gültigkeit in Sekunden
	*/

	private static function _cache_expires()
	{
		return 60 * 60 * self::$options['cache_expires'];
	}


	/**
	* Rückgabe des Cache-Hash-Wertes
	*
	* @since   0.1
	* @change  2.0
	*
	* @param   string  $url  URL für den Hash-Wert [optional]
	* @return  string        Cachify-Hash-Wert
	*/

  	private static function _cache_hash($url = '')
	{
		return md5(
			empty($url) ? ( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) : ( parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH) )
		) . '.cachify';
	}


	/**
	* Splittung nach Komma
	*
	* @since   0.9.1
	* @change  1.0
	*
	* @param   string  $input  Zu splittende Zeichenkette
	* @return  array           Konvertierter Array
	*/

	private static function _preg_split($input)
	{
		return (array)preg_split('/,/', $input, -1, PREG_SPLIT_NO_EMPTY);
	}


	/**
	* Prüfung der WordPress-Version
	*
	* @since  2.0
	* @change 2.0
	*
	* @param	integer  $version  Gesuchte WP-Version
	* @return	boolean            TRUE, wenn mindestens gesuchte
	*/

	private static function _is_wp($version) {
		return version_compare(
			$GLOBALS['wp_version'],
			$version. 'alpha',
			'>='
		);
	}


	/**
	* Prüfung auf Index
	*
	* @since   0.6
	* @change  1.0
	*
	* @return  boolean  TRUE bei Index
	*/

	private static function _is_index()
	{
		return basename($_SERVER['SCRIPT_NAME']) != 'index.php';
	}


	/**
	* Prüfung auf Mobile Devices
	*
	* @since   0.9.1
	* @change  2.0.5
	*
	* @return  boolean  TRUE bei Mobile
	*/

	private static function _is_mobile()
	{
		return ( strpos(TEMPLATEPATH, 'wptouch') or strpos(TEMPLATEPATH, 'carrington') or strpos(TEMPLATEPATH, 'jetpack') );
	}


	/**
	* Prüfung auf eingeloggte und kommentierte Nutzer
	*
	* @since   2.0.0
	* @change  2.0.5
	*
	* @return  boolean  $diff  TRUE bei "vermerkten" Nutzern
	*/

	private static function _is_logged_in()
	{
		/* Eingeloggt */
		if ( is_user_logged_in() ) {
			return true;
		}

		/* Cookie? */
		if ( empty($_COOKIE) ) {
			return false;
		}

		/* Loopen */
		foreach ( $_COOKIE as $k => $v) {
			if ( preg_match('/^(wp-postpass|wordpress_logged_in|comment_author)_/', $k) ) {
				return true;
			}
		}
	}


	/**
	* Definition der Ausnahmen für den Cache
	*
	* @since   0.2
	* @change  2.0
	*
	* @return  boolean  TRUE bei Ausnahmen
	*/

	private static function _skip_cache()
	{
		/* Optionen */
		$options = self::$options;

		/* Filter */
		if ( self::_is_index() or is_search() or is_404() or is_feed() or is_trackback() or is_robots() or is_preview() or post_password_required() ) {
			return true;
		}

		/* Request */
		if ( !empty($_POST) or (!empty($_GET) && get_option('permalink_structure')) ) {
			return true;
		}

		/* Logged in */
		if ( $options['only_guests'] && self::_is_logged_in() ) {
			return true;
		}

		/* WP Touch */
		if ( self::_is_mobile() ) {
			return true;
		}

		/* Post IDs */
		if ( $options['without_ids'] && is_singular() ) {
			if ( in_array( $GLOBALS['wp_query']->get_queried_object_id(), self::_preg_split($options['without_ids']) ) ) {
				return true;
			}
		}

		/* User Agents */
		if ( $options['without_agents'] && isset($_SERVER['HTTP_USER_AGENT']) ) {
			if ( array_filter( self::_preg_split($options['without_agents']), create_function('$a', 'return strpos($_SERVER["HTTP_USER_AGENT"], $a);') ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	* Minimierung des HTML-Codes
	*
	* @since   0.9.2
	* @change  2.0.1
	*
	* @param   string  $data  Zu minimierender Datensatz
	* @return  string  $data  Minimierter Datensatz
	*/

	private static function _minify_cache($data) {
		/* Minimierung? */
		if ( !self::$options['compress_html'] ) {
			return($data);
		}

		/* Verkleinern */
		$cleaned = preg_replace(
			array(
				'/<!--[^\[><](.*?)-->/s',
				'#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre)\b))*+)(?:<(?>textarea|pre)\b|\z))#'
			),
			array(
				'',
				' '
			),
			(string) $data
		);

		/* Fehlerhaft? */
		if ( strlen($cleaned) <= 1 ) {
			return($data);
		}

		return $cleaned;
	}


	/**
	* Löschung des Cache für eine URL
	*
	* @since   0.1
	* @change  2.0
	*
	* @param  string  $url  URL für den Hash-Wert
	*/

	private static function _delete_cache($url)
	{
		call_user_func(
			array(
				self::$method,
				'delete_item'
			),
			self::_cache_hash($url),
			$url
		);
	}


	/**
	* Zurücksetzen des kompletten Cache
	*
	* @since   0.1
	* @change  2.0
	*/

	public static function flush_cache()
	{
		/* DB */
		Cachify_DB::clear_cache();

		/* APC */
		Cachify_APC::clear_cache();

		/* HD */
		Cachify_HDD::clear_cache();

		/* Transient */
		delete_transient('cachify_cache_size');
	}


	/**
	* Zuweisung des Cache
	*
	* @since   0.1
	* @change  2.0
	*
	* @param   string  $data  Inhalt der Seite
	* @return  string  $data  Inhalt der Seite
	*/

	public static function set_cache($data)
	{
		/* Leer? */
		if ( empty($data) ) {
			return '';
		}

		/* Speicherung */
		call_user_func(
			array(
				self::$method,
				'store_item'
			),
			self::_cache_hash(),
			self::_minify_cache($data),
			self::_cache_expires()
		);

		return $data;
	}


	/**
	* Verwaltung des Cache
	*
	* @since   0.1
	* @change  2.0
	*/

	public static function manage_cache()
	{
		/* Kein Caching? */
		if ( self::_skip_cache() ) {
			return;
		}

		/* Daten im Cache */
		$cache = call_user_func(
			array(
				self::$method,
				'get_item'
			),
			self::_cache_hash()
		);

		/* Kein Cache? */
		if ( empty($cache) ) {
			ob_start('Cachify::set_cache');
			return;
		}

		/* Cache verarbeiten */
		call_user_func(
			array(
				self::$method,
				'print_cache'
			),
			$cache
		);
	}


	/**
	* Einbindung von CSS
	*
	* @since   1.0
	* @change  2.0
	*/

	public static function add_css()
	{
		/* Infos auslesen */
		$data = get_plugin_data(CACHIFY_FILE);

		/* CSS registrieren */
		wp_register_style(
			'cachify_css',
			plugins_url('css/styles.min.css', CACHIFY_FILE),
			array(),
			$data['Version']
		);

		/* CSS einbinden */
		wp_enqueue_style('cachify_css');
	}


	/**
	* Einfügen der Optionsseite
	*
	* @since   1.0
	* @change  2.0.2
	*/

	public static function add_page()
	{
		$page = add_options_page(
			'Cachify',
			'Cachify',
			'manage_options',
			'cachify',
			array(
				__CLASS__,
				'options_page'
			)
		);
	}


	/**
	* Verfügbare Cache-Methoden
	*
	* @since  2.0
	* @change 2.0
	*
	* @param  array  $available  Array mit verfügbaren Arten
	*/

	private static function _method_select()
	{
		/* Verfügbar */
		$available = array(
			0 => 'Datenbank',
			1 => 'APC',
			2 => 'Festplatte'
		);

		/* Kein APC */
		if ( !extension_loaded('apc') ) {
			unset($available[1]);
		}

		/* Keine Permalinks */
		if ( !get_option('permalink_structure') ) {
			unset($available[2]);
		}

		return $available;
	}


	/**
	* Registrierung der Settings
	*
	* @since   1.0
	* @change  1.0
	*/

	public static function register_settings()
	{
		register_setting(
			'cachify',
			'cachify',
			array(
				__CLASS__,
				'validate_options'
			)
		);
	}


	/**
	* Valisierung der Optionsseite
	*
	* @since   1.0.0
	* @change  2.0.5
	*
	* @param   array  $data  Array mit Formularwerten
	* @return  array         Array mit geprüften Werten
	*/

	public static function validate_options($data)
	{
		/* Cache leeren */
		self::flush_cache();

		/* Hinweis */
		if ( self::$options['use_apc'] != $data['use_apc'] && $data['use_apc'] >= 1 ) {
			add_settings_error(
				'cachify_method_tip',
				'cachify_method_tip',
				'Die Server-Konfigurationsdatei (z.B. .htaccess) muss jetzt erweitert werden [<a href="http://playground.ebiene.de/cachify-wordpress-cache/" target="_blank">?</a>]',
				'updated'
			);
		}

		/* Rückgabe */
		return array(
			'only_guests'	 => (int)(!empty($data['only_guests'])),
			'compress_html'	 => (int)(!empty($data['compress_html'])),
			'cache_expires'	 => (int)(@$data['cache_expires']),
			'without_ids'	 => (string)sanitize_text_field(@$data['without_ids']),
			'without_agents' => (string)sanitize_text_field(@$data['without_agents']),
			'use_apc'	 	 => (int)$data['use_apc']
		);
	}


	/**
	* Darstellung der Optionsseite
	*
	* @since   1.0
	* @change  2.0
	*/

	public static function options_page()
	{ ?>
		<div class="wrap" id="cachify_main">
			<?php screen_icon('cachify') ?>

			<h2>
				Cachify
			</h2>

			<form method="post" action="options.php">
				<?php settings_fields('cachify') ?>

				<?php $options = self::_get_options() ?>

				<div class="table rounded">
					<table class="form-table">
						<caption class="rounded">Einstellungen</caption>

						<tr>
							<th>
								Aufbewahrungsort für Cache
							</th>
							<td>
								<select name="cachify[use_apc]">
									<?php foreach( self::_method_select() as $k => $v ) { ?>
										<option value="<?php echo esc_attr($k) ?>" <?php selected($options['use_apc'], $k); ?>><?php echo esc_html($v) ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>

						<tr>
							<th>
								Cache-Gültigkeit in Stunden
							</th>
							<td>
								<input type="text" name="cachify[cache_expires]" value="<?php echo $options['cache_expires'] ?>" class="small" />
							</td>
						</tr>

						<tr>
							<th>
								Minimierung der Ausgabe
							</th>
							<td>
								<input type="checkbox" name="cachify[compress_html]" value="1" <?php checked('1', $options['compress_html']); ?> />
							</td>
						</tr>
					</table>
				</div>

				<div class="table rounded">
					<table class="form-table">
						<caption class="rounded">Filter</caption>

						<tr>
							<th>
								Ausnahme für (Post/Pages) IDs
							</th>
							<td>
								<input type="text" name="cachify[without_ids]" value="<?php echo $options['without_ids'] ?>" />
							</td>
						</tr>

						<tr>
							<th>
								Ausnahme für User Agents
							</th>
							<td>
								<input type="text" name="cachify[without_agents]" value="<?php echo $options['without_agents'] ?>" />
							</td>
						</tr>

						<tr>
							<th>
								Kein Cache für eingeloggte<br />bzw. kommentierende Nutzer
							</th>
							<td>
								<input type="checkbox" name="cachify[only_guests]" value="1" <?php checked('1', $options['only_guests']); ?> />
							</td>
						</tr>
					</table>
				</div>

				<div class="submit">
					<p>
						<a href="http://playground.ebiene.de/cachify-wordpress-cache/" target="_blank">Handbuch</a><a href="http://www.amazon.de/dp/B0091LDUVA" target="_blank">Kindle eBook</a><a href="https://flattr.com/donation/give/to/sergej.mueller" target="_blank">Flattr</a><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=5RDDW9FEHGLG6" target="_blank">PayPal</a>
					</p>

					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</div>
			</form>
		</div><?php
	}
}