<?php

namespace o;


/**
 * @property array items
 */
class oApp extends oCore {

    /**
     * @var \DateTime
     */
    public static $currentTime;

    /**
     * @var string
     */
    public static $settings = array();

    function __construct() {

        // parent::iCore();
        // $this->settings = iSettings::getSettings();
        // $this->connector = new iRestConnector();
        // $this->setStartTime();
        // $this->setEndTime();

    }


	static function init() {

		parent::init();
		self::run();


	}

	static function getClientIp() {

		if($_SERVER['HTTP_X_FORWARDED_FOR'])
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if($_SERVER['HTTP_X_FORWARDED'])
			$ip = $_SERVER['HTTP_X_FORWARDED'];
		else if($_SERVER['HTTP_FORWARDED_FOR'])
			$ip = $_SERVER['HTTP_FORWARDED_FOR'];
		else if($_SERVER['HTTP_FORWARDED'])
			$ip = $_SERVER['HTTP_FORWARDED'];
		else if($_SERVER['REMOTE_ADDR'])
			$ip = $_SERVER['REMOTE_ADDR'];
		else
			$ip = null;
		return $ip;
	}

    static function run() {
    info("iApp::run()");

    }

	static function getWebroot() {


		$protocol  = empty($_SERVER['HTTPS']) ? 'http' : 'https';
		$dir = dirname($_SERVER['PHP_SELF']);
		$host = trim($_SERVER['HTTP_HOST'],'/');
		if ($dir == '/' || $dir == '\\') {
			$webroot = $protocol.'://'.$host.'/';
		} else {
			$dir = trim($dir,'/');
			$webroot = $protocol.'://'.$host.'/'.$dir.'/';
		}


		return $webroot;

	}


	
    function check_config() {
    msg('Checking configuration file');

        try {
            self::$config = oConfig::init();
        } catch (oException $e) {
            oException::handleException($e);
            return false;
        }


    msg('Using configuration file *'.oConfig::getEnvironment().'.ini*');
    return true;
    }


    static function createDb() {

        $db_config = oConfig::database();

        msg('Trying to create database '.$db_config['database'].'');
        try {
            q("CREATE DATABASE `".$db_config['database']."`");
        } catch (oException $e) {
            msg('Could not create database '.$db_config['database'].'');
            return false;
            // oException::handleException($e);
        }

    }


    static function checkDatabase() {
        msg('Checking connection to database');

        $db_config = oConfig::database();


        try {
            oDatabase::connect();
            msg('Connected to database');
        } catch (oException $e) {
            msg('Couldn\'t connect to database: '.$e->getMessage());
            return false;
        }

        msg("Checking if database {$db_config['database']} exists");
        try {
            if ($db_config['type'] == 'mysql' || $db_config['type'] == 'mysqli') {
                $result = q("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$db_config['database']."'");
                if ($result) {
                    msg('Database '.$db_config['database'].' exists');
                } else {
                    self::createDb();
                }
            } else if ($db_config['type'] == 'sqlite') {
                if (file_exists(ROOT.DS.$db_config['database'])) {
                    msg('Database '.$db_config['database'].' exists');
                }
            }
        } catch (oException $e) {
            msg('Database '.$db_config['database'].' doesn\'t exist');
            // oException::handleException($e);
            self::createDb();
        }

        msg('Checking if table exists in database');
        try {
            $db_config = oConfig::database();
            $found = false;
            if ( $db_config['type'] == 'mysql' ) {
                $result = q("SHOW TABLES LIKE '" . $db_config['table'] . "'");
                if ($result) {
                    $found = true;
                }
            } else if ( $db_config['type'] == 'sqlite' ) {
                $result = q("SELECT name FROM sqlite_master WHERE type = 'table'");
                foreach ($result as $row) {
                    if ($row['name'] == $db_config['table']) {
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                msg('Table '.$db_config['table'].' doesn\'t exist, creating');
                try {
                    if ( $db_config['type'] == 'mysql' ) {
                    $source = <<<HEREDOC
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CurrentEmailAddress` varchar(255) DEFAULT NULL,
  `InviteKey` varchar(255) DEFAULT NULL,
  `InviteSent` int(11) DEFAULT NULL,
  `NewEmailAddress` varchar(255) DEFAULT NULL,
  `TransferHelp` varchar(255) DEFAULT NULL,
  `TransferComplete` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
HEREDOC;
                    } else if ($db_config['type'] == 'sqlite') {
                        $source = <<<HEREDOC
CREATE TABLE  IF NOT EXISTS `users` (
  `id` INTEGER PRIMARY KEY,
  `CurrentEmailAddress` VARCHAR DEFAULT NULL,
  `InviteKey` VARCHAR DEFAULT NULL,
  `InviteSent` INTEGER DEFAULT NULL,
  `NewEmailAddress` VARCHAR DEFAULT NULL,
  `TransferHelp` VARCHAR DEFAULT NULL,
  `TransferComplete` VARCHAR DEFAULT NULL,
);
HEREDOC;

                    }

                    q($source);
                    $found = false;
                    if ( $db_config['type'] == 'mysql' ) {
                        $result = q("SHOW TABLES LIKE '" . $db_config['table'] . "'");
                        if ($result) {
                            $found = true;
                        }
                    } else if ( $db_config['type'] == 'sqlite' ) {
                        $result = q("SELECT name FROM sqlite_master WHERE type = 'table'");
                        foreach ($result as $row) {
                            if ($row['name'] == $db_config['table']) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if (!$found) {
                        msg('Couldn\'t create table *'.$db_config['table'].'* in database, exiting');
                        return false;
                    }
                } catch (oException $e) {
                    msg('Some error happened while trying to create table '.$db_config['table'].'');
                    msg($e->getMessage());
                    return false;
                    // oException::handleException($e);
                }

            }
            msg('Table *'.$db_config['table'].'* exists');
        } catch (oException $e) {
            msg('Some error happened while checking for table in database');
            return false;
        }

    return true;
    }



    static function stndin() {
        return strtolower(trim(fgets(STDIN)));
    }

    function isValidListCode($ListCode) {
        if (self::$code_to_name[$ListCode]) {
            return true;
        }
    return false;
    }




    static function finish(oException $e=null) {
    message('iApp::finish()');

        msg(self::$addedCounter.' emails were added to lists');
        msg(self::$deletedCounter.' emails were removed from lists');
        // msg('Run took x seconds (todo)');

        if (!$e) {
            $status = 'success';
            msg('iApp::finish() success');
        } else {
            $status = 'failure';
            msg('iApp::finish() failure');
        }

        $admin = oConfig::Admin();
        $email = $admin['email'];
        $added = self::$addedCounter;
        $deleted = self::$deletedCounter;
        $started = self::$currentTime;
        $message = <<<HEREDOC
Run started at $started
{$added} emails were added to lists.
{$deleted} emails were removed from lists.
HEREDOC;

        mail ( $email , "iApp email lists script execution" , $message );

        $log = oException::getLogInFormat('text');
        oException::logRun();


        // oException::reset();


        /*
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // echo json_encode($data);
            // exit;
        }
        else {
            // prd($data);
        }
        */

        /*
        $this->run->stop();
        $this->run->setLog( $log );
        $this->run->setStatus( $status );
        $this->run->setException( $e );
        $this->run->save();
        */


        if ($e instanceof \Exception || $e instanceof oException) {
            oException::logException($e);
        }
        if ($e instanceof oException) {
            if ($e->getLevel() <= 1) {
                echo $e;
                throw $e;
            }
            // oException::handleException($e);
        }

    exit;
    // return $this->run;
    }






}