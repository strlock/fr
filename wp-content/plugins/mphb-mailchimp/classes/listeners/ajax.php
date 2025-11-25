<?php

namespace MPHB\Addons\MailChimp\Listeners;

class Ajax
{
    public $nonceName = 'mphb_nonce';

    public $actions = array(
        'mphb_mc_update_lists' => [
            'method' => 'POST',
            'nopriv' => false
        ],
        'mphb_mc_store_force_sync' => [
            'method' => 'POST',
            'nopriv' => false
        ],
        'mphb_mc_store_force_stop' => [
            'method' => 'POST',
            'nopriv' => false
        ],
        'mphb_mc_store_disconnect' => [
            'method' => 'POST',
            'nopriv' => false
        ]
    );

    public function __construct()
    {
        $this->registerActions($this->actions);
    }

    public function registerActions($actions)
    {
        foreach ($actions as $action => $args) {
            add_action("wp_ajax_{$action}", [$this, $action]);

            if ($args['nopriv']) {
                add_action("wp_ajax_nopriv_{$action}", [$this, $action]);
            }
        }
    }

    protected function verifyNonce($action)
    {
        $input = $this->retrieveInput($action);
        $nonce = isset($input[$this->nonceName]) ? $input[$this->nonceName] : '';

        return wp_verify_nonce($nonce, $action);
    }

    protected function checkNonce($action)
    {
        if (!$this->verifyNonce($action)) {
            wp_send_json_error([
                'message' => esc_html__('Request does not pass security verification. Please refresh the page and try one more time.', 'mphb-mailchimp')
            ]);
        }
    }

    protected function retrieveInput($action)
    {
        $method = $this->actions[$action]['method'];

        switch (strtolower($method)) {
            case 'get': return $_GET; break;
            case 'post': return $_POST; break;
            default: return $_REQUEST; break;
        }
    }

    public function mphb_mc_update_lists()
    {
        $this->checkNonce(__FUNCTION__);

        $input = $this->retrieveInput(__FUNCTION__);

        if (!isset($input['iteration'])) {
            wp_send_json_error([
                'message' => esc_html__('Something went wrong in the main plugin: no iteration number passed.', 'mphb-mailchimp')
            ]);
        }

        $iteration = absint($input['iteration']);

        $sync = mphbmc()->service()->listsSync();

        if ($sync->isInProgress()) {
            wp_send_json_success(['inProgress' => true]);
        } else {
            if ($iteration == 1) {
                $sync->startSync();
                wp_send_json_success(['inProgress' => true]);
            } else {
                wp_send_json_success();
            }
        }
    }

    public function mphb_mc_store_force_sync()
    {
        $this->checkNonce(__FUNCTION__);

        $input = $this->retrieveInput(__FUNCTION__);

        if (!isset($input['iteration'])) {
            wp_send_json_error([
                'message' => esc_html__('Something went wrong in the main plugin: no iteration number passed.', 'mphb-mailchimp')
            ]);
        }

        $iteration = absint($input['iteration']);

        $sync = mphbmc()->service()->storeSync();

        if ($sync->isInProgress()) {
            wp_send_json_success(['inProgress' => true, 'message' => mphbmc()->settings()->getStoreSyncStatus()]);
        } else {
            if ($iteration == 1) {
                $sync->startSync();
                wp_send_json_success(['inProgress' => true, 'message' => mphbmc()->settings()->getStoreSyncStatus()]);
            } else {
                wp_send_json_success();
            }
        }
    }

    public function mphb_mc_store_force_stop()
    {
        $this->checkNonce(__FUNCTION__);

        $sync = mphbmc()->service()->storeSync();

        if ($sync->isInProgress() && !$sync->isAborting()) {
            $sync->cancel();
            mphbmc()->settings()->setStoreSyncStatus(esc_html__('Cancelled by user.', 'mphb-mailchimp'));
        }

        wp_send_json_success(['inProgress' => $sync->isInProgress()]);
    }

    public function mphb_mc_store_disconnect()
    {
        global $wpdb;

        $this->checkNonce(__FUNCTION__);

        if (mphbmc()->service()->storeSync()->isInProgress()) {
            mphbmc()->service()->storeSync()->cancel();
        }

        mphbmc()->settings()->setStoreListId('');
        mphbmc()->settings()->setStoreId('');
        mphbmc()->settings()->setStoreSyncStatus(esc_html__('Never synced with MailChimp.', 'mphb-mailchimp'));

        // Remove old tracking fields
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE `meta_key` LIKE '\_mphb\_mc\_track%'");

        wp_send_json_success();
    }
}
