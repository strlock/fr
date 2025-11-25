<?php

namespace MPHB\Addons\MailChimp;

class Plugin
{
    /** @var self */
    protected static $instance = null;

    // Single components
    /** @var \MPHB\Addons\MailChimp\Listeners\Ajax */
    protected $ajax = null;
    /** @var \MPHB\Addons\MailChimp\ScriptsManager */
    protected $scriptsManager = null;
    /** @var \MPHB\Addons\MailChimp\Settings */
    protected $settings = null;
    /** @var \MPHB\Addons\MailChimp\Admin\SettingsTab */
    protected $settingsTab = null;
    /** @var \MPHB\Addons\MailChimp\Update\PluginUpdater */
    protected $pluginUpdater = null;
    /** @var \MPHB\Addons\MailChimp\Listeners\Subscription */
    protected $subscriptionListener = null;
    /** @var \MPHB\Addons\MailChimp\Listeners\RoomsUpdate */
    protected $roomEventsListener = null;
    /** @var \MPHB\Addons\MailChimp\Listeners\BookingsUpdate */
    protected $bookingEventsListener = null;
    /** @var \MPHB\Addons\MailChimp\Listeners\Tracking */
    protected $campaignTrackingListener = null;

    // Containers
    /** @var \MPHB\Addons\MailChimp\Containers\ApisContainer */
    protected $apisContainer = null;
    /** @var \MPHB\Addons\MailChimp\Containers\RepositoriesContainer */
    protected $repositoriesContainer = null;
    /** @var \MPHB\Addons\MailChimp\Containers\ServicesContainer */
    protected $servicesContainer = null;

    // Other fields
    protected $pluginHeaders = [];

    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'load']);
        add_action('init', [$this, 'init']);
    }

    public function load()
    {
        if (!class_exists('HotelBookingPlugin')) {
            return;
        }

        $this->scriptsManager = new ScriptsManager();
        $this->settings       = new Settings();

        $this->apisContainer         = new Containers\ApisContainer();
        $this->repositoriesContainer = new Containers\RepositoriesContainer();
        $this->servicesContainer     = new Containers\ServicesContainer();

        add_action('init', [$this, 'loadTranslations']);

        if (wp_doing_ajax()) {
            $this->ajax = new Listeners\Ajax();
        } else {
            $this->settingsTab   = new Admin\SettingsTab();
            $this->pluginUpdater = new Update\PluginUpdater();

            if ($this->settings->subscriptionsEnabled()) {
                $this->subscriptionListener = new Listeners\Subscription();
            }

            if ($this->settings->storeSet()) {
                $this->roomEventsListener = new Listeners\RoomsUpdate();
                $this->bookingEventsListener = new Listeners\BookingsUpdate();
                $this->campaignTrackingListener = new Listeners\Tracking();
            }
        }
    }

    public function init()
    {
        if (!class_exists('HotelBookingPlugin')) {
            return;
        }

        // Create instance of background synchronizers to listen AJAX and cron calls
        $this->servicesContainer->listsSync()->touch();
        $this->servicesContainer->storeSync()->touch();
    }

    public function loadTranslations()
    {
        $pluginDir = plugin_basename(PLUGIN_DIR); // "mphb-mailchimp" or renamed name
        load_plugin_textdomain('mphb-mailchimp', false, $pluginDir . '/languages');
    }

    /**
     * @return \MPHB\Addons\MailChimp\ScriptsManager
     */
    public function scripts()
    {
        return $this->scriptsManager;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Settings
     */
    public function settings()
    {
        return $this->settings;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Containers\ApisContainer
     */
    public function api()
    {
        return $this->apisContainer;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Containers\RepositoriesContainer
     */
    public function repository()
    {
        return $this->repositoriesContainer;
    }

    /**
     * @return \MPHB\Addons\MailChimp\Containers\ServicesContainer
     */
    public function service()
    {
        return $this->servicesContainer;
    }

    /**
     * @return string
     */
    public function pluginUri()
    {
        $headers = $this->pluginHeaders();
        return $headers['PluginURI'];
    }

    /**
     * @return string
     */
    public function pluginVersion()
    {
        $headers = $this->pluginHeaders();
        return $headers['Version'];
    }

    /**
     * @return string
     */
    public function pluginAuthor()
    {
        $headers = $this->pluginHeaders();
        return $headers['Author'];
    }

    /**
     * @return string[]
     */
    public function pluginHeaders()
    {
        if (empty($this->pluginHeaders)) {
            if (!function_exists('get_plugin_data')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            $headers = get_plugin_data(PLUGIN_FILE, false, false);
            $headers = array_merge([
                'PluginURI' => 'https://motopress.com/products/hotel-booking-mailchimp/',
                'Version'   => '1.0',
                'Author'    => 'MotoPress'
            ], $headers);

            $this->pluginHeaders = $headers;
        }

        return $this->pluginHeaders;
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
