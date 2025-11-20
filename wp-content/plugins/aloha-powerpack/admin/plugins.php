<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;

$pluginHelper = AlohaPlugins::getInstance();
$plugins = $pluginHelper->getAllPlugins();
$cantInstallSomePlugins = $pluginHelper->cantInstallSomePlugins();
$errorSvg = '' .
        '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">' .
        '<path d="M7.7 7.7H6.3V3.5H7.7V7.7ZM7.7 10.5H6.3V9.1H7.7V10.5ZM7 0C6.08075 0 5.17049 0.18106 4.32122 0.532843C3.47194 0.884626 2.70026 1.40024 2.05025 2.05025C0.737498 3.36301 0 5.14348 0 7C0 8.85651 0.737498 10.637 2.05025 11.9497C2.70026 12.5998 3.47194 13.1154 4.32122 13.4672C5.17049 13.8189 6.08075 14 7 14C8.85651 14 10.637 13.2625 11.9497 11.9497C13.2625 10.637 14 8.85651 14 7C14 6.08075 13.8189 5.17049 13.4672 4.32122C13.1154 3.47194 12.5998 2.70026 11.9497 2.05025C11.2997 1.40024 10.5281 0.884626 9.67878 0.532843C8.8295 0.18106 7.91925 0 7 0Z" fill="#EB4D4D"/>' .
        '</svg>';

$updateSvg = '<svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 15H12V13.2353H0V15ZM12 5.29412H8.57143V0H3.42857V5.29412H0L6 11.4706L12 5.29412Z" fill="#BAD344"></path></svg>';
$pluginNeedUpdate = $pluginHelper->doPluginsNeedUpdate();
?>

