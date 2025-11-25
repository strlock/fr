<?php

namespace MPHB\Addons\MailChimp\Services;

use NSCL\WordPress\Async\BackgroundProcess;

class ListsSync extends BackgroundProcess
{
    public $prefix = 'mphb_mc';
    public $action = 'sync_lists';

    public function startSync()
    {
        $this->clearErrors();

        // If previous sync failed then remove it's pending lists
        $this->removeLists('pending');

        $this->addTasks([
            ['action' => 'fetch_lists']
        ]);

        $this->run();
    }

    public function task($workload)
    {
        if (!isset($workload['action'])) {
            return false;
        }

        switch ($workload['action']) {
            case 'fetch_lists':      return $this->fetchLists($workload); break;
            case 'fetch_categories': return $this->fetchCategories($workload); break;
            case 'fetch_groups':     return $this->fetchGroups($workload); break;
            default:                 return false; break;
        }
    }

    /**
     * @param array $workload [action]
     * @return bool
     */
    protected function fetchLists($workload)
    {
        // Fetch lists from MailChimp API
        $api = mphbmc()->api()->mailchimp();
        $lists = $api->getLists();

        if ($lists === false) {
            $this->saveError($api->getLastError());
            return false;
        }

        // Save lists
        if (!empty($lists)) {
            // Push lists into database
            $repository = mphbmc()->repository()->lists();
            $repository->addLists($lists, 'pending');

            // Get numeric list IDs for new tasks
            $ids = $repository->getLists(['fields' => 'ids', 'sync_status' => 'pending']);

            // Add "fetch_categories" tasks
            $categoryTasks = array_map(function ($listId, $remoteId) {
                return [
                    'action'         => 'fetch_categories',
                    'list_id'        => $listId,
                    'list_remote_id' => $remoteId
                ];
            }, $ids, array_keys($ids));

            $this->addTasks($categoryTasks);
        }

        return true;
    }

    /**
     * @param array $workload [action, list_id, list_remote_id]
     * @return bool
     */
    public function fetchCategories($workload)
    {
        // Fetch categories from MailChimp API
        $api = mphbmc()->api()->mailchimp();
        $categories = $api->getCategories($workload['list_remote_id']);

        if ($categories === false) {
            $this->saveError($api->getLastError());
            return false;
        }

        // Save categories
        if (!empty($categories)) {
            // Push categories into database
            $repository = mphbmc()->repository()->categories();
            $repository->addCategories($categories, $workload['list_id']);

            // Get numeric category IDs
            $ids = $repository->findIdsByListId($workload['list_id']);

            // Add "fetch_groups" tasks
            $groupTasks = array_map(function ($categoryId, $remoteId) use ($workload) {
                return [
                    'action'             => 'fetch_groups',
                    'list_remote_id'     => $workload['list_remote_id'],
                    'category_id'        => $categoryId,
                    'category_remote_id' => $remoteId
                ];
            }, $ids, array_keys($ids));

            $this->addTasks($groupTasks);
        }

        return true;
    }

    /**
     * @param array $workload [action, list_remote_id, category_id, category_remote_id]
     * @return bool
     */
    public function fetchGroups($workload)
    {
        // Fetch groups from MailChimp API
        $api = mphbmc()->api()->mailchimp();
        $groups = $api->getGroups($workload['list_remote_id'], $workload['category_remote_id']);

        if ($groups === false) {
            $this->saveError($api->getLastError());
            return false;
        }

        // Save groups
        if (!empty($groups)) {
            // Push groups into database
            $repository = mphbmc()->repository()->groups();
            $repository->addGroups($groups, $workload['category_id']);
        }

        return true;
    }

    protected function afterSuccess()
    {
        // Remove previous lists
        $this->removeLists('synced');

        mphbmc()->repository()->lists()->approvePendings();

        parent::afterSuccess();
    }

    protected function afterCancel()
    {
        // Remove unfinished lists
        $this->removeLists('pending');

        parent::afterCancel();
    }

    protected function removeLists($syncStatus)
    {
        $previousIds = mphbmc()->repository()->mailchimp()->collectIds($syncStatus);

        if (!empty($previousIds['lists'])) {
            mphbmc()->repository()->lists()->removeLists($previousIds['lists']);
        }

        if (!empty($previousIds['categories'])) {
            mphbmc()->repository()->categories()->removeCategories($previousIds['categories']);
        }

        if (!empty($previousIds['groups'])) {
            mphbmc()->repository()->groups()->removeGroups($previousIds['groups']);
        }
    }

    protected function saveError($error)
    {
        update_option($this->name . '_last_error', $error, 'no');
    }

    protected function clearErrors()
    {
        delete_option($this->name . '_last_error');
    }
}
