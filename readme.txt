=== jonradio Perpetual Calendar ===
Contributors: jonradio
Donate link: http://jonradio.com/plugins
Tags: calendar, weekday, date, history, shortcode, function, php, plugin
Requires at least: 3.0
Tested up to: 3.4
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides both a Shortcode and a php Function that each return a message indicating the full name of the day of the week for a given date.

== Description ==

Gives web site visitors a quick way to determine the Day of the Week for any date up to almost seven thousand years in the past or future.  The plugin provides both a `[pcal]` shortcode and jr_weekday function, providing a choice in how it is implemented.

The term "Perpetual Calendar" normally refers to the display of a calendar of any week or month across a broad range of years.  However, it is almost always used to determine the day of the week for a given date, which is what this plugin does.

Once the plugin is successfully installed and activated, adding the Shortcode `[pcal]` to any WordPress Post or Page will insert an HTML `<form>` that prompts the user to select a Month, Day, and Year from drop-down lists.  Clicking the Display Day of Week button will generate a message above the `<form>` indicating the full name of the day of the week for the given date.  Or an error message for all invalid dates.  Clicking the "Help and Info" button will display, right below the Perpetual Calendar, instructions explaining what it is and how to use it.

To reduce the size of the drop-down lists, the Year is entered in three parts:  (1) first one or two digits ("century"); (2) second to last digit ("tens"); and (3) last digit.  Plugin Settings are provided within the WordPress Admin panels to display current era dates as A.D., CE or solely by the numeric Year;  ancient dates are displayed as B.C. or BCE, or are not allowed when current era dates are displayed solely as numbers.  A.D./B.C. is the default.

The jr_weekday function is also defined upon plugin activation, so that it can be called in any php code, including templates, within your WordPress environment.  It returns the same message as the `[pcal]` shortcode; it does NOT directly display through echo or other means.  And it does not display the user input form, i.e. - the HTML `<form>`.

If Network is turned on in WordPress, Network Activation of the plugin allows both the plugin and shortcode to be called from all WordPress sites within the Network.  Alternatively, the plugin can also be activated for individual sites within the Network.

Supported dates range from November 25, 4714 B.C. (1 A.D. when Plugin Settings specify "Do not allow Dates more then 2000 Years in the Past") to December 31, 9999 A.D.; the jr_weekday function accepts years larger than 9999, but it has not been tested for accuracy past the year 9999.  Illegal dates, such as February 30 of any year, and the Year Zero (A.D. or B.C.), are detected and an error message is returned in place of the message indicating the weekday.

Multiple uses of the `[pcal]` Shortcode on the same Page are detected:  the first works normally, but all others are ignored.  Likewise, displaying multiple Posts, with `[pcal]` occuring more than once across all the displayed Posts.

The `[pcal]` shortcode has been tested with the following popular Themes:  Twenty Eleven, Twenty Ten, Delicate, PageLines, Responsive, Suffusion.

== Installation ==

This section describes how to install the plugin and get it working.

1. Use "Add Plugin" within the WordPress Admin panel to download and install this plugin from the WordPress.org plugin repository (preferred method).  Or download and unzip this plugin, then upload the `/jonradio-perpetual-calendar/` directory to your WordPress web site's `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Insert the `[pcal]` shortcode in a WordPress Page or Post; or call the jr_weekday function from any php code, passing the function integer values for Year, Month and Day of Month, and displaying the message returned by the function.
1. Review the Plugin's Settings page in the WordPress Admin panels, which allows you to choose whether your site visitors will see current dates labelled as A.D., CE or not labelled at all.  And whether ancient dates will be labelled B.C., BCE or not allowed.

== Frequently Asked Questions ==

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

== Screenshots ==

1. WordPress Page with `[pcal]` Shortcode when first displayed
2. WordPress Page with `[pcal]` Shortcode after a date has been entered and Display Day of Week button has been pushed
3. WordPress Page with error message displayed because of invalid date ("February 31")
4. Plugin site page displayed when Help and Info link clicked by site visitor

== Changelog ==

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

= 2.0 =
Version 1 forced use of A.D./B.C. display of dates, which Version 2 allows to be changed via a Setting.  Version 1 also linked to the plugin site for Help, while Version 2 provides in-line Help for site visitors using the Perpetual Calendar.

= 1.0 =
Beta version 0.9 had not been tested with popular themes other than Twenty Eleven; width added to HTML SELECT tags