<div class="thmv-body">
    <!-- welcome sec -->
    <div class="thmv-dash-welcome thmv-bundled-plugins-heading">
        <div class="thmv-dash-row">
            <h1 class="thmv-dash-heading">Bundled Plugins</h1>
            <!--            <button class="thmv-addnew-btn">Add new</button>-->
        </div>
    </div>
    <!-- welcome sec -->

    <!-- Registration form -->
    <?php if ($cantInstallSomePlugins) { ?>
        <section class="thmv-dash-bundled-plugins">
            <div class="thmv-dash-row">
                <div class="thmv-dash-bundled-plugins-box">
                    <div class="thmv-bundled-plugins-info">
                        <p class="thmv-dash-paragraph">
                            <b><?php echo __('Please register/renew the theme to install premium bundled plugins.', ALOHA_DOMAIN); ?></b>
                        </p>
                    </div>
                    <!--button>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 2.91L13.09 0L8 5.09L2.91 0L0 2.91L5.09 8L0 13.09L2.91 16L8 10.91L13.09 16L16 13.09L10.91 8L16 2.91Z" fill="#888D94"/>
                        </svg>
                    </button-->
                </div>
            </div>	
        </section>
    <?php } ?>
    <!-- end Registration form -->

    <!-- Getting started  -->
    <div class="thmv-dash-started-welcome">
        <h1 class="thmv-dash-heading">All Plugins</h1>
    </div>
    <?php if ($pluginNeedUpdate): ?><div class="thmv-dash-update-all"><a href="#" id="update_all_plugins">Update All</a></div>
    <?php endif; ?>
    <div class="thmv-dash-all-plugins"  id="plugins-area">
        <div class="thmv-dash-row">

            <div class="thmv-dash-all-plugins-box">
                <?php
                foreach ($plugins as $plug):
                    $requires_update = isset($plug['update']) && $plug['update'] && !$plug['activate'];
                    $deactivateURL = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . urlencode($plug['file_path']) . '&amp;plugin_status=' . 'all' . '&amp;paged=' . 1 . '&amp;s=' . '', 'deactivate-plugin_' . $plug['file_path']);
                    ?>
                    <div class="thmv-column-3 plugin-container <?= isset($plug['cant_install']) ? 'no_install' : '' ?>">
                        <div class="installation-progress"><span class="loading-area"></span><span class="status-area"></span></div>
                        <div class="plugin-holder">
                            <h3 class="thmv-dash-title">
                                <div><?= $plug['name'] ?> <?= $requires_update ? '&nbsp;<a class="update-link" href="#">' . $updateSvg . 'Update</a>' : '' ?> <?= (($plug['install'] or $plug['activate']) && $plug['required']) ? $errorSvg : ''; ?></div> 
                                <label class="thmv-switch">
                                    <input type="checkbox" <?= (isset($plug['uninstall-prompt']) ? 'data-prompt="1" ' : '') ?> <?= (!$plug['install'] && !$plug['activate'] ? 'data-deactivate_url="'.$deactivateURL.'" ' : '') ?> <?= $plug['file_path'] ? 'data-plugin="' . $plug['file_path'] . '"' : '' ?> <?= isset($plug['update']) && $plug['update'] ? 'data-update="1"' : '' ?> <?= $plug['install'] ? 'data-install="1"' : '' ?> <?= $plug['activate'] ? 'data-activate="1"' : '' ?> <?= $plug['required'] ? 'data-required="1"' : '' ?> data-slug="<?= $plug['slug'] ?>" <?= isset($plug['cant_install']) ? 'data-cant-install="' . $plug['cant_install'] . '"' : '' ?> <?= isset($plug['cant_uninstall']) ? 'data-cant-uninstall="' . $plug['cant_uninstall'] . '"' : '' ?> <?= ($plug['install'] || $plug['activate'] ? "" : 'checked') ?>>
                                    <span class="thmv-slider thmv-round"></span>
                                </label>
                            </h3>
                            <p class="thmv-dash-paragraph"><?= isset($plug['plugin_decription'])? $plug['plugin_decription'] : '' ?></p> 
                        </div>

                        <?php if(isset($plug['plugin_doc_link'])):?>
                        <div class="thmv-help-guide">
                            <a href="<?= $plug['plugin_doc_link'] ?>" target="_blank">Documentation</a>
                        </div>
                       <?php endif;?>
                    </div>
                <?php endforeach; ?>
            </div>	

        </div>
    </div>
    <!--end Getting started -->

    <?php
    $showMotoPressSettings = $pluginHelper->showMotoPressSettings();
    $motopressCookieOption = $pluginHelper->getMotoPressCookieOptionValue();
    $motopressReservationPageCacheOption = $pluginHelper->getMotopressReservationPageCacheOptionValue();
    ?>
    <?php if ($showMotoPressSettings): ?>
        <div class="thmv-dash-started-welcome">
            <h2 class="thmv-dash-heading">Misc Settings</h2>
        </div>
        <section class="thmv-dash-all-plugins">
            <div class="thmv-dash-row">
                <div class="thmv-dash-all-plugins-box" id="misc_settings">
                    <div class="thmv-column-3">
                        <h3 class="thmv-dash-title">
                            <div><?php echo __('Disable MotoPress Cookie'); ?></div> 
                            <label class="thmv-switch">
                                <input data-action="aloha_set_motopress_cookie_setting" name="motopress_cookie" type="checkbox" <?php checked($motopressCookieOption, true) ?>>
                                <span class="thmv-slider thmv-round"></span>
                            </label>
                        </h3>
                        <p class="thmv-dash-paragraph">
                            MotoPress cookie can cause caching not to work. (Default: Cookie Enabled)
                        </p> 

                    </div>
                    <div class="thmv-column-3">
                        <h3 class="thmv-dash-title">
                            <div><?php echo __('Disable motopress reservation page cache'); ?></div> 
                            <label class="thmv-switch">
                                <input data-action="aloha_set_motopress_reservation_page_cache_setting" name="motopress_reservation_page_cache" type="checkbox" <?php checked($motopressReservationPageCacheOption, true) ?>>
                                <span class="thmv-slider thmv-round"></span>
                            </label>
                        </h3>
                        <p class="thmv-dash-paragraph">
                            Browsers caching can result in serving a cached version of the reservation page that can result in allowing the user to make multiple reservations and produce inconsistent results. (Default: Cache Enabled)
                        </p> 

                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

</div>
