=== Navigation Menu IDs & Classes ===
Contributors: aarontgrogg
Tags: navigation, menu, id, class, semantic, clean
Requires at least: 3.0
Tested up to: 4.1.1
Stable tag: 2.5

To reduce the extraneous WordPress classes and add unique IDs to navigation menus.


== Description ==

This plug-in limits the classes WordPress added to navigation menus to only those desired by the Theme owner,
adds a unique ID to each `li` and removes any empty `class` attributes.

More about this plug-in can be found at:
http://aarontgrogg.com/2011/09/28/wordpress-plug-in-navigation-menu-ids-classes/

Please let me know if you have any questions/suggestions/thoughts,

Atg


== Installation ==

1. Download the ZIP
2. Unzip the ZIP
3. Copy/paste the unzipped files into your WP plug-in directory (`/wp-content/plugins/`)
4. From within WP's Plugin Admin panel, Activate the 'Navigation Menu IDs & Classes' plug-in
5. Choose which, if any, WP classes you wish to have in your HTML
6. Marvel at the power of technology!


== Frequently Asked Questions ==

= Why bother? =
* WP bakes in a ton of extraneous, border-line-useless, IDs and classes on navigation menu LIs (are you really going to
  target `id="menu-item-72"` or `class="page-item-58"` in your CSS?).  This plug-in greatly reduces those classes,
  and adds classes that reflect the link's page name, in slug form, so you can easily target menu LIs in your CSS.
= What WP classes are allowed to remain? =
* Any that you choose.
= Does this work with standard and custom menus? =
* Yes, both standard and custom menus will get class names that reflect the page name from the link they contain, such as:
  `<li class="about-us"><a href="about-us">About Us</a></li>`


== Screenshots ==

1. HTML before plug-in
2. HTML after plug-in


== Changelog ==

= 2.5 =
2015-03-13:
* Fixing issue if no cusotm classes exist

= 2.4 =
2013-07-11:
* Added ability to retain custom class added via the Menus Admin page; thanks to Bryce for the idea, and sorry it took me so long to get around to!

= 2.3 =
2013-04-07:
* Fixed naming issue preventing plug-in from working and fixed screenshots

= 2.1 =
2013-01-11:
* Fixed an issue where individual checkboxes seemed to not respond to click (in fact, thanks to WP's layout and jQuery's bubbling, my toggle function was toggling twice).  Was able to fix issue and remove the toggle function altogether.  Thanks, Erin Allen!
* Fixed an issue where the `for` attribute for all of the `label`s was not getting added correctly.  Now the `label` for each row spans the entire row, meaning you can click anywhere in a row to toggle that checkbox.
* Changed `NMIC.checkboxes.attr` to `NMIC.checkboxes.prop` in the `checkall` and `uncheckall` functions.

= 2.0 =
2012-10-03:
* Finally added the admin screen!
* Added `if ( ! function_exists( '...' ) ):` blocks around each plug-in function
* Changed function namesspace from `nmic` to `NMIC`
* Still want to find a way to gather possible WP class names programmatically, for "this" version of WP; any ideas?

= 1.2 =
2012-02-27:
* Page name slug classes were not working; this has been fixed.  Thanks to Christopher Bright for getting me to look into this.
* Removed nmic_remove_empty_classes function, as it is no longer necessary now that all nav menu LIs will have at least one class: the page name slug.

= 1.0 =
2011-09-28:
Well, this is the first version, so... here it is, hope you like it!
