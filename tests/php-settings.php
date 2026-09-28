<?php

define('ABSPATH', __DIR__);
define('HOUR_IN_SECONDS', 3600);

$GLOBALS['oce_actions'] = array();
$GLOBALS['oce_filters'] = array();
$GLOBALS['oce_registered_settings'] = array();
$GLOBALS['oce_options'] = array();
$GLOBALS['oce_menu'] = null;
$GLOBALS['oce_old_menu_calls'] = 0;
$GLOBALS['oce_styles'] = array();
$GLOBALS['oce_inline_scripts'] = array();
$GLOBALS['oce_current_user_can'] = true;

function plugin_dir_path($file)
{
    return dirname($file) . '/';
}
function plugin_dir_url($file)
{
    return 'https://example.test/plugins/openconsent-eu/';
}
function plugin_basename($file)
{
    return 'openconsent-eu/openconsent-eu.php';
}
function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
{
    $GLOBALS['oce_actions'][$hook] = $callback;
}
function add_filter($hook, $callback, $priority = 10, $accepted_args = 1)
{
    $GLOBALS['oce_filters'][$hook] = $callback;
}
function apply_filters($hook, $value)
{
    return $value;
}
function register_setting($group, $option, $args)
{
    $GLOBALS['oce_registered_settings'][$option] = array($group, $args);
}
function add_menu_page(...$args)
{
    $GLOBALS['oce_menu'] = $args;
    return 'toplevel_page_openconsent-eu';
}
function add_options_page(...$args)
{
    $GLOBALS['oce_old_menu_calls']++;
}
function wp_enqueue_style(...$args)
{
    $GLOBALS['oce_styles'][] = $args;
}
function plugins_url($path, $file)
{
    return 'https://example.test/plugins/openconsent-eu/' . $path;
}
function get_option($name, $default = false)
{
    return array_key_exists($name, $GLOBALS['oce_options']) ? $GLOBALS['oce_options'][$name] : $default;
}
function wp_parse_args($args, $defaults)
{
    return array_merge($defaults, is_array($args) ? $args : array());
}
function __($text, $domain = null)
{
    return $text;
}
function esc_html__($text, $domain = null)
{
    return htmlspecialchars($text, ENT_QUOTES);
}
function esc_html_e($text, $domain = null)
{
    echo esc_html__($text, $domain);
}
function esc_attr_e($text, $domain = null)
{
    echo esc_attr($text);
}
function esc_html($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES);
}
function esc_attr($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES);
}
function esc_textarea($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES);
}
function esc_url($url)
{
    return esc_url_raw($url);
}
function esc_url_raw($url)
{
    return is_string($url) && preg_match('#^https?://#i', $url) ? filter_var($url, FILTER_SANITIZE_URL) : '';
}
function sanitize_text_field($text)
{
    return trim(strip_tags((string) $text));
}
function sanitize_textarea_field($text)
{
    return trim(strip_tags((string) $text));
}
function sanitize_hex_color($color)
{
    return is_string($color) && preg_match('/^#[a-f0-9]{6}$/i', $color) ? $color : null;
}
function sanitize_key($key)
{
    return preg_replace('/[^a-z0-9_-]/', '', strtolower((string) $key));
}
function wp_unslash($value)
{
    return $value;
}
function absint($value)
{
    return abs((int) $value);
}
function wp_json_encode($value)
{
    return json_encode($value);
}
function add_settings_error(...$args)
{
}
function current_user_can($capability)
{
    return $GLOBALS['oce_current_user_can'];
}
function admin_url($path = '')
{
    return 'https://example.test/wp-admin/' . $path;
}
function add_query_arg($args, $url)
{
    return $url . '?' . http_build_query($args);
}
function settings_errors()
{
}
function settings_fields($group)
{
    echo '<input type="hidden" name="option_page" value="' . esc_attr($group) . '">';
}
function submit_button($label)
{
    echo '<button>' . esc_html($label) . '</button>';
}
function checked($checked)
{
    echo $checked ? 'checked="checked"' : '';
}
function disabled($disabled)
{
    echo $disabled ? 'disabled="disabled"' : '';
}
function wpautop($text)
{
    return '<p>' . $text . '</p>';
}
function get_privacy_policy_url()
{
    return 'https://example.test/privacy';
}
function is_admin()
{
    return false;
}
function wp_print_inline_script_tag($script, $attributes = array())
{
    $GLOBALS['oce_inline_scripts'][] = array($script, $attributes);
}

