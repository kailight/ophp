<?php
/**
 * Created by: i.
 * Date: 10.02.2015
 * Time: 0:27
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;

use Dumbo\Dumbo;


class iTemplate {

    /**
     * @var bool
     */
    public $group = null;

    // private $_initialized = false;

    /**
     * @var iFile
     */
    public $cache  = null;
    /**
     * @var iFile
     */
    public $template = null;


    function __construct($relative_path) {
        $this->init($relative_path);
    }


    function init ( $relative_path ) {
    rec(__METHOD__.' initializing template '.$relative_path);

        iUtils::addIncludePath( iUI::getThemeCachePath() );
        iUtils::addIncludePath( iUI::getThemePath() );

        if ($absolute_path = stream_resolve_include_path($relative_path)) {
            $this->template = new iFile( iUI::getThemePath(null,true).$relative_path  );
            $this->cache    = new iFile( iUI::getThemeCachePath(null,true).$relative_path );
        }
        else {
            iUtils::addIncludePath( iUI::getThemeCachePath( iUI::DEFAULT_THEME ) );
            iUtils::addIncludePath( iUI::getThemePath( iUI::DEFAULT_THEME ) );
            if ($absolute_path = stream_resolve_include_path($relative_path)) {
                $this->template = new iFile( iUI::getThemePath(iUI::DEFAULT_THEME,true).$relative_path  );
                $this->cache    = new iFile( iUI::getThemeCachePath(iUI::DEFAULT_THEME,true).$relative_path );
            } else {
                throw new iException(iException::ERROR_TEMPLATE_FILE_NOT_FOUND, array('level'=>1,$relative_path));
            }
        }


        if ($this->cache->exists && !$this->template->exists) {
            $this->cache->delete();
        }
        // @todo sort the file timestamps
        $content = $this->parse();
        $this->cache->putContents($content);

        /*
        if ($this->cache->exists && $this->template->exists) {
            if ($this->cache->modified < $this->template->modified) {
                // update template cache
                $content = $this->parse();
                $this->cache->putContents($content);
                $this->cache->setModified($this->template->modified);
            }
            // just include cache
        }
        if (!$this->cache->exists) {
            $content = $this->parse();
            $this->cache->putContents($content);
            $this->cache->setModified($this->template->getModified());
        }
        */


    }

    /**
     * need clean wrapper to execute template
     */
    function evaluate() {
    rec(__METHOD__.' evaluating template '.$this->cache->dir_path);

        iUtils::addIncludePath($this->cache->dir_path);
        include $this->cache->path;
        iUtils::removeIncludePath($this->cache->dir_path);

    }


    function parse() {

        $content = $this->template->getContents();

        if ( $content === false ) {
            throw new iException('Template file *'.self::$_current_template['file'].'* not found');
        }


        $parsed_contents = Dumbo::parse( $content );


        $this->cache->putContents($parsed_contents);
        // $this->cache->putContents($content);


        /*
        if (self::$_iteration == 2) {
            prd(self::$_current_template);
        }
        */

        /*
        $is_valid_php = self::isValidPHP(self::$_current_template['result']);
        if ($is_valid_php !== true) {
            $error = $is_valid_php;
            if ($error['line']) {
                $chunks = explode("\n",self::$_current_template['result']);
                $problematic_line = htmlentities($chunks[$error['line']-1]);
                throw new \Exception ("Error in template $file on line {$error['line']}, {$error['error']}, look for $problematic_line.");
            }
        }
        unset($is_valid_php);
        */


        /*
        define('i','i');
        ob_start();

        echo $content;
        /*
        try {
            extract($vars);
            unset($vars);
            unset($file);
            unset($include_type);
            eval ("\nnamespace ".__NAMESPACE__.";?> \n". self::$_current_template['result'] . " \n <?php ");
        }
        catch (\Exception $e) {
            error($e);
        }
        *

        // ob_get_clean();
        $output = ob_get_clean();

        prd($output);
        return $output;

        */




        // iUtils::restoreOriginalIncludePath();
        // self::previousTemplate();

        // $template->cache->putContents($content);
        return $content;
        // return $output;
    }



    function test() {

    }


} 