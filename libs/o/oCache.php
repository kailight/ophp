<?php
/**
 * Created by: i.
 * Date: 05.02.2015
 * Time: 3:33
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;


class oCache extends iLocations {

    const DIR_BASE           = "cache";



    /**
     * @param string $theme Theme to check
     * @return bool
     */
    static function themeExists($theme=null) {
        $theme = $theme ? $theme : self::$theme;

        if ( file_exists( self::getThemePath($theme) ) ) {
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

        if ( file_exists( self::getLayoutPath($layout) ) ) {
            return true;
        }

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
        $absolute_page_path     = ROOT.self::DIR_BASE.DS.$relative_page_path;

        return $absolute_page_path;
    }



    /**
     * @param string $block
     * @return string layout path if exists
     * @throws oException
     */
    static function getBlockPath( $block=null ) {

        if (!$block) {
            $block = self::$block;
        }
        $relative_block_path     = parent::getBlockPath($block);
        $absolute_block_path     = ROOT.self::DIR_BASE.DS.$relative_block_path;

    return $absolute_block_path;
    }



    /**
     * @param string $theme
     * @return bool|string
     */
    static function getThemePath($theme=null) {

        if (!$theme) {
            $theme = self::$theme;
        }
        $relative_theme_path = parent::getThemePath($theme);
        $absolute_theme_path = ROOT.self::DIR_BASE.DS.$relative_theme_path;

        return $absolute_theme_path;
    }



    /**
     * @param null $layout
     * @return string layout path if exists
     * @throws oException
     */
    static function getLayoutPath( $layout=null ) {

        if (!$layout) {
            $layout = self::$layout;
        }
        $relative_layout_path = parent::getLayoutPath($layout);
        $absolute_layout_path = ROOT.self::DIR_BASE.$relative_layout_path;

        return $absolute_layout_path;
    }



    static function setTheme($theme) {

        self::$theme = $theme;
    }


    static function setLayout($layout) {

        if (self::getLayoutPath($layout)) {
            self::$layout = $layout;
        }
    }


    static function getTheme() {

        return self::$theme;
    }


    static function isCacheFile($filename) {

        $filename = realpath($filename);
        if (stristr($filename, ROOT.self::DIR_BASE )) {
            return true;
        }

    }



} 