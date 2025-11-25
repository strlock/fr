<?php

namespace MPHB\Addons\MailChimp\API;

use DrewM\MailChimp\MailChimp as MailChimpApiWrapper;

class MailChimpApi extends MailChimpApiWrapper
{
    protected $constructFailed = false;
    protected $constructError = '';
    protected $lastError = false;

    public function __construct($apiKey)
    {
        try {
            /**
             * @throws \Exception cURL not supported or API key is wrong.
             */
            parent::__construct($apiKey);
        } catch (\Exception $e) {
            $this->constructFailed = true;
            $this->constructError = $e->getMessage();
        }
    }

    /**
     * @param \MPHB\Entities\Customer $customer
     * @param array $lists [List remote ID => [Group remote IDs]]
     * @param bool $replaceLists Optional. Replace interests instead of merging
     *     them. TRUE by default.
     * @return string|false Member ID (hash) or FALSE if failed to subscribe.
     *
     * @see https://mailchimp.com/developer/reference/lists/list-members/
     */
    public function subscribeCustomer($customer, $lists, $replaceLists = true)
    {
        $email = $customer->getEmail();
        $memberHash = self::subscriberHash($email);

        $subscribedToLists = [];

        foreach ($lists as $listRemoteId => $remoteIds) {
            // Get the interests to replace
            if ($replaceLists) {
                // [Remote IDs]
                $listInterests = mphbmc()->repository()->mailchimp()->findInterestsByListRemoteId($listRemoteId);
                // [Remote ID => false]
                $replaceInterests = array_combine($listInterests, array_fill(0, count($listInterests), false));
            } else {
                $replaceInterests = [];
            }

            // Get the interests to add
            if (!empty($remoteIds)) {
                // [Remote ID => true]
                $addInterests = array_combine($remoteIds, array_fill(0, count($remoteIds), true));
            } else {
                $addInterests = [];
            }

            // [Remote ID => true|false]
            $interests = array_merge($replaceInterests, $addInterests);

            $subscribeArgs = [
                'email_address' => $email,
                'status'        => mphbmc()->settings()->doubleOptInEnabled() ? 'pending' : 'subscribed',
                'interests'     => $interests,
                'merge_fields'  => [
                    'FNAME' => $customer->getFirstName(),
                    'LNAME' => $customer->getLastName(),
                    'PHONE' => $customer->getPhone()
                ]
            ];

            // Add something?
            $subscribeArgs = apply_filters('mphb_mailchimp_subscribe_args', $subscribeArgs, $customer);

            $this->makeRequest('put', "lists/{$listRemoteId}/members/{$memberHash}", $subscribeArgs);

            if ($this->success()) {
                $subscribedToLists[] = $listRemoteId;
            }
        }

        if (!empty($subscribedToLists)) {
            return $memberHash;
        } else {
            return false;
        }
    }

    /**
     * @return array|false Array of [id, name] or FALSE in case of error.
     *
     * @see https://mailchimp.com/developer/reference/lists/
     */
    public function getLists()
    {
        $response = $this->makeRequest('get', 'lists', [
            'fields' => 'lists.id,lists.name',
            'count'  => apply_filters('mphb_mailchimp_request_lists_count', 100)
        ]);

        if ($response !== false) {
            return $response['lists'];
        } else {
            return false;
        }
    }

    /**
     * @param string $listRemoteId Remote list ID.
     * @return array|false Array of [id, name] or FALSE in case of error.
     *
     * @see https://mailchimp.com/developer/reference/lists/interest-categories/
     */
    public function getCategories($listRemoteId)
    {
        $response = $this->makeRequest('get', "lists/{$listRemoteId}/interest-categories", [
            'fields' => 'categories.id,categories.title',
            'count'  => apply_filters('mphb_mailchimp_request_categories_count', 100)
        ]);

        if ($response !== false) {
            // Replace "title" with "name", like in lists and groups
            $categories = array_map(function ($category) {
                return [
                    'id'   => $category['id'],
                    'name' => $category['title']
                ];
            }, $response['categories']);

            return $categories;
        } else {
            return false;
        }
    }

