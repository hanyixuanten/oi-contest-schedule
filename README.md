# OI Contest Schedule

[简体中文](README_zh.md)

OI Contest Schedule is a lightweight WordPress plugin that displays currently running and upcoming competitive programming contests in the WordPress dashboard or on any post or page. Contests that have ended are not displayed.

Contest data is provided by [OI-contest-fetch](https://github.com/hanyixuanten/OI-contest-fetch) and displayed in each visitor's client device time zone, not the time zone configured in WordPress. The schedule identifies the client time zone by its IANA name, such as `Asia/Shanghai`.

## Features

- Adds an **Upcoming OI Contests** widget to the WordPress dashboard.
- Provides an **OI Contest Schedule** block for posts and pages.
- Keeps the `[oi_contest_schedule]` shortcode available for compatibility and templates.
- Shows contest platform, title, start and end times, and a live countdown.
- Identifies contests that are currently running.
- Supports responsive and compact layouts.
- Caches the remote contest feed for five minutes with the WordPress Transients API.
- Includes English and Simplified Chinese translations.

## Requirements

- WordPress 6.4 or later
- PHP 7.4 or later
- Outbound HTTPS access to `raw.githubusercontent.com`

## Installation

### Install a release package

1. Download the latest plugin ZIP from the repository's [Releases](https://github.com/hanyixuanten/oi-contest-schedule/releases) page.
2. In WordPress, go to **Plugins > Add New Plugin > Upload Plugin**.
3. Select the ZIP file, then install and activate the plugin.

### Install from source

1. Copy or clone this repository to `wp-content/plugins/oi-contest-schedule`.
2. Activate **OI Contest Schedule** from the WordPress **Plugins** page.

After activation, the dashboard widget appears automatically on **Dashboard > Home**.

## Editor Block

In the post or page editor, select **Add block**, search for **OI Contest Schedule**, and insert the block. Use the block settings sidebar to choose how many contests to display (1-50) and whether to use the compact layout.

The block is rendered dynamically, so contest data remains current without editing the post.

## Shortcode

For classic editors or templates, the shortcode remains available:

```text
[oi_contest_schedule]
```

The shortcode accepts the following attributes:

| Attribute | Default | Description |
| --- | --- | --- |
| `limit` | `10` | Number of contests to show. Values are constrained to the range 1-50. |
| `compact` | `false` | Set to `true` to use the compact layout. |

Example:

```text
[oi_contest_schedule limit="20" compact="true"]
```

## Data And Caching

The plugin retrieves contest data from the public JSON feed maintained by [OI-contest-fetch](https://github.com/hanyixuanten/OI-contest-fetch). Only running and upcoming contests with valid links are shown, ordered by start time.

The normalized response is cached for five minutes. Uninstalling the plugin removes this cached transient. Site developers can replace the feed URL with the `oics_contest_data_url` filter:

```php
add_filter(
    'oics_contest_data_url',
    static function () {
        return 'https://example.com/contests.json';
    }
);
```

The replacement endpoint must return the same JSON structure as the default feed.

## Development

The plugin has no Composer or npm dependencies. To build an installable archive, ensure `grep`, `sed`, GNU gettext's `msgfmt`, `zip`, and `unzip` are available, then run:

```bash
./build.sh build
```

The command validates translations and package contents, then creates `oi-contest-schedule-<version>.zip` in the repository root. To remove local build artifacts, run:

```bash
./build.sh clean
```

## Project Structure

```text
oi-contest-schedule.php   Plugin bootstrap and metadata
includes/                 Data client, renderer, and plugin integration
assets/                   Frontend styles and countdown script
languages/                Translation template and Chinese translation
uninstall.php             Cache cleanup on uninstall
build.sh                  Release package builder
```

## License

This project is licensed under the [GNU General Public License v3.0](LICENSE).
