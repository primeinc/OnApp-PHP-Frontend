<?php if ( ! defined('ONAPP_PATH')) die('No direct script access allowed');

/**
 * Gets list of directories in specified directory
 *
 * @param string directory to get the list from
 * @return array list of directories
 */
function onapp_get_dir_list($dir) {
    onapp_debug("Get directory list of ($dir)");

    $directories = scandir(ONAPP_PATH.ONAPP_DS.$dir);

    foreach( $directories as $directory)
        if (! is_dir($directory) && ! is_file(ONAPP_PATH.ONAPP_DS.$dir.ONAPP_DS.$directory) )
            $result[] = $directory;

    onapp_debug("onapp_get_dir_list ($dir):\nreturn => " . print_r($result, true) );

    return $result;
}


/**
 * Returns a string from language package
 *
 * @param string $string language package constant
 *
 * @return string language package string value
 *
 */
function onapp_string($string = NULL) {
    if( $string == NULL)
        return NULL;
    global $_LANG;

    onapp_debug("Get string" );

    $return = isset($_LANG[$string]) ?
        $_LANG[$string] :
        "String $string not found";

    $return = isset($_LANG[$string]) ?
        $_LANG[$string] :
        "String $string not found";

    onapp_debug("onapp_string: 'string' => '$string', return => $return");

    return $return;
}

/**
 * Returns a value of particular variable from global GET or POST array.
 *
 * @param string $string variable name
 *
 * @param string $method http method [POST|post|GET|get] default = ''
 *
 * @return mixed variable value
 */
function onapp_get_arg($string, $method='') {
    onapp_debug('Get arguments');

    switch($method) {
        case "get":
        case "GET":
            $return = array_key_exists($string, $_GET) ? $_GET[$string] : NULL;
            break;
        case "post":
        case "POST":
            $return = array_key_exists($string, $_POST) ? $_POST[$string] : NULL;
            break;
       case "session":
       case "SESSION":
            $return = $_SESSION;
            break;
        default :
            $value_post = array_key_exists($string, $_POST) ? $_POST[$string] : NULL;
            $value_get  = array_key_exists($string, $_GET) ? $_GET[$string] : NULL;
            $return = $value_post ? $value_post : $value_get;
            break;
    }

    onapp_debug("onapp_get_arg: string => $string, method => $method, return => $return");

    return $return;
}

/**
 * This function is called from controller classes to display proper view and
 * template depends on controller and method. Uses Smarty template engine.
 *
 * @param string $view Name of a Smarty template file to display.
 *
 * @param string $template Name of a particular template. Not realised yet!
 *
 * @param array $params Array consists of variables' keys and values need to be
 * passed to Smarty template view file.
 *
 * @todo  VERIFICATION IF TEMPLATE FILE EXSITS!
 *
 * @return void
 *
 */
function onapp_show_template($view, $params = array()) {
    global $_ALIASES, $_SCREEN_IDS;

    onapp_debug('Show templates');
    onapp_debug("onapp_show_template: view => $view, params => " . print_r($params, true));
    
    $template = ONAPP_PATH.ONAPP_DS.'templates'.ONAPP_DS.ONAPP_TEMPLATE.ONAPP_DS. 'views' .ONAPP_DS.str_replace('_',ONAPP_DS,$view).'.tpl';

    if( ! file_exists($template))
        die("File $template not found");

    $globals = array(
        'navigation'    => $_SCREEN_IDS,
        '_ALIASES'      => $_ALIASES,
        'langs'         => onapp_get_dir_list('lang')
    );

    $smarty_params = is_array($params)
        ? array_merge($params, $globals)
        : $globals;

    require_once ONAPP_PATH.ONAPP_DS.'libs'.ONAPP_DS.'smarty'.ONAPP_DS.'Smarty.class.php';

    $smarty = new Smarty;

    $smarty->template_dir = ONAPP_SMARTY_TEMPLATE_DIR;
    $smarty->compile_dir  = ONAPP_SMARTY_COMPILE_DIR;
    $smarty->cache_dir    = ONAPP_SMARTY_CACHE_DIR;
    $smarty->caching      = ONAPP_SMARTY_CACHING_ENABLE;
    //in seconds
    $smarty->cache_lifetime = ONAPP_SMARTY_CACHING_LIFETIME;
    // set false for best performance when no templates changes needed
    $smarty->compile_check = ONAPP_SMARTY_COMPILE_CHECK;
    // set false for best performance when no templates changes needed
    $smarty->force_compile = ONAPP_SMARTY_FORCE_COMPILE;

    foreach($smarty_params as $key => $value)
        $smarty->assign("$key",$value);

    $smarty->display($template);

    unset($_SESSION['message']);

    onapp_debug('Display Smarty Template');
}

