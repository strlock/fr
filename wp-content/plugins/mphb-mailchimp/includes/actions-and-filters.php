<?php

if (!defined('ABSPATH')) {
    exit;
}

add_filter('mphb_create_mailchimp_lists_field', function ($instance, $name, $args, $value) {
    return new \MPHB\Addons\MailChimp\Admin\MailchimpListsField($name, $args, $value);
}, 10, 4);

add_filter('mphb_create_tied_mailchimp_list_field', function ($instance, $name, $args, $value) {
    return new \MPHB\Addons\MailChimp\Admin\TiedMailchimpListField($name, $args, $value);
}, 10, 4);

// Just after the Terms & Conditions checkbox (priority 60)
add_action('mphb_sc_checkout_form', 'mphb_mc_tmpl_display_subscription_checkbox', 70);
add_action('mphb_cb_checkout_form', 'mphb_mc_tmpl_display_subscription_checkbox', 70);

add_action('update_option_mphb_mc_store_list_id', function ($oldValue, $newValue) {
    if (empty($oldValue) && !empty($newValue)) {
        $sync = mphbmc()->service()->storeSync();

        if (!$sync->isInProgress()) {
            $sync->startSync();
        }
    }
}, 10, 2);
