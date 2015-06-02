<?php
/**
 * Created by: i.
 * Date: 06.02.2015
 * Time: 12:52
 * Contact: xander@inspiration-vibes.com 
 */

namespace o;


class iFile {

    /**
     * @var string
     */
    public $filename = null;
    /**
     * @var string
     */
    public $path = null;
    /**
     * @var string
     */
    public $extension = null;
    /**
     * @var string
     */
    public $dir = null;
    /**
     * @var string
     */
    public $name = null;
    /**
     * @var bool
     */
    private $exists = null;

    /**
     * @var \DateTime
     */
    private $modified = null;

    function __construct($full_path) {

        $this->init($full_path);


    }


    /**
     * @param $dir iDir
     * @return iFile
     * @throw iException
     */
    function copy($newDir) {

        if (!$newDir instanceof iDir) {
            throw new iException(__CLASS__.__METHOD__.' first argument should be instance of iDir',0);
        }
        else {
            $contents = $this->getContents();
            $newFile = new iFile($newDir->path.DIRECTORY_SEPARATOR.$this->filename);
            $newFile->create()->putContents($contents);
            return $newFile;
            // $this->init($dir);
        }

    }


    function create() {

        if (!$this->exists) {
            $file = fopen($this->path, "w");
            if ($file) {
                $this->exists = true;
            }
        }

    return $this;
    }


    function init($full_path) {
    rec(__METHOD__.' initializing file '.$full_path);


        // $full_path = realpath($full_path);

        if (!$full_path) {
            warning("iFile::init() Couldn't resolve filename %s",array($full_path));
        }

        $info = pathinfo($full_path);
        if ($info['dirname'] == '.') {
            $this->path = realpath(__DIR__).DS.$full_path;
        } else {
            $this->path = $full_path;
        }

        $this->extension = $info['extension'];
        $this->dir       = $info['dirname'];
        $this->name      = $info['filename'];
        $this->filename  = $info['basename'];
        $this->path      = $full_path;


    }



    function rename($newName) {

        if ($this->validateFilename($newName)) {
            rename($this->path,$this->dir.DIRECTORY_SEPARATOR.$newName.'.'.$this->extension);
            $this->init($this->dir.DIRECTORY_SEPARATOR.$newName.'.'.$this->extension);
            // $this->extension = $newName;
        } else {
            throw new iException("iFile::rename() Couldn't rename file",0);
        }

        message("iFile::rename() File renamed to %s",array($newName));

    return $this;
    }



    function validateFilename() {
        return true;
    }


    function getContents() {
        return file_get_contents($this->path);
    }



    function putContents($data) {
        file_put_contents($this->path,$data);
    }


    /**
     * @return \DateTime
     */
    function getModified() {
        $time = stat($this->path);
        $time = $time['mtime'];
        if (!$time) {
            $time = time();
        }
        $time = new \DateTime($time);
        prd($time);
        return $time;
    }


    function delete() {
        unlink($this->path);
    }


    /**
     * @param $modified mixed
     */
    function setModified($modified) {

        if (!$modified instanceof \DateTime) {
            $modified = new \DateTime($modified);
        }
        prd($modified);
        $timestamp = $modified->getTimestamp();
        prd($timestamp);
        touch($this->path,$timestamp);
        $this->modified = $modified;
    }



    function exists() {
        return file_exists($this->path);
    }


    function __get($property) {
        if ($property == 'modified') {
            return $this->getModified();
        }
        if ($property == 'exists') {
            return $this->exists();
        }
    }


    function __set($property,$value) {
        if ($property == 'modified') {
            $this->setModified($value);
        }
    }

} 