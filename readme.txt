=== jonradio Perpetual Calendar ===
Contributors: dgewirtz, jonradio
Donate link: http://zatzlabs.com/plugins/
Tags: calendar, weekday, date, history, shortcode, function, php, plugin
Requires at least: 3.0
Tested up to: 4.2
Stable tag: 3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows Site Visitors to display the Day of Week for any Date they enter, via Shortcode or PHP Function.

== Description ==

Display the Day of Week for virtually any Date, from 6500 years in the past to 8000 years in the future.  A Shortcode allows you to insert (anywhere on your WordPress site) a Form for your Web Site Visitors so that they can enter a Date and display the Name of the Day of the Week for that Date.  Both the Name of the Shortcode, which defaults to `[pcal]`, and the Format of the Date can be modified from the Admin Settings panel for this plugin.  A "Help and Info" button provides Visitors with background and usage details.

Providing your site visitors with a Perpetual Calendar is as easy as installing and activating this Plugin with **Add Plugin** and inserting the `[pcal]` Shortcode in a Page, Post or any other place where Shortcodes are allowed.

Click here for a Live Demo of the Plugin:  http://zatzlabs.com/plugins/

This Plugin has been Internationalized ("Translation-Ready").  Both the Admin and User (Site Visitor) views are presently available in either English or Spanish, with a .POT translation file available in the /languages/ directory within the Plugin, for any translators wishing to translate it into other languages, which we would love to include in future versions of the Plugin.  Special thanks to Andrew Kurtis of WebHostingHub for the Spanish translation.

Once the plugin is successfully installed and activated, adding the plugin's Shortcode (by default, `[pcal]`) to any WordPress Post or Page will insert an HTML `<form>` that prompts the user to select a Month, Day, and Year from drop-down lists.  Clicking the "Display Day of Week" button will generate a message above the `<form>` indicating the full name of the day of the week for the given date.  Or an error message for all invalid dates.  Clicking the "Help and Info" button will display, right below the Perpetual Calendar, instructions explaining what it is and how to use it.

To reduce the size of the drop-down lists, the Year is entered in three parts:  (1) first one or two digits ("century"); (2) second to last digit ("tens"); and (3) last digit.  Plugin Settings are provided within the WordPress Admin panels to display current era dates as A.D., CE or solely by the numeric Year;  ancient dates are displayed as B.C. or BCE, or are not allowed when current era dates are displayed solely as numbers.  A.D./B.C. is the default.

A PHP Function, `jr_weekday`, given Day, Month and Year as input parameters, will return the Date and Day of the Week as a formatted message that you can insert anywhere in the PHP code of your site.  The Format of the Date in the message can be modified from the Admin Settings panel for this plugin.  Like the Shortcode and Admin panels, the language of the message returned by the PHP function is controlled by the 'WPLANG' constant defined in wp-config.php

If Network is turned on in WordPress, Network Activation of the plugin allows both the plugin and shortcode to be called from all WordPress sites within the Network.  Alternatively, the plugin can also be activated for individual sites within the Network.

Supported dates range from November 25, 4714 B.C. (1 A.D. when Plugin Settings specify "Do not allow Dates more then 2000 Years in the Past") to December 31, 9999 A.D.; the jr_weekday function accepts years larger than 9999, but it has not been tested for accuracy past the year 9999.  Illegal dates, such as February 31 of any year, and the Year Zero (A.D. or B.C.), are detected and an error message is returned in place of the message indicating the weekday.

Multiple uses of the plugin's Shortcode on the same Page are detected:  the first works normally, but all others display the Shortcode's Name, surrounded by square brackets, followed by "(duplicate)".  Likewise when displaying multiple Posts, with the Shortcode occurring more than once across all the displayed Posts.

