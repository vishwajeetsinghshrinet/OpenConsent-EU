<table class="form-table" role="presentation">
    <tr>
        <th scope="row">
            <?php esc_html_e('EU privacy-rights information', 'openconsent-eu'); ?>
        </th>
        <td><label><input type="checkbox"
                    name="<?php echo esc_attr(OCE_Settings::OPTION_VISITOR_INFORMATION); ?>[enabled]" value="1" <?php checked($visitor_information['enabled']); ?>>
                <?php esc_html_e('Show EU privacy-rights information in the preference dialog', 'openconsent-eu'); ?>
            </label></td>
    </tr>
    <tr>
        <th scope="row"><label for="oce-visitor-heading">
                <?php esc_html_e('Section heading', 'openconsent-eu'); ?>
            </label></th>
        <td><input class="regular-text" id="oce-visitor-heading"
                name="<?php echo esc_attr(OCE_Settings::OPTION_VISITOR_INFORMATION); ?>[heading]" type="text"
                value="<?php echo esc_attr($visitor_information['heading']); ?>"></td>
    </tr>
    <tr>
        <th scope="row"><label for="oce-visitor-intro">
                <?php esc_html_e('Introductory text', 'openconsent-eu'); ?>
            </label></th>
        <td><textarea class="large-text" id="oce-visitor-intro"
                name="<?php echo esc_attr(OCE_Settings::OPTION_VISITOR_INFORMATION); ?>[intro]"
                rows="4"><?php echo esc_textarea($visitor_information['intro']); ?></textarea></td>
    </tr>
    <?php foreach ($visitor_information['resources'] as $index => $resource): ?>
        <tr>
            <th scope="row">
                <?php echo esc_html(sprintf(__('Resource link %d', 'openconsent-eu'), $index + 1)); ?>
            </th>
            <td>
                <label for="oce-visitor-resource-label-<?php echo esc_attr($index); ?>">
                    <?php esc_html_e('Link label', 'openconsent-eu'); ?>
                </label>
                <input class="regular-text" id="oce-visitor-resource-label-<?php echo esc_attr($index); ?>"
                    name="<?php echo esc_attr(OCE_Settings::OPTION_VISITOR_INFORMATION); ?>[resources][<?php echo esc_attr($index); ?>][label]"
                    type="text" value="<?php echo esc_attr($resource['label']); ?>">
                <label for="oce-visitor-resource-url-<?php echo esc_attr($index); ?>">
                    <?php esc_html_e('URL', 'openconsent-eu'); ?>
                </label>
                <input class="large-text" id="oce-visitor-resource-url-<?php echo esc_attr($index); ?>"
                    name="<?php echo esc_attr(OCE_Settings::OPTION_VISITOR_INFORMATION); ?>[resources][<?php echo esc_attr($index); ?>][url]"
                    type="url" value="<?php echo esc_attr($resource['url']); ?>">
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <th scope="row">
            <?php esc_html_e('Project attribution', 'openconsent-eu'); ?>
        </th>
        <td><label><input type="checkbox"
                    name="<?php echo esc_attr(OCE_Settings::OPTION_VISITOR_INFORMATION); ?>[show_about]" value="1" <?php checked($visitor_information['show_about']); ?>>
                <?php esc_html_e('Show a subtle “About OpenConsent EU” link', 'openconsent-eu'); ?>
            </label>
            <p class="description">
                <?php esc_html_e('When enabled, the footer says “Consent controls provided by OpenConsent EU” and links to the project repository.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php esc_html_e('Important information', 'openconsent-eu'); ?>
        </th>
        <td>
            <p class="description">
                <?php esc_html_e('These links are general information only. They do not make your website legally compliant. Check links periodically and use official sources.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
</table>