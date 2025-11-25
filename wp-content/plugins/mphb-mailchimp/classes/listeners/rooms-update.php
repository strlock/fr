<?php

namespace MPHB\Addons\MailChimp\Listeners;

use MPHB\Addons\MailChimp\Entities;

class RoomsUpdate
{
    public function __construct()
    {
        $this->addActions();
    }

    protected function addActions()
    {
        $roomsPostType = mphb()->postTypes()->roomType()->getPostType();

        // Hooks for post update: https://wordpress.stackexchange.com/a/134667
        add_action("save_post_{$roomsPostType}", [$this, 'updateProduct'], 10, 2);
        add_action('delete_post', [$this, 'removeProduct']);
    }

    /**
     * @param int $postId
     * @param \WP_Post $post
     */
    public function updateProduct($postId, $post)
    {
        // Skip drafts
        if (in_array($post->post_status, ['auto-draft', 'draft', 'pending', 'trash', 'inherit'])) {
            return;
        }

        $storeId = mphbmc()->settings()->getStoreId();
        $product = new Entities\MailchimpProduct($postId);

        // Update product
        mphbmc()->api()->mailchimp()->saveProduct($storeId, $product);

        // Sometimes WordPress triggers "save_post" action twice. Only one update
        // required per single request. One already done, remove others
        remove_action(current_action(), [$this, __FUNCTION__]);
    }

    /**
     * @param int $postId
     */
    public function removeProduct($postId)
    {
        $postType = get_post_type($postId);

        if ($postType === false || $postType != mphb()->postTypes()->roomType()->getPostType()) {
            return;
        }

        $storeId = mphbmc()->settings()->getStoreId();
        $remoteId = (string)$postId;

        $api = mphbmc()->api()->mailchimp();

        if ($api->productExists($storeId, $remoteId)) {
            $api->removeProduct($storeId, $remoteId);
        }
    }
}
