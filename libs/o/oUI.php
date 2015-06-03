<?php
/**
 * Created by: i.
 * Date: 01.02.2015
 * Time: 0:10
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;


class oUI implements StaticInit {



    /**
     * @var string
     */
    public static $theme = self::DEFAULT_THEME;
    /**
     * @var string
     */
    public static $layout = self::DEFAULT_LAYOUT;
    /**
     * @var string
     */
    public static $page = self::DEFAULT_PAGE;
    /**
     * @var string
     */
    public static $block = null;


    private static $_initialized = false;





    const DEFAULT_THEME  = "default";
    const DEFAULT_LAYOUT = "default";
    const DEFAULT_PAGE   = "default";



    function __construct() {

    }


    static function init() {
    rec(__METHOD__);

        if (self::$_initialized) {
            return;
        }
        self::$_initialized = true;
        self::setTheme(self::DEFAULT_THEME);
        self::setLayout(self::DEFAULT_LAYOUT);
        self::checkIntegrity();

    }



    /**
     * @throws oException if something goes wrong
     */
    static function checkIntegrity() {
    rec(__METHOD__);

        if (!self::themeExists(self::DEFAULT_THEME)) {
            throw new oException(oException::ERROR_DEFAULT_THEME_404, array('level'=>1,ROOT.self::getThemePath() ) );
        }
        if (!self::layoutExists(self::DEFAULT_LAYOUT)) {
            throw new oException( oException::ERROR_DEFAULT_LAYOUT_404, array('level'=>1, ROOT.self::getLayoutPath() ) );
        }

    }



    static function getThemePagesPath( $theme=null ) {
        return self::getThemePath($theme).iLocations::DIR_PAGES.DS;
    }


    static function getThemeBlocksPath( $theme=null ) {
        return self::getThemePath($theme).iLocations::DIR_BLOCKS.DS;
    }






    /**
     * @param null $layout
     * @return string layout path if exists
     * @throws oException
     */
    static function getLayoutPath( $layout=null, $absolute = false ) {

        if (!$layout) {
            $layout = self::$layout;
        }
        $layout_path = self::getThemePath( null, $absolute ).$layout.'.php';

    return $layout_path;
    }


    /**
     * @param string $theme
     * @param bool $relative
     * @return string
     */
    static function getThemePath($theme=null, $absolute = false) {

        if (!$theme) {
            $theme = self::$theme;
        }

        $theme_path = iLocations::getThemesPath($absolute).$theme.DS;

    return $theme_path;
    }


    /**
     * @param string $theme
     * @param bool $relative
     * @return string
     */
    static function getThemeCachePath($theme=null, $absolute = false) {

        if (!$theme) {
            $theme = self::$theme;
        }

        $theme_cache_path = iLocations::getThemesCachePath($absolute).$theme.DS;

    return $theme_cache_path;
    }

    /**
     * @param string $page
     * @return string layout path if exists
     * @throws oException
     */
    static function getPagePath( $page=null ) {

        if (!$page) {
            $page = self::$page;
        }
        $relative_page_path     = parent::getPagePath($page);
        $absolute_page_path     = ROOT.self::DIR_BASE.$relative_page_path;

    return $absolute_page_path;
    }



    /**
     * @param string $theme Theme to check
     * @throws oException
     * @return bool
     */
    static function themeExists($theme=null) {
        $theme = $theme ? $theme : self::$theme;

        if ( is_dir( ROOT.self::getThemePath($theme) ) ) {
            return true;
        }

    return false;
    }



    /**
     * @var string $layout layout name
     * @return bool
     */
    static function layoutExists($layout=null) {
        $layout = $layout ? $layout : self::$layout;

        if ( file_exists( ROOT.self::getLayoutPath($layout) ) ) {
            return true;
        }

        prd(self::getLayoutPath($layout));

    return false;
    }



    /**
     * @var string $page page name
     * @return bool
     */
    static function pageExists($page=null) {
        $page = $page ? $page : self::$page;

        if ( file_exists( self::getPagePath($page) ) ) {
            return true;
        }

    return false;
    }






    static function getBlock($block_name) {

        $block_files = getFilesInDir(ROOT.DS.'blocks');
        $block_file = str_replace('.php','',$block_name);
        $block_file = $block_file.'.php';
        prd($block_file);

        if (in_array ($block_name, $block_files) ) {
            return ROOT.DS.'blocks'.DS.$block_file;
        } else {
            throw new oException("Block $block_name not found",1);
        }

    }






    static function getTheme() {
        return self::$theme;
    }

    static function getLayout() {
        return self::$layout;
    }

    static function getPage() {
        return self::$page;
    }





    static function setTheme($theme) {
        self::$theme = $theme;
    }

    static function setLayout($layout) {
        self::$layout = $layout;
    }

    static function setPage($page) {
        self::$page = $page;
    }

    static function setBlock($block) {
        self::$block = $block;
    }






    /**
     * Serious business, rendering layout
     *
     * @param null $layout
     */
    static function layout($layout=null) {

        // iUtils::saveOriginalIncludePath();

        self::setLayout($layout);

        iTemplates::add( self::getLayoutFilename() );
        iTemplates::run();

    }







    static function getLayoutRelativePath($layout=null) {

        $layout = $layout ? $layout : self::$layout;
        $layout = str_replace('.php', '', $layout);
        $layout_filename = $layout.'.php';

        return self::DIR_THEMES.DS.self::getTheme().DS.$layout_filename;
    }



    static function getLayoutFilename( $layout=null ) {

        $layout = $layout ? $layout : self::$layout;
        $layout = str_replace('.php', '', $layout);
        $layout_filename = $layout.'.php';

        return $layout_filename;
    }


    static function getPageRelativePath($page=null) {

        $page = $page ? $page : self::$page;
        $page = str_replace('.php', '', $page);
        $page_filename = $page.'.php';

        return self::DIR_THEMES .DS. self::getTheme() .DS. self::DIR_PAGES .DS. $page_filename;

    }



    static function getBlockRelativePath( $block=null ) {

        $block = $block ? $block : self::$block;
        $block = str_replace('.php', '', $block);
        $block_filename = $block.'.php';

        return self::DIR_THEMES.DS. self::getTheme(). self::DIR_BLOCKS.DS. $block_filename;

    }




    static function getPageFilename($page) {
        return $page.'.php';
    }




}



