<?php


class oObject {

    protected $_data;

    function __construct() {

    }

    function __get($var) {

        if ($this->_data[$var]) {
            return $this->_data[$var];
        }

    }



    function __set($var,$val) {

        $this->_data[$var] = $val;

    }





}




