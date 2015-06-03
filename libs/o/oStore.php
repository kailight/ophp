<?php
/**
 * Created by: i.
 * Date: 02.02.2015
 * Time: 1:26
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;


class oStore {

    /**
     * @var string|null
     */
    private $code = null;
    /**
     * @var string|null
     */
    private $name = null;
    /**
     * @var string|null
     */
    private $address = null;
    /**
     * @var string|null
     */
    private $user = null;
    /**
     * @var string|null
     */
    private $pass = null;
    /**
     * @var array
     */
    private $_settings = array();


    function __construct($code) {

        /**
         * check registry, its singleton
         */
        if (iStores::isInitialized()) {
            return iStores::get($code);
        }
        else {
            self::init($code);
        }

    }


    function init($code) {

        $this->setCode($code);

    }




    /**
     * @param string $setting
     * @param mixed $val
     */
    function setSetting($setting,$val) {


    $this->_settings[$setting] = $val;
    }



    function getSetting($setting) {


    return $this->_settings[$setting];
    }


    function getSettings() {
        return $this->_settings;
    }



    function setCode($code) {

        if (iStores::isValidCode($code)) {
            $this->code = $code;
        } else {
            throw new oException("Code $code is not valid code for iStore");
        }

    }



    function setName($name) {
        $this->name = $name;
    }


    function setAddress($address) {
        $this->address = $address;
    }



    function setUser($user) {
        $this->user = $user;
    }


    function setPass($pass) {
        $this->pass = $pass;
    }



    function getCode() {
        return $this->code;
    }


    function getName() {
        return $this->name;
    }


    function getAddress() {
        return $this->address;
    }


    function getUser() {
        return $this->user;
    }


    function getPass() {
        return $this->pass;
    }





    /**
     * @param $method string
     * @param $args array
     * @return mixed
     * @throws oException
     */
    function __call($method,$args=array()) {

        if ( strpos( $method,'get' ) === 0 ) {
            $methodPart = str_replace('get','',$method);
            $param = iUI::deCamelize( $methodPart );
            array_unshift($args,$param);
            $result = call_user_func_array( array($this,'getSetting'),$args);
        }

        elseif ( strpos( $method,'require' ) === 0 ) {
            $methodPart = str_replace('require','',$method);
            $param = iUI::deCamelize( $methodPart );
            array_unshift($args,$param);
            $result = call_user_func_array( array($this,'requireSetting'),$args);
        }

        else {
            throw new oException("iStore::$method() - unknown method" );
        }


    return $result;
    }



    function __get($property) {


        $camelized_property = iUi::camelize($property);
        $camelizedMethod = 'get'.$camelized_property;


        if (method_exists($this,$camelizedMethod)) {
            return call_user_func_array( array ( $this, $camelizedMethod ), array() );
        } else {
            try {
                return $this->getSetting($property);
            } catch (oException $e) {
                throw new oException("iStore->$property - not found" );
            }
        }

    }


} 