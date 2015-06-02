<?php
/**
 * Created by: Xander.
 * Date: 03.02.2015
 * Time: 11:44
 * Contact: xander@inspiration-vibes.com 
 */

namespace Dumbo;

use Doctrine\Common\Inflector\Inflector as Inflector;

class Dumbo {



    /**
     * Stores original template
     *
     * @var string
     */
    public static $template;


    /**
     * Settings, can be accessed using setter+getter or directly
     * Dumbo::setting('setting_key')
     * Dumbo::setting('setting_key','custom_value')
     *
     * @var array
     */
    static public $settings = array(
        'autoCleanup' => true,
        'keyFormat' => '%s__',
        'use e shortcut' => true,
        'use self::e' => true,
        'closeMissingTags' => true
    );


    /**
     * @var callback $callback
     */
    static public $callback;

    /**
     * @var callback $output
     */
    static public $output;


    /**
     * @var array
     */
    static $vars = array();

    /**
     * History, stores all them templates parsed :)
     *
     * @var array
     */
    public static $history = array();

    /**
     * Current history index
     *
     * @var int
     */
    static private $historyIndex = 0;


    /**
     * Stores matches
     *
     * @var array
     */
    static private $matches = array();


    /**
     * Stores tokens
     *
     * @var array
     */
    static private $tokens = array();


    /**
     * Spacer to make code look better
     *
     * @var string
     */
    static private $spacer='';


    /**
     * @var string
     */
    static private $result=null;

    /**
     * @param bool $initialized
     */
    static private $initialized = false;


    function __construct($template=null) {
        self::init($template);
    }



    static function init($template=null) {

        if (!self::$initialized) {
            if (!self::$callback) {
                self::setCallback('\\'.__CLASS__,'parse');
            }
            if (!self::$output) {
                self::setOutput(__CLASS__,'e');
            }
            self::$initialized = true;
        }



        /*
        if (!self::$template && $template) {
            self::$template = str_replace("\r\n","",$template);
            self::$history[self::$historyIndex]['template'] = &self::$template;
        }
        else if (!$template) {
            self::$template = str_replace("\r\n","",$template);
            self::$history[self::$historyIndex]['template'] = &self::$template;
        }
        */

        /*
        if (!self::$template && $template) {
            self::$template = str_replace("\r\n","\n",$template);
            self::$history[self::$historyIndex]['template'] = self::$template;
        }
        else if (!$template) {
            self::$template = str_replace("\r\n","\n",$template);
            self::$history[self::$historyIndex]['template'] = self::$template;
        }
        self::$history[self::$historyIndex]['vars'] = $vars;
        */


    }



    static function next() {

        self::$historyIndex++;
        self::$matches = array();
        self::$tokens = array();
        self::$vars   = array();
        self::$template = '';
        self::$result = '';

    }



    static public function parse($template=null) {


        self::init();

        if (self::$historyIndex == 2) {
            echo 'index2';
            prd($template);
        }

        if ($template) {
            self::$template = str_replace("\r\n","\n",$template);
            self::$history[self::$historyIndex]['template'] = &self::$template;
        }

        if (!$template) {
            throw new \Exception("Dumbo::parse() - \$template is empty",0);
        }

        self::match();
        self::tokenize();
        self::evil();
        self::adjustIncludes();

        if (self::setting('autoCleanup')) {
            self::cleanup();
        }
        if (self::setting('closeMissingTags')) {
            self::closeMissingTags();
        }

        // destroy reference
        $result = self::$result;

    return $result;
    }



    static function setCallback($class_or_namespace,$method=null) {

        if ($method == null) {
            $method = $class_or_namespace;
            $class_or_namespace = null;
        }

        // prd(is_callable("\\var_dump"));

        if ($class_or_namespace && $method && is_callable(array($class_or_namespace,$method))) { // class::method()
            self::$callback = "$class_or_namespace::$method";
        } else if (is_callable($class_or_namespace.'\\'.$method)) { // namespace/function()
            self::$callback = $class_or_namespace.'\\'.$method;
        } else if (is_callable($method)) {
            self::$callback = $method;
        } else {
            if (func_num_args() == 1) {
                throw new \Exception("Dumbo::setCallback(".var_export($method,true).") - is not callable");
            }
            if (func_num_args() == 2) {
                throw new \Exception("Dumbo::setCallback(".var_export($class_or_namespace,true).",".var_export($method,true).") - is not callable");
            }
        }


    }



