<?php

if (!defined('ABSPATH')) {
    exit;
}

final class OCE_Script_Blocker
{
    public static function init()
    {
        add_filter('script_loader_tag', array(__CLASS__, 'defer_configured_script'), 10, 3);
        add_filter('wp_inline_script_attributes', array(__CLASS__, 'defer_inline_script_attributes'));
    }

    public static function defer_configured_script($tag, $handle, $src)
    {
        if (is_admin() || !is_string($tag) || preg_match('/\bdata-oce-consent-category\s*=/i', $tag)) {
            return $tag;
        }

        $handles = self::get_handles();
        if (empty($handles[$handle])) {
            return $tag;
        }

        $category = $handles[$handle];
        $original_type = 'text/javascript';
        if (preg_match('/\stype\s*=\s*(["\'])(.*?)\1/i', $tag, $matches)) {
            $original_type = sanitize_text_field($matches[2]);
        }

        $tag = preg_replace('/\s+type\s*=\s*(["\']).*?\1/i', '', $tag, 1);
        $attributes = ' type="text/plain" data-oce-consent-category="' . esc_attr($category) . '" data-oce-original-type="' . esc_attr($original_type) . '"';

        return preg_replace('/<script\b/i', '<script' . $attributes, $tag, 1);
    }

    public static function defer_inline_script_attributes($attributes)
    {
        if (is_admin() || !is_array($attributes) || empty($attributes['id'])) {
            return $attributes;
        }

        foreach (self::get_handles() as $handle => $category) {
            if (0 !== strpos($attributes['id'], $handle . '-js-')) {
                continue;
            }

            $attributes['data-oce-consent-category'] = $category;
            $attributes['data-oce-original-type'] = isset($attributes['type']) ? $attributes['type'] : 'text/javascript';
            $attributes['type'] = 'text/plain';
            break;
        }

        return $attributes;
    }

    private static function get_handles()
    {
        $general = OCE_Settings::get_general();
        $handles = array();

        foreach (preg_split('/\r\n|\r|\n/', $general['script_handles']) as $line) {
            $parts = array_map('trim', explode(':', $line, 2));
            if (2 === count($parts) && preg_match('/^[a-zA-Z0-9_-]+$/', $parts[0]) && in_array($parts[1], OCE_Categories::optional_ids(), true)) {
                $handles[$parts[0]] = $parts[1];
            }
        }

        return $handles;
    }
}
