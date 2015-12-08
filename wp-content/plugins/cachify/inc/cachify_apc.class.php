<?php


/* Secure check */
if ( !class_exists('Cachify') ) {
	die();
}


/**
* Cachify_APC
*/

final class Cachify_APC {
	
	
	/**
	* Speicherung im Cache
	*
	* @since   2.0
	* @change  2.0
	*
	* @param   string   $hash      Hash des Eintrags
	* @param   string   $data      Inhalt des Eintrags
	* @param   integer  $lifetime  Lebensdauer des Eintrags
	*/
	
	public static function store_item($hash, $data, $lifetime)
	{
		/* Leer? */
		if ( empty($hash) or empty($data) ) {
			wp_die('APC add item: Empty input.');
		}
		
		/* Store */
		apc_store(
			$hash,
			gzencode( $data . self::_cache_signatur(), 9),
			$lifetime
		);
	}
	
	
	/**
	* Lesen aus dem Cache
	*
	* @since   2.0
	* @change  2.0
	*
	* @param   string  $hash  Hash des Eintrags
	* @return  mixed   $diff  Wert des Eintrags
	*/
	
	public static function get_item($hash)
	{
		/* Leer? */
		if ( empty($hash) ) {
			wp_die('APC get item: Empty input.');
		}
		
		return ( function_exists('apc_exists') ? apc_exists($hash) : apc_fetch($hash) );
	}
	
	
	/**
	* Entfernung aus dem Cache
	*
	* @since   2.0
	* @change  2.0
	*
	* @param   string  $hash  Hash des Eintrags
	* @param   string  $url   URL des Eintrags [optional]
	*/
	
	public static function delete_item($hash, $url = '')
	{
		/* Leer? */
		if ( empty($hash) ) {
			wp_die('APC delete item: Empty input.');
		}
		
		/* Löschen */
		apc_delete($hash);
	}
	
	
	/**
	* Leerung des Cache
	*
	* @since   2.0
	* @change  2.0
	*/
	
	public static function clear_cache()
	{
		if ( extension_loaded('apc') ) {
			apc_clear_cache('user');
		}
	}
	
	
	/**
	* Ausgabe des Cache
	*
	* @since   2.0
	* @change  2.0
	*/
	
	public static function print_cache()
	{
		return;
	}
	
	
	/**
	* Ermittlung der Cache-Größe
	*
	* @since   2.0
	* @change  2.0
	*
	* @return  mixed  $diff  Cache-Größe
	*/
	
	public static function get_stats()
	{
		/* Infos */
		$data = apc_cache_info('user');
		
		/* Leer */
		if ( empty($data['mem_size']) ) {
			return NULL;
		}
		
		return $data['mem_size'];
	}
	
	
	/**
	* Generierung der Signatur
	*
	* @since   2.0
	* @change  2.0.5
	*
	* @return  string  $diff  Signatur als String
	*/
	
	private static function _cache_signatur()
	{
		return sprintf(
			"\n\n<!-- %s\n%s @ %s -->",
			'Cachify | http://cachify.de',
			'APC Cache',
			date_i18n(
				'd.m.Y H:i:s',
				current_time('timestamp')
			)
		);
	}
}