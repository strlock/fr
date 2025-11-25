<?php

namespace MPHB\Addons\MailChimp\Entities;

class MailchimpStore
{
    /** @var string */
    public $id = '';

    /** @var string */
    public $listId = '';

    /** @var bool */
    public $isSyncing = false;

    /**
     * @param string $listId
     * @param array $args Optional.
     * @param string $args['id']
     * @param bool $args['is_syncing']
     */
    public function __construct($listId, $args = [])
    {
        $this->id = isset($args['id']) ? $args['id'] : md5(home_url()) . '-' . $listId;
        $this->listId = $listId;

        if (isset($args['is_syncing'])) {
            $this->isSyncing = $args['is_syncing'];
        }
    }

    public function toArray()
    {
        $storeInfo = [
            'id'             => $this->id,
            'list_id'        => $this->listId,
            'name'           => get_bloginfo('name'),
            'platform'       => 'Hotel Booking',
            'domain'         => mphb_current_domain(),
            'is_syncing'     => $this->isSyncing,
            'email_address'  => get_site_option('admin_email'),
            'currency_code'  => mphb()->settings()->currency()->getCurrencyCode(),
            'money_format'   => html_entity_decode(mphb()->settings()->currency()->getCurrencySymbol()),
            'primary_locale' => substr(get_locale(), 0, 2),
            'timezone'       => mphb_mc_get_wp_timezone()
        ];

        return $storeInfo;
    }
}
