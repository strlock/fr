<?php

namespace MPHB\Addons\MailChimp\Admin;

use MPHB\Admin\Fields\FieldFactory;
use MPHB\Admin\Groups\SettingsGroup;
use MPHB\Admin\Tabs\SettingsSubTab as SettingsSubtab;

class SettingsTab
{
    public function __construct()
    {
        add_action('mphb_generate_extension_settings', [$this, 'registerSettings']);
        add_action('load-mphb_room_type_page_mphb_settings', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts()
    {
        $settingsPage = mphb()->getSettingsMenuPage();

        if ($settingsPage->detectTab() == 'extensions' && $settingsPage->detectSubTab() == 'mailchimp') {
            mphbmc()->scripts()->enqueueStyle('mphb-mc-admin-css');
        }
    }

    /**
     * @param \MPHB\Admin\Tabs\SettingsTab $tab
     */
    public function registerSettings($tab)
    {
        $subtab = new SettingsSubtab('mailchimp', esc_html__('MailChimp', 'mphb-mailchimp'),
            $tab->getPageName(), $tab->getName());

        $mainGroup = new SettingsGroup('mphb_mc_general', '', $subtab->getOptionGroupName());

        $apiInstructions = __('Enter a valid <a>MailChimp API key</a> here to get started. You will need to have at least one MailChimp list set up.', 'mphb-mailchimp');
        $apiInstructions = wp_kses($apiInstructions, ['a' => []]);
        $apiInstructions = str_replace('<a>', '<a href="https://mailchimp.com/help/about-api-keys/#Find-or-Generate-Your-API-Key" target="_blank">', $apiInstructions);

        $mainFields = [
            'enable_subscriptions' => FieldFactory::create('mphb_mc_enable_subscriptions', [
                'type'           => 'checkbox',
                'inner_label'    => esc_html__('Subscribe users to Mailchimp after booking confirmation', 'mphb-mailchimp'),
                'default'        => true
            ]),
            'api_key' => FieldFactory::create('mphb_mc_api_key', [
                'type'           => 'text',
                'label'          => esc_html__('MailChimp API Key', 'mphb-mailchimp'),
                'description'    => $apiInstructions,
                'default'        => ''
            ]),
            'update_lists' => FieldFactory::create('mphb_mc_update_lists', [
                'type'           => 'action-button',
                'label'          => esc_html__('Update Lists', 'mphb-mailchimp'),
                'inner_label'    => esc_html__('Refresh', 'mphb-mailchimp'),
                'description'    => esc_html__('Get your current MailChimp lists and interests.', 'mphb-mailchimp'),
                'check_interval' => 1000, // 1 second
                'reload_after'   => true,
                'in_progress'    => mphbmc()->service()->listsSync()->isInProgress(),
                'disabled'       => !mphbmc()->settings()->apiKeySet()
            ]),
            'subscribe_to' => FieldFactory::create('mphb_mc_subscribe_to', [
                'type'           => 'mailchimp-lists',
                'label'          => esc_html__('Subscribe New Customers To', 'mphb-mailchimp'),
                'description'    => esc_html__('Select the lists and interests you wish a new customer to be subscribed by default.', 'mphb-mailchimp'),
                'lists'          => mphbmc()->repository()->mailchimp()->getAvailableInterests(),
                'default'        => []
            ])
        ];

        if (!mphb_version_at_least('3.7')) {
            // Current version of Hotel Booking don't have "action-button" field
            // and hooks to add custom fields
            unset($mainFields['update_lists']);
            unset($mainFields['subscribe_to']);

            $mainFields['version_notice'] = FieldFactory::create('version_notice', [
                'type'           => 'placeholder',
                'label'          => esc_html__('Subscribe To', 'mphb-mailchimp'),
                'default'        => esc_html__('Upgrade your version of Hotel Booking to at least 3.7 to manage MailChimp lists.', 'mphb-mailchimp')
            ]);
        }

        $mainGroup->addFields($mainFields);
        $subtab->addGroup($mainGroup);

        $doubleOptInGroup = new SettingsGroup('mphb_mc_double_opt_in', esc_html__('Double Opt-In', 'mphb-mailchimp'), $subtab->getOptionGroupName());

        $doubleOptInFields = [
            'subscription_policy' => FieldFactory::create('mphb_mc_subscription_policy', [
                'type'           => 'radio',
                'label'          => esc_html__('Subscription Policy', 'mphb-mailchimp'),
                'list'           => [
                    'auto'            => esc_html__('Subscribe automatically', 'mphb-mailchimp'),
                    'double_opt_in'   => esc_html__('Enable double opt-in', 'mphb-mailchimp'),
                    'ask_on_checkout' => esc_html__('Ask the customer on the checkout page', 'mphb-mailchimp')
                ],
                'default'        => 'auto'
            ]),
            'checkbox_label' => FieldFactory::create('mphb_mc_checkbox_label', [
                'type'           => 'text',
                'label'          => esc_html__('Checkbox Label on Checkout Page', 'mphb-mailchimp'),
                'default'        => esc_html__('Subscribe to our newsletter', 'mphb-mailchimp')
            ]),
            'checkbox_default' => FieldFactory::create('mphb_mc_checkbox_default', [
                'type'           => 'select',
                'label'          => esc_html__('Checkbox Default', 'mphb-mailchimp'),
                'list'           => [
                    // translators: Context: "The checkbox is checked by default".
                    true  => esc_html__('Checked', 'mphb-mailchimp'),
                    // translators: Context: "The checkbox is unchecked by default".
                    false => esc_html__('Unchecked', 'mphb-mailchimp')
                ],
                'default'        => true
            ])
        ];

        $doubleOptInGroup->addFields($doubleOptInFields);
        $subtab->addGroup($doubleOptInGroup);

        $ecommerceGroup = new SettingsGroup('mphb_mc_ecommerce', esc_html__('E-Commerce Settings', 'mphb-mailchimp'), $subtab->getOptionGroupName());

        $availableLists = mphbmc()->repository()->lists()->getLists(['fields' => 'names']);
        $listsToSelect = array_merge(['' => esc_html__('— Select —', 'mphb-mailchimp')], $availableLists);

        $ecommerceFields = [
            'store_list' => FieldFactory::create('mphb_mc_store_list_id', [
                'type'           => 'tied-mailchimp-list',
                'label'          => esc_html__('Connect To List', 'mphb-mailchimp'),
                'description'    => wp_kses(__('Tie a Store to a specific Mailchimp list/audience. <b>After a Store is tied to a list/audience, it cannot be connected to a different list/audience.</b>', 'mphb-mailchimp'), ['b' => []]),
                'list'           => $listsToSelect,
                'default'        => ''
            ]),
            'subscribe_customers' => FieldFactory::create('mphb_mc_subscribe_ecommerce_customers', [
                'type'           => 'checkbox',
                'inner_label'    => esc_html__('Subscribe e-commerce customers', 'mphb-mailchimp'),
                'description'    => esc_html__('Auto subscribe new customers during e-commerce actions.', 'mphb-mailchimp'),
                'default'        => false
            ]),
            'store_disconnect' => FieldFactory::create('mphb_mc_store_disconnect', [
                'type'           => 'action-button',
                'inner_label'    => esc_html__('Disconnect from current list/audience', 'mphb-mailchimp'),
                'description'    => esc_html__('Untie a Store from the current list/audience. Will not remove any data from MailChimp - Products, Orders or Customers. You\'ll need to remove it manually to completely disconnect your site.', 'mphb-mailchimp'),
                'button_classes' => 'button-primary',
                'reload_after'   => true
            ]),
            'store_sync_status' => FieldFactory::create('mphb_mc_store_sync_status', [
                'type'           => 'placeholder',
                'label'          => esc_html__('E-Commerce Sync Status', 'mphb-mailchimp'),
                'default'        => mphbmc()->settings()->getStoreSyncStatus()
            ]),
            'store_force_sync' => FieldFactory::create('mphb_mc_store_force_sync', [
                'type'           => 'action-button',
                'inner_label'    => esc_html__('Force Sync Now', 'mphb-mailchimp'),
                'check_interval' => 5000, // 5 second
                'reload_after'   => true
            ]),
            'store_force_stop' => FieldFactory::create('mphb_mc_store_force_stop', [
                'type'           => 'action-button',
                'inner_label'    => esc_html__('Cancel Sync', 'mphb-mailchimp'),
                'check_interval' => 2000, // 2 second
                'reload_after'   => true
            ])
        ];

        if (empty(mphbmc()->settings()->getStoreListId())) {
            unset($ecommerceFields['store_disconnect']);
            unset($ecommerceFields['store_force_sync']);
            unset($ecommerceFields['store_force_stop']);
        } else {
            if (mphbmc()->service()->storeSync()->isInProgress()) {
                unset($ecommerceFields['store_force_sync']);
            } else {
                unset($ecommerceFields['store_force_stop']);
            }
        }

        $ecommerceGroup->addFields($ecommerceFields);
        $subtab->addGroup($ecommerceGroup);

        // Add License group
        if (mphb_mc_use_edd_license()) {
            $licenseGroup = new LicenseSettingsGroup('mphb_mc_license', esc_html__('License', 'mphb-mailchimp'), $subtab->getOptionGroupName());
            $subtab->addGroup($licenseGroup);
        }

        $tab->addSubTab($subtab);
    }
}
