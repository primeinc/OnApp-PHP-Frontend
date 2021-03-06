<?php
define( 'ONAPP_PATH', dirname( dirname(__FILE__) ) );
define('ONAPP_DS', DIRECTORY_SEPARATOR);
require_once ONAPP_PATH .  ONAPP_DS . 'functions.php';


  /**
   * Writes log settings changes to onapp frontend configuration file
   *
   * @param array onapp frontend configurations array
   * @param string path to onapp frontend configuration file
   * @return boolean true|false
   */
   function write_config($config_array, $path){
       $content = '';
       foreach ( $config_array as $key=>$value )
         $content .= "$key=$value"."\n";

       if ( !$handle = fopen($path, 'w') ){
           return false;
       }
       else if ( ! fwrite($handle, $content) ) {
           return false;
       }

       fclose($handle);

       return true;
    }

    if ( ! isset( $_POST['step'] ) ) {
        $passed_txt = '<span class="yes">.......Passed</span>';

        $version_compare_txt = !version_compare( PHP_VERSION, '5.2.0', '<' )
            ? $passed_txt
            : '<span class="red">You have to upgrade you php version to at least 5.2.0+ </span>';
        
        $mod_rewrite_txt = in_array('mod_rewrite', apache_get_modules())
            ? $passed_txt
            : '<span class="red">You have to enable mod_rewrite on your server </span>';

        $mod_php_txt = in_array('mod_php5', apache_get_modules())
            ? $passed_txt
            : '<span class="red">You have to enable mod_php5 on your server </span>';

        $curl_txt = extension_loaded('curl')
            ? $passed_txt
            : '<span class="red">You have to install and enable Curl extension on your server </span>';

        $mcrypt_txt = extension_loaded('mcrypt')
            ? $passed_txt
            : '<span class="red">You have to install and enable Mcrypt extension on your server </span>';

        $config_file_txt = is_writable(ONAPP_PATH . ONAPP_DS . 'config.ini')
            ? $passed_txt
            : '<span class="red">You must set permissions for the config.ini file so it can be written to (chmod 777) </span>';

        $logs_dir_txt = is_writable(ONAPP_PATH . ONAPP_DS . 'logs' . ONAPP_DS)
            ? $passed_txt
            : '<span class="red">You must set permissions for the logs/ directory so it can be written to (chmod 777) </span>';

        $templates_dir_txt = is_writable(ONAPP_PATH . ONAPP_DS . 'templates_c' . ONAPP_DS)
            ? $passed_txt
            : '<span class="red">You must set permissions for the templates_c/ directory so it can be written to (chmod 777) </span>';

        $cache_dir_txt = is_writable(ONAPP_PATH . ONAPP_DS . 'cache' . ONAPP_DS)
            ? $passed_txt
            : '<span class="red">You must set permissions for the cache/ directory so it can be written to (chmod 777) </span>';

        $events_dir_txt = is_writable(ONAPP_PATH . ONAPP_DS . 'events' . ONAPP_DS)
            ? $passed_txt
            : '<span class="red">You must set permissions for the events/ directory so it can be written to (chmod 777) </span>';

        $htaccess_fite_txt = file_exists(ONAPP_PATH . ONAPP_DS . '.htaccess')
            ? $passed_txt
            : '<span class="red">You must miss the .htaccess file somewhere :) </span>';

        $passes = array (
            $htaccess_fite_txt,
            $cache_dir_txt,
            $events_dir_txt,
            $templates_dir_txt,
            $logs_dir_txt,
            $config_file_txt,
            $mcrypt_txt,
            $curl_txt,
            $mod_php_txt,
            $mod_rewrite_txt,
            $version_compare_txt
        );

        $not_passed = count ( array_unique ( $passes ) ); 
         
        $not_passed_txt = ( $not_passed > 1 ) 
            ? '<p class="red"> Some of your system parameters doesn\'t meet the requirements you need to fix them in order to continue </p>'
            : '';
        $disabled = ( $not_passed > 1 )
            ? 'disabled'
            : '';
        
        require_once 'step1.inc';
    }
    elseif ( $_POST['step'] == 2 ) {
        $base = (
            ( isset($_SERVER['HTTPS'])
                && strtolower($_SERVER['HTTPS']) !== 'off'
                    ? 'https://'
                    : 'http://'
            ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ); 

        $base = preg_replace('/\/install\/|index.php/', '', $base);

        $settings = array(
            'base_url' => $base,
            'hostname' => '',
            'default_alias' => 'profile',
            'secret_key' => uniqid(mt_rand()),
            'session_lifetime' => 3600,
            'default_language' => 'English',
            'template' => 'default',
            'controllers' => 'default',
            'log_directory' => 'logs',
            'debug_file_name' => 'frontend.log',
            'third_part_product_report_enable' => 1,
            'problem_report_debug_log_enable' => 1,
            'wrapper_log_report_enable' => 1,
            'log_level_frontend' => 'ONAPP_E_FATAL',
            'log_level_php' => 'E_ERROR',
            'log_rotation_days' => 30,
            'log_rotation_size' => 1000,
            'smarty_template_dir' => 'templates',
            'smarty_compile_dir' => 'templates_c',
            'smarty_cache_dir' => 'cache',
            'smarty_caching_enable' => 0,
            'smarty_caching_lifetime' => 3600,
            'smarty_force_compile' => 0,
            'smarty_compile_check' => 0,
            'show_date_in_logs'  => 0,
            'whmcs_api_file_url' => '',
            'whmcs_client_area_url' => '',
            'whmcs_login' =>  '',
            'whmcs_password' => '',
            'ssh_host'    => '',
            'ssh_user'  =>  '',
            'ssh_password' => '',
            'ssh_port'  => ''
        );
        
        $result = write_config($settings, ONAPP_PATH . ONAPP_DS . 'config.ini');

        onapp_load_event_classes();
        global $_EVENT_CLASSES;

        $events_dir = ONAPP_PATH. ONAPP_DS . 'events' . ONAPP_DS ;

        foreach ( $_EVENT_CLASSES as $event => $classes_array ) {
            $event_dir = $events_dir . $event . ONAPP_DS;
           if ( ! file_exists ($event_dir) ) {
                mkdir($event_dir , 0777, true );
                mkdir($event_dir . 'script', 0777, true );
                mkdir($event_dir. 'mail', 0777, true );
                mkdir($event_dir . 'exec', 0777, true );
           }
        }

        require_once 'step2.inc';
    }