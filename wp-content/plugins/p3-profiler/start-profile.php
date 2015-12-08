<?php

// If profiling hasn't started, start it
if ( function_exists( 'get_option' ) && !isset( $GLOBALS['p3_profiler'] ) && basename( __FILE__ ) !=  basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	$opts = get_option( 'p3-profiler_options' );
	if ( !empty( $opts['profiling_enabled'] ) ) {
		$file = realpath( dirname( __FILE__ ) ) . '/classes/class.p3-profiler.php';
		if ( !file_exists( $file ) ) {
			return;
		}
		@include_once $file;
		declare( ticks = 1 ); // Capture every user function call
		if ( class_exists( 'P3_Profiler' ) ) {
			$GLOBALS['p3_profiler'] = new P3_Profiler(); // Go
		}
	}
	unset( $opts );
}
	
/**
 * Get the user's IP
 * @return string
 */
function p3_profiler_get_ip() {
	static $ip = '';
	if ( !empty( $ip ) ) {
		return $ip;
	} else {
		if ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( !empty ( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}

/**
 * Disable profiling
 * @return void
 */
function p3_profiler_disable() {
	$opts = get_option( 'p3-profiler_options' );
	$opts['profiling_enabled'] = false;
	update_option( 'p3-profiler_options', $opts );
}
