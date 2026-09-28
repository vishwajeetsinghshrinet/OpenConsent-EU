<?php

if (!defined('ABSPATH')) {
    exit;
}

final class OCE_Consent
{
    public static function get_public_config()
    {
        $general = OCE_Settings::get_general();
        $all_categories = OCE_Categories::get_all();
        $categories = array();

        foreach ($all_categories as $key => $category) {
            if (!$category['enabled']) {
                continue;
            }

            $categories[$key] = array(
                'required' => $category['required'],
                'enabled' => true,
            );
        }

        $configuration = array(
            'categories' => $all_categories,
            'script_handles' => $general['script_handles'],
        );
        $configuration_hash = substr(hash('sha256', wp_json_encode($configuration)), 0, 16);

        return array(
            'consentVersion' => $general['consent_version'] . '-' . $configuration_hash,
            'expiryDays' => min(365, max(1, absint($general['expiry_days']))),
            'googleConsentMode' => !empty($general['google_consent_mode']),
            'categories' => $categories,
            'saveError' => __('Your choice is active for this visit, but could not be saved in this browser.', 'openconsent-eu'),
        );
    }
}
