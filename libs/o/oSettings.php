<?php

namespace o;

class oSettings {

    static $settings = array();


    /**
     * Grabs servers settings from xOver via include
     * caches in self::$settings
     *
     * @return array
     */
    static function getServers() {

        // $q = 'SELECT * FROM servers WHERE name LIKE "%'.$server.'%" OR code LIKE "%'.$server.'%"';
        if (empty(self::$settings['servers'])) {
            $servers = include XOVER.'eatme'.DS.'stores.php';
        } else {
            $servers = self::$settings['servers'];
        }
        foreach ($servers as $code=>$server) {
            $servers[$code]['public.appid']  = 'com.tececigs.skynet';
            $servers[$code]['email']         = 'webmaster@tececigs.com';
            $servers[$code]['private.appid'] = '5ca2df56-7ba3-4b2e-a578-dcce7fe2f69e';
        }
        self::$settings['servers'] = $servers;


    return $servers;
    }



    static function get() {
    rec('iSettings()::get()');

        if (self::$settings) {
            return self::$settings;
        }

        $q = 'SELECT * FROM settings';
        try {
            $settingsFromDB = q($q);
        } catch (oException $e) {
            $message = $e->getLastMessage()->message;
            // @ToDo preg match
            if (stristr($message,'Table') && stristr($message,"doesn't exist")) {
                return self::$settings;
            } else {
                oException::handleException($e);
            }
        }

        foreach (@$settingsFromDB as $setting) {
            self::$settings[$setting['group']] = is_array( self::$settings[$setting['group']] ) ? self::$settings[$setting['group']] : array();
            self::$settings[$setting['group']][$setting['name']] = $setting;
        }

        self::getServers();
        self::getCoreSettings();


    return self::$settings;
    }


    static function getCoreSettings() {

        self::$settings['database'] = iConfig::database();
        self::$settings['logs']     = iConfig::logs();

    }




    static function createServer($server) {
        $q = <<<HEREDOC
INSERT INTO servers (code,name,address,user,pass)
VALUES ("{$server['code']}","{$server['name']}","{$server['address']}","{$server['user']}","{$server['pass']}")
HEREDOC;
    q($q);
    self::getServers();
    }



    static function deleteServer($server) {
        $q = "SELECT * FROM servers WHERE code = '{$server['code']}'";
        $result = q($q);
        if ($result && $result[0]) {
            $q = "DELETE FROM servers WHERE code = '{$server['code']}'";
            q($q);
        }
        self::getServers();
    }



    static function saveServer($server) {
        $q = <<<HEREDOC
UPDATE servers SET
code = "{$server['code']}",
name = "{$server['name']}",
address = "{$server['address']}",
user = "{$server['user']}",
pass = "{$server['pass']}"
WHERE code = "{$server['code']}"
HEREDOC;
        q($q);
        self::getServers();
    }

}


?>