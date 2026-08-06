# OI Contest Schedule WordPress Plugin

This subproject packages the OI contest feed as a WordPress plugin. It follows the repository layout and release pattern used by `wp-translate`: the installable plugin lives in its slug directory, while `build.sh` creates a versioned ZIP archive under `build/`.

## Features

- Adds an **Upcoming OI Contests** widget to the WordPress dashboard.
- Provides the `[oi_contest_schedule]` shortcode for posts and pages.
- Formats timestamps in the visitor's browser time zone.
- Shows live countdowns and distinguishes currently running contests.
- Caches the public GitHub JSON feed with a five-minute WordPress transient.

## Requirements

- WordPress 6.4 or newer
- PHP 7.4 or newer

## Usage

Activate the plugin and add a Shortcode block containing:

```text
[oi_contest_schedule]
```

The shortcode accepts `limit` from 1 to 50 and a compact layout flag:

```text
[oi_contest_schedule limit="20" compact="true"]
```

## Build

Run from this directory:

```bash
chmod +x build.sh
./build.sh build
```

The archive is written to `build/oi-contest-schedule-<version>.zip`.