<?php

function mphb_mc_tmpl_display_subscription_checkbox()
{
    if (!mphbmc()->settings()->subscriptionsEnabled() || !mphbmc()->settings()->askForSubscription()) {
        return;
    }

    $checked = mphbmc()->settings()->checkboxCheckedByDefault();
    $label = mphbmc()->settings()->getCheckboxLabel();
    ?>
    <section class="mphb-checkout-mailchimp-subscription-wrapper mphb-checkout-section">
        <p class="mphb-mailchimp-subscription">
            <label>
                <input type="checkbox" id="mphb_mc_confirm_subscription" name="mphb_mc_confirm_subscription" value="1" <?php checked($checked); ?> />
                <?php echo esc_html($label); ?>
            </label>
        </p>
    </section>
    <?php
}
