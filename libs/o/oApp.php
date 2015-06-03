<?php

namespace o;


/**
 * @property array items
 */
class oApp extends iCore {

    /**
     * @var \DateTime
     */
    public static $currentTime;

    /**
     * @var string
     */
    public static $settings = array();
    static $xml_file = '';
    static $xml = '';

    /**
     * @var array code_to_name
     */
    static $code_to_name = null;

    /**
     * @var int addedCounter
     */
    static $addedCounter = 0;

    /**
     * @var int deletedCounter
     */
    static $deletedCounter = 0;

    /**
     * @var array items
     */
    public static $items = array();

    function __construct() {

        // parent::iCore();
        // $this->settings = iSettings::getSettings();
        // $this->connector = new iRestConnector();
        // $this->setStartTime();
        // $this->setEndTime();

    }





    function requireServer() {

        if (!$this->server) {
            error("Server is not set");
        }

    }





    function run() {
    info("iApp::run()");


        self::$xml_file = ROOT.'data'.DS.'xml.xml';

        s();

        msg('Setting execution time to unlimited');
        ini_set('max_execution_time', 0); //300 seconds = 5 minutes, 0 - unlimited

        $steps = 'check_config,setTimezone,check_database,check_codeToName,getXML,parseXML,insertRecords,markForDeletion,addToList,removeFromList';
        $steps = explode(',',$steps);
        foreach ($steps as $step) {
            if ($this->$step()) {
                continue;
            } else {
                msg('!Terminating');
                break;
            }
        }
        self::finish();

    }


    function setTimezone() {
    msg('Setting timezone');

        date_default_timezone_set('America/New_York');
        $hardcoded_timezone = iConfig::timeZone();

        if ($hardcoded_timezone) {
            msg('Using timezone '.$hardcoded_timezone);
            $timezone = new \DateTimeZone($hardcoded_timezone);
            $now = new \DateTime(null,$timezone);
        } else {
            msg('Using server timezone');
            $now = new \DateTime();
            $now = $now->format('Y-m-d H:i:s');
            msg('Starting at '.$now);
            $now = new \DateTime(null);
        }
        $now = $now->format('Y-m-d H:i:s');
        msg('Set current time to '.$now);
        self::$currentTime = $now;

    return true;
    }

    function check_config() {
    msg('Checking configuration file');

        try {
            self::$config = iConfig::init();
        } catch (oException $e) {
            oException::handleException($e);
            return false;
        }


    msg('Using configuration file *'.iConfig::getEnvironment().'.ini*');
    return true;
    }


    function createDb() {

        $db_config = iConfig::database();

        msg('Trying to create database '.$db_config['database'].'');
        try {
            q("CREATE DATABASE `".$db_config['database']."`");
        } catch (oException $e) {
            msg('Could not create database '.$db_config['database'].'');
            return false;
            // oException::handleException($e);
        }

    }


