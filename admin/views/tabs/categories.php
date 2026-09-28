<table class="form-table" role="presentation">
    <?php foreach ($categories as $key => $category): ?>
        <tr>
            <th scope="row"><label for="oce-category-<?php echo esc_attr($key); ?>">
                    <?php echo esc_html($category['label']); ?>
                </label></th>
            <td>
                <label class="oce-category-toggle">
                    <?php if (!$category['required'] && !empty($category['script_mapped'])): ?>
                        <input type="hidden"
                            name="<?php echo esc_attr(OCE_Settings::OPTION_CATEGORIES); ?>[<?php echo esc_attr($key); ?>][enabled]"
                            value="1">
                    <?php endif; ?>
                    <input type="checkbox"
                        name="<?php echo esc_attr(OCE_Settings::OPTION_CATEGORIES); ?>[<?php echo esc_attr($key); ?>][enabled]"
                        value="1" <?php checked($category['enabled']); ?> <?php disabled($category['required'] || !empty($category['script_mapped'])); ?>>
                    <?php
                    if ($category['required']) {
                        esc_html_e('Always active', 'openconsent-eu');
                    } elseif (!empty($category['script_mapped'])) {
                        esc_html_e('Required by a configured script mapping', 'openconsent-eu');
                    } else {
                        esc_html_e('Offer this category to visitors', 'openconsent-eu');
                    }
                    ?>
                </label>
                <?php if (!empty($category['script_mapped'])): ?>
                    <p class="description">
                        <?php esc_html_e('Remove or reassign its script mappings in General before disabling this category.', 'openconsent-eu'); ?>
                    </p>
                <?php endif; ?>
                <div class="oce-category-fields">
                    <label for="oce-category-<?php echo esc_attr($key); ?>">
                        <?php esc_html_e('Category name', 'openconsent-eu'); ?>
                    </label>
                    <input class="regular-text" id="oce-category-<?php echo esc_attr($key); ?>"
                        name="<?php echo esc_attr(OCE_Settings::OPTION_CATEGORIES); ?>[<?php echo esc_attr($key); ?>][label]"
                        type="text" value="<?php echo esc_attr($category['label']); ?>">
                    <label for="oce-category-description-<?php echo esc_attr($key); ?>">
                        <?php esc_html_e('Describe its purpose', 'openconsent-eu'); ?>
                    </label>
                    <textarea class="large-text" id="oce-category-description-<?php echo esc_attr($key); ?>"
                        name="<?php echo esc_attr(OCE_Settings::OPTION_CATEGORIES); ?>[<?php echo esc_attr($key); ?>][description]"
                        rows="2"><?php echo esc_textarea($category['description']); ?></textarea>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