/**
 * Initializes global language array depends on language pack name.
 * Takes package name from the session.English is default language.
 *
 * @param string $language Name of language package
 *
 * @return void
 *
 */
function onapp_load_language($lang = '') {
    global $_LANG;

    onapp_debug("Load language: lang => '$lang'");

    if(isset($_SESSION["language"]) && $_SESSION["language"] != '')
        $language = $_SESSION["language"];
    else
        $language = $lang;

    $file = ONAPP_PATH.ONAPP_DS.'lang'.ONAPP_DS.$language.ONAPP_DS.'strings.php';

    if(file_exists($file))
        include $file;
    else {
        onapp_die("Language file $file not found");
    }
}

/**
 * Parsing menu xml file. Converts xml dom data to array. Initializes global screen ids arrays.
 *
 * @return void
 *
 */
function onapp_load_screen_ids($SimpleXMLElement = null, $parrent_id = '') {
    global $_SCREEN_IDS, $_ALIASES;

    if(is_null($SimpleXMLElement))
        if(file_exists(ONAPP_PATH.ONAPP_DS.'menu.xml'))
            $SimpleXMLElement = simplexml_load_file(ONAPP_PATH.ONAPP_DS.'menu.xml');
        else
            onapp_die('Could not find file menu.xml');

    for($id = 1; $id < count($SimpleXMLElement)+1; $id++) {
        $current_id = $parrent_id != '' ? "$parrent_id.$id" : $id;

        foreach($SimpleXMLElement->screen[ $id -1 ]->attributes() as $k=>$v)
            $_SCREEN_IDS["$current_id"][$k] = (String)$v;

        if(isset($_SCREEN_IDS["$current_id"]['title'])){

            // TODO move on onapp_show_template function
            $file_path = 'controllers'. ONAPP_DS .  ONAPP_CONTROLLERS . ONAPP_DS . 'c_'.strtolower($_SCREEN_IDS["$current_id"]['class']).'.php';
            if( file_exists( $file_path ) )
            {   
                require_once $file_path;
                $_SCREEN_IDS["$current_id"]['show'] = call_user_func( array( $_SCREEN_IDS["$current_id"]['class'], $_SCREEN_IDS["$current_id"]['access'] ) );
                // $_SCREEN_IDS["$current_id"]['show'] = true;
            }
                else
                    die('File '.$file_path.' doesn\'t exists');

        }
        $_ALIASES[$_SCREEN_IDS["$current_id"]["alias"]] = $current_id;

        onapp_load_screen_ids($SimpleXMLElement->screen[ $id -1 ], "$current_id");
    }
}

/**
 *
 */
function onapp_init_screen_ids() {
    global $_ALIASES, $_SCREEN_IDS;

    onapp_debug('Load Screen IDs');

    onapp_load_screen_ids();             
    onapp_debug(print_r($_ALIASES, true));
    onapp_debug(print_r($_SCREEN_IDS, true));
}

/**
 * Encrypts and Decrypts data with Mcrypt php extension
 *
 * @param string $value data to be processed with onapp_cryptData function
 *
 * @param string $action action to do with data [encrypt|decrypt]
 *
 * @return string processed data
 *
 */
function onapp_cryptData($value, $action) {
   $key = ONAPP_SECRET_KEY;
   $text = $value;
   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

   switch($action) {
      case "encrypt":
               $text = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
          break;
      case "decrypt":
               $text = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv));
          break;
      default:
              onapp_die("Wrong crypt data type $type");
          break;
   };

   return $text;
}

/**
 * Redirects to specified url
 *
 * @param string url redirect to
 *
 * @return void
 *
 */
function onapp_redirect($url) {
    onapp_debug("Redirect: url => '$url'");

    if (!headers_sent()) {
        header('Location: '.$url); exit;
    } else {
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit;
    }
}

/*
 * TODO add description
 */

function onapp_init_config() {
    if (file_exists('config.ini') )
        $config = parse_ini_file('config.ini');
    else
        onapp_die('Config file not found');

    foreach( $config as $key => $value)
        define('ONAPP_'.strtoupper($key), $value);
}

/**
 *
 * Checks PHP version, config options, functions and PHP extenssions
 *
 * @return void
 *
 */
