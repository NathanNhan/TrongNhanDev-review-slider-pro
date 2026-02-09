=== Trongnhandev Review Slider Pro ===
Donate link: https://trongnhandev.com/
Tags: review, slider
Requires at least: 6.3
Tested up to: 6.9
Stable tag: 1.6
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Upload the tnd Review Slider plugin to your blog, activate it, and then enter your place ID and API key from tnd.

1, 2, 3: You’re done!

== Description ==
User can upload this plugin to wordpress, extract it, type place ID to settings and use shortcode name as [tnd_review_slider limit=10] for display data.
Major features in tnd Review Slider include:
Display review tnd from Place ID 
Control limit to display reviews
Use shortcode to display reviews list

== External services ==

This plugin connects to an API to obtain Review slider information, it's needed to show the Google Review information in the shortcode.

It sends the user's location every time the shortcode is loaded by using Google Map API. You need to pass an place ID and Google API key for configuration. 
Besides, you can control shortcode attrbutes with params: limit which can display how many slide items show in frontend. [terms of use](https://www.google.com/help/terms_maps/), [privacy policy](https://policies.google.com/privacy)

Example::
includes/class-grs-api-handler.php:40 'https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&fields=name,rating,reviews,user_ratings_total&key=%s',

A few notes about the sections above:

* nhantrong13091997
* tnd,tnd review slider,tnd api
* 5.6
* 6.5
* 1.0.0

Note that the `readme.txt` value of stable tag is the one that is the defining one for the plugin.  If the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used for displaying information about the plugin.

If you develop in trunk, you can update the trunk `readme.txt` to reflect changes in your in-development version, without having that information incorrectly disclosed about the current stable version that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

If no stable tag is provided, your users may not get the correct version of your code.

== Frequently Asked Questions ==

= How to use this plugin ? =

Use shortcode with name as [tnd_review_slider limit=10] to display data

= How to get placeID from tnd api ? =

Access to this tnd map api to get placeID based on address.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Screenshots are stored in the /assets directory.
2. This is the second screen shot

== Changelog ==
= 1.6 =
Fix Bug
= 1.5 =
Fix bug group settings
= 1.4 =
* fix bug
= 1.3 =
* updated prefix for plugin correctly.
= 1.2 =
* updated feedbacks from wordpress.org.
= 1.0 =
* This is my final version for TrongNhanDev Review Slider.


== A brief Markdown Example ==

Markdown is what the parser uses to process much of the readme file.

[markdown syntax]: https://daringfireball.net/projects/markdown/syntax

Ordered list:

1. Display review slider with shortcode
1. Display review slider from tnd via placeID and limit from shortcode attribute
1. Apply cache to boost performance

Unordered list:

* We have some security for this plugin
* We have developer to maintaince this plugin


`<?php code(); ?>`