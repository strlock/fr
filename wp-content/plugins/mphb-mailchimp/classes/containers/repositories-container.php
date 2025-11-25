<?php

namespace MPHB\Addons\MailChimp\Containers;

use MPHB\Addons\MailChimp\Repositories;

class RepositoriesContainer
{
    protected $listsRepository = null;
    protected $categoriesRepository = null;
    protected $groupsRepository = null;
    protected $generalRepository = null;
    protected $roomsRepository = null;

    /**
     * @return \MPHB\Addons\MailChimp\Repositories\ListsRepository
     */
    public function lists()
    {
        if (is_null($this->listsRepository)) {
            $this->listsRepository = new Repositories\ListsRepository();
        }

        return $this->listsRepository;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Repositories\CategoriesRepository
     */
    public function categories()
    {
        if (is_null($this->categoriesRepository)) {
            $this->categoriesRepository = new Repositories\CategoriesRepository();
        }

        return $this->categoriesRepository;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Repositories\GroupsRepository
     */
    public function groups()
    {
        if (is_null($this->groupsRepository)) {
            $this->groupsRepository = new Repositories\GroupsRepository();
        }

        return $this->groupsRepository;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Repositories\MailchimpRepository
     */
    public function mailchimp()
    {
        if (is_null($this->generalRepository)) {
            $this->generalRepository = new Repositories\MailchimpRepository();
        }

        return $this->generalRepository;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Repositories\RoomsRepository
     */
    public function rooms()
    {
        if (is_null($this->roomsRepository)) {
            $this->roomsRepository = new Repositories\RoomsRepository();
        }

        return $this->roomsRepository;
    }
}
