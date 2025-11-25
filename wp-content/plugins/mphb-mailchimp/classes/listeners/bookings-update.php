<?php

namespace MPHB\Addons\MailChimp\Listeners;

use MPHB\Addons\MailChimp\Entities;
use MPHB\PostTypes\BookingCPT\Statuses as BookingStatuses;
use MPHB\PostTypes\PaymentCPT\Statuses as PaymentStatuses;

class BookingsUpdate
{
    public function __construct()
    {
        $this->addActions();
    }

    protected function addActions()
    {
        // Handle booking changes
        $bookingPostType = mphb()->postTypes()->booking()->getPostType();

        add_action('mphb_booking_status_changed',  [$this, 'onPendingBooking'     ], 10, 2);
        add_action('mphb_booking_confirmed',       [$this, 'onConfirmBooking'     ], 15, 2); // 15 - call after subscription
        add_action('mphb_payment_status_changed',  [$this, 'onPaidBooking'        ]);
        add_action('mphb_booking_cancelled',       [$this, 'onCancelBooking'      ], 10, 2);
        add_action('mphb_booking_status_changed',  [$this, 'onAbandonBooking'     ], 10, 2);
        add_action("save_post_{$bookingPostType}", [$this, 'onUpdateBooking'      ], 10, 2);
        add_action('mphb_update_edited_booking',   [$this, 'onUpdateEditedBooking'], 10, 2);
        add_action('delete_post',                  [$this, 'onDeleteBooking'      ]);
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     * @param string $oldStatus
     */
    public function onPendingBooking($booking, $oldStatus)
    {
        // Skip posts restored from the trash 
        if ($oldStatus == 'trash') {
            return;
        }

        if (!mphb_mc_carts_available()) {
            return;
        }

        // Skip non-pending bookings here
        if (!in_array($booking->getStatus(), mphb()->postTypes()->booking()->statuses()->getPendingRoomStatuses())) {
            return;
        }

        $storeId = mphbmc()->settings()->getStoreId();
        $cart = new Entities\MailchimpCart($booking->getId(), $booking);

        $api = mphbmc()->api()->mailchimp();

        // Admin can change the status to Pending
        if (!$api->orderExists($storeId, $cart->id)) {
            if ($api->cartExists($storeId, $cart->id)) {
                $api->updateCart($storeId, $cart);
            } else if ($api->addCart($storeId, $cart)) {
                $booking->addLog(esc_html__('MailChimp cart successfully added.', 'mphb-mailchimp'));
            } else {
                $booking->addLog(sprintf(esc_html__('Failed to add MailChimp cart. API response: "%s"', 'mphb-mailchimp'), $api->getLastError()));
            }
        }
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     * @param string $oldStatus
     */
    public function onConfirmBooking($booking, $oldStatus)
    {
        // Skip posts restored from the trash 
        if ($oldStatus == 'trash') {
            return;
        }

        $storeId = mphbmc()->settings()->getStoreId();
        $remoteId = (string)$booking->getId();

        $api = mphbmc()->api()->mailchimp();

        // Remove cart
        if ($api->cartExists($storeId, $remoteId)) {
            $api->removeCart($storeId, $remoteId);
        }

        // Save order
        $order = new Entities\MailchimpOrder($booking->getId(), $booking);

        if ($api->orderExists($storeId, $remoteId)) {
            $api->updateOrder($storeId, $order);
        } else if ($api->addOrder($storeId, $order)) {
            $booking->addLog(esc_html__('MailChimp order successfully added.', 'mphb-mailchimp'));
        } else {
            $booking->addLog(sprintf(esc_html__('Failed to add MailChimp order. API response: "%s"', 'mphb-mailchimp'), $api->getLastError()));
        }

        // Payment transition action will follow the current action. Don't push
        // the same order twice
        remove_action('mphb_payment_status_changed', [$this, 'onPaidBooking']);
    }

    /**
     * @param \MPHB\Entities\Payment $payment
     */
    public function onPaidBooking($payment)
    {
        if ($payment->getStatus() != PaymentStatuses::STATUS_COMPLETED) {
            return;
        }

        $booking = mphb()->getBookingRepository()->findById($payment->getBookingId());

        if (is_null($booking)) {
            return;
        }

        // Set financial status to "paid"
        if (mphb_mc_booking_paid($booking)) {
            $storeId = mphbmc()->settings()->getStoreId();
            $remoteId = (string)$booking->getId();

            mphbmc()->api()->mailchimp()->orderPaid($storeId, $remoteId);
        }
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     * @param string $oldStatus
     */
    public function onCancelBooking($booking, $oldStatus)
    {
        // Skip posts restored from the trash
        if ($oldStatus == 'trash') {
            return;
        }

        $storeId = mphbmc()->settings()->getStoreId();
        $remoteId = (string)$booking->getId();

        $api = mphbmc()->api()->mailchimp();

        if ($api->orderExists($storeId, $remoteId)) {
            $order = new Entities\MailchimpOrder($booking->getId(), $booking);

            if ($api->updateOrder($storeId, $order)) {
                $booking->addLog(esc_html__('MailChimp order successfully cancelled.', 'mphb-mailchimp'));
            }
        } else if ($api->cartExists($storeId, $remoteId)) {
            if ($api->removeCart($storeId, $remoteId)) {
                $booking->addLog(esc_html__('Cancelled cart removed from MailChimp.', 'mphb-mailchimp'));
            }
        }
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     * @param string $oldStatus
     */
    public function onAbandonBooking($booking, $oldStatus)
    {
        // Skip posts restored from the trash
        if ($oldStatus == 'trash') {
            return;
        }

        // Skip non-abandoned booking here
        if ($booking->getStatus() != BookingStatuses::STATUS_ABANDONED) {
            return;
        }

        $storeId = mphbmc()->settings()->getStoreId();
        $remoteId = (string)$booking->getId();

        $api = mphbmc()->api()->mailchimp();

        if ($api->orderExists($storeId, $remoteId)) {
            $order = new Entities\MailchimpOrder($booking->getId(), $booking);

            if ($api->updateOrder($storeId, $order)) {
                $booking->addLog(esc_html__('MailChimp order successfully cancelled.', 'mphb-mailchimp'));
            }
        } else if ($api->cartExists($storeId, $remoteId)) {
            if ($api->removeCart($storeId, $remoteId)) {
                $booking->addLog(esc_html__('Abandoned cart removed from MailChimp.', 'mphb-mailchimp'));
            }
        }
    }

    /**
     * @param int $postId
     * @param \WP_Post $post
     */
    public function onUpdateBooking($postId, $post)
    {
        // Skip drafts. Don't skip "pending" - for booking it has different meaning
        if (in_array($post->post_status, ['auto-draft', 'draft', 'trash', 'inherit'])) {
            return;
        }

        // Our own status control on Edit Booking page
        if (!isset($_POST['mphb_post_status'])) {
            return;
        }

        $oldStatus = $post->post_status;
        $newStatus = sanitize_text_field($_POST['mphb_post_status']);

        if ($oldStatus == $newStatus) {
            $storeId = mphbmc()->settings()->getStoreId();
            $remoteId = (string)$postId;

            $api = mphbmc()->api()->mailchimp();

            if (in_array($newStatus, mphb()->postTypes()->booking()->statuses()->getPendingRoomStatuses())) {
                if ($api->cartExists($storeId, $remoteId)) {
                    $cart = new Entities\MailchimpCart($postId);
                    $api->updateCart($storeId, $cart);
                }
            } else {
                if ($api->orderExists($storeId, $remoteId)) {
                    $order = new Entities\MailchimpOrder($postId);
                    $api->updateOrder($storeId, $order);
                }
            }
        }

        // When we save new status from our own status control, we trigger
        // "save_post" action second time. So don't push the same order twice
        remove_action(current_action(), [$this, __FUNCTION__]);
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     * @param \MPHB\Entities\ReservedRoom[] $oldRooms
     *
     * @since 1.0.2
     */
    public function onUpdateEditedBooking($booking, $oldRooms)
    {
        $bookingId = $booking->getId();

        $storeId = mphbmc()->settings()->getStoreId();
        $remoteId = (string)$bookingId;

        $api = mphbmc()->api()->mailchimp();

        if (in_array($booking->getStatus(), mphb()->postTypes()->booking()->statuses()->getPendingRoomStatuses())) {
            if ($api->cartExists($storeId, $remoteId)) {
                $cart = new Entities\MailchimpCart($bookingId, $booking);
                $api->updateCart($storeId, $cart);
            }
        } else {
            if ($api->orderExists($storeId, $remoteId)) {
                $order = new Entities\MailchimpOrder($bookingId, $booking);
                $updated = $api->updateOrder($storeId, $order);

                if ($updated !== false) {
                    $newIds = $booking->getReservedRoomIds();

                    foreach ($oldRooms as $room) {
                        if (!in_array($room->getId(), $newIds)) {
                            $api->removeOrderLine($storeId, $remoteId, (string)$room->getId());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param int $postId
     */
    public function onDeleteBooking($postId)
    {
        $postType = get_post_type($postId);

        if ($postType !== mphb()->postTypes()->booking()->getPostType()) {
            return;
        }

        $storeId = mphbmc()->settings()->getStoreId();
        $remoteId = (string)$postId;

        $api = mphbmc()->api()->mailchimp();

        if ($api->orderExists($storeId, $remoteId)) {
            $api->removeOrder($storeId, $remoteId);
        } else if ($api->cartExists($storeId, $remoteId)) {
            $api->removeCart($storeId, $remoteId);
        }
    }
}