For more information, please [click here](http://wordpress.org/plugins/jonradio-perpetual-calendar/faq/) or on [the FAQ tab above](http://wordpress.org/plugins/jonradio-perpetual-calendar/faq/).

== Installation ==

This section describes how to install the *jonradio Perpetual Calendar* plugin and get it working.

1. Use **Add Plugin** within the WordPress Admin panel to download and install this *jonradio Perpetual Calendar* plugin from the WordPress.org plugin repository (preferred method).  Or download and unzip this plugin, then upload the `/jonradio-perpetual-calendar/` directory to your WordPress web site's `/wp-content/plugins/` directory.
1. Activate the *jonradio Perpetual Calendar* plugin through the **Installed Plugins** Admin panel in WordPress.  If you have a WordPress Network ("Multisite"), you can either **Network Activate** this plugin, or Activate it individually on the sites where you wish to use it.  Activating on individual sites within a Network avoids some of the confusion created by WordPress' hiding of Network Activated plugins on the Plugin menu of individual sites.    Alternatively, to avoid this confusion, you can install the *jonradio Reveal Network Activated Plugins* plugin.
1. Insert the `[pcal]` shortcode in a WordPress Page, Post or anywhere else where WordPress processes Shortcodes (other plugins can determine this); or call the `jr_weekday()` function from any php code, passing the function integer values for Year, Month and Day of Month, and displaying the message returned by the function.
1. Review the *jonradio Perpetual Calendar* plugin's Settings page in the WordPress Admin panels, especially the Date Form Input Layout section, since Themes vary so widely in Column Width and Font Size.  Settings also allow you to choose whether your site visitors will see current dates labelled as A.D., CE or not labelled at all.  And whether ancient dates will be labelled B.C., BCE or not allowed.  Even the name of the Shortcode can be changed.

== Frequently Asked Questions ==

= Will this plugin work with the Theme(s) that I am using? =

To date, we have not received any reports of Themes where this plugin does not work properly.  A lot of work may be required in the Date Form Input Layout section of the plugin's Settings (WordPress Admin panels) to find the layout that works best for any specific Theme.

= Why doesn't the plugin's Setting for Height of New Line (in Pixels) work? =

This can be very confusing.  This measurement is from the bottom of the new line that begins with the Date Field where Height and "New Line Before?" are selected.  Any value smaller than the minimum vertical space required to display the new line is ignored.  A value of 50 is a good starting point.

= What is a Perpetual Calendar? =

The term Perpetual Calendar normally refers to the display of a calendar of any week or month across a broad range of years.  However, it is almost always used to determine the day of the week for a given date, which is what this plugin does.

= Is the plugin available in my language and/or date format? =

The format of the Date can be changed in the Settings for the plugin.

At present, this plugin is only available in English and Spanish. Anyone interested in providing translation files and localization information for this plugin into any other language and culture is strongly encouraged to visit this page for more information:  http://zatzlabs.com/plugins/

= Can I eliminate the reference to years as A.D., as my site visitors find it confusing? =

Yes, see the Plugin's Settings page in the WordPress Admin panels.  There you can choose "Do not allow Dates more then 2000 Years in the Past" to eliminate "A.D." before or after Years.

= Can I change Year references from A.D./B.C. to CE/BCE? =

Yes, see the Plugin's Settings page in the WordPress Admin panels.  There you can choose "Current and Future Dates as CE, Ancient Dates as BCE".

= Can I use the jr_weekday php function to validate a date? =

Yes, by checking the first three characters of the returned value for "Err".  If there is enough interest, we will create a new plugin with a jr_validate_date function.  Please let us know, including your ideas on how you would like it to work, and how you would use this function.  Note that php has a similar date validation function, but it supports a different range of dates, so please let us know what range of dates you are interested in.

= In a WordPress Network (Multisite) installation, how do I force Perpetual Calendar on only some sites? =

Do not Network Activate this plugin.  Instead, Activate it on each site individually, using the Admin panel for each site, not the Network Admin panel.

= How can I get just the day of the week, without all the other information? =

If there is enough interest, we will add that to a future version.  Please let us know, including your ideas on how you would like it to work, and how you would use this feature.

== Changelog ==

= 3.0 =
* Internationalized for Translation of Admin panel, Shortcode output and PHP Function message returned
* Spanish translation included
* Date Form Input Layout section added to Settings, providing very detailed adjustments to support different Themes, Font Sizes and Localization
* Add Setting to change name of Shortcode
* Major rewrite to allow Internationalization and Translation

= 2.2 =
* Correct Network Activation to match change in WordPress notifying that a Network Activation has occurred

= 2.1 =
* Change Recommendation on Network Settings page for Plugin
* Setting was not automatically added and initialized to default on upgrade to Version 2
* Update screenshots and fix those that did not display on some browsers
* Replace add_network_options_page function call with add_submenu_page

= 2.0 =
* Add Admin Settings page to control how B.C./BCE dates are handled
* Prevent duplicate display of `<form>` when `[pcal]` appears twice on the same web page
* Move Help and Info from plugin web site to just below where `<form>` is displayed

= 1.0 =
* style=width added to all SELECT HTML tags, to avoid huge wide date fields in some Themes

= 0.9 =
* Make private plugin conform to WordPress plugin repository standards.
* Add link below ShortCode's form to explanation of the plugin.

== Upgrade Notice ==

= 3.0 =
Admin control of Date Format via Settings, and Spanish translation.

= 2.2 =
Corrects missed activation steps for Network Activation.

= 2.1 =
Corrects errors with Network Activation and version upgrades not initializing Settings.

= 2.0 =
Version 1 forced use of A.D./B.C. display of dates, which Version 2 allows to be changed via a Setting.  Version 1 also linked to the plugin site for Help, while Version 2 provides in-line Help for site visitors using the Perpetual Calendar.

= 1.0 =
Beta version 0.9 had not been tested with popular themes other than Twenty Eleven; width added to HTML SELECT tags