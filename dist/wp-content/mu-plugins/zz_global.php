<?php

	// Snagged from: http://wpmu.org/wpmu-robotstxt-globally/
	function my_global_robots_function(){
		global $wpdb;
		$blog = $wpdb->blogid;
		echo "Disallow: /wp-admin" . PHP_EOL;
		echo "Disallow: /wp-includes" . PHP_EOL;
		echo "Disallow: /wp-login.php" . PHP_EOL;
		echo "Disallow: /wp-content/plugins" . PHP_EOL;
		echo "Disallow: /wp-content/cache" . PHP_EOL;
		echo "Disallow: /wp-content/themes" . PHP_EOL;
		echo "Disallow: /trackback" . PHP_EOL;
		echo "Disallow: /comments" . PHP_EOL;
		echo "Disallow: */trackback" . PHP_EOL;
		echo "Disallow: */comments" . PHP_EOL;
		echo "Disallow: /*?*" . PHP_EOL;
		echo "Disallow: /*?" . PHP_EOL;
		//echo "Allow: /wp-content/blogs.dir/" . $blog . "/files/*n" . PHP_EOL;
		echo "Sitemap: " . get_bloginfo('url') . "/sitemap.xml";
	}
	add_action('do_robots', 'my_global_robots_function');

?>