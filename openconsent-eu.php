<?php
/**
 * Plugin Name: OpenConsent EU
 * Plugin URI: https://github.com/vishwajeetsinghshrinet/OpenConsent-EU
 * Update URI: https://github.com/vishwajeetsinghshrinet/OpenConsent-EU
 * Description: Lightweight, open-source cookie consent management for WordPress.
 * Version: 0.1.0
 * Author: Vishwajeet Singh
 * Author URI: https://vishwajeetsinghshrinet.github.io/portfolio/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: openconsent-eu
 */

if (!defined('ABSPATH')) {
	exit;
}

final class OpenConsent_EU_GitHub_Updater
{
	const REPOSITORY = 'vishwajeetsinghshrinet/OpenConsent-EU';
	const RELEASE_TRANSIENT = 'openconsent_eu_github_latest_release';
	const CACHE_DURATION = 6 * HOUR_IN_SECONDS;

	public static function init()
	{
		add_filter('pre_set_site_transient_update_plugins', array(__CLASS__, 'check_for_update'));
		add_filter('plugins_api', array(__CLASS__, 'plugin_information'), 10, 3);
		add_filter('upgrader_source_selection', array(__CLASS__, 'fix_package_directory'), 10, 4);
	}

	public static function check_for_update($transient)
	{
		$plugin_file = plugin_basename(__FILE__);

		if (!is_object($transient) || empty($transient->checked[$plugin_file])) {
			return $transient;
		}

		$release = self::get_latest_release();
		if (empty($release['version']) || !version_compare($release['version'], $transient->checked[$plugin_file], '>')) {
			return $transient;
		}

		$transient->response[$plugin_file] = (object) array(
			'slug' => 'openconsent-eu',
			'plugin' => $plugin_file,
			'new_version' => $release['version'],
			'url' => $release['url'],
			'package' => $release['package'],
		);

		return $transient;
	}

	public static function plugin_information($result, $action, $args)
	{
		if ('plugin_information' !== $action || empty($args->slug) || 'openconsent-eu' !== $args->slug) {
			return $result;
		}

		$release = self::get_latest_release();
		if (empty($release['version'])) {
			return $result;
		}

		$information = new stdClass();
		$information->name = 'OpenConsent EU';
		$information->slug = 'openconsent-eu';
		$information->version = $release['version'];
		$information->author = '<a href="https://github.com/vishwajeetsinghshrinet">Vishwajeet Singh</a>';
		$information->homepage = $release['url'];
		$information->download_link = $release['package'];
		$information->sections = array(
			'description' => 'Lightweight, open-source cookie consent management for WordPress.',
			'changelog' => wpautop(esc_html($release['changelog'])),
		);

		return $information;
	}

	public static function fix_package_directory($source, $remote_source, $upgrader, $hook_extra)
	{
		$plugin_file = plugin_basename(__FILE__);
		if (is_wp_error($source) || empty($hook_extra['plugin']) || $plugin_file !== $hook_extra['plugin']) {
			return $source;
		}

		$plugin_directory = dirname($plugin_file);
		if ('.' === $plugin_directory || basename(untrailingslashit($source)) === $plugin_directory) {
			return $source;
		}

		$target = trailingslashit(dirname(untrailingslashit($source))) . $plugin_directory;
		global $wp_filesystem;
		if (!is_object($wp_filesystem) || !$wp_filesystem->move($source, $target)) {
			return new WP_Error('openconsent_eu_update_directory_failed', 'Could not prepare the OpenConsent EU update package.');
		}

		return $target;
	}

	private static function get_latest_release()
	{
		$release = get_site_transient(self::RELEASE_TRANSIENT);
		if (false !== $release) {
			return $release;
		}

		$response = wp_remote_get(
			'https://api.github.com/repos/' . self::REPOSITORY . '/releases/latest',
			array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/vnd.github+json',
					'User-Agent' => 'OpenConsent-EU/' . self::get_current_version(),
				),
			)
		);

		$release = array();
		if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
			$data = json_decode(wp_remote_retrieve_body($response), true);
			if (is_array($data) && !empty($data['tag_name']) && !empty($data['html_url']) && !empty($data['zipball_url'])) {
				$version = preg_replace('/^v/i', '', $data['tag_name']);
				if (is_string($version) && preg_match('/^[0-9]+(?:\.[0-9]+){1,3}(?:[-+][0-9A-Za-z.-]+)?$/', $version)) {
					$release = array(
						'version' => $version,
						'url' => esc_url_raw($data['html_url']),
						'package' => esc_url_raw($data['zipball_url']),
						'changelog' => isset($data['body']) && is_string($data['body']) ? $data['body'] : '',
					);
				}
			}
		}

		set_site_transient(self::RELEASE_TRANSIENT, $release, self::CACHE_DURATION);
		return $release;
	}

	private static function get_current_version()
	{
		$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
		return $plugin_data['Version'];
	}
}

OpenConsent_EU_GitHub_Updater::init();

final class OpenConsent_EU_Preview_Banner
{
	public static function init()
	{
		if (is_admin()) {
			return;
		}

		add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_styles'));
		add_action('wp_footer', array(__CLASS__, 'render'));
	}

	public static function enqueue_styles()
	{
		wp_enqueue_style(
			'openconsent-eu-preview',
			plugins_url('assets/css/consent-preview.css', __FILE__),
			array(),
			'0.1.0'
		);
	}

	public static function render()
	{
		echo '<aside class="oce-preview" role="note" aria-labelledby="oce-preview-title">';
		echo '<div class="oce-preview__eyebrow"><span class="oce-preview__indicator" aria-hidden="true"></span>Site preview</div>';
		echo '<h2 class="oce-preview__title" id="oce-preview-title">OpenConsent EU</h2>';
		echo '<p class="oce-preview__message">This card confirms the plugin is active. Consent choices and cookie blocking are not enabled yet.</p>';
		echo '</aside>';
	}
}

OpenConsent_EU_Preview_Banner::init();
