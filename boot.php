<?php

namespace o;

error_reporting(E_ALL & ~E_NOTICE);

define('DS',DIRECTORY_SEPARATOR);
define('ROOT',realpath(__DIR__).DS);

spl_autoload_register(function ($class) {

    $class = str_replace("\\",DS,$class);

    include_once ROOT.'libs'.DS.$class.'.php';

    if ( method_exists( $class, 'init' ) ) {
        $reflection_class = new \ReflectionClass($class);
        if( in_array('o\StaticInit', $reflection_class->getInterfaceNames())) {
            $class::init();
        }
    }

});

include 'libs/aliases.php';



