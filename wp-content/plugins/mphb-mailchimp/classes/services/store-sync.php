<?php

namespace MPHB\Addons\MailChimp\Services;

use MPHB\Addons\MailChimp\Entities;
use NSCL\WordPress\Async\BackgroundProcess;

class StoreSync extends BackgroundProcess
{
    public $prefix = 'mphb_mc';
    public $action = 'sync_store';

    public function startSync()
    {
        $this->clearErrors();

        $this->addTasks($this->siteTasks());
        $this->addTasks($this->roomTasks());
        $this->addTasks($this->bookingTasks());

        $this->updateStatus(esc_html__('Preprocessing...', 'mphb-mailchimp'));

        $this->run();
    }

    protected function siteTasks()
    {
        return [
            ['action' => 'connect_site']
        ];
    }

    protected function roomTasks()
    {
        mphb()->translation()->setupDefaultLanguage();
        $roomTypeIds = mphb()->getRoomTypePersistence()->getPosts();
        mphb()->translation()->restoreLanguage();

        $roomTasks = array_map(function ($roomTypeId) {
            return [
                'action'       => 'add_room_type',
                'room_type_id' => $roomTypeId
            ];
        }, $roomTypeIds);

        return $roomTasks;
    }

    protected function bookingTasks()
    {
        $bookingIds = mphb()->getBookingPersistence()->getPosts([
            'post_status' => mphb()->postTypes()->booking()->statuses()->getBookedRoomStatuses(),
            'orderby'     => 'ID',
            'order'       => 'ASC'
        ]);

        $bookingTasks = array_map(function ($bookingId) {
            return [
                'action'     => 'add_booking',
                'booking_id' => $bookingId
            ];
        }, $bookingIds);

        return $bookingTasks;
    }

    public function task($workload)
    {
        if (!isset($workload['action'])) {
            return false;
        }

        switch ($workload['action']) {
            case 'connect_site':  return $this->connectSite(); break;
            case 'add_room_type': return $this->addRoomType($workload['room_type_id']); break;
            case 'add_booking':   return $this->addBooking($workload['booking_id']); break;
            default:              return false; break;
        }
    }

    /**
     * @return bool
     */
    protected function connectSite()
    {
        $listId = mphbmc()->settings()->getStoreListId();

        if (empty($listId)) {
            $this->updateStatus(esc_html__('The store is not connected to any list/audience.', 'mphb-mailchimp'));
            $this->cancel();

            return false;
        }

        $store = new Entities\MailchimpStore($listId, ['is_syncing' => true]);
        $api = mphbmc()->api()->mailchimp();

        $saved = $api->saveStore($store);

        if ($saved) {
            mphbmc()->settings()->setStoreId($store->id); // Even on update
        } else {
            $this->updateStatus($api->getLastError());
            $this->cancel();
        }

        return $saved;
    }

    /**
     * @param int $roomTypeId
     * @return bool
     */
    protected function addRoomType($roomTypeId)
    {
        $storeId = mphbmc()->settings()->getStoreId();
        $product = new Entities\MailchimpProduct($roomTypeId);

        $api = mphbmc()->api()->mailchimp();

        if ($api->saveProduct($storeId, $product)) {
            return true;
        } else {
            $this->saveError($api->getLastError());
            return false;
        }
    }

    /**
     * @param int $bookingId
     * @return bool
     */
    protected function addBooking($bookingId)
    {
        $booking = mphb()->getBookingRepository()->findById($bookingId);

        if (is_null($booking)) {
            return false;
        }

        $storeId = mphbmc()->settings()->getStoreId();
        $order = new Entities\MailchimpOrder($bookingId, $booking);

        $api = mphbmc()->api()->mailchimp();

        $existed = $api->orderExists($storeId, $order->id);

        if (!$existed) {
            $saved = $api->addOrder($storeId, $order);
        } else {
            $saved = $api->updateOrder($storeId, $order);
        }

        if ($saved) {
            if (!$existed) {
                $booking->addLog(esc_html__('The booking is in sync with MailChimp now.', 'mphb-mailchimp'));
            }
        } else {
            $this->saveError($api->getLastError());
        }

        return $saved;
    }

    /**
     * @param mixed $workload
     * @param mixed $response
     */
    protected function taskComplete($workload, $response)
    {
        parent::taskComplete($workload, $response);

        if (!$this->isAborting) {
           $this->updateStatus(sprintf(esc_html__('In progress... (%d%% done)', 'mphb-mailchimp'), $this->tasksProgress()));
        }
    }

    protected function afterSuccess()
    {
        $this->updateStatus(esc_html__('In sync with MailChimp.', 'mphb-mailchimp'));
        parent::afterSuccess();
    }

    protected function afterComplete()
    {
        if (mphbmc()->settings()->storeSet()) {
            mphbmc()->api()->mailchimp()->syncStore(mphbmc()->settings()->getStoreId(), false);
        }

        parent::afterComplete();
    }

    protected function updateStatus($statusText)
    {
        mphbmc()->settings()->setStoreSyncStatus($statusText);
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
