<?php
/**
 * Created by: i.
 * Date: 05.02.2015
 * Time: 3:44
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;


class oLocations {


    const DIR_CACHE      = "cache";
    const DIR_THEMES     = "themes";
    const DIR_PAGES      = "pages";
    const DIR_BLOCKS     = "blocks";


    static function getThemesPath($absolute=false) {

        $themes_path = self::DIR_THEMES.DS;

        if ($absolute) {
            $themes_path = ROOT.$themes_path;
        }

    return $themes_path;
    }



    static function getThemesCachePath($absolute=false) {

        $themes_cache_path = self::DIR_CACHE.DS.self::DIR_THEMES.DS;

        if ($absolute) {
            $themes_cache_path = ROOT.$themes_cache_path;
        }

    return $themes_cache_path;
    }


} 