=== Plugin Name ===
Contributors: mail@punchcreative.nl
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=H6Y3AFKN2QL76&lc=NL&item_name=Punch%20Creative&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: numbers, random, subscription, email, bingo
Requires at least: 3.6
Tested up to: 3.7
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin generates and sends subscribers a unique number within a range you can set.

== Description ==

This is a Wordpress plugin to help with subscription to an event with a maximum of participants but a lot more people who are willing to participate. The plugin gives each subscriber a unique number, like a lottery. The numbers are generated within a range you can set in the admin settings for this plugin. For instance: You want to enable subscription to a spinning event with a maximum of 35 participants. There are more people who want to subscribe than that there are spinning bikes available and you don't want to use the principle 'who's first with describing gets a bike'. Well here's what get-your-number does for you.

set a range of numbers which largely overrides the amount of available places in the event (in my example i know the association has 100 members so i choose a range from 1 to 100, knowing not everyone is going to subscribe this is a safe range)
let people subscribe using their name and email
after registration, mail the subscriber, and the GYN administrator, the random and unique number that has been given
If a person has subscribed the name, number and email is logged in the Wordpress options table. If a subscribers email is found in the options table, it's not possible to request another number. The subscriber get's the number presented that's already sent.

After you close the subscription, you can decide on how to make use of the numbers that are given.

Install the plugin in a folder named get-your-number in your Wordpress plugins directory

Playing a Bingo could be one ;-)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory in a directory called get-your-number
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the shortcode [gyn/] in a post or on a page or put `<?php do_shortcode('gyn'); ?>` in your templates

== Frequently Asked Questions ==

None yet

== Screenshots ==


== Changelog ==

= 1.1=
* Added admin tools
* Added logging in options table
* Added meta boxes in admin area

= 1.0 =
* Started a basic random number generator.
* Added mail function.

== Upgrade Notice ==

= 1.1 =
Stable release 


== Arbitrary section ==


== A brief Markdown Example ==