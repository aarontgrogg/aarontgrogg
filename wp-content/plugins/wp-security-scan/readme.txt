=== Acunetix WP Security ===
Contributors: Acunetix
Author: Acunetix
Tags: security, securityscan, chmod, permissions, admin, administration, authentication, database, dashboard, post, notification, password, plugin, posts,
plugins, private, protection, tracking, wordpress
Requires at least: 3.0
Tested up to: 3.6
Stable tag: trunk

Scans your WordPress installation for security vulnerabilities.

== Description ==
Acunetix WP Security plugin is a free and comprehensive security tool that helps you secure your WordPress
installation and suggests corrective measures for: securing file permissions, security of the database, version hiding,
WordPress admin protection and lots more.

Acunetix WP Security checks your WordPress website/blog for security vulnerabilities and suggests corrective actions such as:

1. Passwords
2. File permissions
3. Database security
4. Version hiding
5. WordPress admin protection/security
6. Removes WP Generator META tag from core code

= Requirements =

* WordPress version 3.0 and higher
* PHP5 (tested with PHP Interpreter >= 5.2.9)


= Key security features: =

* Easy backup of WordPress database for disaster recovery
* Removal of error-information on login-page
* Addition of index.php to the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listings
* Removal of wp-version, except in admin-area
* Removal of Really Simple Discovery meta tag
* Removal of Windows Live Writer meta tag
* Removal of core update information for non-admins
* Removal of plugin-update information for non-admins
* Removal of theme-update information for non-admins (only WP 2.8 and higher)
* Hiding of wp-version in backend-dashboard for non-admins
* Removal of version in URLs from scripts and stylesheets only on frontend
* Reporting of security overview after WordPress blog is scanned
* Reporting of file permissions following security checks
* Live traffic tool to monitor your website activity in real time
* Integrated tool to change the database prefix
* Disabling of database error reporting (if enabled)
* Disabling of PHP error reporting

For more information on the Acunetix WP Security plug-in and other WordPress security news, visit the
<a href="http://www.acunetix.com/blog/" target="_blank">Acunetix Blog</a> and join
our <a href="http://www.facebook.com/Acunetix" target="_blank">Facebook</a> page. Post any questions or feedback
on the <a href="http://wordpress.org/support/plugin/wp-security-scan" target="_blank">Acunetix WP Security plug-in forum</a>.



== Installation ==

* Make a backup of your current installation
* Unpack the downloaded package
* Upload the extracted files to the /wp-content/plugins/ directory
* Activate the plugin through the 'Plugins' menu in WordPress

If you encounter any bugs, or have comments or suggestions, please post them on the
<a href="http://wordpress.org/support/plugin/wp-security-scan" target="_blank">Acunetix WP Security plug-in forum</a>.

For more information on the Acunetix WP Security plug-in and other WordPress security news, visit the
<a href="http://www.acunetix.com/blog/" target="_blank">Acunetix Blog</a> and join
our <a href="http://www.facebook.com/Acunetix" target="_blank">Facebook</a> page. Post any questions or feedback
on the <a href="http://wordpress.org/support/plugin/wp-security-scan" target="_blank">Acunetix WP Security plug-in forum</a>.


== Frequently Asked Questions ==

= Can I deactivate Acunetix WP Security once I've run it once? =

No.  Acunetix WP Security needs to be left activated to work.  Version hiding,
turning off DB errors, removing WP ID META tag from HTML output, and other
functionality will cease if you deactivate the plugin.

= How do I change the file permissions on my WordPress installation?  =

From the Linux command line (for advanced users):
    chmod xxx filename.ext
    (replace xxx with with the permissions settings for the file or folder).

From your FTP client:
    Most FTP clients, such as FileZilla, etc, allow for changing file
permissions.  Please consult your client's documentation for your specific
directions.

For more information, please visit http://codex.wordpress.org/Changing_File_Permissions

= Why do I need to hide my version of WordPress?  =

Many attackers and automated tools will try and determine software versions
before launching exploit code. Removing your WordPress blog version may
discourage some attackers and certainly will mitigate virus and malware programs
that rely on software versions.

