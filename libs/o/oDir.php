<?php
/**
 * Created by: i.
 * Date: 06.02.2015
 * Time: 12:52
 * Contact: xander@inspiration-vibes.com
 */

namespace o;


class oDir {

    /**
     * @var string
     */
    public $path = null;
    /**
     * @var string
     */
    public $name = null;
    /**
     * @var bool
     */
    public $exists = null;


    function __construct($full_path) {

        $this->init($full_path);


    }


    function init($full_path) {

        if ( realpath($full_path) ) {
            $full_path = realpath($full_path);
        }

        $info = pathinfo($full_path);
        if ($info['dirname'] == '.') {
            $this->path = realpath(__DIR__).DS.$full_path;
        } else {
            $this->path = $full_path;
        }
        $this->name      = $info['filename'];
        $this->exists    = is_dir($full_path);

    }


    function create() {
        if (!file_exists($this->path)) {
            rec("iDir::create() creating dir *%s*",$this->path);
            mkdir($this->path,0777,true);
        } else {
            rec("iDir::create() dir *%s* already exists",$this->path);
        }

    }


    /**
     * @return oFile[]
     */
    function scan() {
        $files = scandir($this->path);
        foreach ($files as $k=>$file) {
            if ($file == '.' || $file == '..') {
                unset($files[$k]);
            }
        }
        sort($files);

        foreach ($files as $k=>$file) {
            $files[$k] = new oFile($this->path.DIRECTORY_SEPARATOR.$file);
        }

    return $files;
    }



    function getFiles() {

        $files = $this->scan();
        return $files;

    }


} 