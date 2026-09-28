<table class="form-table" role="presentation">
    <tr>
        <th scope="row"><label for="oce-appearance-mode">
                <?php esc_html_e('Color mode', 'openconsent-eu'); ?>
            </label></th>
        <td><select id="oce-appearance-mode" name="<?php echo esc_attr(OCE_Settings::OPTION_APPEARANCE); ?>[mode]">
                <option value="light" <?php selected($appearance['mode'], 'light'); ?>>
                    <?php esc_html_e('Light', 'openconsent-eu'); ?>
                </option>
                <option value="dark" <?php selected($appearance['mode'], 'dark'); ?>>
                    <?php esc_html_e('Dark', 'openconsent-eu'); ?>
                </option>
                <option value="system" <?php selected($appearance['mode'], 'system'); ?>>
                    <?php esc_html_e('Follow visitor device', 'openconsent-eu'); ?>
                </option>
            </select></td>
    </tr>
    <tr>
        <th scope="row"><label for="oce-font-family">
                <?php esc_html_e('Font family', 'openconsent-eu'); ?>
            </label></th>
        <td><select id="oce-font-family" name="<?php echo esc_attr(OCE_Settings::OPTION_APPEARANCE); ?>[font_family]">
                <?php foreach (OCE_Settings::font_families() as $font_key => $font_stack): ?>
                    <option value="<?php echo esc_attr($font_key); ?>" <?php selected($appearance['font_family'], $font_key); ?>>
                        <?php echo esc_html(ucfirst($font_key)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                <?php esc_html_e('The default and final fallback is sans-serif.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="oce-font-size">
                <?php esc_html_e('Base font size', 'openconsent-eu'); ?>
            </label></th>
        <td><input id="oce-font-size" name="<?php echo esc_attr(OCE_Settings::OPTION_APPEARANCE); ?>[font_size]"
                type="number" min="12" max="32" step="1" value="<?php echo esc_attr($appearance['font_size']); ?>"> px
            <p class="description">
                <?php esc_html_e('Choose a size from 12 px to 32 px.', 'openconsent-eu'); ?>
            </p>
        </td>
    </tr>
    <?php foreach (array('light' => __('Light palette', 'openconsent-eu'), 'dark' => __('Dark palette', 'openconsent-eu')) as $mode => $mode_label): ?>
        <tr>
            <th scope="row">
                <?php echo esc_html($mode_label); ?>
            </th>
            <td>
                <div class="oce-color-grid">
                    <?php foreach (array('background' => __('Background', 'openconsent-eu'), 'text' => __('Text', 'openconsent-eu'), 'accent' => __('Accent', 'openconsent-eu'), 'border' => __('Border', 'openconsent-eu')) as $color_key => $color_label): ?>
                        <label for="oce-<?php echo esc_attr($mode . '-' . $color_key); ?>">
                            <?php echo esc_html($color_label); ?><input
                                id="oce-<?php echo esc_attr($mode . '-' . $color_key); ?>"
                                name="<?php echo esc_attr(OCE_Settings::OPTION_APPEARANCE); ?>[colors][<?php echo esc_attr($mode); ?>][<?php echo esc_attr($color_key); ?>]"
                                type="color" value="<?php echo esc_attr($appearance['colors'][$mode][$color_key]); ?>">
                        </label>
                    <?php endforeach; ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>