<?php

namespace o;

/**
 * oException
 *
 * @see test
 *
 * @category   i
 * @package    i
 */
class oException extends \Exception {

    static private $_messages           = array();

    /**
     * @var oMessage
     */
    private $_this_message       = null;
    static public  $_errors      = array();
    static private $_initialized = array();
    static public  $cli          = false;
    static public  $errorLogFile = null;
    static public  $runLogFile   = null;
    static public  $data         = array();

    /**
     * @var \DateTime
     */
    static public  $now;

    const ERROR_UNKNOWN                  = "Unknown error";
    const ERROR_UNLOGGABLE               = "Uncaught Exception. Could not log. Ask tech guy to fix.";
    const ERROR_LDAP0                    = "Unknown ldap error";
    const ERROR_LDAP19                   = "Unable to bind to server: Constraint violation";
    const ERROR_CONFIG0                  = "Could not detect environment";
    const ERROR_CONFIG1                  = "Failed to getDatabaseConfig()";
    const ERROR_CONFIG2                  = "Call to getDatabaseConfig() before detectEnvironment()";
    const ERROR_CONFIG3                  = "Call to getLogfile() before detectEnvironment()";
    const ERROR_CONFIG4                  = "Run log file not found";
    const ERROR_CONFIG5                  = "Error log file not found";
    const ERROR_CONFIG6                  = "Could not get log files from oConfig";
    const ERROR_CONFIG7                  = "No ini files are found in config dir";
    const ERROR_CURL                     = "Unknown curl error %s";
    const ERROR_STUB                     = "STUB";
    const ERROR_TEMPLATE_FILE_NOT_FOUND  = "Template file %s not found";
    const ERROR_REST_CANT_CONNECT        = "Couldn't connect to server %s";
    const ERROR_UNDER_CONSTRUCTION       = "This part of i is under construction";
    const ERROR_DEFAULT_LAYOUT_404       = "Default layout not found at *%s*, please consult your psychiatrist";
    const ERROR_DEFAULT_THEME_404        = "Default theme not found at *%s*, please murder your psychiatrist";



    const DEFAULT_CODE                    = "ERROR_UNKNOWN";
    const FORMAT_HTML    = "html";
    const FORMAT_TEXT    = "text";
    const FORMAT_JSON    = "json";
    const FORMAT_PHP     = "php";


    const DUMP_CSS_CLASS = "oExceptionDump";

    /**
     * @var string one of FORMAT_HTML
     */
    static private $_format = null;

    /**
     * @see   errors.ini
     * @param string $message message or error code
     * @param mixed $data (level,code,$data to fit into message)
     */
    public function __construct($message='', $data = 1) {

	    /**
	     * Here we set level, if level is undetected it will result in fatal error
	     */
	    $level = 0;
	    if (is_numeric($data)) {
		    $level = $data;
	    }

	    if (is_array($data)) {
		    if ( in_array ('level', array_keys($data) ) ) {
			    $level = $data['level'];
			    unset( $data['level'] );
		    }
		    if (is_array($data['data'])) {
			    $data = $data['data'];
		    }
	    }

	    /**
	     * We need this to avoid infinite loop
	     */
	    if (!self::$_initialized && $level == 0) {
		    self::finish($message);
	    }

        $this->init($message,$data);
        parent::__construct($message);

        $message = self::addMessage($message,$level,$data);
        $this->_this_message = $message;

        if ($level == 0) {
            self::finish();
        }

    }



    public static function reset() {
        self::$_messages = array();
    }


    public function getLevel() {
        return $this->getThisMessage()->getLevel();
    }


    function getiCode() {
        return $this->getThisMessage()->getCode();
    }


    static function getRunLog() {
        return self::getLogInFormat(self::FORMAT_TEXT);
    }



    static function logRun($run_number=null) {

        $runLogFile = self::$runLogFile;

        if ($run_number) {
            $run_number = ' Run #'.$run_number;
        }

        if ( $runLogFile ) {
            // file_put_contents($runLogFile,'===='.$run_number.' ('.self::$now->format('Y-m-d H:i:s').") ====\n".self::getLogInFormat('text')."\n",FILE_APPEND);
            // file_put_contents($runLogFile,self::getLogInFormat('text')."\n",FILE_APPEND);
            rec('Writing log to '.$runLogFile);
            file_put_contents($runLogFile,self::getLogInFormat('text'));
        }


    }

