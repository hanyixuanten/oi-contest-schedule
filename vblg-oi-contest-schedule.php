<?php
/**
 * Plugin Name: vblg OI Contest Schedule
 * Plugin URI: https://github.com/hanyixuanten/oi-contest-schedule
 * Description: Display upcoming OI contests in a dashboard widget or with an editor block.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: hanyixuanten
 * Author URI: https://www.vblg.top
 * License: GPL-3.0-only
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: vblg-oi-contest-schedule
 */

defined( 'ABSPATH' ) || exit;

define( 'OICS_VERSION', '0.1.0' );
define( 'OICS_FILE', __FILE__ );
define( 'OICS_PATH', plugin_dir_path( __FILE__ ) );
define( 'OICS_URL', plugin_dir_url( __FILE__ ) );

require_once OICS_PATH . 'includes/class-contest-client.php';
require_once OICS_PATH . 'includes/class-schedule-renderer.php';
require_once OICS_PATH . 'includes/class-plugin.php';

add_action( 'plugins_loaded', array( 'OICS_Plugin', 'instance' ) );