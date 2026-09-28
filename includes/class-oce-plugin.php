<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once OCE_PLUGIN_DIR . 'includes/class-oce-github-updater.php';
require_once OCE_PLUGIN_DIR . 'includes/class-oce-categories.php';
require_once OCE_PLUGIN_DIR . 'includes/class-oce-settings.php';
require_once OCE_PLUGIN_DIR . 'includes/class-oce-banner.php';
require_once OCE_PLUGIN_DIR . 'includes/class-oce-consent.php';
require_once OCE_PLUGIN_DIR . 'includes/class-oce-script-blocker.php';

final class OCE_Plugin
{
    public static function init()
    {
        OpenConsent_EU_GitHub_Updater::init();
        OCE_Settings::init();
        OCE_Banner::init();
        OCE_Script_Blocker::init();
    }
}
