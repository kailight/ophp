<?php


namespace o;

class iCore {

    /**
     * @var \mysqli | \SQLiteDatabase
     */
    static $db_link = null;
    static $config = array();
    static $configs = array();
    static $environment = 'unknown';
    static $executionTime = null;
    public static $currentTime = null;

    function iCore() {
        $this->init();
    }


    function init() {

        /*
        self::startSession();
        putenv("TZ=America/Chicago");
        self::stopwatch();
        rec('Setting max execution time to unlimited');
        ini_set('max_execution_time', 0); //300 seconds = 5 minutes, 0 - unlimited
        self::connectToDatabase();
        $this->setCurrentTime();
        */

    }



    function setCurrentTime() {

        $timezone = new \DateTimeZone('America/Chicago');
        $time = new \DateTime(null,$timezone);
        $time = $time->format('Y-m-d H:i:s');
        rec('setting current time to '.$time);
        $this->currentTime = $time;

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
        session_start();
    }









    /**
     * Connect to MYSQL DB
     */
    static function connectToDatabase() {
    rec('connecting to database');

        $database_config = iConfig::database();

        if ($database_config['type'] == "mysql") {
            self::$db_link = new \mysqli($database_config['host'], $database_config['user'], $database_config['pass']);
            if (self::$db_link->connect_errno) {
                msg ("Failed to connect to MySQL: (" . self::$db_link->connect_errno . ") " . self::$db_link->connect_error);
            }
            self::$db_link->select_db($database_config['database']);
            if (self::$db_link->connect_error) {
                throw new iException(self::$db_link->connect_error,0);
            }

            // $max_allowed_packet = self::$db_link->query( 'SELECT @@global.max_allowed_packet' )->fetch_array();
            // echo $max_allowed_packet[ 0 ];
            // self::query( 'SET @@global.max_allowed_packet = ' . 500 * 1024 * 1024 );
            // self::query( 'SET NAMES utf8' );
            // self::query( 'SET CHARACTER SET utf8' );
        } else if ($database_config['type'] == "sqlite") {
            if (phpversion() > '5.4' && phpversion() < '5.5') {
                self::$db_link = new \SQLite3(ROOT.$database_config['database']);
            } else {
                self::$db_link = new \SQLiteDatabase(ROOT.$database_config['database']);
            }
        }

    }

    /**
     * @param string $query
     * @param string $database
     * @return array
     * @throws iException
     */
    static function query( $query ) {

        if (!self::$db_link) {
            self::connectToDatabase();
        }

        $result = self::$db_link->query($query);
        if (!$result) {
            throw new iException( self::$db_link->error, 1);
        }

        $db_config = iConfig::database();

        $data = array();
        if ($db_config['type'] == 'mysql') {
            if ($result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
        }
        else if ($db_config['type'] == 'sqlite') {
            while($row = $result->fetchArray(1)){
                $data[] = $row;
            }
        }

    return $data;
    }


}