    static function logException($exception=null) {

        if (!$exception) {
            $exception = $this;
        }

        $errorLogFile = self::$errorLogFile;
        
        if ( !$errorLogFile ) {
            echo self::ERROR_UNLOGGABLE;
            exit;
        } else {
            file_put_contents($errorLogFile, '==== ' . self::$now->format('Y-m-d H:i:s') . " ====\n" . $exception . "\n"."", FILE_APPEND);
        }

    }


    private static function init() {

        if (self::$_initialized) {
            return;
        }

        self::$now = new \DateTime();
        self::_detectFormat();

        try {
            $logs = oConfig::logs();
        } catch ( oException $e ) {
            self::addMessage( self::ERROR_CONFIG6, 0);
            self::finish();
        }
        if ( !$logs['error'] ) {
            self::addMessage( self::ERROR_CONFIG5, 0);
            self::finish();
        }
        if ( !$logs['run'] ) {
            self::addMessage( self::ERROR_CONFIG4, 0);
            self::finish();
        }

        self::$errorLogFile     = $logs['error'];
        self::$runLogFile       = $logs['run'];

        self::$_initialized = true;

    }

    /**
     * @param $message
     * @param $level
     * @param array $data
     * @return oMessage
     */
    private static function addMessage($message,$level,$data=array()) {

        /**
         * Here we set code, if code is undetected it will result in DEFAULT_CODE
         */
        $code = self::getErrorCodeByMessage($message);
        if ($code == self::DEFAULT_CODE) {
            if ($_message = self::getErrorMessageByCode($message)) {
                $code = $message;
                $message = $_message;
            }
        }
        $skynet_message = self::makeMessage($message,$code,$level,$data);
        self::$_messages[] = $skynet_message;

    return $skynet_message;
    }



    private static function makeMessage($message,$code,$level,$data) {

        try {
            $message = new oMessage($message,$code,$level,$data);
        } catch (\Exception $e) {
            echo $e;
            exit;
        }

    return $message;
    }





    public function getData() {


    return $this->getThisMessage()->getData();
    }



    public static function finish($exception_text='') {

        if ( self::$_format == self::FORMAT_HTML ) {
            echo self::$now->format('Y-m-d H:i:s')."<br>";
            echo "i quit unexpectedly.<br>" . self::getLastError()."<br>";
            if (self::$errorLogFile) {
                echo "Check error log at <b>".self::$errorLogFile."</b><br>";
            }
            if (self::$runLogFile) {
                echo "Check run log at <b>".self::$runLogFile."</b><br>";
            }
        } elseif ( self::$_format == self::FORMAT_TEXT ) {
            echo self::$now->format('Y-m-d H:i:s')."\n";
            echo "i quit unexpectedly.\n" . self::getLastError().PHP_EOL;
            if (self::$errorLogFile) {
                echo "Check error log at *" . self::$errorLogFile."* ".PHP_EOL;
            }
            if (self::$runLogFile) {
                echo "Check run log at *".self::$runLogFile."*". PHP_EOL;
            }
        } elseif ( self::$_format == self::FORMAT_PHP ) {
            echo self::$now->format('Y-m-d H:i:s')."\n";
            echo "i quit unexpectedly.\n" . self::getLastError();
            if (self::$errorLogFile) {
                echo "Check error log at *" . self::$errorLogFile."* \n";
            }
            if (self::$runLogFile) {
                echo "Check run log at *".self::$runLogFile."* \n";
            }
        } else {
            echo "i quit unexpectedly.\n" . self::getLastError();
        }

        echo $exception_text;
        self::logRun();
        exit;

    }


    /**
     * @return oMessage
     */
    public function getThisMessage() {

    return $this->_this_message;
    }



    /**
     * @return oMessage
     */
    public static function getLastMessage() {

        foreach (self::$_messages as $message) {
            $last_message = $message;
        }

    return $last_message;
    }



    /**
     * @return oMessage
     */
    public static function getLastWarning() {

        foreach (self::$_messages as $message) {
            if ($message->getCode() == 2) {
                $last_warning = $message;
            }
        }

    return $last_warning;
    }

