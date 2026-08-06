=== OI Contest Schedule ===
Contributors: hanyixuanten
Tags: contest, schedule, block, dashboard, competitive programming
Requires at least: 6.4
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPL-3.0-only
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Display upcoming OI and competitive programming contests in the WordPress dashboard or with an editor block.

== Description ==

OI Contest Schedule displays upcoming competitive programming contests with local times and live countdowns.

The plugin adds an **Upcoming OI Contests** widget to the WordPress dashboard. To display the schedule on the front end, add the **OI Contest Schedule** block to a post or page.

Features include:

* Upcoming and currently running contests ordered by start time.
* Contest platform, title, start time, end time, and live countdown.
* Dates formatted in each visitor's device time zone.
* Responsive and compact layouts.
* A five-minute cache using the WordPress Transients API.
* English and Simplified Chinese translations.

= Block settings =

Add the **OI Contest Schedule** block from the block inserter. Its settings sidebar lets you display between 1 and 50 contests and enable the compact layout. The block is rendered dynamically so it always uses current contest data.

= Shortcode options =

The shortcode remains available for classic editors and templates. By default it displays up to 10 contests:

`[oi_contest_schedule]`

Use `limit` to display between 1 and 50 contests:

`[oi_contest_schedule limit="20"]`

Set `compact` to `true` to use the compact layout:

`[oi_contest_schedule limit="20" compact="true"]`

== Installation ==

1. Upload the `oi-contest-schedule` directory to `/wp-content/plugins/`, or install the plugin ZIP through the WordPress Plugins screen.
2. Activate **OI Contest Schedule** through the Plugins screen.
3. Open the WordPress dashboard to see the contest widget.
4. To show the schedule on the front end, add the **OI Contest Schedule** block to a post or page.

== Frequently Asked Questions ==

= Which contests are shown? =

The plugin shows contests whose end time is still in the future, including contests that are currently running. Finished contests and entries without a valid URL are omitted.

= Which time zone is used? =

Dates are formatted in the visitor's device time zone using the browser's internationalization API.

= How often is the contest data refreshed? =

The remote response is cached by WordPress for five minutes. After the cache expires, the next schedule request retrieves fresh data.

= What happens if the data source is unavailable? =

The schedule displays a temporary-unavailable notice instead of contest entries. WordPress pages and the dashboard continue to work normally.

= Can I use another data source? =

Developers can replace the feed URL with the `oics_contest_data_url` filter. The replacement endpoint must return the same JSON structure as the default feed.

== External Services ==

This plugin connects to GitHub Raw Content to retrieve the public `contests_all.json` feed maintained by the OI-contest-fetch project. This connection is required to obtain current contest information and occurs when the five-minute WordPress transient cache has expired.

The plugin does not send visitor content, WordPress account data, or cookies. The request includes a user-agent containing the plugin version and the site's home URL. Standard network metadata, including the server IP address, is visible to GitHub when the request is made.

Service and data source:

* Contest feed: https://raw.githubusercontent.com/hanyixuanten/OI-contest-fetch/master/contests_all.json
* OI-contest-fetch project: https://github.com/hanyixuanten/OI-contest-fetch
* GitHub Terms of Service: https://docs.github.com/en/site-policy/github-terms/github-terms-of-service
* GitHub General Privacy Statement: https://docs.github.com/en/site-policy/privacy-policies/github-general-privacy-statement

== Changelog ==

= 0.1.0 =

* Added the upcoming contest dashboard widget.
* Added the **OI Contest Schedule** editor block with limit and compact layout settings.
* Added the `[oi_contest_schedule]` shortcode with `limit` and `compact` options.
* Added visitor-local time formatting and live countdowns.
* Added platform styling and responsive layouts.
* Added five-minute transient caching for the contest feed.
* Added English and Simplified Chinese translations.

== Upgrade Notice ==

= 0.1.0 =

Initial release.