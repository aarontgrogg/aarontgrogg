=== Plugin Name ===
Contributors: aarontgrogg
Tags: html5, boilerplate
Requires at least: 3.1
Tested up to: 4.1.1
Stable tag: 5.0.1

Based on the [HTML5 Boilerplate](http://html5boilerplate.com/) created by
[Paul Irish](http://paulirish.com/) and [Divya Manian](http://nimbupani.com/),
this plug-in allows for easy inclusion and removal of all HTML5 Boilerplate options
that are pertinent to WP.

More about this plug-in can be found at http://aarontgrogg.com/html5boilerplate/

== Description ==

Standing on the foreheads of giants (namely Paul Irish and Divya Manian and 
the good folks that have helped them create and continue the growth of HTML5 Boilerplate, 
I present to you my first WordPress plug-in, HTML5 Boilerplate.  As a spin-off of my 
Boilerplate - Starkers WP Theme, this plug-in can be added to any theme, new, pre-existing, 
already customized, whatevs!

The clumsiest part of this is dealing with the Boilerplate CSS and JS files.
To avoid any changes you make from being overwritten during upgrades,
"starter" files have been created in the `/css` and `/js` directories.  I recommend
creating copies of the starter files (removing '-starter' from the new filenames)
that you can safely edit.  That way, if the starter files are updated later, you can
simply copy/paste from them into your files again, and all is fine.

Another route would be to add additional links in your pages, but this does increase
your HTTP Requests, which hurts performance...
Your call, let me know if you can think of a better implementation.

More about this plug-in can be found at:
http://aarontgrogg.com/html5boilerplate/

I also built a Boilerplate theme based on Elliot Jay Stocks' Starkers Theme that can be found at:
http://aarontgrogg.com/boilerplate/

Please let me know if you have any questions/suggestions/thoughts,

Atg

http://aarontgrogg.com/

aarontgrogg@gmail.com


== Installation ==

1. Download the ZIP
2. Unzip the ZIP
3. Copy/paste the unzipped files into your WP plug-in directory (`/wp-content/plugins/`)
4. From within WP's Plugin Admin panel, Activate the HTML5 Boilerplate plug-in
5. In the left-nav, within the Settings menu, you should now have an HTML5 Boilerplate link
6. Click the link to view the HTML5 Boilerplate Admin panel
7. Check and un-check options to add and remove stuff from your site!


== Frequently Asked Questions ==

= What HTML5 Boilerplate options does the plug-in let me manipulate? =
* Use HTML5 `DOCTYPE`?
* Add IE Conditional `<html>` Tags?
* Move XFN profile from `<head>` to `<link>`?
* Use HTML5 Character-Encoding `<meta>` Tag?
* Kill IE6 Image Toolbar?
* Force IE-edge / Google Chrome?
* Add Google Verification?
* Force iThings to Use Full Zoom?
* Add Favicon?
* Add iThing Favicon?
* Add IE-only CSS file?
* Add Modernizr JS?
* Add Respond JS?
* Add jQuery JS?
* Which jQuery version?
* Put jQuery in `<head>` or at end of `<body>`?
* Add jQuery Plug-ins JS?
* Add Site-specific JS?
* Add Google Analytics?
* Use HTML5 Search `<input>` Type?
* Add Search `placeholder` Text?
* Add Cache Buster to CSS &amp; JS Files?


== Screenshots ==

1. Admin Screen
2. View Source Before HTML5 Boilerplate
3. View Source After HTML5 Boilerplate


== Changelog ==

= 5.0.1 2015-03-13 =
* The first update in nearly two years, on Friday the 13th...  What could go wrong??  ;-)
* Updated `css/site-specific.css` with latest `normal.css` and `main.css`
* Removed `docs/*`; if you want this, please refer to https://github.com/h5bp/html5-boilerplate/tree/master/dist/doc
* Updated `plugins.js`
* Updated `jquery.js` to 1.11.2
* Updated `modernizr.js` to 2.8.3
* Updated `respond.js` to 1.4.2 (from separate repo: https://github.com/scottjehl/Respond)
* Updated `html5shiv-printshiv.js` to 3.7.3 (from separate repo: https://github.com/aFarkas/html5shiv)
* Updated default viewport setting to `width=device-width, initial-scale=1`
* Please note that, while HTML5 Boilerplate, and truly most of the rest of the world, has moved beyond IE6, 7, and even 8,
  I retain things like IE Conditionals, IE-only CSS, etc., since you, the developer, can check or uncheck any of these options,
  based on your individual project needs.
* Updated `admin-style.css` to make sure if worked well on smaller screens, too
* Renamed _LICENSE as LICENSE.txt and README as README.txt; and I'm done playing the Rename Game with these two, H5BP peeps...
* Removed extraneous screenshot images from root
* Tested & verified in WP 4.1.1

= 4.4 2013-04-11 =
* Updated `css/site-specific.css`, concatenating `normal.css` and `main.css`
* Updated `docs/*`
* Updated `plugins.js`
* Updated `modernizr.js` to 2.6.2
* Updated `respond.js`
* Updated name of `ieshiv.js` to `html5shiv.js`
* Replaced contents of `html5shiv.js` with `html5shiv-printshiv.js`; I know it means 4kb instead of 2kb, but it just seems right...
* Updated `_LICENSE.txt` to reflect HTML5 Boilerplate `LICENSE.md`
* Removed protocol check in Google Analytics block

= 4.3 2013-04-05 =
* Big thanks to Frédéric Bolduc for pointing out that jQuery was being added twice, once from the Google CDN,
  then again the local version, because I had `!window.jQuery || ...` instead of `window.jQuery || ...`  Doh!
* And speaking of jQuery, updated the local version to 1.9.1.
* Finally figured out how Screenshots work, so... whew-hew!
* Full verison update is due again, I guess, hopefully soon!

= 4.2 2013-01-12 =
* Tested & verified in WP 3.5

= 4.1 2012-11-14 =
* Fixed an issue where the `html5shiv.js` was getting applied to a page 2-3 times in IE < 9...  erps!

= 4.0 2012-09-28 =
NOTE:
  I find myelf at a very troublesome crossroads with the good folks of HTM5Boilerplate project...
  With the release of their 4.0, they have greatly changed a number of structure items, such as directory and file names
  that will work just fine for their project, because it is intended as a starting point for new projects, but is somewhat
  problematic for things like WP Themes & Plugins, as they tend to serve initially as starting points, but then quickly become
  something that must be updatable, and therefore be backwards-compatible...  And splitting `/css/style.css` into `/css/normalize.css`
  and `/css/main.css` could cause issues for developers already using a Theme/Plugin.  So, in the interest of my users, I am staying
  with the old directory and file names, regardless of the additional work this will cause me.  I hope this doesn't confuse anyone...

* Updated jQuery to 1.8.2
* Updated Modernizr to 2.6.2
* Updated `/css/style-starter.css` to latest HTML5 Boilerplate version (combination of `/css/normalize.css` and `/css/main.css`)
* Updated `/js/plugins.js` to latest HTML5 Boilerplate version
* Updated `/js/respond.js` to latest version: https://github.com/scottjehl/respond (keeping this separate from Modernizr, as you might only want one)
* Updated `/js/ieshiv.js` to latest version: https://raw.github.com/aFarkas/html5shiv
* Added `/js/plugins-starter.js` file to prevent overwrites during Theme upgrades
* Added ability to force custom site-specific JS into the `&lt;head&gt;` instead of before the `&lt;/body&gt;`
* Added `&lt;link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png"&gt;` option
* Added `/doc` directory from latest HTML5 Boilerplate version
* Added `readme.md` from latest HTML5 Boilerplate version
* Added "Check All | Uncheck All" links to top of BP Admin form
* Reordered apple-touch-icon links per H5BP docs; thanks once again, Micah!

= 3.4.2 =
Oof!  Sloppy programming on my part, neglected to name-space functions, which was causing conflicts with other plug-ins...
Not any more, all functions & `plugin_options` names now begin `H5BP_`...  Sorry to all, and special thanks to outlierdesign and waldbach
for bringing this up in the WP Forum...

= 3.4.1 =
I hate SVN some times...

= 3.3 =
2012-02-24:
* Converted `... />` to  `...>` for all the stuff this plug-in writes to the page.
* Updated `/css/style-starter.css` to latest HTML5 Boilerplate version.
* Updated jQuery to 1.7.1.
* Updated Modernizr to 2.5.3, Custom Build.
* Added 57x57 iThing favicon link.
* Fixed Bug introduced by WP 3.3+ that causes jQuery to be loaded after site-specific JS.

= 3.2 =
2011-06-09:
Was still calling jQuery 1.5.1, erps!  Not only fixed that, but now allow users to enter version number, so they can upgrade when they want.  Thanks, Micah.
Line 522 had the wrong function comment.  Thanks, Micah.
Added option of putting jQuery/plug-ins in the `<head>` to make it more compatible with more plugins.  Thanks, Micah.
Fixed `ob_end_flush()` issues with some themes.  Thanks, Chris!
Improved `DOCTYPE`, `<html>` and `<head>` filtering to cover various versions.

= 3.1 =
2011-06-09:
Attempting to fix a few odd text appearances.

= 3.0 =
2011-06-01:
Bumping-up revisions a whole knotch, as quite a few changes here...
* Fixed typo on Admin panel (removing trailing `/` in HTML5 doctype), thanks @paul_irish.
* Per Paul & Divya recommendations:
	- Dropping cdnjs link for Modernizr, resorting to local link only, hopefully soon that will be replaced with Google CDN link.
	- Removed handheld.css, because "our research has shown not enough devices read it to make it worthwhile". Additionally, if you're doing your CSS right (a la Responsive Design, you're building for smaller screens first, then adding CSS for larger screens via `@media` queries, right?).
	- Removed print.css because "extra print stylesheets are downloaded at load, so its a big hit"; this, too, is best served via `@media` queries in your main CSS.
	- Removed YUI Profiling stuff because you "probably weren't using it anyway", right?
	- Removed Belated PNG because it "is a really slow solution and an overkill for PNGs", check [http://html5boilerplate.com/docs/#Notes-on-using-PNG](http://html5boilerplate.com/docs/#Notes-on-using-PNG) for deets on dealing with PNGs in ye olde IE.
* Added removal of IE6 Image Toolbar to Admin panel.
* Added iPad and iPhone 4 favicon links to existing "iThing Favicon" block.
* Added [Respond.js](http://filamentgroup.com/lab/respondjs_fast_css3_media_queries_for_internet_explorer_6_8_and_more/) option to Admin panel.
* Added [Google Verification](http://www.google.com/support/webmasters/bin/answer.py?answer=35179) option to Admin panel.
* Updated `/css/style-starter.css` to latest HTML5 Boilerplate version.
* Updated jQuery to 1.6.1.
* Updated `/js/plugins.js` to include `console.log` bit.

= 2.2 =
2011-05-11:
Added features to move XFN Profile link, convert Search input type to "search", add custom placeholder text, and add cache buster to all CSS/JS URLs.
Phew!

= 2.1 =
2011-05-10:
Forgot the `/` between `BP_PLUGIN_URL` and the rest of the URL... erps!  Also did a little clean-up on how the Google Analytics gets applied.
Lastly, giving the screenshots one... last... try...

= 2.0 =
2011-05-10:
Finally found [an article](http://wordpress.org/support/topic/updated-my-plugin-listing-still-showing-old-version?replies=7) that tells me that
not only do I need to update the "Stable tag" in the `readme.txt` and copy all the plug-in files to a new `Tag` folder, but also the `Version` in the plug-in
file itself... So, hopefully this will finally get the latest plug-in into the Repository...  :-)

= 1.3 =
2011-05-08:
Updating jQuery version to 1.6 and hopefully fixing links on Screenshot page.

= 1.2 =
2011-04-25:
Reviewing additional HTML5 Boilerplate pages, adding [Google Fix URL option](http://www.google.com/support/webmasters/bin/answer.py?answer=136085)
to HTML5 Boilerplate Admin panel.

= 1.1 =
2011-04-24:
Trying to get Screenshot links working in `readme.txt`...
May or may not work...  :-)

= 1.0 =
2011-04-21:
Well, this is the first version, so... here it is!  This version includes
all of the nutritious goodness from HTML5 Boilerplate as of April 21, 2011.
