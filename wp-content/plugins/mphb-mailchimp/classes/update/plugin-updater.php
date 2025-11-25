<?php

namespace MPHB\Addons\MailChimp\Update;

class PluginUpdater
{
    public function __construct()
    {
        add_action('admin_init', [$this, 'initEddUpdater'], 9);
    }

    public function initEddUpdater()
    {
        if (mphb_mc_use_edd_license()) {
            $apiData = array(
                'version' => mphbmc()->pluginVersion(),
                'license' => mphbmc()->api()->eddLicense()->getKey(),
                'item_id' => mphbmc()->api()->eddLicense()->getProductId(),
                'author'  => mphbmc()->pluginAuthor()
            );

            $pluginFile = \MPHB\Addons\MailChimp\PLUGIN_FILE;

            new EddPluginUpdater(mphbmc()->pluginUri(), $pluginFile, $apiData);
        }
    }
}
