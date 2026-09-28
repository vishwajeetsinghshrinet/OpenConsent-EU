<div class="wrap">
    <div class="oce-admin-heading">
        <img src="<?php echo esc_url(OCE_PLUGIN_URL . 'assets/images/openconsent-eu-icon.png'); ?>" alt="" width="48"
            height="48">
        <div>
            <h1>OpenConsent EU</h1>
            <p><?php esc_html_e('Configure the consent choices and information shown on your site.', 'openconsent-eu'); ?>
            </p>
        </div>
    </div>
    <nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e('Plugin settings', 'openconsent-eu'); ?>">
        <?php foreach ($tabs as $tab_key => $tab_label): ?>
            <a class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>"
                href="<?php echo esc_url(add_query_arg(array('page' => 'openconsent-eu', 'tab' => $tab_key), admin_url('options-general.php'))); ?>"><?php echo esc_html($tab_label); ?></a>
        <?php endforeach; ?>
    </nav>
    <?php settings_errors(); ?>
    <p class="description">
        <?php esc_html_e('Your configuration is saved in this WordPress site. Describe your actual cookies and services; this plugin does not determine legal compliance for you.', 'openconsent-eu'); ?>
    </p>
    <form action="options.php" method="post">
        <?php settings_fields('oce_' . $active_tab . '_settings'); ?>
        <?php
        $view_files = array(
            'content' => 'banner-content.php',
            'categories' => 'categories.php',
            'appearance' => 'appearance.php',
            'general' => 'general.php',
        );
        require OCE_PLUGIN_DIR . 'admin/views/tabs/' . $view_files[$active_tab];
        ?>
        <?php submit_button(__('Save settings', 'openconsent-eu')); ?>
    </form>
</div>