function oce_assert($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

require dirname(__DIR__) . '/openconsent-eu.php';
OCE_Settings::register_settings();
OCE_Settings::add_settings_page();

oce_assert(is_array($GLOBALS['oce_menu']), 'top-level admin menu registered');
oce_assert($GLOBALS['oce_menu'][2] === 'manage_options', 'menu requires manage_options');
oce_assert($GLOBALS['oce_menu'][3] === 'openconsent-eu', 'menu retains its settings slug');
oce_assert($GLOBALS['oce_menu'][4] === array('OCE_Settings', 'render_settings_page'), 'menu retains its callback');
oce_assert($GLOBALS['oce_old_menu_calls'] === 0, 'settings page is no longer added under Settings');
oce_assert($GLOBALS['oce_menu'][5] === OCE_PLUGIN_URL . 'assets/images/openconsent-eu-icon.png', 'plugin icon is used as menu icon');

OCE_Settings::enqueue_admin_styles('settings_page_openconsent-eu');
oce_assert(count($GLOBALS['oce_styles']) === 0, 'admin CSS does not load on the old Settings page hook');
OCE_Settings::enqueue_admin_styles('toplevel_page_openconsent-eu');
oce_assert(count($GLOBALS['oce_styles']) === 1, 'admin CSS loads on the top-level page only');

$visitor_defaults = OCE_Settings::get_visitor_information();
oce_assert($visitor_defaults['enabled'] === true, 'privacy-rights disclosure is enabled by default');
oce_assert(count(array_filter($visitor_defaults['resources'], function ($resource) {
    return '' !== $resource['url'];
})) === 2, 'two official privacy resources are prefilled');
oce_assert($visitor_defaults['show_about'] === false, 'project attribution is disabled by default');

$visitor_sanitizer = $GLOBALS['oce_registered_settings'][OCE_Settings::OPTION_VISITOR_INFORMATION][1]['sanitize_callback'];
$sanitized = call_user_func($visitor_sanitizer, array(
    'enabled' => '1',
    'heading' => '<b>Rights</b>',
    'intro' => '<script>bad()</script>General information',
    'resources' => array(
        array('label' => '<b>EU source</b>', 'url' => 'https://example.eu/rights'),
        array('label' => 'Bad source', 'url' => 'javascript:alert(1)'),
        array('label' => 'Empty source', 'url' => ''),
    ),
    'show_about' => '1',
));
oce_assert($sanitized['heading'] === 'Rights', 'heading is sanitized as text');
oce_assert($sanitized['intro'] === 'bad()General information' && false === strpos($sanitized['intro'], '<script>'), 'intro is sanitized as text');
oce_assert($sanitized['resources'][0]['label'] === 'EU source', 'resource labels are sanitized');
oce_assert($sanitized['resources'][0]['url'] === 'https://example.eu/rights', 'https URLs are retained');
oce_assert($sanitized['resources'][1]['url'] === '', 'unsafe URLs are removed');
oce_assert($sanitized['show_about'] === true, 'about attribution can be enabled');
$GLOBALS['oce_options'][OCE_Settings::OPTION_VISITOR_INFORMATION] = $sanitized;

$GLOBALS['oce_current_user_can'] = false;
ob_start();
OCE_Settings::render_settings_page();
$forbidden_page = ob_get_clean();
oce_assert('' === $forbidden_page, 'non-administrators cannot render settings');
$GLOBALS['oce_current_user_can'] = true;
$_GET['tab'] = 'visitor_information';
ob_start();
OCE_Settings::render_settings_page();
$visitor_page = ob_get_clean();
oce_assert(strpos($visitor_page, 'Show EU privacy-rights information in the preference dialog') !== false, 'visitor-information tab renders its enable switch');
oce_assert(strpos($visitor_page, 'These links are general information only.') !== false, 'visitor-information disclaimer is visible');

ob_start();
OCE_Banner::render();
$banner = ob_get_clean();
oce_assert(strpos($banner, 'Rights') !== false, 'customized rights disclosure is rendered');
oce_assert(strpos($banner, 'target="_blank"') !== false && strpos($banner, 'rel="noopener noreferrer"') !== false, 'resource links open safely in a new tab');
oce_assert(strpos($banner, 'javascript:alert') === false, 'unsafe visitor resource URLs are never rendered');
oce_assert(strpos($banner, 'Consent controls provided by OpenConsent EU') !== false, 'optional neutral attribution renders when enabled');

$GLOBALS['oce_options'][OCE_Settings::OPTION_GENERAL] = array('consent_version' => '1', 'expiry_days' => 180, 'script_handles' => 'analytics:analytics');
$GLOBALS['oce_options'][OCE_Settings::OPTION_CATEGORIES] = array('analytics' => array('label' => 'Analytics', 'description' => 'Usage statistics', 'enabled' => false));
$categories = OCE_Categories::get_all();
oce_assert($categories['analytics']['enabled'] === true && $categories['analytics']['script_mapped'] === true, 'a category mapped to a script remains available for a visitor choice');
$consent_version = OCE_Consent::get_public_config()['consentVersion'];
$GLOBALS['oce_options'][OCE_Settings::OPTION_CATEGORIES]['analytics']['description'] = 'Changed purpose';
oce_assert(OCE_Consent::get_public_config()['consentVersion'] !== $consent_version, 'purpose changes invalidate old consent');

fwrite(STDOUT, "PHP settings and configuration checks passed\n");
