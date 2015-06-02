<?php

namespace o;

class iMessage {

    public $level   = null;
    public $code    = null;
    /**
     * @var string | null
     */
    public $message = null;
    public $data    = null;
    /**
     * @var string
     */
    public static $_format = null;


    const FORMAT_HTML    = "html";
    const FORMAT_TEXT    = "text";
    const FORMAT_JSON    = "json";
    const FORMAT_PHP     = "php";

    function __construct($message,$code,$level,$data=array()) {

        if ($level >= 1 && $code == iException::DEFAULT_CODE) {
            $code = null;
        }

        try {
            $_message = @vsprintf($message, $data);
        } catch (\Exception $e) {
            $_message = $message;
        }
        $message = $_message;

        $this->setMessage($message);
        $this->setLevel($level);
        $this->setCode($code);
        $this->cleanup_data($data);
        $this->setData($data);

    }



    function cleanup_data($data) {

        $new_data = array();

        if ($this->code == 'REST_CANT_CONNECT') {
            $new_data['server'] = $data[0];
        }

        if ($new_data) {

        }



    return $data;
    }



    function getData() {

    return $this->data;
    }



    function getLevel() {

    return $this->level;
    }



    function getMessage() {

    return $this->message;
    }


    /**
     * Formats message for output depending on device
     *
     * @param string $message text to format
     * @return string $formatted_message
     */
    static function formatMessage($message='') {
        $s = $message;

        if (self::$_format == self::FORMAT_HTML) {
            $s = preg_replace('/\s_(.+?)_\s/',' <i>\1</i> ',$s);
            // $s = preg_replace('/\s*?\*(.+?)\*\s*?/','<b>\1</b>',$s);
            $s = preg_replace('/\*(.+?)\*\s*?/','<b>\1</b>',$s);
            $s = str_replace("\n",'<br>',$s);
        }
        $s = str_replace('\\n',"\n",$s);
        $formatted_message = $s;

        return $formatted_message;
    }


    function setData($data=array()) {

        if ($data) {
            $this->data = $data;
        }

    return $this;
    }



    function setLevel($level) {

        if ($level <= 4 && $level >= 0) {
            $this->level = $level;
        } else {
            trigger_error("iMessage::setLevel($level) - unknown level");
        }

    return $this;
    }



    function setMessage($message) {

        if (is_string($message)) {
            $this->message = $message;
        }

    return $this;
    }





    function setCode($code) {

        if ($code) {
            $this->code = $code;
        }

    return $this;
    }



    function getCode() {

    return $this->code;
    }



    /**
     * @return string
     */
    function __toString() {


        if ( self::$_format == iException::FORMAT_TEXT ) {
            $level = $this->level == 0 ? "CORE ERROR:"   : $this->level;
            $level = $this->level == 1 ? "ERROR:"        : $level;
            $level = $this->level == 2 ? "WARNING:"      : $level;
            $level = $this->level == 3 ? ""              : $level;
            $level = $this->level == 4 ? "info"          : $level;
            $message = self::formatMessage($this->getMessage());
            $code = $this->code;
            if ( $code == iException::DEFAULT_CODE ) {
                $code = ' ';
            } else {
                $code = "*$code* ";
            }
            return "{$level}{$code}{$message}";
        }

        elseif ( self::$_format == iException::FORMAT_HTML ) {
            $level = $this->level == 0  ?   "CORE ERROR" : $this->level;
            $level = $this->level == 1  ?   "ERROR"      : $level;
            $level = $this->level == 2  ?   "WARNING"    : $level;
            $level = $this->level == 3  ?   "Message"    : $level;
            $level = $this->level == 4  ?   "Info"       : $level;
            $message = self::formatMessage($this->getMessage());
            $code = $this->getCode();
            $string = "";
            if ($this->level >= 3) {
                if ($code) {
                    $string .= "Code: <strong>$code</strong>";
                }
                $string .= "<em>$message</em>";
            } else {
                $string .= "<strong>$level</strong><br>";
                $string .= "Code: <strong>$code</strong><br>";
                $string .= "Message: <em>$message</em>";
            }
            return $string;
        }

        elseif ( self::$_format == iException::FORMAT_PHP ) {
            $string = serialize($this);
            return $string;
        }

        else {
            return $this->getMessage();
        }


    }


}