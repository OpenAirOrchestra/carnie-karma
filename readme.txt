=== Workshop Attendance ===
Contributors: darrylf Richard K
Donate link: http://www.thecarnivalband.com/
Tags: gigs, particpation
Requires at least: 5.1
Tested up to: 6.1
Stable tag: 1.3.1

A plugin to calculate and display participation Karma for The Carnival Band

== Description ==

Calculate a Karma rating based on participation in Carnival Band
Workshops and Gigs.  Karma is based on participation, with a
decay factor with a half life of about a year, so recent particpation
is what matters most.  But what have you done for me lately?

This plugin depends upon the carnig-gigs plugin and the
workshop-attendance plugin.  It extracts data from the
tables those plugins use.

== Installation ==

1. Install and activate the carnie-gigs wordpress plugin.
2. Install and activate the workshop-attendance wordpress plugin.
3. Upload this plugin to the `/wp-content/plugins/` directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.
5. Put the shortcode [carniekarma] somewhere on your website.

== Frequently Asked Questions ==

= Why did you create this plugin =

Because the Open Air Orchestra Board asked me to.

== Changelog ==

= 0.1 =
* Initial Version.

= 0.2 -
* Export tools

= 0.3 =
Filter out users with no role from listings.

= 0.4 =
Used php code instead of database views for karma calculations

= 1.0 =
Github updater integration

= 1.1 =
Fix deprecated calls.

= 1.2 =
Fix minor bugs

= 1.3.1 =
Fix shortcode rendering