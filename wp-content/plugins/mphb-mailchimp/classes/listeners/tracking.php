<?php

namespace MPHB\Addons\MailChimp\Listeners;

class Tracking
{
    public function __construct()
    {
        $this->addActions();
    }

    protected function addActions()
    {
        add_action('init', [$this, 'setupSessionParameters']);
        add_action('mphb_create_booking_by_user', [$this, 'saveTrackingData']);

        // Important for bookings made with confirmation mode "By customer via
        // email" when we need to save tracking data from Abandoned Cart automation
        add_action('mphb_booking_confirmed', [$this, 'saveTrackingData'], 5); // Call before subscription
    }

    public function setupSessionParameters()
    {
        if (isset($_GET['mc_eid'])) {
            mphb()->getSession()->set('mc_eid', sanitize_text_field($_GET['mc_eid']));
        }
        if (isset($_GET['mc_cid'])) {
            mphb()->getSession()->set('mc_cid', sanitize_text_field($_GET['mc_cid']));
        }
    }

    public function saveTrackingData($booking)
    {
        $session = mphb()->getSession();

        $emailId = $session->get('mc_eid');
        $campaignId = $session->get('mc_cid');

        if (!is_null($emailId)) {
            add_post_meta($booking->getId(), "_mphb_mc_track_email_id", $emailId, true);
            $session->set('mc_eid', null); // Remove after first usage
        }

        if (!is_null($campaignId)) {
            add_post_meta($booking->getId(), "_mphb_mc_track_campaign_id", $campaignId, true);
            $session->set('mc_cid', null); // Remove after first usage
        }
    }
}
