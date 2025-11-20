<?php

/**
 * admin lib for processing backend events
 *
 */
class AlohaSystemStatus {

    static $instance;

    public static function getInstance() {
        static $instance;
        $class = __CLASS__;
        if (!$instance instanceof $class) {
            $instance = new $class;
        }
        return $instance;
    }

    function getSystemStatusArray() {
        $allVars = array('php_version', 'max_execution_time', 'max_input_time', 'memory_limit', 'post_max_size', 'upload_max_filesize');
        $finalVars = [];
        foreach ($allVars as $var) {
            $current = $var === 'php_version' ? PHP_VERSION : ini_get($var);
            $recommended = constant('ALOHA_SYSTEM_RECOMMENDED_' . strtoupper($var));
            if ($recommended) {
                if ($var === 'php_version') {
                    $passed = $current < $recommended ? false : true;
                } else if($var==='max_execution_time' && (INT)$current===0){
                    $current = 'unlimited!';
                    $passed = true;
                } else {
                    $passed = ((INT) $current < (INT) $recommended ) ? false : true;
                }

                $finalVars[$var] = array('current' => $current, 'recommended' => $recommended, 'passed' => $passed);
            }
        }
        //check for system error
        $status = apply_filters('aloha_plugin_server_test',false);
        $help = __('cURL error 28', ALOHA_DOMAIN) . ' <a href="'.ALOHA_CURL_ERROR_HELP_URL.'">'.__('Details', ALOHA_DOMAIN).'</a>';
        $finalVars['plugin_server'] = array('current' => (int)$status, 'passed' => (bool)$status===true, 'help'=>$help);
        return $finalVars;
    }

}
