<?php

namespace MPHB\Addons\MailChimp;

class ScriptsManager
{
    public $isDebugMode = false;

    protected $scriptsToEnqueue = [];
    protected $stylesToEnqueue = [];

    public function __construct()
    {
        $this->isDebugMode = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;

        add_action('admin_init', [$this, 'registerScripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function registerScripts()
    {
        wp_register_style('mphb-mc-admin-css', $this->scriptUrl('assets/css/admin.min.css'), [], mphbmc()->pluginVersion(), 'all');
    }

    public function enqueueScripts()
    {
        foreach ($this->scriptsToEnqueue as $script) {
            wp_enqueue_script($script);
        }
        foreach ($this->stylesToEnqueue as $style) {
            wp_enqueue_style($style);
        }
    }

    /**
     * @param string $script Relative path to the script file.
     * @return self
     */
    public function enqueueScript($script)
    {
        $this->scriptsToEnqueue[] = $script;
        return $this;
    }

    /**
     * @param string $style Relative path to the style file.
     * @return self
     */
    public function enqueueStyle($style)
    {
        $this->stylesToEnqueue[] = $style;
        return $this;
    }

    public function scriptUrl($script)
    {
        if ($this->isDebugMode) {
            $script = str_replace(['.min.js', '.min.css'], ['.js', '.css'], $script);
        }

        return mphb_mc_url_to($script);
    }
}
