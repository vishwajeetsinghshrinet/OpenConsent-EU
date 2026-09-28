<?php

if (!defined('ABSPATH')) {
    exit;
}

final class OCE_Banner
{
    public static function init()
    {
        add_action('wp_head', array(__CLASS__, 'output_google_consent_defaults'), 1);
        add_action('wp_head', array(__CLASS__, 'output_early_consent_state'), 2);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_styles'));
        add_action('wp_footer', array(__CLASS__, 'render'));
    }

    public static function output_google_consent_defaults()
    {
        if (is_admin() || empty(OCE_Settings::get_general()['google_consent_mode'])) {
            return;
        }

        $script = "window.dataLayer=window.dataLayer||[];window.gtag=window.gtag||function(){window.dataLayer.push(arguments);};window.gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',functionality_storage:'denied',personalization_storage:'denied',security_storage:'granted'});";
        wp_print_inline_script_tag($script, array('id' => 'openconsent-eu-consent-default'));
    }

    public static function output_early_consent_state()
    {
        if (is_admin()) {
            return;
        }

        $consent_version = wp_json_encode(OCE_Consent::get_public_config()['consentVersion']);
        $script = "try{var c=JSON.parse(localStorage.getItem('openconsent-eu-consent')||'null');if(c&&c.version===$consent_version&&Number.isFinite(c.expiresAt)&&c.expiresAt>Date.now()&&c.categories&&typeof c.categories==='object'){document.documentElement.classList.add('oce-consent-recorded');}}catch(e){}";
        wp_print_inline_script_tag($script, array('id' => 'openconsent-eu-early-state'));
    }

    public static function enqueue_styles()
    {
        $appearance = OCE_Settings::get_appearance();
        $fonts = OCE_Settings::font_families();
        $font = isset($fonts[$appearance['font_family']]) ? $fonts[$appearance['font_family']] : 'sans-serif';
        $colors = $appearance['colors'];
        $variables = sprintf(
            '--oce-font-family:%s;--oce-font-size:%dpx;--oce-light-bg:%s;--oce-light-text:%s;--oce-light-accent:%s;--oce-light-border:%s;--oce-dark-bg:%s;--oce-dark-text:%s;--oce-dark-accent:%s;--oce-dark-border:%s',
            $font,
            min(32, max(12, absint($appearance['font_size']))),
            sanitize_hex_color($colors['light']['background']),
            sanitize_hex_color($colors['light']['text']),
            sanitize_hex_color($colors['light']['accent']),
            sanitize_hex_color($colors['light']['border']),
            sanitize_hex_color($colors['dark']['background']),
            sanitize_hex_color($colors['dark']['text']),
            sanitize_hex_color($colors['dark']['accent']),
            sanitize_hex_color($colors['dark']['border'])
        );

        wp_enqueue_style(
            'openconsent-eu-banner',
            plugins_url('assets/css/banner.css', OCE_PLUGIN_FILE),
            array(),
            OCE_VERSION
        );
        wp_add_inline_style('openconsent-eu-banner', ':root{' . $variables . '}');
        wp_enqueue_script('openconsent-eu-banner', plugins_url('assets/js/banner.js', OCE_PLUGIN_FILE), array(), OCE_VERSION, true);
        wp_localize_script('openconsent-eu-banner', 'OCEConsentConfig', OCE_Consent::get_public_config());
    }

