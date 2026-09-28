<?php

if (!defined('ABSPATH')) {
    exit;
}

final class OCE_Settings
{
    const OPTION_NAME = 'openconsent_eu_banner_content';
    const OPTION_CATEGORIES = 'openconsent_eu_categories';
    const OPTION_APPEARANCE = 'openconsent_eu_appearance';
    const OPTION_GENERAL = 'openconsent_eu_general';
    const OPTION_VISITOR_INFORMATION = 'openconsent_eu_visitor_information';

    private static $font_families = array(
        'sans-serif' => 'sans-serif',
        'system' => 'system-ui, -apple-system, BlinkMacSystemFont, sans-serif',
        'arial' => 'Arial, Helvetica, sans-serif',
        'serif' => 'Georgia, serif',
    );

    public static function init()
    {
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_menu', array(__CLASS__, 'add_settings_page'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_styles'));
    }

    public static function register_settings()
    {
        self::register_option('oce_content_settings', self::OPTION_NAME, 'sanitize_content', self::default_content());
        self::register_option('oce_categories_settings', self::OPTION_CATEGORIES, 'sanitize_categories', OCE_Categories::defaults());
        self::register_option('oce_appearance_settings', self::OPTION_APPEARANCE, 'sanitize_appearance', self::default_appearance());
        self::register_option('oce_general_settings', self::OPTION_GENERAL, 'sanitize_general', self::default_general());
        self::register_option('oce_visitor_information_settings', self::OPTION_VISITOR_INFORMATION, 'sanitize_visitor_information', self::default_visitor_information());
    }

    private static function register_option($group, $option, $sanitizer, $default)
    {
        register_setting(
            $group,
            $option,
            array(
                'type' => 'array',
                'sanitize_callback' => array(__CLASS__, $sanitizer),
                'default' => $default,
            )
        );
    }

    public static function add_settings_page()
    {
        add_menu_page(
            'OpenConsent EU',
            'OpenConsent EU',
            'manage_options',
            'openconsent-eu',
            array(__CLASS__, 'render_settings_page'),
            OCE_PLUGIN_URL . 'assets/images/openconsent-eu-icon.png',
            81
        );
    }

    public static function enqueue_admin_styles($hook)
    {
        if ('toplevel_page_openconsent-eu' === $hook) {
            wp_enqueue_style('openconsent-eu-admin', plugins_url('assets/css/admin.css', OCE_PLUGIN_FILE), array(), OCE_VERSION);
        }
    }

    public static function sanitize_content($input)
    {
        $input = is_array($input) ? $input : array();
        $defaults = self::default_content();

        return array(
            'label' => isset($input['label']) && is_string($input['label']) ? sanitize_text_field(wp_unslash($input['label'])) : $defaults['label'],
            'title' => isset($input['title']) && is_string($input['title']) ? sanitize_text_field(wp_unslash($input['title'])) : $defaults['title'],
            'message' => isset($input['message']) && is_string($input['message']) ? sanitize_textarea_field(wp_unslash($input['message'])) : $defaults['message'],
            'policy_url' => isset($input['policy_url']) && is_string($input['policy_url']) ? esc_url_raw(wp_unslash($input['policy_url'])) : $defaults['policy_url'],
        );
    }

    public static function sanitize_categories($input)
    {
        $input = is_array($input) ? $input : array();
        $defaults = OCE_Categories::defaults();
        $categories = array();

        foreach ($defaults as $key => $default) {
            $value = isset($input[$key]) && is_array($input[$key]) ? $input[$key] : array();
            $categories[$key] = array(
                'label' => isset($value['label']) && is_string($value['label']) ? sanitize_text_field(wp_unslash($value['label'])) : $default['label'],
                'description' => isset($value['description']) && is_string($value['description']) ? sanitize_textarea_field(wp_unslash($value['description'])) : $default['description'],
                'enabled' => $default['required'] || (isset($value['enabled']) && in_array($value['enabled'], array('1', 1, true), true)),
            );
        }

        return $categories;
    }

    public static function sanitize_appearance($input)
    {
        $input = is_array($input) ? $input : array();
        $defaults = self::default_appearance();
        $mode = isset($input['mode']) && is_string($input['mode']) ? $input['mode'] : '';
        $font_family = isset($input['font_family']) && is_string($input['font_family']) ? $input['font_family'] : '';
        $font_size = isset($input['font_size']) && is_scalar($input['font_size']) && is_numeric($input['font_size'])
            ? min(32, max(12, absint($input['font_size'])))
            : $defaults['font_size'];
        $appearance = array(
            'mode' => in_array($mode, array('light', 'dark', 'system'), true) ? $mode : $defaults['mode'],
            'font_family' => isset(self::$font_families[$font_family]) ? $font_family : $defaults['font_family'],
            'font_size' => $font_size,
        );

        $input_colors = isset($input['colors']) && is_array($input['colors']) ? $input['colors'] : array();
        foreach ($defaults['colors'] as $mode => $colors) {
            foreach ($colors as $key => $default) {
                $raw_color = isset($input_colors[$mode][$key]) && is_string($input_colors[$mode][$key]) ? $input_colors[$mode][$key] : '';
                $color = sanitize_hex_color($raw_color);
                $appearance['colors'][$mode][$key] = $color ? $color : $default;
            }
        }

        return $appearance;
    }

    public static function sanitize_general($input)
    {
        $input = is_array($input) ? $input : array();
        $defaults = self::default_general();
        $version = isset($input['consent_version']) && is_string($input['consent_version'])
            ? sanitize_text_field(wp_unslash($input['consent_version']))
            : $defaults['consent_version'];
        $version = preg_replace('/[^a-zA-Z0-9._-]/', '', $version);
        $expiry_days = isset($input['expiry_days']) && is_scalar($input['expiry_days']) && is_numeric($input['expiry_days'])
            ? min(365, max(1, absint($input['expiry_days'])))
            : $defaults['expiry_days'];

        return array(
            'consent_version' => '' !== $version ? $version : $defaults['consent_version'],
            'expiry_days' => $expiry_days,
            'script_handles' => isset($input['script_handles']) && is_string($input['script_handles']) ? self::sanitize_script_handles(wp_unslash($input['script_handles'])) : '',
            'google_consent_mode' => isset($input['google_consent_mode']) && in_array($input['google_consent_mode'], array('1', 1, true), true),
        );
    }

    public static function sanitize_visitor_information($input)
    {
        $input = is_array($input) ? $input : array();
        $defaults = self::default_visitor_information();
        $resources = isset($input['resources']) && is_array($input['resources']) ? $input['resources'] : array();
        $clean_resources = array();

        for ($index = 0; $index < 3; $index++) {
            $resource = isset($resources[$index]) && is_array($resources[$index]) ? $resources[$index] : array();
            $clean_resources[] = array(
                'label' => isset($resource['label']) && is_string($resource['label'])
                    ? sanitize_text_field(wp_unslash($resource['label']))
                    : '',
                'url' => isset($resource['url']) && is_string($resource['url'])
                    ? esc_url_raw(wp_unslash($resource['url']))
                    : '',
            );
        }

        return array(
            'enabled' => isset($input['enabled']) && in_array($input['enabled'], array('1', 1, true), true),
            'heading' => isset($input['heading']) && is_string($input['heading'])
                ? sanitize_text_field(wp_unslash($input['heading']))
                : $defaults['heading'],
            'intro' => isset($input['intro']) && is_string($input['intro'])
                ? sanitize_textarea_field(wp_unslash($input['intro']))
                : $defaults['intro'],
            'resources' => $clean_resources,
            'show_about' => isset($input['show_about']) && in_array($input['show_about'], array('1', 1, true), true),
        );
    }

    private static function sanitize_script_handles($value)
    {
        $valid_categories = OCE_Categories::optional_ids();
        $lines = preg_split('/\r\n|\r|\n/', sanitize_textarea_field($value));
        $handles = array();
        $invalid_lines = array();

        foreach ($lines as $line) {
            if ('' === trim($line)) {
                continue;
            }

            $parts = array_map('trim', explode(':', $line, 2));
            if (2 !== count($parts) || 'openconsent-eu-banner' === $parts[0] || !preg_match('/^[a-zA-Z0-9_-]+$/', $parts[0]) || !in_array($parts[1], $valid_categories, true)) {
                $invalid_lines[] = trim($line);
                continue;
            }

            $handles[] = $parts[0] . ':' . $parts[1];
        }

        if ($invalid_lines && function_exists('add_settings_error')) {
            add_settings_error(
                self::OPTION_GENERAL,
                'invalid_script_handles',
                sprintf(
                    __('Some script mappings were ignored because they are invalid. Use a registered script handle and an optional category (%s).', 'openconsent-eu'),
                    implode(', ', OCE_Categories::optional_ids())
                ),
                'error'
            );
        }

        return implode("\n", array_unique($handles));
    }

    public static function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $tabs = array(
            'content' => __('Banner content', 'openconsent-eu'),
            'categories' => __('Categories', 'openconsent-eu'),
            'appearance' => __('Appearance', 'openconsent-eu'),
            'general' => __('General', 'openconsent-eu'),
            'visitor_information' => __('Visitor information', 'openconsent-eu'),
        );
        $tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'content';
        $active_tab = isset($tabs[$tab]) ? $tab : 'content';
        $content = self::get_content();
        $categories = OCE_Categories::get_all();
        $appearance = self::get_appearance();
        $general = self::get_general();
        $visitor_information = self::get_visitor_information();
        require OCE_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    public static function get_content()
    {
        $saved = get_option(self::OPTION_NAME, array());
        $saved = is_array($saved) ? $saved : array();
        $content = wp_parse_args($saved, self::default_content());

        if (empty($content['policy_url']) && function_exists('get_privacy_policy_url')) {
            $content['policy_url'] = get_privacy_policy_url();
        }

        return $content;
    }

    public static function get_appearance()
    {
        $defaults = self::default_appearance();
        $saved = get_option(self::OPTION_APPEARANCE, array());
        $saved = is_array($saved) ? $saved : array();
        $appearance = wp_parse_args($saved, $defaults);
        $saved_colors = isset($saved['colors']) && is_array($saved['colors']) ? $saved['colors'] : array();

        foreach ($defaults['colors'] as $mode => $colors) {
            $mode_colors = isset($saved_colors[$mode]) && is_array($saved_colors[$mode]) ? $saved_colors[$mode] : array();
            $appearance['colors'][$mode] = wp_parse_args($mode_colors, $colors);
        }

        return $appearance;
    }

    public static function get_general()
    {
        return wp_parse_args(get_option(self::OPTION_GENERAL, array()), self::default_general());
    }

    public static function get_visitor_information()
    {
        $defaults = self::default_visitor_information();
        $saved = get_option(self::OPTION_VISITOR_INFORMATION, array());
        $saved = is_array($saved) ? $saved : array();
        $information = wp_parse_args($saved, $defaults);
        $resources = isset($saved['resources']) && is_array($saved['resources']) ? $saved['resources'] : array();
        $information['resources'] = array();

        for ($index = 0; $index < 3; $index++) {
            $resource = isset($resources[$index]) && is_array($resources[$index])
                ? $resources[$index]
                : $defaults['resources'][$index];
            $information['resources'][] = wp_parse_args($resource, array('label' => '', 'url' => ''));
        }

        return $information;
    }

    public static function font_families()
    {
        return self::$font_families;
    }

    public static function default_content()
    {
        return array(
            'label' => __('Your privacy matters', 'openconsent-eu'),
            'title' => __('Choose your cookie settings', 'openconsent-eu'),
            'message' => __('Choose whether to allow the optional categories configured for this site. Necessary storage is always active. You can change or withdraw your choice at any time.', 'openconsent-eu'),
            'policy_url' => function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '',
        );
    }

    private static function default_appearance()
    {
        return array(
            'mode' => 'light',
            'font_family' => 'sans-serif',
            'font_size' => 16,
            'colors' => array(
                'light' => array('background' => '#ffffff', 'text' => '#18343a', 'accent' => '#137c72', 'border' => '#d5e2df'),
                'dark' => array('background' => '#202a2e', 'text' => '#f3f5f3', 'accent' => '#70c7b7', 'border' => '#49565c'),
            ),
        );
    }

    private static function default_general()
    {
        return array(
            'consent_version' => '1',
            'expiry_days' => 180,
            'script_handles' => '',
            'google_consent_mode' => false,
        );
    }

    private static function default_visitor_information()
    {
        return array(
            'enabled' => true,
            'heading' => __('Learn about your privacy rights', 'openconsent-eu'),
            'intro' => __('This information is provided for general guidance. Your privacy choices on this website are controlled above. For official information about data protection rights in the European Union, visit the links below.', 'openconsent-eu'),
            'resources' => array(
                array(
                    'label' => __('Your data-protection rights in the EU', 'openconsent-eu'),
                    'url' => 'https://commission.europa.eu/law/law-topic/data-protection/information-individuals_en',
                ),
                array(
                    'label' => __('Data protection and online privacy in the EU', 'openconsent-eu'),
                    'url' => 'https://europa.eu/youreurope/citizens/consumers/internet-telecoms/data-protection-online-privacy/index_en.htm',
                ),
                array('label' => '', 'url' => ''),
            ),
            'show_about' => false,
        );
    }

}
