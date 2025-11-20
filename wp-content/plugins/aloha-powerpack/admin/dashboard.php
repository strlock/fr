<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
$mailChimpFieldValue = AlohaMailchimp::getInstance()->getMailChimpFieldValue();
$alreadySubbed = AlohaMailchimp::getInstance()->getMailChimpSubscription();
$systemStatusObj = AlohaSystemStatus::getInstance();
$statusArray = $systemStatusObj->getSystemStatusArray();

$pluginsOjb = AlohaPlugins::getInstance();
$pluginsNeedUpdate = $pluginsOjb->doPluginsNeedUpdate();

$isRegistered = apply_filters('aloha_is_theme_registered', null);
$isPremiumStatusValid = apply_filters('aloha_is_premium_status_valid', false);
$currentThemeVersion = apply_filters('aloha_get_theme_current_version', false);
$remoteThemeVersion = apply_filters('aloha_get_theme_remote_version', false);
$themeUpdateLink = apply_filters('aloha_get_theme_update_link', false);

$installText = __('Watch Video', ALOHA_DOMAIN);
$exclamation_mark = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<path d="M11 11H9V5H11V11ZM11 15H9V13H11V15ZM10 0C8.68678 0 7.38642 0.258658 6.17317 0.761205C4.95991 1.26375 3.85752 2.00035 2.92893 2.92893C1.05357 4.8043 0 7.34784 0 10C0 12.6522 1.05357 15.1957 2.92893 17.0711C3.85752 17.9997 4.95991 18.7362 6.17317 19.2388C7.38642 19.7413 8.68678 20 10 20C12.6522 20 15.1957 18.9464 17.0711 17.0711C18.9464 15.1957 20 12.6522 20 10C20 8.68678 19.7413 7.38642 19.2388 6.17317C18.7362 4.95991 17.9997 3.85752 17.0711 2.92893C16.1425 2.00035 15.0401 1.26375 13.8268 0.761205C12.6136 0.258658 11.3132 0 10 0Z" fill="#EB4D4D"/>'
        . '</svg>';
$updateMark = '<svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<path d="M0 15H12V13.2353H0V15ZM12 5.29412H8.57143V0H3.42857V5.29412H0L6 11.4706L12 5.29412Z" fill="#BAD344"/>'
        . '</svg>';
?>