    static function setOutput($class_or_namespace,$method=null) {

        if ($method == null) {
            $method = $class_or_namespace;
            $class_or_namespace = null;
        }

        // prd(is_callable("\\var_dump"));
        if (0 !== strpos('\\',$class_or_namespace)) {
            $class_or_namespace = '\\'.$class_or_namespace;
        }
        if ($class_or_namespace && $method && is_callable(array($class_or_namespace,$method))) { // class::method()
            self::$output = "$class_or_namespace::$method";
        } else if (is_callable($class_or_namespace.'\\'.$method)) { // namespace/function()
            self::$output = $class_or_namespace.'\\'.$method;
        } else if (is_callable($method)) {
            self::$output = $method;
        } else {
            if (func_num_args() == 1) {
                throw new \Exception("Dumbo::setOutput(".var_export($method,true).") - is not callable");
            }
            if (func_num_args() == 2) {
                throw new \Exception("Dumbo::setOutput(".var_export($class_or_namespace,true).",".var_export($method,true).") - is not callable");
            }
        }

    }




    static function adjustIncludes() {

        $result = self::$result ? self::$result : self::$template;

        $tokens = token_get_all($result);
        foreach ( $tokens as $pos=>$token) {
            if (is_array($token)) {
                $tokens[$pos]['name'] = token_name($token[0]);
            }
        }


        $found_includes = array();
        $current_pos = 0;
        foreach ( $tokens as $pos=>$token) {
            if (@in_array($token['name'], explode(' ','T_INCLUDE T_REQUIRE T_INCLUDE_ONCE T_REQUIRE_ONCE') )) {
                @$include_type = $token['name'];
                $include_include = $token[1];
                $badpos = $pos;
                $rest = array_slice($tokens,$pos);
                $include_text = '';
                foreach ($rest as $token2) {
                    if (is_array($token2)) {
                        if ( in_array($token2['name'],array('T_CLOSE_TAG','T_LOGICAL_OR') ) ) {
                            break;
                        } else {
                        }
                    } elseif (strstr($token2,';')) {
                        $include_text .= $token2;
                        break;
                    } elseif (strstr($token2,"\n")) {
                        $include_text .= trim("$token2");
                        break;
                    }
                    $include_text .= ($token2[1] ? $token2[1] : $token2[0]);
                }
                $current_pos++;
                $found_includes[] = array(
                    'text' => $include_text,
                    'include' => $include_include,
                    'type' => $include_type,
                );
            }
        }


        foreach ( $found_includes as $found_include ) {

            // $found_include['text_replacement'] = str_replace( $found_include['include'], "echo \self::$parse(", trim($found_include['text']) );
            $found_include['text_replacement'] = str_replace( $found_include['include'], "echo ".self::$callback."(", trim($found_include['text']) );
            $found_include['text_replacement'] = trim($found_include['text_replacement']);
            $found_include['text_replacement'] = rtrim($found_include['text_replacement'],';');
            $found_include['text_replacement'] = $found_include['text_replacement'].", get_defined_vars(), {$found_include['type']} );";
            // echo 'replacing '.htmlentities($found_include['text']).' with '.htmlentities($found_include['text_replacement']);
            self::$result = str_replace( $found_include['text'], $found_include['text_replacement'], $result );

        }



        return self::$result;
    }



    static function cleanup($text='') {

        $result = $text ? $text : self::$result;
        if ($clean = preg_replace('/\?>[\s]?+<\?php/s','',$result,-1,$count)) {
            $result = $clean;
        }
        /*
        if ($clean = preg_replace('/\?>\n/',"?>\n",$result,-1,$count)) {
            $result = $clean;
        }
        */
        self::$result = $result;

    return $result;
    }


