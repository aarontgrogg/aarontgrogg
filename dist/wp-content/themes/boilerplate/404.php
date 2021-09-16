<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */

/*
 * START WP 404 ALERTS
 * http://wp-mix.com/wordpress-404-email-alerts/
 */

// set status
header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");

// site info
$blog  = get_bloginfo('name');
$site  = get_bloginfo('url') . '/';
$email = get_bloginfo('admin_email');

// theme info
if (!empty($_COOKIE["nkthemeswitch" . COOKIEHASH])) {
	$theme = clean($_COOKIE["nkthemeswitch" . COOKIEHASH]);
} else {
	$theme_data = wp_get_theme();
	$theme = clean($theme_data->Name);
}

// referrer
if (isset($_SERVER['HTTP_REFERER'])) {
	$referer = clean($_SERVER['HTTP_REFERER']);
} else {
	$referer = "undefined";
}
// request URI
if (isset($_SERVER['REQUEST_URI']) && isset($_SERVER["HTTP_HOST"])) {
	$request = clean('http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
} else {
	$request = "undefined";
}
// query string
if (isset($_SERVER['QUERY_STRING'])) {
	$string = clean($_SERVER['QUERY_STRING']);
} else {
	$string = "undefined";
}
// IP address
if (isset($_SERVER['REMOTE_ADDR'])) {
	$address = clean($_SERVER['REMOTE_ADDR']);
} else {
	$address = "undefined";
}
// user agent
if (isset($_SERVER['HTTP_USER_AGENT'])) {
	$agent = clean($_SERVER['HTTP_USER_AGENT']);
} else {
	$agent = "undefined";
}
// identity
if (isset($_SERVER['REMOTE_IDENT'])) {
	$remote = clean($_SERVER['REMOTE_IDENT']);
} else {
	$remote = "undefined";
}
// log time
$time = clean(date("F jS Y, h:ia", time()));

// sanitize
function clean($string) {
	$string = rtrim($string);
	$string = ltrim($string);
	$string = htmlentities($string, ENT_QUOTES);
	$string = str_replace("n", "<br>", $string);

	if (get_magic_quotes_gpc()) {
		$string = stripslashes($string);
	}
	return $string;
}

$message =
	"TIME: "            . $time    . "n" .
	"*404: "            . $request . "n" .
	"SITE: "            . $site    . "n" .
	"THEME: "           . $theme   . "n" .
	"REFERRER: "        . $referer . "n" .
	"QUERY STRING: "    . $string  . "n" .
	"REMOTE ADDRESS: "  . $address . "n" .
	"REMOTE IDENTITY: " . $remote  . "n" .
	"USER AGENT: "      . $agent   . "nnn";

mail($email, "404 Alert: " . $blog . " [" . $theme . "]", $message, "From: $email");

/* END WP 404 ALERTS */

get_header(); ?>
			<article id="post-0" class="post error404 not-found" role="main">
				<h1><?php _e( 'Not Found', 'boilerplate' ); ?></h1>
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'boilerplate' ); ?></p>
				<?php
					get_search_form();
					// add focus to search <input>
					echo '<script>document.getElementById(\'s\') && document.getElementById(\'s\').focus();</script>'.PHP_EOL;
				?>
			</article>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
