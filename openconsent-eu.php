<?php
/**
 * Plugin Name: OpenConsent EU
 * Plugin URI: https://github.com/vishwajeetsinghshrinet/OpenConsent-EU
 * Update URI: https://github.com/vishwajeetsinghshrinet/OpenConsent-EU
 * Description: Lightweight, open-source cookie consent management for WordPress.
 * Version: 1.0.0
 * Requires at least: 5.7
 * Requires PHP: 7.4
 * Author: Vishwajeet Singh
 * Author URI: https://vishwajeetsinghshrinet.github.io/portfolio/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: openconsent-eu
 */

if (!defined('ABSPATH')) {
	exit;
}

define('OCE_PLUGIN_FILE', __FILE__);
define('OCE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OCE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('OCE_VERSION', '1.0.0');

require_once OCE_PLUGIN_DIR . 'includes/class-oce-plugin.php';

OCE_Plugin::init();