For more information on the Acunetix WP Security plug-in and other WordPress security news, visit the
<a href="http://www.acunetix.com/blog/" target="_blank">Acunetix Blog</a> and join
our <a href="http://www.facebook.com/Acunetix" target="_blank">Facebook</a> page. Post any questions or feedback
on the <a href="http://wordpress.org/support/plugin/wp-security-scan" target="_blank">Acunetix WP Security plug-in forum</a>.


== Screenshots ==

1. The File Scan Report
2. The Settings page, displaying all configurable options
3. The Live Traffic page
4. The new Dashboard page


== WordPress Security ==

Security Scanner:

1. Scans Wordpress installation for file/directory permissions vulnerabilites
2. Recommends corrective actions
3. Scans for general security vulnerabilities


== Other Notes ==
= License =
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog.

For more information on the Acunetix WP Security plug-in and other WordPress security news, visit the
<a href="http://www.acunetix.com/blog/" target="_blank">Acunetix Blog</a> and join
our <a href="http://www.facebook.com/Acunetix" target="_blank">Facebook</a> page. Post any questions or feedback
on the <a href="http://wordpress.org/support/plugin/wp-security-scan" target="_blank">Acunetix WP Security plug-in forum</a>.


== Changelog ==

= 4.0.1 =
* Fixed the LiveTraffic bug


= 4.0.0 =
* Complete core update
* Added live traffic functionality
* Added check for the wp-config.php file one level above if not found in the install directory
* Fixed broken functionalities
* Security settings are now configurable


= 3.1.0 =
* Removed the registration requirement

= v3.0.9 =
* New feature: Added tooltips to the most important sections of the System Information box
* Update: Added 'nonce' fields to forms where needed (thanks to julio from boiteaweb.fr)
* Update: Updated the deprecated function call get_bloginfo('siteurl') to get_bloginfo('url')
* Update: Updated paths to external resources such as javascript and css files for ssl compatibility
* Update: Updated validation for plug-in form fields (email address, user name, target id, etc.)
* Update: Enhanced the input validation for the Change Database Prefix tool
* Update: Plugin settings are now accessible only by WordPress administrators
* Update: Improved user-rights retrieval in the WordPress database


= v3.0.8 =
* New feature: Option to open / close WebsiteDefender dashboard widget
* Update: More help resource links
* Update: Internal code updates

= v3.0.7 =
* BugFix: The bug reported about ALTER rights retrieval has been addressed
* Update: Code cleanup
* Update: Minor internal updates

= v3.0.6 =
* Feature: The WebsiteDefender RSS widget added to the admin dashboard
* Update: The plug-in has been made compatible with Secure WP and WebsiteDefender WordPress Security
* Update: The plug-in can be safe used in a Multi User WP environment
* Bugfix: Fixed the bug related to the database ALTER rights retrieval
* BugFix: Fixed the version information script

= v3.0.5 (07/20/2011) =
* Bugfix: Updated the links to websitedefender.com

= v3.0.4 (07/20/2011) =
* Update: the plug-in has been made compatible with Secure WP plug-in

= v3.0.3 (07/14/2011) =
* Bugfix: Fixed the code error in the change database prefix feature
* Update: More code cleanup

= v3.0.2 (07/11/2011) =
* Feature: Enabled automatic database table prefix rename feature
* Feature: Database backup functionality used in databse renaming tables
* Feature: Included more documentation / links for a number of features
* Feature: Prevent blog owner from changing table prefix if the wp-config.php file is not writable
* Update: Updated several error messages text (user friendly)
* Update: major code cleanup
* Bugfix: Fixed a number of bugs in the database tables rename utility

= v3.0.1 (03/24/2011) =
* Regression: Temporarily disabled database change feature
* Fixed: Resolved conflict with plugins using the reCAPTCHA library
* Bugfix: Fixed CSS image background not showing corectly

= v3.0.0 (03/22/2011) =
* Feature: Release new stable version
* Feature: Rebranding of the plugin
* Feature: Integrated WebsiteDefender.com registration in Settings

For more information on the Acunetix WP Security plug-in and other WordPress security news, visit the
<a href="http://www.acunetix.com/blog/" target="_blank">Acunetix Blog</a> and join
our <a href="http://www.facebook.com/Acunetix" target="_blank">Facebook</a> page. Post any questions or feedback
on the <a href="http://wordpress.org/support/plugin/wp-security-scan" target="_blank">Acunetix WP Security plug-in forum</a>.
