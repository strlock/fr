<?php

namespace MPHB\Addons\MailChimp;

class Settings
{
    /**
     * @return bool
     */
    public function subscriptionsEnabled()
    {
        return (bool)get_option('mphb_mc_enable_subscriptions', true);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return get_option('mphb_mc_api_key', '');
    }

    /**
     * @return bool
     */
    public function apiKeySet()
    {
        return !empty($this->getApiKey());
    }

    /**
     * @return array [List remote ID => [Group remote IDs]]
     */
    public function getInterestsToSubscribe()
    {
        $interests = get_option('mphb_mc_subscribe_to', []);
        return is_array($interests) ? $interests : [];
    }

    /**
     * Replace lists instead of merging them.
     *
     * @return bool
     */
    public function replaceLists()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function doubleOptInEnabled()
    {
        $policy = get_option('mphb_mc_subscription_policy', 'auto');
        return $policy === 'double_opt_in';
    }

    /**
     * @return bool
     */
    public function askForSubscription()
    {
        $policy = get_option('mphb_mc_subscription_policy', 'auto');
        return $policy === 'ask_on_checkout';
    }

    /**
     * @return string
     */
    public function getCheckboxLabel()
    {
        $checkoutLabel = get_option('mphb_mc_checkbox_label', false);

        if ($checkoutLabel !== false) {
            $checkoutLabel = apply_filters('mphb_translate_string', $checkoutLabel, 'mphb_mc_checkbox_label');
        } else {
            $checkoutLabel = esc_html__('Subscribe to our newsletter', 'mphb-mailchimp');
        }

        return $checkoutLabel;
    }

    /**
     * @return bool
     */
    public function checkboxCheckedByDefault()
    {
        return (bool)get_option('mphb_mc_checkbox_default', true);
    }

    /**
     * @return string Store ID or empty string if the store not connected to any
     *     list.
     */
    public function getStoreId()
    {
        return get_option('mphb_mc_store_id', '');
    }

    /**
     * @param string $storeId
     */
    public function setStoreId($storeId)
    {
        // Use autoload for this option. We'll need the value on each run
        update_option('mphb_mc_store_id', $storeId, 'yes');
    }

    /**
     * @return bool
     */
    public function storeSet()
    {
        return !empty($this->getStoreId());
    }

    /**
     * @return string List remote ID or empty string.
     */
    public function getStoreListId()
    {
        return get_option('mphb_mc_store_list_id', '');
    }

    /**
     * @param string $listId
     */
    public function setStoreListId($listId)
    {
        update_option('mphb_mc_store_list_id', $listId, 'no');
    }

    /**
     * @return string Status text. "Never synced", "In progress" etc.
     */
    public function getStoreSyncStatus()
    {
        $defaultStatus = esc_html__('Never synced with MailChimp.', 'mphb-mailchimp');
        return get_option('mphb_mc_store_sync_status', $defaultStatus);
    }

    /**
     * @param string $statusText
     */
    public function setStoreSyncStatus($statusText)
    {
        update_option('mphb_mc_store_sync_status', $statusText, 'no');
    }

    /**
     * @return bool
     */
    public function subscribeEcommerceCustomers()
    {
        return (bool)get_option('mphb_mc_subscribe_ecommerce_customers', false);
    }
}