    static function closeMissingTags($text='') {


        /*
        $result = $text ? $text : self::$result;
        prd($result);
        preg_match_all('/<\?php/',$result,$matches);
        $opening_tags_amount = (count($matches[0]));
        preg_match_all('/\?>/',$result,$matches);
        prd($matches);
        $closing_tags_amount = (count($matches[0]));
        prd($closing_tags_amount);
        */



        $result = $text ? $text : self::$result;

        $tokens = token_get_all($result);
        foreach ( $tokens as $pos=>$token) {
            if (is_array($token)) {
                $tokens[$pos]['name'] = token_name($token[0]);
            }
        }


        $found_includes = array();
        $current_pos = 0;
        $open_tags_amount = 0;
        $close_tags_amount = 0;

        foreach ( $tokens as $pos=>$token) {
            if (in_array(@$token['name'], explode(' ','T_OPEN_TAG T_OPEN_TAG_WITH_ECHO') )) {
                $open_tags_amount++;
            } elseif ( @$token['name'] == 'T_CLOSE_TAG') {
                $close_tags_amount++;
            }
        }

        if ($open_tags_amount > $close_tags_amount) {
            $result .= "\n\n?>";
        }

        self::$result = $result;

    return $result;
    }


    static function match() {

        self::matchLoops();
        self::matchVars();

    }



    static function matchLoops($template=null) {

        $template = $template ? $template : self::$template;

        if (false !== preg_match_all('/\[([\w]+?)[:]+?([\w]+?)?\](.+?)\[\/\1\]/s',$template,$matches,PREG_SET_ORDER)) {

            foreach ($matches as $_match) {
                $match = array();
                $match['type'] = 'loop';
                $match['match'] = $_match[0];
                if ($_match[2]) {
                    $match['structure'] = array($_match[1],$_match[2]);
                } else {
                    $match['structure'] = array($_match[1]);
                }
                /**
                 * recursion
                 */
                self::matchLoops($_match[3]);
                array_push(self::$matches,$match);
            }

        }

    }


    static function setting($setting, $value=null) {

        if (!self::$settings[$setting]) {
            return false;
        }
        if ($value) {
            self::$settings[$setting] = $value;
        } else {
            return self::$settings[$setting];
        }
    }


    static function matchVars() {

        $template = self::$template;
        if (false !== preg_match_all('/\[@([\w\.]+?)\]/',$template,$matches,PREG_SET_ORDER)) {
            foreach ($matches as &$match) {
                $match['type'] = "var";
                $match['match'] = $match[1];
                unset($match[0],$match[1]);
                array_push(self::$matches,$match);
            }
        }

    }



    static function tokenize($matches=null) {
        self::$tokens = $matches ? $matches : self::$matches;

        foreach ( self::$tokens as &$token ) {
            if ($token['type'] == 'loop') {
                self::$spacer='';
                $token = self::patternForeach2($token);
                // $token = self::patternWhile($token);
                self::$spacer='';
            }
            if ($token['type'] == 'var') {
                $token = self::patternVar($token);
            }
        }

    }



    function formatVar($string) {

        return strtolower(Inflector::singularize($string));

    }


    function unsetLast(&$array) {

        $last = end($array);
        $last = key($array);
        unset($array[$last]);

        return $array;
    }


    static function patternForeach2(&$token) {
        $token['replacements'] = array();

        if (count($token['structure']) == 1) {
            $token['structure'][] = $nextvar = self::formatVar($token['structure'][0]);
            $token['match'] = str_replace($token['structure'][0].':]', $token['structure'][0].':'.$nextvar.']',$token['match']);
            self::$template = str_replace($token['structure'][0].':]', $token['structure'][0].':'.$nextvar.']',self::$template);
        }


        $keyFormat = self::setting('keyFormat');
        foreach ( $token['structure'] as $current_index=>$var ) {
            $token['replacements'][$current_index] = array( 'start'=>'', 'end'=>'' );
            $start = &$token['replacements'][$current_index]['start'];
            $end = &$token['replacements'][$current_index]['end'];
            $start .= "<?php".self::$spacer." ";

            $key = sprintf($keyFormat,$token['structure'][$current_index+1]);
            /*
            $start .= "foreach ($$var as \${$key} => \${$token['structure'][$current_index+1]}):?>\n";
            $end .= "<?php endforeach; endif;?>";
            */

            // $formatedVar = self::$formatVar($token['structure'][$current_index+1]);
            $formatedVar = $token['structure'][$current_index+1];
            if ($token['structure'][$current_index+1]) {
                $key = sprintf($keyFormat,$token['structure'][$current_index+1]);
                $start .= "foreach ((array) $$var as \${$key} => \${$formatedVar}): ?>\n\n";
                $end .= "<?php endforeach;?>";
            }
            else {
                // $key = sprintf($keyFormat,$token['structure'][$current_index]);
                $start .="extract (\$$var,EXTR_PREFIX_INVALID,'$var'); ?>";
                $end .= "";
            }
            self::$spacer .= '';
        }


        return $token;
    }


