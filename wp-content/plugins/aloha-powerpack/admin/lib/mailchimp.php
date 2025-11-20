<?php

/**
 * admin lib for processing backend events
 *
 */
class AlohaMailchimp {

    static $instance;

    public static function getInstance() {
        static $instance;
        $class = __CLASS__;
        if (!$instance instanceof $class) {
            $instance = new $class;
            $instance->init();
        }
        return $instance;
    }

    function loadAssets() {
        // Admin Scripts
        if (aloha_active_page('dashboard')) {
            $jsTimeModified = filemtime(ALOHA_ADMIN_JS_DIR . '/mailchimp.js');
            wp_register_script('aloha-mailchimp', ALOHA_ADMIN_JS_DIR_URL . '/mailchimp.js', ['jquery'], $jsTimeModified, true);
            wp_enqueue_script('aloha-mailchimp');
        }
    }

    function init() {
        //register stuff, load css js
        add_action('wp_ajax_aloha_subscribe_to_mailchimp', [$this, 'subscribeToMailchimp']);
        add_action('admin_enqueue_scripts', [$this, 'loadAssets']);
    }

    function getMailChimpFieldValue() {
        $result = $this->getMailChimpSubscription();
        if (!$result) {
            return get_bloginfo('admin_email');
        }
        return (string) $result;
    }

    function removeMailChimpSubscription() {
        //@todo should also call up aloha-helper/mailchimp-unsubscribe
        return delete_option(ALOHA_OPTION_MAILCHIMP_SUBSCRIPTION);
    }

    function getMailChimpSubscription() {
        return get_option(ALOHA_OPTION_MAILCHIMP_SUBSCRIPTION);
    }

    function setMailChimpSubscription($email) {
        return update_option(ALOHA_OPTION_MAILCHIMP_SUBSCRIPTION, $email);
    }

    function subscribeToMailchimp() {
        $email = $_POST['email'];
        $ERROR = __('Some error occurred, try again later', ALOHA_DOMAIN);
        $SUCCESS = __('Subscribed successfully', ALOHA_DOMAIN);
        $response = array(
            'success' => false,
            'message' => $ERROR
        );
        if (!empty($email)) {
            $tags = apply_filters('aloha_mailchimp_tags', []);
            //mainly, we want the template name (slug)
            $data = [
                'email' => $email,
                'status' => 'subscribed',
                'firstname' => '',
                'lastname' => '',
                'tags'=> $tags
            ];

            // NOTE: status having 4 Option --"subscribed","unsubscribed","cleaned","pending"
            $res = $this->syncMailchimp($data);
            if ($res === 200) {
                $response['success'] = true;
                $response['message'] = $SUCCESS;
                $this->setMailChimpSubscription($email);
            }
        }

        wp_send_json($response);
    }

    function syncMailchimp($data) {
        
        $json = json_encode($data);
        $url = ALOHA_REST_HELPER_URL.'mailchimp_subscription';
        //$auth = 'Bearer: xxxyy';
        $response = wp_remote_post(
                $url,
                array(
                    'method' => 'PUT',
                    'timeout' => 15,
                    'redirection' => 1,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        //'Authorization' => $auth
                    ),
                    'body' => $json, // Payload, text to analyse
                )
        );
        
        if (is_wp_error($response)) {
            $errorResponse = $response->get_error_message();
            return false;
        }
       
        //check if code is 200 if not then error
        if (isset($response['response']['code'])) {
            return $response['response']['code'];
        }

        return false;
    }

}

//initialize to register scripts and actions
$alohaMailchimp = AlohaMailchimp::getInstance();
