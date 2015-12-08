=== JM Twitter Cards ===
Contributors: jmlapam
Tags: twitter, cards, semantic markup, metabox, meta
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=tweetpressfr%40gmail%2ecom&item_name=JM%20Twitter%20Cards&no_shipping=0&no_note=1&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8
Requires at least: 3.0
Tested up to: 3.6
License: GPLv2 or later
Stable tag: trunk
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin meant to simplify Twitter cards integration on WordPress. You can customize your Twitter cards per each post.

== Description ==

Once activated the plugin adds Twitter cards on your posts according to your settings. Enjoy !
Compatible with : WP SEO by Yoast (do not check cards option in this plugin if activated) and All in One SEO

The plugin allows you to customize your cards per each post. But do not activate cards in SEO by Yoast plugin. Otherwise markup will be added twice.
If you choose full customization metabox is now on the right side in your edit. It's shorter simply because now twitter account is retrieved from author's profile.

<a href="http://twitter.com/intent/user?screen_name=tweetpressfr">Follow me on Twitter</a>


––––
En Français 
–––––––––––––––––––––––––––––––––––

Une fois activé le plugin s'occupe d'ajouter une card Twitter sur vos posts selon vos réglages. Profitez-en bien !

Compatible avec WP SEO by Yoast(ne pas ajouter l'option cards dans ce plugin si activé) et All in One SEO

Le plugin vous permet de personnaliser les cards pour chaque post. Attention malgré tout à ne pas activer l'option card de Yoast ou sinon le markup sera ajouté 2 fois.
En mode full custom la metabox est maintenant située sur le côté tout en haut, elle est réduite car le compte Twitter de l'auteur est récupéré directement depuis son profil.

<a href="http://twitter.com/intent/user?screen_name=tweetpressfr">Me suivre sur Twitter</a>


== Installation ==

1. Upload plugin files to the /wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. Then go to settings > JM Twitter Cards to configure the plugin
4. To display the metabox in edit section (posts, pages, attachments), enable option in section called **Custom Twitter Cards**

––––
En Français 
–––––––––––––––––––––––––––––––––––

1. Chargez les fichiers de l'archive dans le dossier /wp-content/plugins/ 
2. Activez le plugin dans le menu extensions de WordPress
3. Allez dans réglages > JM Twitter Cards pour configurer le plugin
4. Pour afficher la metabox dans vos admin de posts, de pages et de médias, activez l'option correspondante dans **Custom Twitter Cards**

== Frequently asked questions ==

= I got problem with Instagram = 
It's a known issue due to Instagram. Twitter said it recently : Users are experiencing issues with viewing Instagram photos on Twitter. Issues include cropped images.This is due to Instagram disabling its Twitter cards integration, and as a result, photos are being displayed using a pre-cards experience. 
So, when users click on Tweets with an Instagram link, photos appear cropped.*

= Plugin is fine but Twitter cards doesn't appear in my tweets =
1. Make sure you correctly fulfilled fields in option page according to <a href="https://dev.twitter.com/docs/cards" title="Twitter cards documentation">Twitter documentation</a>
2. Make sure you have correctly <a href="https://dev.twitter.com/docs/cards/validation/validator" title="Twitter cards submit">submitted your website to Twitter</a>
3. Wait for Twitter's answer (a mail that tells you your site has been approved)
4. Be careful with your robots.txt and put some rules to allow Twitter to fetch your website :
`User-agent: Twitterbot
    Disallow:`
If it still doesn't work please open a thread on support or at this URL: <a href="http://tweetpress.fr/en/plugin/new-plugin-jm-twitter-cards/">TweetPress, JM Twitter Cards</a>


––––
En Français 
–––––––––––––––––––––––––––––––––––

= Problème avec Instagram = 
C'est un problème connu. Cela vient d'Instagram lui-même qui préfère que ses utilisateurs partagent les photos chez lui plutôt que sur Twitter. Instagram a supprimé ses Twitter Cards.

= Le plugin marche mais je n'obtiens pas de Twitter Cards dans mes tweets =
1. Assurez-vous bien d'avoir rempli correctement les champs dans la page d'options suivant <a href="https://dev.twitter.com/docs/cards" title="Twitter cards documentation">la documentation Twitter</a>
2. Assurez-vous bien d'avoir <a href="https://dev.twitter.com/docs/cards/validation/validator" title="Twitter cards formulaire de validation">soumis votre site à Twitter</a>
3. Attendez la réponse de Twitter (un mail qui vous indique que votre site a été approuvé)
4. Attention avec le fichier robots.txt, vous devez autoriser le robot de Twitter à crawler votre site:
`User-agent: Twitterbot
    Disallow:`
Si cela ne marche toujours pas SVP ouvrez un topic sur le support du plugin ou à cette adresse : <a href="http://tweetpress.fr/plugin/jm-twitter-cards">TweetPress, JM Twitter Cards</a>

== Screenshots ==
1. admin
2. confirmation mail
3. metabox

== Changelog ==

=  3.1.6 =
* 05 Jun 2013
* Remove useless spaces in content for meta description. Thanks to @aarontgrogg for having reported this issue and shared the solution.

=  3.1.5 =
* 24 May 2013
* Remove support for gallery cards because Twitter is not completely ready for it (approval is problematic)
* Add support for external images (now you can set image file URL for each post)
* Improve features in meta box

=  3.1.4 =
* 03 May 2013
* Remove markup from 404 and other pages where it's useless

=  3.1.3 =
* 29 Apr 2013
* Add some flexibility for SEO plugin option as suggested by Matsuri Times in support. Now even if SEO option is activated, you can leave SEO fields blank. The plugin will grab post title and/or desc from the content. 

=  3.1.2 =
* 27 Apr 2013
* Totally rebuild extra user profile field for multi-author blog with appropriate name for meta key (less confusing) and a real section in profile, not just another field that users might skip.
* Add some regex to eliminate any unecessary @ from input values.
* Special thank to Greglone, these hooks are good !

=  3.1.1 =
* 20 Apr 2013
* Add support for summary_large_image (newest feature)

=  3.1.0 =
* 06 Apr 2013
* Add gallery card support and fix minor bug regarding photo option
* Update link to validator 

=  3.0.5 =
* 02 Apr 2013
* Add option in settings for those who use WP SEO or All in One SEO BUT do not want to use custom meta title and desc as twitter meta
* Important ! default is yes so you have to deactivate it by selecting no on settings if you want to do this

=  3.0.0 =
* 29 mar 2013
* Add support for All in One SEO
* Double check : this time it won't break ! 

= 2.2.9 =
* 23 mar 2013
* Correct bug with wrong call of class WPSEO_Frontend()
* Sorry for multiple udpates and thanks to those who report bugs

= 2.2.8 =
* 22 mar 2013
* Correct bug in options page
* Sorry last change guys

= 2.2.7 =
* 22 mar 2013
* Add support for SEO plugins. Title can be retrieved thanks to wp_title() and meta description is no longer set according to excerpt but from the custom meta in your edit if SEO by Yoast is active.
* Enjoy

= 2.2.6 =
* 22 mar 2013
* just quit support for Open Graph because people use their own plugin for that so the markup is no longer added twice.

= 2.2.5 =
* 20 mar 2013
* fix problem with meta image

= 2.2.4 =
* 20 mar 2013
* Fix metabox that JUST DID NOT WORK ! Add support for custom post types. Next update will regard meta description...

= 2.2.3 =
* 18 mar 2013
* Fix og meta (site and creator) ignored by Twitter thanks to report by Tyrannous

= 2.2.2 =
* 17 mar 2013
* Add support for open graph
* Replace meta attr name with property which is mroe compliant with W3C 

= 2.2.1 =
* 13 fev 2013
* Add some user role verification for Twitter option in author's profile. Only author and admin should see the field for Twitter Cards in their profile. 
* Fix issue with attachment
* Fix notice with nonce

= 2.2 =
* 12 fev 2013
* Fix bug with posts page if homepage

= 2.1 =
* 11 fev 2013
* Fix bug for posts page

= 2.0 =
* 10 fev 2013
* fix wrong function with get_the_author_meta !

= 1.1.9 =
* 10 fev 2013
* add Twitter field in user profile in case it's not there !

= 1.1.8 =
* 10 fev 2013
* find a goodway to make code lighter, pretty lighter !

= 1.1.7 =
* 17 jan 2013
* add WP Custom-fields by GeekPress. Code is cleaner.

= 1.1.6 =
* 28 dec 2012
* fixed bug with empty args on function get_post_by_id, put html out of my translation and remove esc_html for esc_attr (silly mistakes and thanks Juliobox for your comment), next update will include lighter code to integrate metabox

= 1.1.5 =
* 27 dec 2012
* add warnings for WP SEO by Yoast users

= 1.1.4 =
* 25 dec 2012
* add features and extra options

= 1.1.3 =
* 22 dec 2012
* fix bug with photo cards and add options

= 1.1.2 =
* 22 dec 2012
* twitter cards only single posts, next updates will offer more options

= 1.1.1 =
* 22 dec 2012
* add links and styles on admin to improve readability

= 1.1 =
* 6 dec 2012
* add a function_exists() in case of the functions are already implemented in functions.php or anywhere else in your theme

= 1.0 =
* 5 dec 2012
* Initial release
* thanks GregLone for his great help regarding optimization

== Upgrade notice ==
Nothing
= 1.0 =


