=== Hotel Booking Multi-Currency ===
Contributors: MotoPress
Donate link: https://motopress.com/
Tags: hotel booking, convert currency
Requires at least: 5.1
Tested up to: 6.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable travelers to switch currencies on your rental property site.

== Description ==
This MotoPress Hotel Booking extension allows you to add a multi-currency widget to your property rental website. It’ll enable travelers to switch currencies to local ones, thus recalculating property rates and totals in a few clicks. It’s easy to add the currency converter widget to the always visible website sections, such as navigation menus and widget zones. Remove the hassle of converting prices for international guests and expand your audience.


== Installation ==

1. Upload the MotoPress plugin to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.


== Copyright ==

Hotel Booking Multi-Currency plugin, Copyright (C) 2021, MotoPress https://motopress.com/
Hotel Booking Multi-Currency plugin is distributed under the terms of the GNU GPL.


== Changelog ==

= 1.2.6, Mar 1 2024 =
* Fixed a price calculation issue for admin created bookings, which was related to the currency selected on the frontend.

= 1.2.5, Sep 15 2023 =
* Fixed the number of decimals in currency rate settings after the point to avoid an exaggerated increase in total.

= 1.2.4, May 4 2023 =
* Fixed a missing currency menu switcher in website navigation menus localized with WPML.

= 1.2.3, Mar 13 2023 =
* Added support for the currency conversion in the availability calendar.
* Fixed price truncation in the availability calendar for the currency conversion with decimals.

= 1.2.2, Oct 7 2022 =
* Fixed cookies to avoid the subdomain overwriting of a selected currency code. Removed caching of pages with a non-default currency in the WP Super Cache plugin.
* Added support for the price conversion in the availability calendar.

= 1.2.1, Aug 22 2022 =
* Fixed issues that may have appeared in a Site Health report.

= 1.2.0, Jun 29 2022 =
* Fixed a fatal error in the theme appearance customizer.

= 1.1.0, May 7 2022 =
* Fixed an issue of the unavailability of setting up a low currency exchange rates in the settings.
* Fixed an issue of not displaying the currency switcher in the Elementor menu.

= 1.0.0, Oct 11 2021 =
* Added: currency switcher widget.
* Added: currency switcher menu item.
* Added: settings of currency exchange rates.
* Added: prices conversion for Hotel Booking, Hotel Booking Payment Request and Hotel Booking WooCommerce Payments plugins.
