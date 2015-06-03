<?php

namespace o;

class oStores {



    /**
     * @var array
     */
    private static $xover_stores = array();
    /**
     * @var iStore[] $stores
     */
    private static $stores = array();
    private static $_initialized = array();


    function __construct() {

        self::init();

    }




    /**
     * @param $code
     * @return bool
     */
    static function isValidCode($code) {


    return self::isValidStore($code);
    }




    static function init() {

        if ( self::$_initialized ) {
            return;
        }


        self::loadStoresFromXover();

        $stores = array();
        foreach ( self::$xover_stores as $xover_store ) {
            $store = new iStore( $xover_store['code'] );
            $store->setName         ( $xover_store['name'] );
            $store->setAddress      ( $xover_store['address'] );
            $store->setUser         ( $xover_store['user'] );
            $store->setPass         ( $xover_store['pass'] );
            $stores[] = $store;
        }

        self::$stores = $stores;

        self::$_initialized = true;

        self::setSettings();



    return;
    }



    static function isInitialized() {
        return self::$_initialized;
    }



    static function loadStoresFromXover() {

        try {
            self::$xover_stores = include XOVER . 'eatme' . DS . 'stores.php';
        } catch (oException $e) {
            oException::handleException($e);
        }

    }


    /**
     * @return iStore[]
     */
    static function getStores() {
    self::init();


    return self::$stores;
    }


    /**
     * @param string $code
     * @throws oException
     */
    static function get($code) {
    self::init();

        $found_store = false;
        foreach (self::$stores as $store) {
            if ($store->getCode() == $code) {
                $found_store = $store;
            }
        }

        if (!$found_store) {
            throw new oException("iStores::get($code) - Store with $code doesn't exist",1);
        }

    return $found_store;
    }

    /**
     * Ah this function prevents initialization loop
     */
    static function defend() {

    }


    static function setSettings() {
    self::init();

        $result = q('SELECT * FROM stores_settings');
        foreach ($result as $row) {
            $store = self::get($row['code']);
            $store->setSetting($row['property'],$row['value']);
        }

    }


    /**
     * @param $setting
     * @return iStore[] array
     */
    static function getStoresValidFor($setting) {
    self::init();

        $stores = array();
        foreach (self::$stores as $store) {
            if ($store->getSetting($setting) ) {
                $stores[] = $store;
            }
        }

    return $stores;
    }


    static function isValidStore($code) {
    // not initializing!

        if (!self::$xover_stores) {
            self::loadStoresFromXover();
        }

        foreach (self::$xover_stores as $store) {

            if( $store['code'] == $code ) {
                return true;
            }

        }

    return false;
    }


}