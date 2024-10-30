=== Brozzme Plugins Thumbnails ===
Contributors: Benoti
Tags: plugin, thumbnails, icon, list-table, add, admin, thumb
Donate link: https://brozzme.com/
Requires at least: 4.7
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add thumbnail column to plugins list table in the admin plugins page even they are not in the repository.

== Description ==
Add thumbnail column to plugins displays thumbnails for WordPress plugins on the plugins list page, on admin (wp-admin/plugins.php).
Brozzme Add Plugins Thumbnails would help to easily identify plugins. Add your own thumbnails for premiums or personal plugins.
Just activate plugin to see thumbnail.

Options

1. Icon width and height
2. Round icon
3. Reset transients
4. Transient expiration
5. Add your special thumbnails

Behaviour

* thumbnail from wordpress.org repository
* thumbnail fallback with text, random colors or your own thubnails
* specials links to the thumbnails
* transients

[Benoti](https://brozzme.com/ "Brozzme") and [WPServeur](https://www.wpserveur.net/?refwps=221 "WPServeur WordPress Hosting").

== Installation ==
1. Upload "brozzme plugins thumbnails" to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Manage options in  Brozzme->Plugin Icons if needed.

== Screenshots ==
1. Thumbnails settings screenshot-1.png.
2. Default thumbnail screenshot-2.png.
3. Round thumbnails screenshot-3.png.

== Changelog ==
= 1.4.5 =
* compatibility fix with other plugin
= 1.4.2 =
* bugfixe on Brozzme plugins css
= 1.4 =
* add filter for premium or unknow plugin
* add setting page for specials plugins
* delete transient
* curl functions replace with wp_get_remote
= 1.0 =
* Initial release.