<?php

namespace MPHB\Addons\MailChimp\Containers;

use MPHB\Addons\MailChimp\Services;

class ServicesContainer
{
    protected $listsSyncService = null;
    protected $storeSyncService = null;

    /**
     * @return \MPHB\Addons\MailChimp\Services\ListsSync
     */
    public function listsSync()
    {
        if (is_null($this->listsSyncService)) {
            $this->listsSyncService = new Services\ListsSync();
        }

        return $this->listsSyncService;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Services\StoreSync
     */
    public function storeSync()
    {
        if (is_null($this->storeSyncService)) {
            $this->storeSyncService = new Services\StoreSync();
        }

        return $this->storeSyncService;
    }
}