    function check_database() {
        msg('Checking connection to database');

        $db_config = iConfig::database();


        try {
            self::connectToDatabase();
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
            $db_config = iConfig::database();
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
CREATE TABLE IF NOT EXISTS `{$db_config['table']}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Contact` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Digest` int(11) DEFAULT NULL,
  `ListCode` varchar(255) DEFAULT NULL,
  `ListName` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `delete` int(11) DEFAULT NULL,
  `ignore` int(11) DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf16;
HEREDOC;
                    } else if ($db_config['type'] == 'sqlite') {
                        $source = <<<HEREDOC
CREATE TABLE `{$db_config['table']}` (
  `id` INTEGER PRIMARY KEY,
  `Contact` VARCHAR DEFAULT NULL,
  `Email` VARCHAR DEFAULT NULL,
  `Digest` INTEGER DEFAULT NULL,
  `ListCode` VARCHAR DEFAULT NULL,
  `ListName` VARCHAR DEFAULT NULL,
  `created` DATETIME DEFAULT NULL,
  `delete` INTEGER DEFAULT NULL,
  `ignore` INTEGER DEFAULT NULL
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



    function check_codeToName() {

        msg('Checking for code_to_name.txt');
        try {
            $list_file = new iFile(ROOT.'data'.DS.'code_to_name.txt');
            if (!$list_file->exists) {
                msg('code_to_name.txt is not found, create empty file with this name and copy data from xls as text into it');
                msg('!Terminating');
                exit;
            }
            $list = $list_file->getContents();
            $rows = explode("\n",$list);
            self::$code_to_name = array();
            msg('code_to_name.txt found at *'.ROOT.'data*');
            foreach ($rows as $row) {
                $row = trim($row,'"');
                $cols = explode('","',$row);
                if (sizeof($cols) == 3) {
                    self::$code_to_name[$cols[0]] = $cols[2];
                }
            }
            if (sizeof(self::$code_to_name) == 0) {
                msg('!Error: no entries found in file code_to_name.txt');
                exit;
            }
            msg(sizeof(self::$code_to_name).' entries found in code_to_name.txt');
        } catch (oException $e) {
            msg('Something went wrong while parsing code_to_name.txt');
            return false;
        }

    return true;
    }


    function parseXML() {
    msg('Parsing retrieved XML');

        // $contents = $file->getContents();
        // $url = "http://dictionary.cambridge.org/pronunciation/british/".$word;

        $contents = self::$xml;
        $contents_start = strpos($contents,"<WISPAEMaiLists");
        $contents = substr($contents,$contents_start);
        $bad_end = substr($contents,strpos($contents,'</NewDataSet>'));
        $contents = str_replace($bad_end,'',$contents);
        $contents = ''."\n".$contents;
        $contents = preg_split('/<\/WISPAEMaiLists>/',$contents);
        $items = array();

        foreach ($contents as $n1=>$item) {
            preg_match_all('/<([A-z0-9]+)>(.+?)<\/\1>/',$item,$matches);
            $entry = array();
            foreach ($matches[1] as $n2=>$match) {
                $entry[$match] = $matches[2][$n2];
            }
            if ($entry) {
                $items[$n1] = $entry;
                $items[$n1]['ListName'] = self::$code_to_name[$items[$n1]['ListCode']];
            }
        }
        msg(sizeof($items). ' entries found in XML');

        foreach ($items as $n=>$item) {
            if (!$item['Email']) {
                unset($items[$n]);
            }
        }
        ksort($items);

        self::$items = $items;

    return true;
    }



    function markForDeletion() {
    msg('Marking existing records for deletion');

        $template = <<<HEREDOC
UPDATE records SET `delete` = 1 WHERE NOT (
*conditions*
)
HEREDOC;

        $conditions = array();
        $items = self::$items;
        foreach ($items as $item) {
            $conditions[] = "(`Contact` = \"{$item['Contact']}\" AND `ListCode` = \"{$item['ListCode']}\")\n";
        }
        $conditions = implode("OR\n",$conditions);
        $query = str_replace('*conditions*',$conditions,$template);
        try {
            q($query);
        } catch (oException $e) {
            self::finish($e);
        }

        msg('Marked for deletion records were deleted');

    return true;
    }

    function insertRecords() {
    msg('Adding records to database');

        msg('Would you like to mark these records as "ignore"?');
        msg('Type "y" or press <ENTER> to skip');
        $ignore = self::stndin();
        if ($ignore == 'y') {
            $ignore = 1;
        } else {
            $ignore = 0;
        }

        $fields = array('Contact','Email','Digest','ListCode','ListName','created','ignore');

        $items = self::$items;
        foreach ($items as $n=>$item) {
            msg('Inserting record #'.$n.' into database ('.$item['Email'].')');
            $query = 'INSERT INTO records (`'.implode('`, `',$fields).'`) VALUES ("'.implode('", "',$item).'","'.self::$currentTime.'",'.$ignore.')';
            try {
                q($query);
            }
            catch (oException $e) {
                msg('Some error happened while trying to insert record #'.$n.' into database ('.$item['Email'].')');
                msg("SQL query that caused problems is: ");
                msg($query);
                return false;
            }
        }

    return true;
    }


    function addToList() {
    msg('Adding members to list');

        $developer = iConfig::developer();
        $mode = $developer['mode'];

/*
To add members that do not want the digest option run this command.
./add_members -w n -a n listname -r path_to_temp_file

To add members that do want to receive digest emails please run this command.
./add_members –w n –a n listname –d path_to_temp_file
        */

        $tmp_file = ROOT.'data'.DS.'tmp.txt';
        msg('Using tmp file'.$tmp_file);
        $tmp_file = new iFile($tmp_file);
        if (!$tmp_file->exists()) {
            $tmp_file->create();
        }

        $blacklist = iConfig::blacklist();
        $whitelist = iConfig::whitelist();
        $whitelisted_counter = 0;
        $blacklisted_counter = 0;
        $remaining_counter = 0;

        $items = self::$items;

        if ($whitelist) {
            msg('Using WHITElist');
            msg('ONLY adding to these lists:');
            msg(implode(',',$whitelist));
            msg('Check if this is right and press <ENTER> to continue -- or stop the script');
            self::stndin();
                foreach ($items as $k=>$item) {
                    if ( in_array (strtolower($item['ListCode']),$whitelist) ) {
                        $remaining_counter++;
                    } else {
                        $whitelisted_counter++;
                        unset($items[$k]);
                    }
                }
                msg("Filtered out ".$whitelisted_counter." emails using whitelist, ".$remaining_counter." passed");
        } else if ($blacklist) {
            msg('Using BLACKlist');
            msg('NOT adding to these lists:');
            msg(implode(',',$blacklist));
            msg('Check if this is right and press <ENTER> to continue -- or stop the script');
            self::stndin();
            foreach ($items as $k=>$item) {
                if ( in_array (strtolower($item['ListCode']),$blacklist) ) {
                    $blacklisted_counter++;
                    unset($items[$k]);
                } else {
                    $remaining_counter++;
                }
            }
            msg("Filtered out ".$blacklisted_counter." emails using blacklist, ".$remaining_counter." passed");
        }

        $locations = iConfig::locations();
        $python_scripts_locations = $locations['python'];
        $python_scripts_locations = rtrim($python_scripts_locations,'/');
        $python_scripts_locations = rtrim($python_scripts_locations,'\\');
        $script_name = 'add_members';
        $script_location = $python_scripts_locations.DS.$script_name;

        msg('Working with emails WITHOUT Digest');
        foreach ($items as $item) {
            if ($item['Digest'] == 0) {
                $tmp_file->putContents($item['Email']);
                // $exec_string = 'python '.$script_location.' -w n -a n '.$item['ListName'].' -r '.$tmp_file->path;
                $exec_string = 'python '.$script_location.' -w n -a n -r '.$tmp_file->path.' '.$item['ListName'];
                if ($mode == 1) {
                    msg('NOT executing shell script *'.$exec_string.'*');
                } else {
                    msg('Executing shell script *'.$exec_string.'*');
                    exec($exec_string);
                }
                if (iConfig::getEnvironment() != "local") {
                    exec($exec_string);
                }
                msg('New record *'.$item['Email'].'* was added to NON-DIGEST list');
                self::$addedCounter++;
            }
        }

        msg('Working with emails WITH Digest');
        foreach ($items as $item) {
            if ($item['Digest'] == 1) {
                $tmp_file->putContents($item['Email']);
                $exec_string = 'python '.$script_location.' -w n -a n -d '.$tmp_file->path.' '.$item['ListName'];
                if ($mode == 1) {
                    msg('NOT executing shell script *'.$exec_string.'*');
                } else {
                    msg('Executing shell script *'.$exec_string.'*');
                    exec($exec_string);
                }
                msg('New record *'.$item['Email'].'* was added to DIGEST list');
                self::$addedCounter++;
            }
        }


    return true;
    }



    function removeFromList() {
        msg('Removing members from list');

        $developer = iConfig::developer();
        $mode = $developer['mode'];

        $tmp_file = ROOT.'data'.DS.'tmp.txt';
        msg('Using tmp file'.$tmp_file);
        $tmp_file = new iFile($tmp_file);
        if (!$tmp_file->exists()) {
            $tmp_file->create();
        }

        msg('!Removing records marked for deletion');
        $db = iConfig::database();
        $table = $db['table'];

        $locations = iConfig::locations();
        $python_scripts_locations = $locations['python'];
        $python_scripts_locations = rtrim($python_scripts_locations,'/');
        $python_scripts_locations = rtrim($python_scripts_locations,'\\');
        $script_name = 'remove_members';
        $script_location = $python_scripts_locations.DS.$script_name;

        $items = q('SELECT * FROM `'.$table.'` WHERE `delete` = 1');
        foreach ($items as $item) {
            self::$deletedCounter++;
            $exec_string = 'python '.$script_location.' -n -N '.$item['ListName'].' '.$item['Email'];
            if ($mode == 1) {
                msg('NOT executing shell script *'.$exec_string.'*');
            } else {
                msg('Executing shell script *'.$exec_string.'*');
                exec($exec_string);
                msg('Record marked for deletion ('.$item['Email'].') was removed from list '.$item['ListName']);
            }
            q('DELETE FROM `'.$table.'` WHERE `id` = '.$item['id']);
            msg('Record marked for deletion #'.$item['id'].' was removed from database');
        }

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

    function enterValidListCode() {

        msg('Enter ListCode (e.g. kenop) or press <ENTER> to skip');
        $ListCode = strtoupper(self::stndin());

        if ($ListCode) {
            if (self::isValidListCode($ListCode)) {
                return $ListCode;
            } else {
                msg("Invalid ListCode *$ListCode*");
                self::enterValidListCode();
            }
        } else {
            msg("Not using any ListCode");
            return '';
        }

    return $ListCode;
    }

    function getXML() {

        $wispa = iConfig::WISPA();

        $fields = "&securityKey=".$wispa['key'];
        $ListCode = self::enterValidListCode();


        if ($ListCode) {
            msg("Using ListCode *$ListCode* to request XML");
        }
        $fields .= '&ListCode='.$ListCode;

        // or
        /*
        $fields = array(
            'securityKey' => $wispa['key'],
        );
        if ($ListCode) {
            $fields['ListCode'] = $ListCode;
        }
        */

        $ch = curl_init();
        // $timeout = 5;
        $headers = array(
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Encoding: gzip, deflate",
            "Accept-Language: en-US,en;q=0.5",
            "Cache-Control: max-age=0",
            "Connection: keep-alive",
            // "Cookie: dnn_IsMobile=False; .ASPXANONYMOUS=eArRFKfD0AEkAAAAZWE3MjUwMGYtZWE3ZC00MmMxLTg2MmItMDZiNmY3MzQyNWIy0; DotNetNukeAnonymous=8b490b54-466e-4e2a-9410-f70ea082d9c1",
            "Host: www.wispa.org",
            "Referer: https://www.wispa.org/DesktopModules/NOAH_Clients/WISPA/EmailLIst.asmx?op=getEmailLists",
            "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:37.0) Gecko/20100101 Firefox/37.0",
        );

        $userAgent = 'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0';
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_URL,$wispa['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_REFERER, $wispa['referer']);
        curl_setopt ($ch, CURLOPT_CAINFO, ROOT."data".DS."cacert.pem");
        // curl_setopt($ch, CURLOPT_HEADER, true);
        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        // curl_setopt($ch, CURLOPT_FAILONERROR, true);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        msg('Sending curl request to '.$wispa['url']);

        $contents = curl_exec($ch);

        if (curl_errno($ch)) {
            msg ("Error: " . curl_error($ch));
            return false;
        } else {
            // Show me the result
            msg('Whoa, this xml is '.strlen($contents)." lines!");
        }
        curl_close($ch);

        $file = new iFile(self::$xml_file);
        if (!$file->exists()) {
            $file->create();
        }
        $file->putContents($contents);

        msg('Saved copy of retrieved xml to '.self::$xml_file);
        self::$xml = $contents;

    return true;
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

        $admin = iConfig::Admin();
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