    static function patternForeach(&$token) {
        $token['replacements'] = array();

        if (count($token['structure']) == 1) {
            $token['structure'][] = $nextvar = self::formatVar($token['structure'][0]);
            $token['match'] = str_replace($token['structure'][0].':]', $token['structure'][0].':'.$nextvar.']',$token['match']);
            self::$template = str_replace($token['structure'][0].':]', $token['structure'][0].':'.$nextvar.']',self::$template);
        }


        $keyFormat = self::setting('keyFormat');
        foreach ( $token['structure'] as $current_index=>$var ) {
            $token['replacements'][$current_index] = array( 'start'=>'', 'end'=>'' );
            $start = &$token['replacements'][$current_index]['start'];
            $end = &$token['replacements'][$current_index]['end'];
            $start .= "<?php".self::$spacer." if (is_array($$var)): ";

            $key = sprintf($keyFormat,$token['structure'][$current_index+1]);
            /*
            $start .= "foreach ($$var as \${$key} => \${$token['structure'][$current_index+1]}):?>\n";
            $end .= "<?php endforeach; endif;?>";
            */

            // $formatedVar = self::$formatVar($token['structure'][$current_index+1]);
            $formatedVar = $token['structure'][$current_index+1];
            if ($token['structure'][$current_index+1]) {
                $key = sprintf($keyFormat,$token['structure'][$current_index+1]);
                $start .= "foreach ($$var as \${$key} => \${$formatedVar}): ?>\n\n";
                $end .= "<?php endforeach; endif;?>";
            }
            else {
                // $key = sprintf($keyFormat,$token['structure'][$current_index]);
                $start .="extract (\$$var,EXTR_PREFIX_INVALID,'$var'); endif;?>";
                $end .= "";
            }
            self::$spacer .= '';
        }


        return $token;
    }


    static function e() {

        $keys = array();

        if (func_num_args() >= 1) {
            $var = func_get_arg(0);
        }
        if (func_num_args() == 1) {
            $keys = func_get_args();
            unset($keys[0]);
        } else {
            for ($i = 1; $i < func_num_args(); $i++ ) {
                $keys[$i] = func_get_arg($i);
            }
        }

        // @todo self loop
        if (is_numeric($var) || is_string ($var)) {
            return $var;
        } elseif (is_array($var) && $keys[1]) {
            if (is_string( $var[$keys[1]]) || is_numeric($var[$keys[1]]) ) {
                return $var[$keys[1]];
            }
        }
    }


    static function patternWhile(&$token) {
        $token['replacements'] = array();

        if (count($token['structure']) == 1) {
            $token['structure'][] = $nextvar = self::formatVar($token['structure'][0]);
            $token['match'] = str_replace($token['structure'][0].':]', $token['structure'][0].':'.$nextvar.']',$token['match']);
            self::$template = str_replace($token['structure'][0].':]', $token['structure'][0].':'.$nextvar.']',self::$template);
        }



        $keyFormat = self::setting('keyFormat');
        foreach ( $token['structure'] as $current_index=>$var ) {
            $token['replacements'][$current_index] = array( 'start'=>'', 'end'=>'' );
            $start = &$token['replacements'][$current_index]['start'];
            $end = &$token['replacements'][$current_index]['end'];
            $start .= "<?php".self::$spacer." ";

            $key = sprintf($keyFormat,$token['structure'][$current_index+1]);
            /*
            $start .= "foreach ($$var as \${$key} => \${$token['structure'][$current_index+1]}):?>\n";
            $end .= "<?php endforeach; endif;?>";
            */

            // $formatedVar = self::$formatVar($token['structure'][$current_index+1]);
            $formatedVar = $token['structure'][$current_index+1];
            if ($token['structure'][$current_index+1]) {
                $key = sprintf($keyFormat,$token['structure'][$current_index+1]);
                $start .= "while ( list(\${$formatedVar}, \${$key}) = $$var ): ?>\n\n";
                $end .= "<?php endwhile; ?>";
            }
            else {
                // $key = sprintf($keyFormat,$token['structure'][$current_index]);
                $start .="extract (\$$var,EXTR_PREFIX_INVALID,'$var'); ?>";
                $end .= "";
            }
            self::$spacer .= '';
        }

        pr($token);

        return $token;
    }



