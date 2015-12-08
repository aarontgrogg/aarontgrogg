=== Plugin Name ===
Contributors: engfer
Donate link: http://www.engfers.com/plugins/donate/
Tags: tinymce, wysiwyg, elements, html, attributes, tiny mce, d
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: 0.3

Allows one to add "invalid" and custom HTML elements to the TinyMCE editor.

== Description ==

By default, WordPress' WYSIWYG editor, TinyMCE, will strip out of your Article and Page HTML code any 
elements that are not defined as "valid elements"; this can be extremely annoying (especially if 
you want to include iframes).

This plugin will allow you to extend what TinyMCE defines as "valid elements". By doing so, 
TinyMCE will no longer remove, delete, or strip-out the additional elements and attributes 
that you specify.

**\*\* NOTE: Make sure after you add elements or attributes you do a *hard refresh (ctrl + F5)* of 
your browser on a _TinyMCE screen *(editing or creating a page/post)* so that the TinyMCE cache 
will be refreshed! You will not see your changes until you do this!**

== Installation ==

1. Upload the `tinymce_valid_elements/` directory and its contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to **Manage -> TinyMCE Valid Elements** and add some elements and attributes.

Read the **Help Section** at the bottom of the management page.

== Frequently Asked Questions ==

= I added new elements/attributes, why is TinyMCE still stripping it out? =

Most likely, you need to do a **hard refresh** of your browser (ctrl + F5). Your JavaScript cache needs to be
refreshed.

== Screenshots ==

1. Plugin screenshot. Make sure to read the Help at the bottom of the page after installation!
