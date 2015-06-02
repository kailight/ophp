<?php
/**
 * Created by: Xander.
 * Date: 03.02.2015
 * Time: 10:30
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;

/**
 * Class i
 * @package i
 * serves as registry
 */
class o implements StaticInit {

    /**
     * data Storage
     *
     * @var array
     */
    static $_params = array();

    static $_initialized = false;

    /**
     * @var bool if page requested with ajax
     */
    static $_ajax = false;
    static $_flash = false;
    static $requireAuthorization = false;

    /**
     * @var iUser
     */
    static $User = null;

    const DEFAULT_PAGE   = 'info';
    const DEFAULT_ACTION = '';



    function __construct() {
        self::init();
    }


    static function init() {

        if (self::$_initialized) {
            return;
        }
        self::$_initialized = true;

        self::init_params();

        if (self::$requireAuthorization) {
            self::init_user();
        }


    }



    static function init_params() {


        self::$_ajax = self::detectAjax();
        self::$_flash = ( isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == 'Shockwave Flash' ) ? true : false;

        /**
         * @todo remove this hack
         */
        /*
        if (preg_match('/\/ajax$/',$_REQUEST['url'])) {
            self::$flash = true;
            $_REQUEST['url'] = preg_replace('/\/ajax/','',$_REQUEST['url']);
        }
        if (self::$flash) {
            header('Cache-Control: no-cache;');
            self::$ajax = true;
        }
        */

        $REQUEST = $_SERVER['REQUEST_URI'];
        $query = explode( '/', $_REQUEST['query'] );

        if ($query[0] == 'ajax') {
            self::$_ajax = true;
            array_shift($query);
        }
        if (self::$_ajax) {
            iUI::setLayout('ajax');
        }

        $page = ( !empty($query) && !empty($query[0]) ) ? $query[0] : self::DEFAULT_PAGE;
        self::setPage($page);
        rec('set $Page to '.$page);

        $action = !empty( $query[1] ) ? $query[1] : self::DEFAULT_ACTION;
        self::setAction($action);
        rec('set $Action to '.$action);


        /**
         * after we have the model and action, check for extra params
         */
        if ( sizeof($query) > 2 ) {
            $query = array_slice($query,2);
        }
        else {
            $query = array();
        }


        $http_query = parse_url($REQUEST);
        $REQUEST = $http_query['query'];
        parse_str($REQUEST,$params);

        $params = array_merge($query,$params);

        foreach ($params as $var=>$val) {
            self::setParam($var,$val);
        }

    }



    static function detectAjax() {
        return ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) ? true : false;
    }



    static function init_locations() {

    }


    static function init_user() {

        self::$User = $User = new iUser();

        if ( self::getParam('Logout') ) {
            $User->logout();
        }
        if ( !$User->isLogged() && !self::getParam('username') ) {
            self::setParam('login',null);
        }
        elseif ( !$User->isLogged() && self::getParam('username') ) {
            $User->login( self::getParam('username'), self::getParam('password') );
        } else {
            if ($User->isAdmin() || $User->isManager()) {
                // @todo
                // $name = $_SESSION['username'];
            }
        }

        if (!self::$User->isLogged()) {
            self::setPage("login");
        }
    }


    /**
     * @param $param
     * @param $default
     * @return null|string
     */
    static function getParam($param,$default='') {

        if ( self::$_params[$param] ) {
            return self::$_params[$param];
        }
        else if ($_REQUEST[$param]) {
            return $_REQUEST[$param];
        } else {
            return $default;
        }

    }

    static function setParam($name,$val) {
        rec("Param $name is $val");
        self::$_params[$name] = $val;
    }

    static function getPage($default='') {
        return self::getParam('Page',$default);
    }

    static function setPage($val) {
        self::setParam('Page',$val);
    }
    static function setAction($val) {
        self::setParam('Action',$val);
    }

    static function getAction($default='') {
        return self::getParam('Action',$default);
    }





    /**
     * @param string $script_name
     * @param string $interface
     * @throws iException
     * @return string
     */
    static function getScript($script_name='',$interface='php') {

        $interfaces = getFilesInDir(SKYNET_INTERFACE_RUN);
        $script_name = str_replace('.php','',$script_name);
        $script_name = $script_name.'.php';

        if ( in_array( $interface,$interfaces ) ) {
            $requested_interface_dir = SKYNET_INTERFACE_RUN.$interface.DS;
        }
        else {
            throw new iException("iUI::runScript() Unknown interface $interface",1);
        }

        $script_names = getFilesInDir($requested_interface_dir);
        if ( in_array( $script_name, $script_names ) ) {
            return $requested_interface_dir.$script_name;
        }
        else {
            throw new iException("iUI::runScript() Script $script_name not found ",1);
        }

    }


}