    /**
     * @param string $listRemoteId Remote list ID.
     * @param string $categoryRemoteId Remote category ID.
     * @return array|false Array of [id, name] or FALSE in case of error.
     *
     * @see https://mailchimp.com/developer/reference/lists/interest-categories/interests/
     */
    public function getGroups($listRemoteId, $categoryRemoteId)
    {
        $response = $this->makeRequest('get', "lists/{$listRemoteId}/interest-categories/{$categoryRemoteId}/interests", [
            'fields' => 'interests.id,interests.name',
            'count'  => apply_filters('mphb_mailchimp_request_groups_count', 100)
        ]);

        if ($response !== false) {
            return $response['interests'];
        } else {
            return false;
        }
    }

    /**
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpStore $store
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/
     */
    public function addStore($store)
    {
        return $this->makeRequest('post', "ecommerce/stores", $store->toArray());
    }

    /**
     * @param string $storeId
     * @return array|false Store fields or FALSE.
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/
     */
    public function getStore($storeId)
    {
        return $this->makeRequest('get', "ecommerce/stores/{$storeId}");
    }

    /**
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpStore $store
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/
     */
    public function updateStore($store)
    {
        return $this->makeRequest('patch', "ecommerce/stores/{$store->id}", $store->toArray());
    }

    /**
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpStore $store
     * @return bool
     */
    public function saveStore($store)
    {
        if (!$this->storeExists($store->id)) {
            return $this->addStore($store);
        } else {
            return $this->updateStore($store);
        }
    }

    /**
     * @param string $storeId
     * @return bool
     */
    public function storeExists($storeId)
    {
        return !empty($this->getStore($storeId));
    }

