<?php

if (!defined('ABSPATH')) {
    exit;
}

final class OCE_Consent
{
    public static function get_public_config()
    {
        $general = OCE_Settings::get_general();
        $categories = array();

        foreach (OCE_Categories::get_all() as $key => $category) {
            if (!$category['enabled']) {
                continue;
            }

            $categories[$key] = array(
                'required' => $category['required'],
                'enabled' => true,
            );
        }

        return array(
            'consentVersion' => $general['consent_version'],
            'expiryDays' => min(365, max(1, absint($general['expiry_days']))),
            'googleConsentMode' => !empty($general['google_consent_mode']),
            'categories' => $categories,
            'saveError' => __('Your choice is active for this visit, but could not be saved in this browser.', 'openconsent-eu'),
        );
    }
}
