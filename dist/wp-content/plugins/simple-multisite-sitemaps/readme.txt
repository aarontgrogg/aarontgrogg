=== Simple Multisite Sitemaps ===
Contributors: luckyduck.networks
Tags: sitemaps, google sitemaps, xml sitemaps, multisite
Requires at least: 2.1
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin, once activated, generates a sitemap.xml on-the-fly for every site in a multisite network. 

== Description ==

This plugin, once activated, generates a sitemap.xml on-the-fly for every site in a multisite network. The sitemap of every site in the multisite only contains location entries which are related to that particular site. The individual sitemaps don't contain traces to other pages in the same network.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the folder `simple-multisite-sitemaps` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress for all sites in your network
3. Every site in your network now provides a sitemap.xml using a url like the following:

[site baseurl]/sitemap.xml

== Frequently Asked Questions ==

= I can't find a sitemap.xml file in my document root =

Hopefully! This plugin generates the sitemap.xml file on-the-fly, once it's requested via http. A sitemap.xml file in the document root would even stop this plugin from working correctly.

== Changelog ==

= 1.1 =
* Only up to 10 posts/pages were added to the sitemap.xml file

= 1.0 =
* Initial public release

== License ==

This plugin is released under the conditions of the GPL license. You're free to use it free of charge on your personal or commercial blog. 