    /**
     * @param string $storeId
     * @param bool $isSyncing
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/
     */
    public function syncStore($storeId, $isSyncing)
    {
        return $this->makeRequest('patch', "ecommerce/stores/{$storeId}", ['is_syncing' => $isSyncing]);
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpProduct $product
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-products/
     */
    public function addProduct($storeId, $product)
    {
        try {
            return $this->makeRequest('post', "ecommerce/stores/{$storeId}/products", $product->toArray());
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $storeId
     * @param string $productId
     * @return array|false Product fields or FALSE.
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-products/
     */
    public function getProduct($storeId, $productId)
    {
        return $this->makeRequest('get', "ecommerce/stores/{$storeId}/products/{$productId}");
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpProduct $product
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-products/
     */
    public function updateProduct($storeId, $product)
    {
        try {
            return $this->makeRequest('patch', "ecommerce/stores/{$storeId}/products/{$product->id}", $product->toArray());
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpProduct $product
     * @return bool
     */
    public function saveProduct($storeId, $product)
    {
        if (!$this->productExists($storeId, $product->id)) {
            return $this->addProduct($storeId, $product);
        } else {
            return $this->updateProduct($storeId, $product);
        }
    }

    /**
     * @param string $storeId
     * @param string $productId
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-products/
     */
    public function removeProduct($storeId, $productId)
    {
        return $this->makeRequest('delete', "ecommerce/stores/{$storeId}/products/{$productId}");
    }

    /**
     * @param string $storeId
     * @param string $productId
     * @return bool
     */
    public function productExists($storeId, $productId)
    {
        return !empty($this->getProduct($storeId, $productId));
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpCart $cart
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-carts/
     */
    public function addCart($storeId, $cart)
    {
        try {
            return $this->makeRequest('post', "ecommerce/stores/{$storeId}/carts", $cart->toArray());
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $storeId
     * @param string $cartId
     * @return array|false Cart fields or FALSE.
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-carts/
     */
    public function getCart($storeId, $cartId)
    {
        return $this->makeRequest('get', "ecommerce/stores/{$storeId}/carts/{$cartId}");
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpCart $cart
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-carts/
     */
    public function updateCart($storeId, $cart)
    {
        try {
            return $this->makeRequest('patch', "ecommerce/stores/{$storeId}/carts/{$cart->id}", $cart->toArray());
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpCart $cart
     * @return bool
     */
    public function saveCart($storeId, $cart)
    {
        if (!$this->cartExists($storeId, $cart->id)) {
            return $this->addCart($storeId, $cart);
        } else {
            return $this->updateCart($storeId, $cart);
        }
    }

    /**
     * @param string $storeId
     * @param string $cartId
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-carts/
     */
    public function removeCart($storeId, $cartId)
    {
        return $this->makeRequest('delete', "ecommerce/stores/{$storeId}/carts/{$cartId}");
    }

    /**
     * @param string $storeId
     * @param string $cartId
     * @return bool
     */
    public function cartExists($storeId, $cartId)
    {
        return !empty($this->getCart($storeId, $cartId));
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpOrder $order
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-orders/
     */
    public function addOrder($storeId, $order)
    {
        try {
            return $this->makeRequest('post', "ecommerce/stores/{$storeId}/orders", $order->toArray());
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $storeId
     * @param string $orderId
     * @return array|false Order fields or FALSE.
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-orders/
     */
    public function getOrder($storeId, $orderId)
    {
        return $this->makeRequest('get', "ecommerce/stores/{$storeId}/orders/{$orderId}");
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpOrder $order
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-orders/
     */
    public function updateOrder($storeId, $order)
    {
        try {
            return $this->makeRequest('patch', "ecommerce/stores/{$storeId}/orders/{$order->id}", $order->toArray());
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $storeId
     * @param \MPHB\Addons\MailChimp\Entities\MailchimpOrder $order
     * @return bool
     */
    public function saveOrder($storeId, $order)
    {
        if (!$this->orderExists($storeId, $order->id)) {
            return $this->addOrder($storeId, $order);
        } else {
            return $this->updateOrder($storeId, $order);
        }
    }

    /**
     * @param string $storeId
     * @param string $orderId
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-orders/
     */
    public function removeOrder($storeId, $orderId)
    {
        return $this->makeRequest('delete', "ecommerce/stores/{$storeId}/orders/{$orderId}");
    }

    /**
     * @param string $storeId
     * @param string $orderId
     * @param string $lineId
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-orders/ecommerce-order-lines/
     *
     * @since 1.0.2
     */
    public function removeOrderLine($storeId, $orderId, $lineId)
    {
        return $this->makeRequest('delete', "ecommerce/stores/{$storeId}/orders/{$orderId}/lines/{$lineId}");
    }

    /**
     * @param string $storeId
     * @param string $orderId
     * @return bool
     */
    public function orderExists($storeId, $orderId)
    {
        return !empty($this->getOrder($storeId, $orderId));
    }

    /**
     * @param string $storeId
     * @param string $orderId
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/ecommerce-stores/ecommerce-orders/
     */
    public function orderPaid($storeId, $orderId)
    {
        return $this->makeRequest('patch', "ecommerce/stores/{$storeId}/orders/{$orderId}", ['financial_status' => 'paid']);
    }

    /**
     * @param string $listRemoteId
     * @param string $campaignId
     * @return bool
     *
     * @see https://mailchimp.com/developer/reference/campaigns/
     */
    public function isListOfCampaign($listRemoteId, $campaignId)
    {
        $response = $this->makeRequest('get', "campaigns/{$campaignId}", ['fields' => 'recipients.list_id']);

        if ($this->success()) {
            return $listRemoteId == $response['recipients']['list_id'];
        } else {
            return false;
        }
    }

    /**
     * @param string $method post|put|get|patch|delete
     * @param string $endpoint
     * @param array $args Optional.
     * @return mixed Response fields, TRUE or FALSE if request failed.
     */
    protected function makeRequest($method, $endpoint, $args = [])
    {
        if ($this->constructFailed) {
            return false;
        }

        $this->lastError = false;

        $response = $this->$method($endpoint, $args);

        if ($this->success()) {
            return $method == 'get' ? $response : true;
        } else {
            return false;
        }
    }

    /**
     * @return string|false Error message or FALSE if there was no errors.
     */
    public function getLastError()
    {
        if ($this->constructFailed) {
            return $this->constructError;
        } else if ($this->lastError !== false) {
            return $this->lastError;
        } else {
            $lastError = parent::getLastError();

            // Last error is something like "... For field-specific details, see
            // the 'errors' array". So get that error message from response body
            $response = $this->getLastResponse();
            $body = !empty($response['body']) ? json_decode($response['body'], true) : '';

            if (isset($body['errors'])) {
                foreach ($body['errors'] as $error) {
                    if (!array_key_exists('message', $error)) {
                        continue;
                    }

                    $message = rtrim($error['message'], '.'); // Not all messages have "." in the end
                    $field = isset($error['field']) ? $error['field'] : '';

                    if (!empty($field)) {
                        $lastError .= " {$message} ({$field}).";
                    } else {
                        $lastError .= " {$message}.";
                    }
                }

                // Remove notice about details array, already used it
                $lastError = str_replace(" For field-specific details, see the 'errors' array.", '', $lastError);
            }

            return $lastError;
        }
    }
}
