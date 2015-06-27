<?php


namespace o;

class oCore implements StaticInit {

	/**
	 * @var array DataStorage
	 */
	static $_params = array();
    static $config = array();
    static $environment = 'unknown';
    static $executionTime = null;

	const DEFAULT_PAGE   = '';
	const DEFAULT_ACTION = '';
    public static $currentTime = null;

	static $_initialized = false;
	/**
	 * @var bool if page requested with ajax
	 */
	static $_ajax = false;
	static $_flash = false;
	static $requireAuthorization = false;

	/**
	 * @var oUser
	 */
	static $User = null;

	/**
	 * @var oDatabase
	 */
	public static $database;

	/**
	 * @var Array collection of all POST/GET params that are not action or page
	 */
	private static $__params;


	function iCore() {
        $this->init();
    }


    static function init() {

	    if (self::$_initialized) {
		    return;
	    }
	    self::$_initialized = true;

	    self::initParams();
	    self::initUser();

        self::startSession();
	    self::setTimezone();
        self::stopwatch();
        rec('Setting max execution time to unlimited');
        ini_set('max_execution_time', 0); //300 seconds = 5 minutes, 0 - unlimited
        self::$database = o::init('oDatabase');

    }





	static function setTimezone() {

		$hardcoded_timezone = oConfig::timeZone();

		if ($hardcoded_timezone) {
			msg('Using timezone '.$hardcoded_timezone);
			date_default_timezone_set($hardcoded_timezone);
			putenv("TZ=".$hardcoded_timezone);
			$timezone = new \DateTimeZone($hardcoded_timezone);
//			$now = new \DateTime(null,$timezone);
		} else {
/*
			msg('Using server timezone');
			$now = new \DateTime();
			$now = $now->format('Y-m-d H:i:s');
			msg('Starting at '.$now);
			$now = new \DateTime(null);
*/
		}
//		$now = $now->format('Y-m-d H:i:s');
		msg('Set current time to '.$now);
//      self::$currentTime = $now;




	}


    static function stopwatch() {

        if(!self::$executionTime) {
            // rec('Stopwatch started');
            self::$executionTime = microtime(true);
        }
        else {
            // rec('Stopwatch stopped');
            return str_pad( (string) round((microtime(true)-self::$executionTime),3), 5, '0');
        }
    }



    static function startSession() {
    rec(__CLASS__,__METHOD__,'Starting session');

        session_start();
    }




	static function initUser() {

		if (self::$requireAuthorization) {
			self::$User = new oUser();
		}

	}



	static function initParams() {


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
			// @todo
			// header("Content-type: application/json");
			// oUI::setLayout('ajax');
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
		parse_str($REQUEST,$data);

		$params = array_merge($query,$data);

		$data = $_REQUEST;
		unset($data['query']);

		$params = array_merge($params,$data);

		foreach ($params as $var=>$val) {
			self::setParam($var,$val);
		}

		self::$__params = $params;

	}



	static function detectAjax() {
		return ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) ? true : false;
	}



	static function getParams() {
		return self::$__params;
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


}