    /**
     * @return oMessage
     */
    private static function getLastError() {

        /** @var oMessage $message */
        foreach (self::$_messages as $message) {
            if ($message->getLevel() == 0 || $message->getLevel() == 1) {
                $last_error = $message;
            }
        }

    return $last_error;
    }



    private static function _detectFormat() {

        if ( !self::$_format ) {
            if (self::isCli()) {
                $format = self::FORMAT_TEXT;
            } else {
                $format = self::FORMAT_HTML;
            }
        } else {
            $format = self::$_format;
        }

    self::setFormat($format);
    }



    /**
     * Use this method to set messages format
     * As this method also sets format for all messages
     *
     * @param $format
     * @return bool
     */
    private static function setFormat($format) {

        $formats = array(
            self::FORMAT_TEXT,
            self::FORMAT_HTML,
            self::FORMAT_JSON,
            self::FORMAT_PHP
        );

        if ( !in_array ($format,$formats) ) {
            trigger_error('oException::setFormat() unknown format '.$format, E_USER_WARNING);
            return false;
        }

        self::$_format = $format;
        oMessage::$_format = self::$_format;

    return true;
    }


    private static function getErrorCodeByMessage($message) {

	    if (__NAMESPACE__) {
		    $namespace = '\\' . __NAMESPACE__;
	    }
        $reflection = new \ReflectionClass($namespace.'\oException');
        $constants = $reflection->getConstants();
        $codes = array_flip( $constants );

        if ( @$codes[$message] ) {
            return $codes[$message];
        }


    return self::DEFAULT_CODE;
    }



    private static function getErrorMessageByCode( $code=self::DEFAULT_CODE ) {

        if ( @self::$_errors[$code] ) {
            return self::$_errors[$code];
        }

    }





    public function __toString() {

        $string = parent::__toString();
        if ( self::$_format == self::FORMAT_HTML ) {
            $string = nl2br($string);
        }
        return $string;

    }