<div class="thmv-body thmv-body--dashboard">


    <!--welcome sec -->
    <!--div class="thmv-dash-welcome">
        <div class="thmv-dash-row">
            <h1 class="thmv-dash-heading">Welcome</h1>
        </div>
    </div-->

    <!-- welcome sec -->

    <section class="thmv-dash-subscribe">
        <div class="thmv-dash-row">
            <div class="thmv-column-1">
                <?php
                ob_start();
                ?>
                <div class="thmv-dash-subscribe_box">
                    <div class="thmv-dash-col-info">
                        <div class="thmv-heading-big">
                            <?php
                            echo apply_filters('aloha_dashboard_title', ALOHA_ADMIN_ALOHA_TEXT);
                            ?>
                        </div>
                        <h3 class="thmv-dash-title">Join us</h3>
                        <p class="thmv-dash-paragraph">Get ​​exclusive access to beta features. We will also let you know when we release something cool.</p>
                        <form id="thmv_email_form" class="thmv_email_form" action="">
                            <input type="email" required placeholder="Email" value="<?= empty($alreadySubbed) ? $mailChimpFieldValue: '' ?>">
                            <button type="submit">Subscribe</button>
                        </form>
                        <?php if(!empty($alreadySubbed)):?>
                        <small style="font-size: 11px;font-size: 11px;padding-left: 15px;padding-top: 5px;"><?=__('Existing subscription:', ALOHA_DOMAIN);?> <?=$alreadySubbed?></small>
                        <?php endif;?>
                    </div>
                </div>
                <?php
                $subscribe_box = ob_get_clean();
                $subscribe_boxFinal = apply_filters('aloha_dashboard_subscribe_box', $subscribe_box);
                echo $subscribe_boxFinal;
                ?>
            </div>
            <div class="thmv-column-2">
                <?php
                ob_start();
                ?>
                <div class="thmv-free-download">
                    <h3 class="thmv-dash-title">Free resources and <br>downloads</h3>
                    <ul>
                        <li>Curated photos, icons, graphics & fonts</li>
                        <li>Inspirational real world designs</li>
                        <li>Helpful guides & tutorials</li>
                        <li>Custom code snippets</li>
                        <li>Plugin integrations</li>
                    </ul>
                    <button class="thmv-launch-setup">Coming Soon</button>
                </div>
                <?php
                $free_resources = ob_get_clean();
                $free_resourcesFinal = apply_filters('aloha_dashboard_free_resources', $free_resources);
                echo $free_resourcesFinal;
                ?>
            </div>
        </div>
    </section>
    <!-- Getting started  -->
    <div class="thmv-dash-started-welcome">
        <h1 class="thmv-dash-heading">Getting started</h1>
    </div>
    <div class="thmv-dash-started">
        <div class="thmv-dash-row">
            <div class="thmv-dash-started-box">

                <div class="thmv-column-3" id="setup-guide">
                    <?php
                    ob_start();
                    ?>
                    <h3 class="thmv-dash-title"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 8V12" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 16H12.01" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>Setup Guide</h3>
                    <p class="thmv-dash-paragraph">Find out how Aloha works.</p>

                    <a class="thmv-primary-button thickbox"  href="#TB_inline?width=555&height=324&inlineId=videowindow"><?= $installText ?></a>
                    <div id="videowindow" style="display:none;">
                        <div style="text-align: center;"><iframe width="560" height="315" src="<?= ALOHA_SETUP_VIDEO ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                    </div>
                    <div class="thmv-help-guide">
                        <a href="https://help.bellevuetheme.com/article/131-theme-setup" target="_blank">Help guide</a>
                    </div>
                    <?php
                    $setupGuide = ob_get_clean();
                    $setupGuideFinal = apply_filters('aloha_dashboard_setup_guide', $setupGuide);
                    echo $setupGuideFinal;
                    ?>
                </div>



                <div class="thmv-column-3">
                    <?php
                    ob_start();
                    ?>
                    <h3 class="thmv-dash-title"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 8V12" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 16H12.01" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>Easy fixes for the most common roadblocks</h3>
                    <ul class="thmv-dash-listing">
                        <li>Check and update your theme and plugins</li>
                        <li>Test for plugin conflicts</li>
                        <li>Temporarily turn off caching and performance options</li>
                        <li>Temporarily turn off security, maintenance and coming soon plugins</li>
                        <li>Boost your server settings</li>
                    </ul>
                    <div class="thmv-learn-more">
                        <a href="https://help.bellevuetheme.com/article/272-easy-fixes-for-the-most-common-roadblocks" target="_blank">Learn More</a>
                    </div>
                    <?php
                    $easyFixes = ob_get_clean();
                    $easyFixesFinal = apply_filters('aloha_dashboard_easy_fixes', $easyFixes);
                    echo $easyFixesFinal;
                    ?>
                </div>
                <div class="thmv-column-3">
                    <?php
                    ob_start();
                    ?>
                    <h3 class="thmv-dash-title"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23 20.9999V18.9999C22.9993 18.1136 22.7044 17.2527 22.1614 16.5522C21.6184 15.8517 20.8581 15.3515 20 15.1299" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 3.12988C16.8604 3.35018 17.623 3.85058 18.1676 4.55219C18.7122 5.2538 19.0078 6.11671 19.0078 7.00488C19.0078 7.89305 18.7122 8.75596 18.1676 9.45757C17.623 10.1592 16.8604 10.6596 16 10.8799" stroke="#0C1015" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>Feedback &amp; feature request</h3>
                    <p class="thmv-dash-paragraph">We are creating a feature request board where anyone can up and down vote. If you would like to contact us before this is ready, please create a <a href="https://themovation.ticksy.com/" target="_blank">ticket for us.</a></p>
                    <button class="thmv-make-request">Coming soon</button>
                    <?php
                    $feedback_feature_request = ob_get_clean();
                    $feedback_feature_requestFinal = apply_filters('aloha_dashboard_feedback_feature_request', $feedback_feature_request);
                    echo $feedback_feature_requestFinal;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- end Getting started  -->

    <?php
    //registration form section
    echo apply_filters('aloha_theme_registration_form', '');
    ?>
    <!-- thmv-dash-three-box -->
    <section class="thmv-dash-three-box">
        <div class="thmv-dash-row">
            <div class="thmv-dash-three-col">

                <div class="thmv-column-3">
                    <?php
                    ob_start();
                    ?>
                    <div class="thmv-column-3-box thmv-customize-bg">
                        <div class="thmv-dash-three-box-info">
                            <div class="thmv-dash-tabs">
                                <!-- tab-menu -->
                                <input type="radio" id="tab-1" class="tab-1 tabnav" name="tab" >
                                <label for="tab-1" class="thmv-tab-title thmv-tab-first">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                    <path d="M11.3458 0.762695C9.15938 0.762695 7.02211 1.41092 5.20421 2.62541C3.38631 3.8399 1.96943 5.5661 1.13274 7.58571C0.29605 9.60533 0.0771129 11.8277 0.503653 13.9717C0.930193 16.1157 1.98304 18.0851 3.52904 19.6308C5.07504 21.1766 7.04476 22.2293 9.18912 22.6557C11.3335 23.0822 13.5562 22.8633 15.5761 22.0268C17.5961 21.1902 19.3225 19.7736 20.5372 17.956C21.7519 16.1383 22.4003 14.0014 22.4003 11.8154C22.4003 8.88404 21.2356 6.07274 19.1625 3.99996C17.0894 1.92717 14.2776 0.762695 11.3458 0.762695ZM11.3458 4.07851C12.0017 4.07851 12.6429 4.27298 13.1882 4.63732C13.7336 5.00167 14.1587 5.51953 14.4097 6.12542C14.6607 6.7313 14.7263 7.39799 14.5984 8.0412C14.4704 8.6844 14.1546 9.27523 13.6908 9.73895C13.227 10.2027 12.6361 10.5185 11.9928 10.6464C11.3494 10.7744 10.6826 10.7087 10.0766 10.4577C9.47067 10.2068 8.95272 9.78177 8.58832 9.23648C8.22391 8.6912 8.0294 8.05012 8.0294 7.39432C8.0294 6.51491 8.3788 5.67152 9.00074 5.04968C9.62267 4.42785 10.4662 4.07851 11.3458 4.07851ZM11.3458 20.0414C10.0357 20.0354 8.74679 19.7099 7.59098 19.0932C6.43516 18.4764 5.44729 17.5871 4.71305 16.5022C4.76602 14.2917 9.13486 13.075 11.3458 13.075C13.5567 13.075 17.9255 14.2917 17.9784 16.5022C17.2434 17.5862 16.2553 18.475 15.0997 19.0916C13.9441 19.7083 12.6556 20.0342 11.3458 20.0414Z" fill="#E6E6E6"/>
                                    </svg>
                                </label>	
                                <input type="radio" id="tab-2" class="tab-2 tabnav" name="tab" checked="checked">
                                <label for="tab-2" class="thmv-tab-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                    <path d="M11.3458 0.762695C9.15938 0.762695 7.02211 1.41092 5.20421 2.62541C3.38631 3.8399 1.96943 5.5661 1.13274 7.58571C0.29605 9.60533 0.0771129 11.8277 0.503653 13.9717C0.930193 16.1157 1.98304 18.0851 3.52904 19.6308C5.07504 21.1766 7.04476 22.2293 9.18912 22.6557C11.3335 23.0822 13.5562 22.8633 15.5761 22.0268C17.5961 21.1902 19.3225 19.7736 20.5372 17.956C21.7519 16.1383 22.4003 14.0014 22.4003 11.8154C22.4003 8.88404 21.2356 6.07274 19.1625 3.99996C17.0894 1.92717 14.2776 0.762695 11.3458 0.762695ZM11.3458 4.07851C12.0017 4.07851 12.6429 4.27298 13.1882 4.63732C13.7336 5.00167 14.1587 5.51953 14.4097 6.12542C14.6607 6.7313 14.7263 7.39799 14.5984 8.0412C14.4704 8.6844 14.1546 9.27523 13.6908 9.73895C13.227 10.2027 12.6361 10.5185 11.9928 10.6464C11.3494 10.7744 10.6826 10.7087 10.0766 10.4577C9.47067 10.2068 8.95272 9.78177 8.58832 9.23648C8.22391 8.6912 8.0294 8.05012 8.0294 7.39432C8.0294 6.51491 8.3788 5.67152 9.00074 5.04968C9.62267 4.42785 10.4662 4.07851 11.3458 4.07851ZM11.3458 20.0414C10.0357 20.0354 8.74679 19.7099 7.59098 19.0932C6.43516 18.4764 5.44729 17.5871 4.71305 16.5022C4.76602 14.2917 9.13486 13.075 11.3458 13.075C13.5567 13.075 17.9255 14.2917 17.9784 16.5022C17.2434 17.5862 16.2553 18.475 15.0997 19.0916C13.9441 19.7083 12.6556 20.0342 11.3458 20.0414Z" fill="#E6E6E6"/>
                                    </svg>
                                </label>	
                                <input type="radio" id="tab-3" class="tab-3 tabnav" name="tab">
                                <label for="tab-3" class="thmv-tab-title thmv-tab-last">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                    <path d="M11.3458 0.762695C9.15938 0.762695 7.02211 1.41092 5.20421 2.62541C3.38631 3.8399 1.96943 5.5661 1.13274 7.58571C0.29605 9.60533 0.0771129 11.8277 0.503653 13.9717C0.930193 16.1157 1.98304 18.0851 3.52904 19.6308C5.07504 21.1766 7.04476 22.2293 9.18912 22.6557C11.3335 23.0822 13.5562 22.8633 15.5761 22.0268C17.5961 21.1902 19.3225 19.7736 20.5372 17.956C21.7519 16.1383 22.4003 14.0014 22.4003 11.8154C22.4003 8.88404 21.2356 6.07274 19.1625 3.99996C17.0894 1.92717 14.2776 0.762695 11.3458 0.762695ZM11.3458 4.07851C12.0017 4.07851 12.6429 4.27298 13.1882 4.63732C13.7336 5.00167 14.1587 5.51953 14.4097 6.12542C14.6607 6.7313 14.7263 7.39799 14.5984 8.0412C14.4704 8.6844 14.1546 9.27523 13.6908 9.73895C13.227 10.2027 12.6361 10.5185 11.9928 10.6464C11.3494 10.7744 10.6826 10.7087 10.0766 10.4577C9.47067 10.2068 8.95272 9.78177 8.58832 9.23648C8.22391 8.6912 8.0294 8.05012 8.0294 7.39432C8.0294 6.51491 8.3788 5.67152 9.00074 5.04968C9.62267 4.42785 10.4662 4.07851 11.3458 4.07851ZM11.3458 20.0414C10.0357 20.0354 8.74679 19.7099 7.59098 19.0932C6.43516 18.4764 5.44729 17.5871 4.71305 16.5022C4.76602 14.2917 9.13486 13.075 11.3458 13.075C13.5567 13.075 17.9255 14.2917 17.9784 16.5022C17.2434 17.5862 16.2553 18.475 15.0997 19.0916C13.9441 19.7083 12.6556 20.0342 11.3458 20.0414Z" fill="#E6E6E6"/>
                                    </svg>
                                </label>
                                <!-- tab-content -->
                                <div class="tab-content">
                                    <section class="tab-item-1">
                                        <h3 class="thmv-dash-title">Need to customize?</h3>
                                        <p class="thmv-dash-paragraph">Take it to the next level.</p>
                                    </section>
                                    <section class="tab-item-2">
                                        <h3 class="thmv-dash-title">Need to customize?</h3>
                                        <p class="thmv-dash-paragraph">Take it to the next level.</p>
                                    </section>
                                    <section class="tab-item-3">
                                        <h3 class="thmv-dash-title">Need to customize?</h3>
                                        <p class="thmv-dash-paragraph">Take it to the next level.</p>
                                    </section>
                                </div>
                            </div>
                            <button class="thmv-launch-setup">Coming Soon</button>
                        </div>
                    </div>
                    <?php
                    $need_to_customize = ob_get_clean();
                    $need_to_customizeFinal = apply_filters('aloha_dashboard_need_to_customize', $need_to_customize);
                    echo $need_to_customizeFinal;
                    ?>
                </div>
                <div class="thmv-column-3">
                    <?php
                    ob_start();
                    ?>
                    <div class="thmv-column-3-box thmv-integrations-bg">
                        <div class="thmv-dash-three-box-info">
                            <img src="<?php echo ALOHA_ADMIN_IMAGES_DIR_URL ?>/integrations-img.png" alt="">
                            <h3 class="thmv-dash-title">Need integrations?</h3>
                            <p class="thmv-dash-paragraph">See our recommended addons and extensions</p>
                            <button class="thmv-launch-setup">Coming Soon</button>
                        </div>
                    </div>
                    <?php
                    $need_to_integrate = ob_get_clean();
                    $need_to_integrateFinal = apply_filters('aloha_dashboard_need_to_integrate', $need_to_integrate);
                    echo $need_to_integrateFinal;
                    ?>
                </div>
                <div class="thmv-column-3">
                    <?php
                    ob_start();
                    ?>
                    <div class="thmv-column-3-box thmv-license-bg">
                        <div class="thmv-dash-three-box-info">
                            <img src="<?php echo ALOHA_ADMIN_IMAGES_DIR_URL ?>/license-img.png" alt="">
                            <h3 class="thmv-dash-title">Need another license? </h3>
                            <p class="thmv-dash-paragraph">You can purchase another license here.</p>
                            <form action="https://themeforest.net/item/bellevue-hotel-bed-breakfast-booking-theme/12482898" target="_blank"><button type="submit" class="thmv-launch-setup">Buy</button></form>
                        </div>
                    </div>
                    <?php
                    $need_another_license = ob_get_clean();
                    $need_another_licenseFinal = apply_filters('aloha_dashboard_need_another_license', $need_another_license);
                    echo $need_another_licenseFinal;
                    ?>
                </div>
            </div>
        </div>		
    </section>
    <!-- end thmv-dash-three-box -->

    <!-- welcome sec -->
    <section class="thmv-dash-health">
        <div class="thmv-dash-row">

            <?php
            //if a themovation theme is installed:
            $system_status_col_class = 'thmv-column-1';
            if (is_themovation_template() && !is_null($isRegistered)) {
                $system_status_col_class = 'thmv-column-2';
                ?>
                <div class="thmv-column-1">
                    <h1 class="thmv-dash-heading">Theme Health</h1>
                    <div class="thmv-dash-health-box">
                        <div class="thmv-dash-col-registered">
                            <h3><?php
                                if ($isRegistered): echo 'Registered';
                                else:
                                    ?>Not Registered <?= $exclamation_mark ?>
                                <?php endif; ?>
                            </h3>

                            <ul id="update-area">
                                <li><b>Theme version number:</b></li>
                                <li id="version-number">v<?= $currentThemeVersion ?></li>
                                <?php if ($currentThemeVersion < $remoteThemeVersion && current_user_can('update_themes')): ?>
                                    <li><a id="update-template-link" data-hide-on-success="#update-area svg" data-version-area="#version-number" target="_blank" href="<?= $themeUpdateLink ?>"><?= $updateMark ?>Update</a> <?= $exclamation_mark ?></li>
                                <?php endif; ?>
                            </ul>
                            <?php
                            //if debug, show version history
                            if (aloha_debug_mode() && function_exists('thmv_get_theme_version_history')) {
                                $version_history = thmv_get_theme_version_history();
                                
                                if (count($version_history)) {
                                    echo '<ul><li><b>'.__('Version history').': </b>';
                                    echo implode(', ', $version_history);
                                    echo '</li></ul>';
                                }
                                
                            }
                            ?>
                            <?php
                            if (current_user_can('install_plugins')):
                                $updateMessage = 'Plugins are up-to-date';
                                if ($pluginsNeedUpdate) {
                                    $updateMessage = 'Plugins need updating';
                                }
                                ?>
                                <ul>
                                    <li><b><?= $updateMessage ?></b></li>
                                    <li></li>
                                    <?php if ($pluginsNeedUpdate): ?>
                                        <li><a href="<?= admin_url('admin.php?page=aloha_plugins') ?>"><?= $updateMark ?>Update</a> <?= $exclamation_mark ?></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="thmv-dash-col-premium">
                            <?php
                            $premiumStatus = $isPremiumStatusValid ? 'Active' : "Expired";
                            ?>
                            <h3>Premium support status:&nbsp;<span class="color--<?= $isPremiumStatusValid ? 'success' : 'error' ?>"><?= $premiumStatus ?></span></h3>
                            <div class="thmv-renew">
                                <form action="https://link.bellevuetheme.com/dash-renew" target="_blank" style="display: inline"><button type="submit" class="thmv-launch-setup">Renew</button></form>
                                <a href="https://help.bellevuetheme.com/article/157-extending-and-renewing-theme-support" target="_blank">How to renew</a>
                            </div>
                        </div>
                    </div>	
                </div>
                <?php
            }
            ?>
            <div class="<?php echo $system_status_col_class; ?>">
                <h1 class="thmv-dash-heading">System status</h1>
                <div class="thmv-system-status">
                    <ul>
                        <?php
                        if (is_array($statusArray)):
                            foreach ($statusArray as $statusKey => $statusVar):
                                ?>
                                <li>
                                    <div class="thmv-version-title"><?= ($statusVar['passed'] === false) ? '<span class="alert-icon">'.$exclamation_mark.'</span>': ""?><?= $statusKey ?>:</div>
                                    <div class="thmv-version-info">
                                        <?php
                                        if ($statusVar['passed'] === false): echo '<span class="error-value">';
                                        endif;
                                        ?>
                                        <?= $statusVar['current'] ?>
                                        <?php
                                        if ($statusVar['passed'] === false): echo '</span>';
                                        endif;
                                        ?>
                                        <?php
                                        if (isset($statusVar['recommended'])):
                                            ?>
                                            (recommended <span class="recommended"><?= $statusVar['recommended'] ?></span>)
                                            <?php
                                        endif;
                                        ?>
                                        <?php
                                        if ($statusVar['passed'] === false && isset($statusVar['help'])):
                                            ?>
                                            <?php
                                            echo '<span class="help">'.$statusVar['help'].'</span>';
                                        endif;
                                        ?>
                                    </div>
                                </li>
                                <?php
                            endforeach;
                        endif;
                        ?>

                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- welcome sec -->


    <!-- Modal content for Generate Envato Token Info -->
    <!--    <div id="envatoModal" class="th-modal">
            <div class="th-modal-content">
                <span class="th-close">&times;</span>
                <div class="th-modal-header">
                    <h2><?php esc_html_e('Create an Envato API Token', 'bellevue'); ?></h2>
                </div>
                <div class="th-modal-body">
    <?php if (is_plugin_active('envato-market/envato-market.php')) : ?>
                                                                            <p><?php printf(esc_html__('Sign into your %s before you proceed.', 'bellevue'), '<a href="https://account.envato.com/sign_in?to=envato-api" target="_blank">' . esc_html__('Envato Account', 'bellevue') . '</a>'); ?></p>
                                                                            <ol>
                                                                                <li><?php printf(esc_html__('%s to create a Token.', 'bellevue'), '<a href="' . envato_market()->admin()->get_generate_token_url() . '" target="_blank">' . esc_html__('Click here', 'bellevue') . '</a>'); ?></li>
                                                                                <li><?php esc_html_e('Give it a name eg: “My WordPress site”.', 'bellevue'); ?></li>
                                                                                <li><?php esc_html_e('Check the following:', 'bellevue'); ?>
                                                                                    <ul>
                                                                                        <li><?php esc_html_e('"View and search Envato sites"', 'bellevue'); ?></li>
                                                                                        <li><?php esc_html_e('"Download your purchased items"', 'bellevue'); ?></li>
                                                                                        <li><?php esc_html_e('"List purchases you\'ve made"', 'bellevue'); ?></li>
                                                                                        <li><?php printf(esc_html__('%s', 'bellevue'), '<a href="https://share.getcloudapp.com/ApuLgvvX" target="_blank">' . esc_html__('Sample', 'bellevue') . '</a></strong>'); ?></li>
                                                                                    </ul>
                                                                                </li>
                                                                                <li><?php esc_html_e('Create the token, then copy and paste the token into the box on the theme activation screen.', 'bellevue'); ?></li>
                                                                                <li><?php esc_html_e('Click the "Save" button, and your purchase code(s) will appear.', 'bellevue'); ?></li>
                                                                                <li><?php esc_html_e('Select one and click "Register".', 'bellevue'); ?></li>
                                                                            </ol>
                                                                            <p><?php printf(esc_html__('%s', 'bellevue'), '<a href="https://help.bellevuetheme.com/article/289-how-do-i-activate-the-theme" target="_blank">' . esc_html__('Need help?', 'bellevue') . '</a></p>'); ?>
    <?php else : ?>
                                                                            <h2><?php esc_html_e('Envato Market Plugin not installed or active?', 'bellevue'); ?></h2>
                                                                            <p><?php esc_html_e('Please close this window and Activate the Envato Market Plugin', 'bellevue'); ?></p>
    <?php endif; ?>
                </div>
            </div>
        </div>
    
         Modal content for Purchase code Info 
        <div id="codeModal" class="th-modal">
            <div class="th-modal-content">
                <span class="th-close">&times;</span>
                <div class="th-modal-header">
                    <h2>Get your purchase code</h2>
                </div>
                <div class="th-modal-body">
                    <p><?php esc_html_e('To get your Envato Purchase Code, follow the steps below:', 'bellevue'); ?></p>
                    <ol>
                        <li><?php printf(esc_html__('Login to the %s', 'bellevue'), '<a href="https://themeforest.net" target="_blank">' . esc_html__('Envato Marketplace', 'bellevue') . '</a></strong>'); ?></li>
                        <li><?php esc_html_e('Once logged in, move your mouse over your username on the top right', 'bellevue'); ?></li>
                        <li><?php printf(esc_html__('Click on the menu item "%s"', 'bellevue'), '<a href="https://themeforest.net/downloads" target="_blank">' . esc_html__('Downloads', 'bellevue') . '</a></strong>'); ?></li>
                        <li><?php esc_html_e('Locate the product, and then click on “Download”', 'bellevue'); ?></li>
                        <li><?php esc_html_e('Click on “Licence certificate & purchase code” to download the text file', 'bellevue'); ?></li>
                        <li><?php esc_html_e('Open it and locate the “Item Purchase Code”', 'bellevue'); ?></li>
                        <li><?php esc_html_e('Copy and paste the purchase code into the box on the theme activation screen.', 'bellevue'); ?></li>
                    </ol>
                    <p><?php printf(esc_html__('%s', 'bellevue'), '<a href="https://help.bellevuetheme.com/article/174-how-to-find-your-envato-purchase-code" target="_blank">' . esc_html__('Need help?', 'bellevue') . '</a></p>'); ?>
                </div>
            </div>
        </div>-->

</div>