<?php

namespace MPHB\Addons\MailChimp\Repositories;

class Repository
{
    // List synchronization statuses
    const STATUS_SYNCED  = 'synced';
    const STATUS_PENDING = 'pending';
    const STATUS_ANY     = 'any';

    protected $listsTable      = 'mphb_mailchimp_lists';
    protected $categoriesTable = 'mphb_mailchimp_categories';
    protected $groupsTable     = 'mphb_mailchimp_groups';

    public function __construct()
    {
        global $wpdb;

        $this->listsTable      = $wpdb->prefix . $this->listsTable;
        $this->categoriesTable = $wpdb->prefix . $this->categoriesTable;
        $this->groupsTable     = $wpdb->prefix . $this->groupsTable;
    }
}