    /**
     * @return string $log
     */
    public static function getLog() {

        $log = '';
        $messages = array();

        /** @var oMessage $message */
        foreach (self::$_messages as $message) {
            $messages[] = (string) $message;
        }

        if ( self::$_format == self::FORMAT_HTML ) {
	        $class = self::DUMP_CSS_CLASS;
	        $log    .= <<<HEREDOC

<style type="text/css">
.$class { color: #666 }
.$class em { color: #000; font-style: normal }
.$class strong { color: #960 }
</style>

HEREDOC;

            $log    .= "<pre class='".self::DUMP_CSS_CLASS."'>";
            $log    .= implode("<br>",$messages);
            $log    .= "</pre>";
        }
        if ( self::$_format == self::FORMAT_PHP ) {
            foreach ($messages as $k=>$message) {
                $messages[$k] = unserialize($message);
            }
            $log = serialize($messages);
        }
        if ( self::$_format == self::FORMAT_TEXT || !self::$_format ) {
            $log = implode($messages,"\n");
        }

    return $log;
    }



    static function removeObjects(&$array) {

        if (!is_array($array)) return $array;

        foreach ( $array as &$v ) {
            if (is_object($v)) {
                $v = get_class($v);
            }
            if (is_array($v)) {
                self::removeObjects($v);
            }
        }
    }



    /**
     * @param mixed $var
     */
    static function pr($var) {

        self::init();

        $backtrace = debug_backtrace();
        $index = 0;

        self::removeObjects($backtrace);



        $rollbacks = array('pr','prd',__NAMESPACE__.'\pr',__NAMESPACE__.'\prd','eval');
        foreach ( $backtrace as $index=>$entry ) {
            if (in_array($entry['function'],$rollbacks)) {
                $file = $backtrace[$index]['file'];
                $line = $backtrace[$index]['line'];
                unset($backtrace[$index]);
            } else {
                break;
            }
        }


        ksort($backtrace);
        $caller = array_shift($backtrace);
        $caller['full_path'] = $caller['file'];
        if ($line) {
            $caller['line'] = $line;
        }
        if ($file) {
            $caller['file'] = $file;
        }

        /*
        $caller = $debug_stack[2];
        echo '<pre>'; var_export($caller);
        die();
        */


        /**
         * @todo
         * Check if errors are allowed to be output, and allowed level
         */
        $allowed = true;


        if (!$allowed) {
            $message = 'Application Error';
            $caller = array('file'=>'','line'=>'');
        }


        if ($caller['file']) {
            $caller['file'] = str_replace(ROOT,'',$caller['file']);
            $message .= '*File*: '.$caller['file']."\n";
        }
        if ($caller['line']) {
            $message .= '\n*Line*: '.$caller['line']."\n";
        }

        if ($caller['object']) {
            $class_or_method = '$'.$caller['class'].'->';
        }
        else if ($caller['class']) {
            $class_or_method = $caller['class'].'::';
        }

        if ($caller['args']) {
            foreach ( $caller['args'] as $argument ) {
                $arguments[] = var_export($argument,true)."";
            }
        }

        if (!empty($caller['function'])) {
            $message .= "*Method*: ".$class_or_method.'';
            if (!empty($arguments)) {
                $message .= $caller['function']."(".implode($arguments,"").");\n";
            } else {
                $message .= $caller['function']."();\n";
            }
        }



        $message = oMessage::formatMessage($message);

        if ( self::$_format == self::FORMAT_HTML ) {
            $log     = "<pre class='".self::DUMP_CSS_CLASS."'>";
            if (is_string($var)) {
                $log    .= htmlspecialchars($var);
            } else {
	            if ($var instanceof Exportable) {
		            $log    .= htmlspecialchars($var->__get_state());
	            } else {
		            $log    .= htmlspecialchars(var_export($var,true));
	            }
            }
            // $log .= "\n<a href='file:///{$caller['full_path']}'>Click to open</a>\n\n (You may need browser plugin like 'Local Filesystem Links' for FireFox</pre>";
            $log .= "</pre>";
        }
        elseif ( self::$_format == self::FORMAT_TEXT ) {
	        if ($var instanceof Exportable) {
                $log = $var->__get_state();
	        } else {
		        $log = var_export($var,true);
	        }
        }
        elseif ( self::$_format == self::FORMAT_PHP ) {
            $log = serialize($var);
        }
        else {
            $log = (string) $var;
        }
        $log = $message.$log;

    echo $log;
    }





    static function getLogInFormat($format) {

        if (!self::$_format) {
            self::_detectFormat();
        }

        $old_format = self::$_format;
        if (self::setFormat($format)) {
            $log = self::getLog();
            self::setFormat($old_format);
            return $log;
        }

    }



    static function prd( $var=null ) {

        self::pr($var);
        echo self::getLog();
        exit;

    }

    static function isCli() {
        if (php_sapi_name() == "cli") {
            self::$cli = true;
        } else {
            self::$cli = false;
        }
    return self::$cli;
    }



    /**
     * LOGGING METHODS
     */

    /**
     * @param $message string
     * @param array $data
     */
    static function message($message,$data=array()) {
        $message = self::addMessage($message,3,$data);
        if (self::isCli()) {
            echo s().' '.$message.PHP_EOL;
        }
    }

    /**
     * @param $message string
     * @param $data array
     */
    static function log($message,$data=array()) {
        $message = self::addMessage($message,4,$data);
        /*
        if (self::isCli()) {
            echo $message.PHP_EOL;
        }
        */
        // throw new oException( $message, array ('level'=>2,'data'=>$data) );
    }




    static function error($message,$data=array()) {
        throw new oException($message,array('level'=>1,'data'=>$data));
        //
        // prd((string) $error);

    }



    static function warning($message,$data=array()) {
        self::addMessage($message,2,$data);
        // throw new oException( $message, array ('level'=>3,'data'=>$data) );
        // prd((string) $error);
    }


    static function handleException($exception) {

        if ($exception instanceof oException) {
            $level = $exception->getLevel();
            if ($level == 1 || $level == 0) {
                $exception->logException((string)$exception);
                $exception->finish((string) $exception);
            } else {
                $lastMessage = (string) $exception->getThisMessage();
                if ($exception->isCli()) {
                    echo $lastMessage;
                } else {
                    echo 'Uncaught oException<br>', $lastMessage;
                    // $exception->finish();
                    // do nothing, exception is logged
                }
            }
        } else {
            echo 'Uncaught '.$exception;
            exit;
            $exception->finish();
        }

    }

}





