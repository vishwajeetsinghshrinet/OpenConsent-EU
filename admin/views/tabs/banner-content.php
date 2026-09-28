<table class="form-table" role="presentation">
    <tr>
        <th scope="row"><label for="oce-banner-label"><?php esc_html_e('Small label', 'openconsent-eu'); ?></label></th>
        <td><input class="regular-text" id="oce-banner-label"
                name="<?php echo esc_attr(OCE_Settings::OPTION_NAME); ?>[label]" type="text"
                value="<?php echo esc_attr($content['label']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><label for="oce-banner-title"><?php esc_html_e('Heading', 'openconsent-eu'); ?></label></th>
        <td><input class="regular-text" id="oce-banner-title"
                name="<?php echo esc_attr(OCE_Settings::OPTION_NAME); ?>[title]" type="text"
                value="<?php echo esc_attr($content['title']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><label
                for="oce-banner-message"><?php esc_html_e('Consent message', 'openconsent-eu'); ?></label></th>
        <td><textarea class="large-text" id="oce-banner-message"
                name="<?php echo esc_attr(OCE_Settings::OPTION_NAME); ?>[message]"
                rows="5"><?php echo esc_textarea($content['message']); ?></textarea>
            <p class="description">
                <?php esc_html_e('Explain the optional cookies or technologies used on this site and how visitors can change their choice.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label
                for="oce-policy-url"><?php esc_html_e('Privacy information URL', 'openconsent-eu'); ?></label></th>
        <td><input class="regular-text" id="oce-policy-url"
                name="<?php echo esc_attr(OCE_Settings::OPTION_NAME); ?>[policy_url]" type="url"
                value="<?php echo esc_attr($content['policy_url']); ?>">
            <p class="description">
                <?php esc_html_e('Link to your privacy or cookie information. Leave blank to omit the link.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
</table>