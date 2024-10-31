=== PhotoShelter Gallery Widget ===
Plugin Name: PhotoShelter Gallery Widget
Version: 1.6.0
Contributors: endortrails, photoshelter
Donate link: http://graphpaperpress.com
Tags: photos, widget, photoshelter
Requires at least: 3.0
Tested up to: 3.4
Stable tag: 1.6.0

PhotoShelter Gallery Widget allows you to show your PhotoShelter galleries into your sidebar.

== Description ==

The [PhotoShelter Gallery Widget](http://graphpaperpress.com/plugins/photoshelter-gallery-widget/) is a plugin for WordPress that allows photographers to easily display the latest photo or photos from any number of their public PhotoShelter galleries. This plugin doesn't show galleries in collections. PhotoShelter is web application used by photographers around the world use to sell and license images.

== Installation ==

= Before installing =
If you have version 1.3.3 or earlier of this plugin installed on your site, delete it.  After deleting it, you can proceed with installation.

= How to install =

1. Download `photoshelter-gallery-widget.zip`
1. Unzip
1. Upload `photoshelter-gallery-widget` directory to your `/wp-content/plugins` directory
1. Go to the plugin management page and enable the plugin
1. Visit your Appearance -> Widgets page and drag the PhotoShelter Gallery widget into one of your widgetized areas

You can find full details of installing a plugin on the [plugin installation page](http://graphpaperpress.com/photoshelter-wordpress-integration/).

== Screenshots ==
1. Widget options
2. Sample display

== Documentation ==

Full documentation can be found on the [PhotoShelter Gallery](http://graphpaperpress.com/plugins/photoshelter-gallery-widget/) page.

== Frequently Asked Questions ==

[Support & FAQ](http://graphpaperpress.com/support/)

== Changelog ==

= Version 1.6.0 =
* Added support for collections.

= Version 1.5.1 =
* Check the XML file returned from PhotoShelter to make sure it's well-formed.

= Version 1.5.0 =
* Using CURL instead of file_get_contents for increased speed.
* Fixed issue with Gallery Collections.

= Version 1.4.1 =
* Fixed error on Admin Widget page when widget is first activated.
* Fixed error on Site page when there is no Gallery.
* Improved error handling

= Version 1.4.0 =
* Updated readme.txt to contain correct references to the new plugin folder name as it appears on WordPress.org plugin repo

= Version 1.3.3 =
* Added conditionals for hiding private galleries
* Added conditionals for invalid XML response
* Updated links to new PhotoShelter URL subdomain structure
* Added error messages

= Version 1.3.2 =
* Changed handle to label for consistency

== Upgrade Notice ==
* New Widget options exist for showing collections. Visit the Appearance -> Widgets page and set your PhotoShelter Gallery Widget options after upgrading.