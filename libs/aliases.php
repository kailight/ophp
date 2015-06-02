<?php

namespace i;


    function ago($time) {
        return Xander::ago($time);
    }

    function q($query) {
        return iCore::query($query);
    }

    function rec($message,$data=array()) {
        \i\iException::log($message,$data);
        // \i\iException::log($message,$data);
    }

    function s() {
        return iApp::stopwatch();
    }

    function log($message,$data=array()) {
        rec($message,$data);
    }

    function info($message,$data=array()) {
        \i\iException::log($message,$data);
    }

    function message($message,$data=array()) {
        \i\iException::message($message,$data);
        // \i\iException::log($message,$data);
    }

    function msg($message,$data=array()) {
        message($message,$data);
    }

    function pr($var=null) {
        \i\iException::pr($var);
    }

    function prd($var=null) {

        \i\iException::prd($var);
    }

    function setExceptionHandler() {
        \set_exception_handler('\i\exceptionHandler');
    }

    function error( $message, $data=array() ) {
        throw new iException( $message, array ( 'level'=>1,'data'=>$data ) );
        // \i\iException::error($message,$data);
    }

    function warning($message,$data=array()) {
        // \i\iException::warning($message,$data);
        throw new iException( $message, array ( 'level'=>2, 'data'=>$data ));
        // \i\iException::error($message,$data);
    }

    function wtf() {

        $log = iException::getLog(iException::FORMAT_HTML);

    }

    /**
     * @param $exception iException
     */
    function exceptionHandler($exception) {

        \i\iException::handleException($exception);

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

