<?php
/**
 * Created by: i.
 * Date: 09.02.2015
 * Time: 22:58
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;


class iParams {


    /**
     * @param $param
     * @param $default
     * @return null|string
     */
    function getParam($param,$default='') {
        if ($_REQUEST[$param]) {
            return $_REQUEST[$param];
        } else {
            return $default;
        }

    return null;
    }



    /**
     * @param $param
     * @param $default
     * @return string
     * @throws iException
     */
    function requireParam( $param ) {

        if (!$_REQUEST[$param]) {
            throw new iException("Required param $param is missing");
        } else {
            return $_REQUEST[$param];
        }

    }


    /**
     * @param $method string
     * @param $args array
     * @return mixed
     */
    function __call($method,$args) {


        if ( strpos( $method,'get' ) === 0 ) {
            $methodPart = str_replace('get','',$method);
            $param = iUtils::deCamelize( $methodPart );
            array_unshift($args,$param);
            $result = call_user_func_array( array($this,'getParam'),$args);
        }

        elseif ( strpos( $method,'require' ) === 0 ) {
            $methodPart = str_replace('require','',$method);
            $param = iUtils::deCamelize( $methodPart );
            array_unshift($args,$param);
            $result = call_user_func_array( array($this,'requireParam'),$args);
        }


    return $result;
    }

} 