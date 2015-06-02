<?php
/**
 * Created by: i.
 * Date: 04.02.2015
 * Time: 8:33
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;


use Dumbo\Dumbo;

class iTemplates implements StaticInit {

    static private $_iteration = 0;
    /**
     * @var iTemplate
     */
    static private $_current_template = array();
    static private $_templates = array();
    static private $_initialized = false;
    static public  $rollbackThemes = true;
    /**
     * @var bool
     */
    static $themesCaching = true;
    /**
     * @var bool
     */
    static $themesRollback = true;
    static public  $settings = array();
    /**
     * @param string $filename
     * @param array $vars
     * @throws iException
     */
    static function init() {


        if (self::$_initialized) {
            return;
        }
        self::$_initialized = true;
        $settings = iSettings::get();
        self::$themesRollback = (bool) $settings['Developer']['Rollback Themes']['value'];
        self::$themesCaching  = (bool) $settings['Developer']['Cache Themes']['value'];
        Dumbo::setCallback('\i\iTemplates::add');

    }


    static function add($file_relative_path) {

        $template = new iTemplate($file_relative_path);
        self::nextTemplate($template);

    }







    /**
     * Check the syntax of some PHP code.
     * @param string $string PHP code to check.
     * @return boolean|array If false, then check was successful, otherwise an array(message,line) of errors is returned.
     */
    static function isValidPHP ($string) {

        ob_start();

        $braces=0;
        $inString=0;

        foreach ( token_get_all($string) as $token) {
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_CURLY_OPEN:
                    case T_DOLLAR_OPEN_CURLY_BRACES:
                    case T_START_HEREDOC: ++$inString; break;
                    case T_END_HEREDOC:   --$inString; break;
                }
            } else if ($inString & 1) {
                switch ($token) {
                    case '`': case '\'':
                    case '"': --$inString; break;
                }
            } else {
                switch ($token) {
                    case '`': case '\'':
                    case '"': ++$inString; break;
                    case '{': ++$braces; break;
                    case '}':
                        if ($inString) {
                            --$inString;
                        } else {
                            --$braces;
                            if ($braces < 0) break 2;
                        }
                        break;
                }
            }
        }
        $inString = @ini_set('log_errors', false);
        $token = @ini_set('display_errors', true);
        ob_start();
        $braces || $string = "if(0){{$string}\n}";
        if (eval($string) === false) {
            if ($braces) {
                $braces = PHP_INT_MAX;
            } else {
                false !== strpos($string,CR) && $string = strtr(str_replace(CRLF,LF,$string),CR,LF);
                $braces = substr_count($string,LF);
            }
            $string = ob_get_clean();
            $string = strip_tags($string);
            if (@preg_match("'syntax error, (.+) in .+ on line (\d+)$'s", $string, $matches)) {
                $matches[2] = (int) $matches[2];
                if ($matches[2] <= $braces) {
                    $result = array('error'=>$matches[1], 'line'=>$matches[2]);
                }
                elseif (stristr($matches[1],'unexpected ')) {
                    $result = array('error'=>$matches[1], 'line'=>$matches[2]);
                } else {
                    $result = array('error'=>'unexpected $end', 'line'=>substr($matches[1], 14));
                }
            }
            else  {
                $matches = array('error'=>'Unknown syntax error', 'line'=> 'unknown');
            }
        } else {
            ob_end_clean();
            $result = true;
        }
        @ini_set('display_errors', $token);
        @ini_set('log_errors', $inString);



    return $result;
    }



    static function run() {

        /**
         * @var $template iTemplate;
         */
        // $template = array_shift(self::$_templates);
        // include $template->cache->path;

    }


    static function nextTemplate($template) {
    rec("nextTemplate: {$template->cache->path}");

        self::$_iteration++;
        self::$_templates[self::$_iteration] = $template;
        self::$_current_template = self::$_templates[self::$_iteration];

        $template =& self::$_current_template;
        $template->parse();
        include $template->cache->path;

    }


    /**
     * Previous template
     */
    function previousTemplate() {

        if ( is_array( self::$_templates[self::$_iteration-1] ) ) {
            self::$_iteration = self::$_iteration-1;
            self::$_current_template = &self::$_templates[self::$_iteration];
        }

    }



    static function generateCodeForTemplate($vars) {


    return md5(self::$_current_template['type'].serialize($vars));
    }



    static function cleanupVars($vars) {

        unset($vars['_GET']);
        unset($vars['_POST']);
        unset($vars['_FILES']);
        unset($vars['_SERVER']);
        unset($vars['_REQUEST']);
        unset($vars['_COOKIE']);

    return $vars;
    }

} 