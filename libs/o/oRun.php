<?php

namespace o;

class oRun {

    /**
     * @var int
     */
    private $id;
    /**
     * @var bool
     */
    public $success = false;
    /**
     * @var string
     */
    public $status = null;
    /**
     * @var null|oException
     */
    public $exception = null;
    /**
     * @var null|string
     */
    public $mode = null;
    /**
     * @var null|string
     */
    public $server = null;
    /**
     * @var data
     */
    public $data = array();
    /**
     * @var float
     */
    public $duration = null;

    /**
     * @var \DateTime
     */
    public $time = null;
    /**
     * @var string
     */
    public $environment = null;
    /**
     * @var string
     */
    public $log = null;

    /**
     * @var int
     */
    public $start;

    /**
     * @var array
     */
    private $info;


    const STATUS_SUCCESS  = "success";
    const STATUS_FAILURE  = "failure";
    const STATUS_PROGRESS = "running";
    const SPLIT_LOG_BY    = 100;


    /**
     * @param $server string
     * @param $time string
     */
    function __construct($server=null,$time=null) {
    message("Creating new iRun");

        if ($server && $time) {
            $this->create($server,$time);
            $this->start();
        }

    }



    function setId($id) {

        $this->id = $id;
        $this->load();



    }



    function delete() {

        $query = "DELETE FROM runs WHERE id = ".$this->id;
        q($query);

    }





    function load() {

        $query = "SELECT * FROM runs WHERE id = ".$this->id;
        $result = q($query);

        if ($result) {
            $data = $result[0];
        } else {
            throw new oException("iRun:: run #'.{$this->id}.' not found in database",1);
        }

        $this->id = $data['id'];
        $this->setTime($data['created']);
        $this->setEnvironment($data['environment']);
        $this->setStatus($data['status']);
        $this->setLog($data['log']);
        $this->setMode($data['mode']);
        $this->duration = $data['duration'];

    }


    /**
     * @return string
     */
    function ago() {
        $ago = ago($this->getTime());
        return $ago;
    }


    function start() {

        $this->start = iCore::stopwatch();

    }



    function create($server,$time) {

        $this->setTime($time);
        $this->setServer($server);

        $this->setStatus(self::STATUS_PROGRESS);
        $query = "INSERT INTO `runs` (`store`,`created`) VALUES ('".$this->server."','".$this->getTime()->format('Y-m-d H:i:s')."')";
        q($query);
        $result = q('SELECT * from runs WHERE created = "'.$this->getTime()->format('Y-m-d H:i:s').'"');

        if ( $result[0] && $result[0]['id'] ) {
            $this->id = $result[0]['id'];
        }

    message("Run #$this->id created");
    }



    function getNumber() {

    return $this->id;
    }



    function getData() {

        if ($this->mode == iApp::MODE_PRODUCTS_DETAILED) {
            $query = "SELECT * FROM products WHERE store_id = '$this->server' AND skynet_updated_detailed >= '{$this->getTime()->format('Y-m-d H:i:s')}'";
            $data = q($query);
        }
        if ($this->mode == iApp::MODE_PRODUCTS_QUICK) {
            $query = "SELECT * FROM products WHERE store_id = '$this->server' AND skynet_updated_quick >= '{$this->getTime()->format('Y-m-d H:i:s')}'";
            $data = q($query);
        }
        if ($this->mode == iApp::MODE_PRODUCTS_PHOTOS) {
            $query = "SELECT * FROM products WHERE store_id = '$this->server' AND skynet_updated_photos >= '{$this->getTime()->format('Y-m-d H:i:s')}'";
            $data = q($query);
        }
        $this->data = $data;

    return $this->data;
    }



    function setException($e) {

        if ($e instanceof oException) {
            $this->exception = $e;
        }

    return $this;
    }



    function getException() {

        if ($this->exception instanceof oException) {
            return $this->exception;
        }

    return null;
    }


    function setServer($server) {

        $this->server = $server;

    return $this;
    }



    function getServer() {

        return $this->server;

    }


    private function setSuccess($success=false) {

        $this->success = $success;

    return $this;
    }



    public function getSuccess() {
        return $this->success;
    }


    /**
     * @param $status string
     * @return $this
     */
    function setStatus($status) {
        $this->status = $status;
        if ($this->getStatus() == self::STATUS_SUCCESS) {
            $this->setSuccess(true);
        } else {
            $this->setSuccess(false);
        }

    return $this;
    }



    function getStatus() {
        return $this->status;
    }



    public function isSuccessful() {
        return $this->getSuccess();
    }



    function setTime($time) {

        if (is_string($time)) {
            $time = new \DateTime($time);
        }

        $this->time = $time;

    return $this;
    }


    function getTime() {

    return $this->time;
    }



    function setLog($log) {

        $this->log = $log;

    return $this;
    }



    function getLog() {

        return $this->log;

    }


    function stop() {
        $this->duration = iCore::stopwatch();
    }


    function getDuration() {
        return $this->duration;
    }


    function save() {

        /*
        if (self::SPLIT_LOG_BY > 0) {
            if (strlen($this->log) > self::SPLIT_LOG_BY) {
                $log_chunks = str_split($this->log, self::SPLIT_LOG_BY);
                $log = implode("\n",$log_chunks);
            } else {
                $log = $this->log;
            }
        } else {
            $log = $this->log;
        }
        */
        $log = mysql_real_escape_string($this->log);

        $query = <<<HEREDOC
UPDATE runs SET
   mode         = "{$this->getMode()}",
   store        = "{$this->getServer()}",
   created      = "{$this->getTime()->format('Y-m-d H:i:s')}",
   log          = "{$log}",
   status       = "{$this->getStatus()}",
   duration     = "{$this->getDuration()}",
   environment  = "{$this->getEnvironment()}"
WHERE id = {$this->id}

HEREDOC;
        

    q($query);


    }



    function addInfo() {


        if ($this->mode == iApp::MODE_PRODUCTS_QUICK) {

        }
        elseif ($this->mode == iApp::MODE_PRODUCTS_DETAILED) {

        }
        elseif ($this->mode == iApp::MODE_PRODUCTS_PHOTOS) {

        }
        // $this->info['$property'] += )

    }


    function getInfo() {
        return $this->info;
    }

    function getMode() {
        return $this->mode;
    }

    function setMode($mode) {
        $this->mode = $mode;
    return $this;
    }

    function getEnvironment() {
        return $this->environment;
    }

    function setEnvironment($environment) {
        $this->environment = $environment;
        return $this;
    }

}