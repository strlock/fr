<?php

declare(strict_types=1);

namespace MPHB\Addons\Invoice\Update;

use MPHB\Addons\Invoice\UsersAndRoles;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @since 1.4.1
 */
class PluginPatcher
{
    const OPTION_DB_VERSION = 'mphb_invoice_db_version';

    private $patch_1_4_1 = null;

    public function __construct()
    {
        $this->patch_1_4_1 = new Patches\PluginPatch_1_4_1();

        if (!wp_doing_ajax()) {
            $this->upgrade();
        }
    }

    private function upgrade()
    {
        $dbVersion = $this->getDbVersion();
        $currentVersion = mphbinvoice()->pluginVersion();

        if (!$dbVersion
            || version_compare($dbVersion, $currentVersion, '<')
        ) {
            UsersAndRoles\Capabilities::setup();

            if (version_compare($dbVersion, '1.4.0', '=')) {
                $this->patch_1_4_1->dispatch();
            }

            $this->updateDbVersion();
        }
    }

    public function getDbVersion(): string
    {
        return get_option(self::OPTION_DB_VERSION, '');
    }

    private function updateDbVersion()
    {
        update_option(self::OPTION_DB_VERSION, mphbinvoice()->pluginVersion());
    }
}
