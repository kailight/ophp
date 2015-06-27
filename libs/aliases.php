<?php

namespace o;


    function ago($time) {
        return Xander::ago($time);
    }

    /**
     * @param $query string|oQuery
     *
     * @return oDatabaseResult|null
     */
    function q($query) {
	    return oApp::$database->query($query);
    }

    function rec($message,$data=array()) {
        \o\oException::log($message,$data);
        // \o\oException::log($message,$data);
    }

    function s() {
        return oApp::stopwatch();
    }

    function log($message,$data=array()) {
        rec($message,$data);
    }

    function info($message,$data=array()) {
        \o\oException::log($message,$data);
    }

    function message($message,$data=array()) {
        \o\oException::message($message,$data);
        // \o\oException::log($message,$data);
    }

    function msg($message,$data=array()) {
        message($message,$data);
    }

    function pr($var=null) {
        \o\oException::pr($var);
    }

    function prd($var=null) {

        \o\oException::prd($var);
    }

    function setExceptionHandler() {
        \set_exception_handler('\o\exceptionHandler');
    }

    function error( $message, $data=array() ) {
        throw new oException( $message, array ( 'level'=>1,'data'=>$data ) );
        // \o\oException::error($message,$data);
    }

    function warning($message,$data=array()) {
        // \o\oException::warning($message,$data);
        throw new oException( $message, array ( 'level'=>2, 'data'=>$data ));
        // \o\oException::error($message,$data);
    }

    function wtf() {

        $log = oException::getLog(oException::FORMAT_HTML);

    }

    /**
     * @param $exception oException
     */
    function exceptionHandler($exception) {

        \o\oException::handleException($exception);

    }




/*
    function deCamelize($string) {
        return iUI::deCamelize($string);
    }


    function underscorize($string) {
        return iUI::deCamelize($string);
    }


    function prd($var=null) {
        return i\prd($var);
    }

    function pr($var) {
        return i\pr($var);
    }

    function info($message,$data=array()) {
        return i\info($message,$data);
    }

    function rec($message,$data=array()) {
        return i\rec($message,$data);
    }

    function msg($message,$data=array()) {
        return i\msg($message,$data);
    }

    function message($message,$data=array()) {
        return i\message($message,$data);
    }

    function xml2array($xml) {
        return i\Xander::xml2array($xml);
    }

    function error($message,$data=array()) {
        return i\error($message,$data);
        // return i\Xander::error($message);
    }

    function warning($message,$data=array()) {
        return i\warning($message,$data);
    }

    function ago($time) {
        return i\Xander::ago($time);
    }

/*
function getLog() {
    return i\Xander::getLog();
}

*/

