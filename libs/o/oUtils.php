<?php
/**
 * Created by: i.
 * Date: 04.02.2015
 * Time: 16:32
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;


class iUtils {

    public static $_original_include_path;

    static function saveOriginalIncludePath() {
        self::$_original_include_path = get_include_path();
    }

    static function addIncludePath($dir) {

        if (!self::$_original_include_path) {
            self::$_original_include_path = get_include_path();
        }
        if (is_dir($dir)) {
            set_include_path(get_include_path().PATH_SEPARATOR.$dir);
        }

    }

    static function restoreOriginalIncludePath() {
        set_include_path(self::$_original_include_path);
    }

    static function removeIncludePath($dir) {
        $include_path = get_include_path();
        $include_path = str_replace($dir.PATH_SEPARATOR,'',$include_path);
        set_include_path($include_path);
    }


    static function glorify($string) {

        $string = self::humanize($string);
        $string = ucwords($string);

        return $string;
    }



    static function camelize($string) {

        $string = self::glorify($string);
        $string = str_replace(' ','',$string);

        return $string;
    }



    static function humanize($string) {
        $string = str_replace('_',' ',$string);
        return $string;
    }



    static function deCamelize($string) {


        if (strtolower($string) == $string) {
            return $string;
        }

        $string = lcfirst($string);
        $string = preg_replace_callback('([A-Z])',
            function ( $match ) {
                return '_'.strtolower($match[0]);
            },
            $string);


        return $string;
    }


} 