    public static function render()
    {
        $content = OCE_Settings::get_content();
        $categories = OCE_Categories::get_all();
        $visitor_information = OCE_Settings::get_visitor_information();
        $appearance = OCE_Settings::get_appearance();
        $policy_url = !empty($content['policy_url']) ? esc_url($content['policy_url']) : '';
        ?>
        <div class="oce-widget" id="oce-widget" data-theme="<?php echo esc_attr($appearance['mode']); ?>">
            <section class="oce-banner" id="oce-banner" aria-labelledby="oce-banner-title">
                <p class="oce-eyebrow"><span class="oce-indicator"
                        aria-hidden="true"></span><?php echo esc_html($content['label']); ?></p>
                <h2 class="oce-title" id="oce-banner-title"><?php echo esc_html($content['title']); ?></h2>
                <div class="oce-copy"><?php echo wpautop(esc_html($content['message'])); ?></div>
                <?php if ($policy_url): ?>
                    <a class="oce-policy-link" href="<?php echo $policy_url; ?>" target="_blank"
                        rel="noopener noreferrer"><?php esc_html_e('Read our privacy information', 'openconsent-eu'); ?></a>
                <?php endif; ?>
                <div class="oce-actions">
                    <button class="oce-button oce-button--primary" type="button"
                        data-oce-accept><?php esc_html_e('Accept all', 'openconsent-eu'); ?></button>
                    <button class="oce-button" type="button"
                        data-oce-reject><?php esc_html_e('Reject optional', 'openconsent-eu'); ?></button>
                    <button class="oce-button oce-button--text" type="button"
                        data-oce-customize><?php esc_html_e('Choose settings', 'openconsent-eu'); ?></button>
                </div>
                <p class="oce-error" data-oce-save-error role="status" aria-live="polite" hidden></p>
            </section>
            <button class="oce-settings-link" id="oce-settings-link" type="button"
                hidden><?php esc_html_e('Cookie settings', 'openconsent-eu'); ?></button>
            <dialog class="oce-dialog" id="oce-dialog" aria-labelledby="oce-dialog-title">
                <div class="oce-dialog__inner">
                    <div class="oce-dialog__header">
                        <div>
                            <p class="oce-eyebrow"><?php esc_html_e('Your choices', 'openconsent-eu'); ?></p>
                            <h2 class="oce-title" id="oce-dialog-title">
                                <?php esc_html_e('Cookie preferences', 'openconsent-eu'); ?>
                            </h2>
                        </div>
                        <button class="oce-close" type="button"
                            data-oce-close><?php esc_html_e('Close', 'openconsent-eu'); ?></button>
                    </div>
                    <p class="oce-copy">
                        <?php esc_html_e('Choose which optional categories this site may use. You can change your selection later using Cookie settings.', 'openconsent-eu'); ?>
                    </p>
                    <div class="oce-category-list">
                        <?php foreach ($categories as $key => $category): ?>
                            <?php if (!$category['enabled']) {
                                continue;
                            } ?>
                            <div class="oce-category">
                                <label class="oce-category__heading" for="oce-choice-<?php echo esc_attr($key); ?>">
                                    <input id="oce-choice-<?php echo esc_attr($key); ?>" type="checkbox"
                                        data-oce-category-input="<?php echo esc_attr($key); ?>" <?php checked($category['required']); ?>             <?php disabled($category['required']); ?>>
                                    <span><?php echo esc_html($category['label']); ?><?php if ($category['required']): ?> <span
                                                class="oce-required"><?php esc_html_e('Always active', 'openconsent-eu'); ?></span><?php endif; ?></span>
                                </label>
                                <p><?php echo esc_html($category['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($visitor_information['enabled']): ?>
                        <details class="oce-privacy-info">
                            <summary><?php echo esc_html($visitor_information['heading']); ?></summary>
                            <div class="oce-privacy-info__body">
                                <p><?php echo esc_html($visitor_information['intro']); ?></p>
                                <ul>
                                    <?php foreach ($visitor_information['resources'] as $resource): ?>
                                        <?php
                                        $resource_url = isset($resource['url']) && is_string($resource['url']) ? esc_url($resource['url']) : '';
                                        $resource_label = isset($resource['label']) && is_string($resource['label']) ? $resource['label'] : '';
                                        if (!$resource_url || '' === trim($resource_label)) {
                                            continue;
                                        }
                                        ?>
                                        <li><a href="<?php echo $resource_url; ?>" target="_blank"
                                                rel="noopener noreferrer"><?php echo esc_html($resource_label); ?><span
                                                    class="oce-screen-reader-text"><?php esc_html_e(' (opens in a new tab)', 'openconsent-eu'); ?></span></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </details>
                    <?php endif; ?>
                    <div class="oce-actions oce-dialog__actions">
                        <button class="oce-button oce-button--primary" type="button"
                            data-oce-save><?php esc_html_e('Save my choices', 'openconsent-eu'); ?></button>
                        <button class="oce-button" type="button"
                            data-oce-reject><?php esc_html_e('Reject optional', 'openconsent-eu'); ?></button>
                        <button class="oce-button" type="button"
                            data-oce-accept><?php esc_html_e('Accept all', 'openconsent-eu'); ?></button>
                    </div>
                    <button class="oce-withdraw" type="button"
                        data-oce-withdraw><?php esc_html_e('Withdraw optional consent', 'openconsent-eu'); ?></button>
                    <?php if ($visitor_information['show_about']): ?>
                        <p class="oce-attribution">
                            <?php esc_html_e('Consent controls provided by OpenConsent EU', 'openconsent-eu'); ?> <a
                                href="https://github.com/vishwajeetsinghshrinet/OpenConsent-EU" target="_blank"
                                rel="noopener noreferrer"><?php esc_html_e('About OpenConsent EU', 'openconsent-eu'); ?><span
                                    class="oce-screen-reader-text"><?php esc_html_e(' (opens in a new tab)', 'openconsent-eu'); ?></span></a>
                        </p>
                    <?php endif; ?>
                    <p class="oce-error" data-oce-save-error role="status" aria-live="polite" hidden></p>
                </div>
            </dialog>
        </div>
        <?php
    }
}
