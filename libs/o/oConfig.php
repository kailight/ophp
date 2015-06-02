<?php

/**
 * @package i
 * @class iConfig
 * Class used for configuration of properties that are inaccessible before database connection
 * (database credentials come in mind first)
 * or have no point to be stored in database (logs)
 */

namespace o;

class iConfig {

    /**
     * @var string filename of one of .ini files in config dir
     */
    private static $_environment = null;
    /**
     * @var bool
     */
    private static $_initialized = false;
    /**
     * @var array filled from ini files
     */
    private static $_configs = array();


    static function init() {
    rec(__METHOD__);


        if ( self::$_initialized ) {
            return;
        }

        $dir = realpath(ROOT.'config');
        $dir = new iDir($dir);
        $files = $dir->scan();
        if (!is_array($files)) {
            throw new iException(iException::ERROR_CONFIG7,0);
        }
        foreach ($files as $file) {
            if ( $file->extension == 'ini' ) {
                $environment = $file->name;
                self::$_configs[$environment] = parse_ini_file($file->path, true);
                self::$_configs[$environment]['Database']['type'] = (!self::$_configs[$environment]['Database']['type']) ? 'mysql' : self::$_configs[$environment]['Database']['type'];
                self::$_configs[$environment]['Database']['type'] = (self::$_configs[$environment]['Database']['type'] == 'mysqli') ? 'mysql' : self::$_configs[$environment]['Database']['type'];

            }
        }

        if ( !self::$_environment ) {
            $environment = self::detectEnvironment();
        }

    self::$_initialized = true;
    return self::$_configs[$environment];
    }

    static function Admin() {
        return self::$_configs[self::$_environment]['Admin'];
    }

    static function WISPA() {
        return self::$_configs[self::$_environment]['WISPA'];
    }

    static function timeZone() {
        return self::$_configs[self::$_environment]['Timezone']['name'];
    }

    static function developerMode() {
        return self::$_configs[self::$_environment]['Developer']['mode'];
    }


    static function setEnvironment($environment) {

        self::$_environment = $environment;

    }


    static function getEnvironment() {

        if (!self::$_environment) {
            self::detectEnvironment();
        }

    return self::$_environment;
    }


    static function database( $environment=null ) {

        $environment = $environment ? $environment : self::$_environment;

    return self::$_configs[$environment]['Database'];
    }



    static function developer() {

    return self::$_configs[self::$_environment]['Developer'];
    }



    static function blacklist() {

        $blacklist = self::$_configs[self::$_environment]['Blacklist'];
        $blacklist = $blacklist['exclude_these_lists'];
        if ($blacklist) {
            $blacklist = explode(' ',$blacklist);
        } else {
            $blacklist = array();
        }

    return $blacklist;
    }



    static function whitelist() {

        $whitelist = self::$_configs[self::$_environment]['Whitelist'];
        $whitelist = $whitelist['include_only_these_lists'];
        if ($whitelist) {
            $whitelist = explode(' ',$whitelist);
        } else {
            $whitelist = array();
        }

    return $whitelist;
    }



    static function locations() {

        return self::$_configs[self::$_environment]['Locations'];
    }

    /**
     * Detects environment (App location) depending on server and file system location
     * This simplifies installation greatly since we don't have to reconfigure script on file transfer
     *
     * @return string environment codename
     * @throws iException
     */
    private static function detectEnvironment() {
    rec(__METHOD__);

        $environment = null;

        foreach (self::$_configs as $_environment=>$config) {
            self::$_configs[$_environment]['Location']['dir'] = rtrim($config['Location']['dir'],DS);
            self::$_configs[$_environment]['Location']['dir'] = $config['Location']['dir'].DS;
            rec(self::$_configs[$_environment]['Location']['dir']);
            if ( ROOT == self::$_configs[$_environment]['Location']['dir'] ) {
                $environment = $_environment;
                break;
            }
        }

        if ( $environment === null ) {
            msg("Need to configure App, ask Xander how");
            msg("!Terminating");
            throw new iException(iException::ERROR_CONFIG0,0);
        } else {
            message(__METHOD__." Detected environment: %s",array($environment));
        }

        /** @var string $environment;
         * using @ (STFU) in case of CLI
         */

        /*
        $environment = ( @$_SERVER['SERVER_NAME'] == 'envelope.local' )
            ? 'local' : $environment;
        $environment = ( @$_SERVER['SERVER_NAME'] == '50.194.120.201' )
            ? 'unknown' : $environment;
        $environment = ( dirname(__FILE__)  == 'Q:\envelope\libs\i' )
            ? 'local' : $environment;
        $environment = ( dirname(__FILE__)  == '/Library/Server/Web/Data/Sites/Default/xander/001/skynet/libs' )
            ? 'tece_001' : $environment;
        */



    self::$_environment = $environment;
    return $environment;
    }



    static function logs( $environment=null ) {

        $environment = $environment ? $environment : self::$_environment;

        $logFiles = self::$_configs[$environment]['Logs'];

        /*
        echo '<pre>'.__FILE__.':'.__LINE__.'<br>';
        var_export(realpath( $runLog ));
        echo '</pre>';
        die();
        */

        $errorLog   = $logFiles['error'];
        $runLog     = $logFiles['run'];

        if ( !realpath( $runLog ) ) {
            $runLog = ROOT.$runLog;
        }
        if ( !realpath( $errorLog ) ) {
            $errorLog = ROOT.$errorLog;
        }
        if ( !$runLog = realpath( $runLog ) ) {
            throw new iException(iException::ERROR_CONFIG4,0);
        }
        if ( !$errorLog = realpath( $errorLog ) ) {
            throw new iException(iException::ERROR_CONFIG5,0);
        }
        if ( is_file($errorLog) && !is_writable( $errorLog ) ) {
            chmod( $errorLog, 0777 );
        }
        if ( is_file($runLog) && !is_writable( $runLog ) ) {
            chmod( $runLog, 0777 );
        }


        $logFiles['error'] = $errorLog;
        $logFiles['run']   = $runLog;


    return $logFiles;
    }


}