    static function loop($var) {

        static $index = 0;
        static $previous_var = null;

        if ($previous_var === null) {
            $var = $previous_var;
        }
        if ($var !== $previous_var) {
            $index++;
        }

        static $items = array();
        static $current_item = 0;

        if (!$items) {

            foreach (Mage::app()->getWebsites() as $website) {
                $items[] = $website;
                foreach ( $website->getGroups() as $group ) {
                    $items[] = $group;
                    foreach ( $group->getStores() as $store ) {
                        $items[] = $store;
                    }
                }
            }
        }


        if ( $current_item < sizeof($items) ) {
            $item = $items[$current_item];
            $current_item++;
        } else {
            $current_item = 0;
            return false;
        }

    }

    static function patternVar(&$token) {



        $var = $token['match'];
        $keyFormat = self::setting('keyFormat');
        $key = sprintf($keyFormat,$var);


        if (self::$output) {
            $output = self::$output;
            /**
             * Case of patterns with dot
             */
            if (strpos($token['match'],'.') > 0) {
                $vars = explode('.',$token['match']);
                $var = '$'.$vars[0];
                unset($vars[0]);
                $var = "{$var}, '".implode($vars,"','").'\'';
                /* $token['replacement'] = "<?php echo ((is_string($$var) || is_numeric($$var)) ? $$var : \$$key); ?>"; */
                $token['replacement'] = "<?= $output($var); ?>";

            }
            /**
             * Case of patterns without dot
             */
            else {
                /* $token['replacement'] = "<?php echo ((is_string($$var) || is_numeric($$var)) ? $$var : \$$key); ?>"; */
                $token['replacement'] = "<?= $output($$var); ?>";
            }

        } else {

            /**
             * Case of patterns with dot
             */
            if (strpos($token['match'],'.') > 0) {
                $vars = explode('.',$token['match']);
                $var = '$'.$vars[0];
                unset($vars[0]);
                $var = "{$var}[', '".implode($vars,"'],['").'\']';
                /* $token['replacement'] = "<?php echo ((is_string($$var) || is_numeric($$var)) ? $$var : \$$key); ?>"; */
                $token['replacement'] = "<?= $var; ?>";

            }
            /**
             * Case of patterns without dot
             */
            else {
                /* $token['replacement'] = "<?php echo ((is_string($$var) || is_numeric($$var)) ? $$var : \$$key); ?>"; */
                $token['replacement'] = "<?= $$var; ?>";
            }

        }


    return $token;
    }



    static function evil($template=null) {
        $template = $template ? $template : self::$template;

        foreach ( self::$tokens as &$token ) {

            if ($token['type'] == 'loop') {

                $start = '';
                $end = '';
                foreach ( $token['replacements'] as &$replacement ) {
                    $start .= $replacement['start'];
                }
                // $start = trim($start);
                $token['replacements'] = array_reverse($token['replacements']);
                foreach ( $token['replacements'] as &$replacement ) {
                    $end .= $replacement['end'];
                }
                // $end = trim($end);
                /*
                                $template = str_replace("{".$token['match'].":}\n", $start."\n\n", $template);
                */
                $template = str_replace('['.$token['structure'][0].':'.$token['structure'][1].']', $start, $template);
                $template = str_replace('[/'.$token['structure'][0].']', $end, $template);

            }
            else if ($token['type'] == 'var') {
                /**
                 * @todo
                 * this hack is necessary? ><
                 */
                $template = str_replace('['.$token['match']."]\n", $token['replacement']."\n\n", $template);
                $template = str_replace('['.$token['match']."]", $token['replacement']."", $template);
            }
            $template = str_replace('<?php echo', '<?php echo', $template);
            // $template = str_replace(IVY_DUMBO_EOL, '', $template);
            /*
               $template = str_replace("?>\n<?php", "?>\n\n<?php", $template);
            */

        }

        self::$result = $template;
        return self::$result;

    }



}


function e() {
    Dumbo::e(func_get_args());
}
