<?php

if (!defined('ABSPATH')) {
    exit;
}

final class OCE_Categories
{
    public static function defaults()
    {
        $categories = array(
            'necessary' => array(
                'label' => __('Necessary', 'openconsent-eu'),
                'description' => __('Required for core site functions, security, and remembering your privacy choices. These cannot be switched off here.', 'openconsent-eu'),
                'enabled' => true,
                'required' => true,
            ),
            'preferences' => array(
                'label' => __('Preferences', 'openconsent-eu'),
                'description' => __('Remember choices that personalize how the site looks or behaves.', 'openconsent-eu'),
                'enabled' => true,
                'required' => false,
            ),
            'analytics' => array(
                'label' => __('Analytics', 'openconsent-eu'),
                'description' => __('Help the site owner understand site use and improve the service.', 'openconsent-eu'),
                'enabled' => true,
                'required' => false,
            ),
            'marketing' => array(
                'label' => __('Marketing', 'openconsent-eu'),
                'description' => __('Support advertising measurement, personalization, or remarketing.', 'openconsent-eu'),
                'enabled' => true,
                'required' => false,
            ),
            'other' => array(
                'label' => __('Other', 'openconsent-eu'),
                'description' => __('Other optional technologies described by the site owner.', 'openconsent-eu'),
                'enabled' => true,
                'required' => false,
            ),
        );

        return apply_filters('openconsent_eu_categories', $categories);
    }

    public static function get_all()
    {
        $defaults = self::defaults();
        $saved = get_option(OCE_Settings::OPTION_CATEGORIES, array());
        $saved = is_array($saved) ? $saved : array();

        foreach ($defaults as $key => $category) {
            if (!isset($saved[$key]) || !is_array($saved[$key])) {
                continue;
            }

            $defaults[$key]['label'] = isset($saved[$key]['label']) ? $saved[$key]['label'] : $category['label'];
            $defaults[$key]['description'] = isset($saved[$key]['description']) ? $saved[$key]['description'] : $category['description'];
            $defaults[$key]['enabled'] = $category['required'] || !empty($saved[$key]['enabled']);
        }

        return $defaults;
    }

    public static function optional_ids()
    {
        $optional = array();
        foreach (self::defaults() as $key => $category) {
            if (empty($category['required'])) {
                $optional[] = $key;
            }
        }

        return $optional;
    }
}
