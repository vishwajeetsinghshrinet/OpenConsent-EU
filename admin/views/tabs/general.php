<table class="form-table" role="presentation">
    <tr>
        <th scope="row"><label for="oce-expiry-days">
                <?php esc_html_e('Consent validity', 'openconsent-eu'); ?>
            </label></th>
        <td><input id="oce-expiry-days" name="<?php echo esc_attr(OCE_Settings::OPTION_GENERAL); ?>[expiry_days]"
                type="number" min="1" max="365" step="1" value="<?php echo esc_attr($general['expiry_days']); ?>">
            <?php esc_html_e('days', 'openconsent-eu'); ?>
            <p class="description">
                <?php esc_html_e('After this period, visitors are asked for a fresh choice. Choose a period that fits your own privacy information and obligations.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="oce-consent-version">
                <?php esc_html_e('Consent version', 'openconsent-eu'); ?>
            </label></th>
        <td><input class="regular-text" id="oce-consent-version"
                name="<?php echo esc_attr(OCE_Settings::OPTION_GENERAL); ?>[consent_version]" type="text"
                value="<?php echo esc_attr($general['consent_version']); ?>">
            <p class="description">
                <?php esc_html_e('Increase this when purposes or consent wording change to request a fresh choice.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="oce-script-handles">
                <?php esc_html_e('WordPress script handles', 'openconsent-eu'); ?>
            </label></th>
        <td><textarea class="large-text code" id="oce-script-handles"
                name="<?php echo esc_attr(OCE_Settings::OPTION_GENERAL); ?>[script_handles]"
                rows="5"><?php echo esc_textarea($general['script_handles']); ?></textarea>
            <p class="description">
                <?php esc_html_e('One handle:category per line, for example: site_analytics:analytics. Only configure scripts after confirming their purpose. This does not discover or block scripts that are not listed.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e('Google Consent Mode v2', 'openconsent-eu'); ?></th>
        <td><label><input type="checkbox"
                    name="<?php echo esc_attr(OCE_Settings::OPTION_GENERAL); ?>[google_consent_mode]" value="1" <?php checked($general['google_consent_mode']); ?>>
                <?php esc_html_e('Send default denied consent signals before Google tags, then update signals from visitor choices.', 'openconsent-eu'); ?></label>
            <p class="description">
                <?php esc_html_e('Optional and disabled by default. This sends Consent Mode signals only; configure and verify your Google tags separately.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
</table>