function onapp_check_configs() {

    onapp_debug('Check configs');

    // Cheching PHP version
    if (version_compare(PHP_VERSION, '5.0.0', '<'))
        onapp_die('You need at least PHP 5.0.0, your current version is '. PHP_VERSION) ;

    // Checking of necessary configuration options
// TODO check is ONAPP_SMARTY_COMPILE_DIR and other config options exists
    $config_options = array(
        'ONAPP_SECRET_KEY',
    );

    foreach($config_options as $option)
        if (! defined($option) )
            onapp_die("Config option $option not found");

    // Checking of necessary fuctions
    $necessary_fuctions = array(
        'onapp_cryptData'
    );

    foreach($necessary_fuctions as $function_name)
       if(!function_exists($function_name))
           onapp_die("Function $function_name not found");

    // Checking of necessary PHP extensions

    // Including manuals

    $enabled_extensions = get_loaded_extensions();
    $require_extensions = array(
        'mcrypt' => 'mcrypt.php'
    );

    foreach( $require_extensions as $extension_name => $manual )
        if( ! in_array($extension_name, $enabled_extensions)) {
            $file = ONAPP_PATH.ONAPP_DS."manuals/$manual";

            if ( file_exists($file) )
                include($file);
            else
                onapp_die("File $file does'n file");

            exit;
        }
}

/**
 * Handles sessions.
 *
 * @param string $time session lifetime
 *
 * @param string $ses session name
 *
 * @return void
 *
 */
function onapp_startSession($ses = 'MYSES') {
    $time =  ONAPP_SESSION_LIFETIME;
    session_set_cookie_params($time);
    session_name($ses);
    session_start();

    onapp_debug('Start SESSION');

    // rotates error log files
    onapp_rotate_error_log();

    // rotates debug log file
    onapp_rotate_debug_log();
    

    // Reset the expiration time upon page load
    if (isset($_COOKIE[$ses]))
      setcookie($ses, $_COOKIE[$ses], time() + $time, "/");
}

/**
 *
 * Verifies whether user is authorithed
 *
 * @return void
 *
 */
function onapp_is_auth() {
    $is_auth = isset($_SESSION)
        && isset($_SESSION["login"])
        && isset($_SESSION["password"])
        && isset($_SESSION["host"])
        && isset($_SESSION["id"]);

    onapp_debug("onapp_is_auth: return => '$is_auth'");

    return $is_auth;
}

/**
 *
 * Checks whether user has permission to perform particular action
 *
 * @param string $permission
 *
 * @return void     
 */
function onapp_permission($permissions) {                                                         
    onapp_debug(__CLASS__.' :: '.__FUNCTION__);
    global $_ALIASES;
    if(is_array($permissions))
    {
        foreach($permissions as $permission)
            if(in_array($permission, $_SESSION['permissions']))
                return;
    }
    else
        if(in_array($permissions, $_SESSION['permissions']))
            return;

    require_once ONAPP_PATH.ONAPP_DS.'controllers'.ONAPP_DS.ONAPP_CONTROLLERS.ONAPP_DS.'c_profile.php';
    $profile = new Profile;
    $profile->show_template_view('YOU_HAVE_NOT_PERMISSION_FOR_THIS_ACTION');
    exit;
}

/**
 * Checks whether user has permission to view particular items
 *
 * @param string $permission
 *
 * @return boolean [true|false]
 *
 */
function onapp_has_permission( $permissions ) { //print('<pre>'); print_r( $_SESSION['permissions'] ); die();
    onapp_debug(__CLASS__.' :: '.__FUNCTION__);
    if( is_array( $permissions ) )
    {
           foreach( $permissions as $permission )
               if( in_array( $permission, $_SESSION['permissions'] ) )
                       return true;

               return false;
    }
    return in_array($permissions, $_SESSION['permissions'] );
}
/**
 * Gets the list of file names in target directory
 *
 * @param string path to directory
 * @return array list of file names in this directory
 */
function onapp_scan_dir( $path ){
    onapp_debug(__CLASS__.' :: '.__FUNCTION__);
    if ( $handle = opendir( $path ) ) {
        while (false !== ($file = readdir($handle))) {
            if( ! is_dir($file) ) {
                $files_list[] = $file;
            }
        }
    }
    else {
        onapp_die('Directory not found -> '. $path);
    }
    closedir($handle);
    return $files_list;

}

function onapp_format_money($number, $fractional=false) {
    if ($fractional) {
        $number = sprintf('%.2f', $number);
    }
    while (true) {
        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
        if ($replaced != $number) {
            $number = $replaced;
        } else {
            break;
        }
    }
    return $number;
}