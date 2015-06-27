<?php

namespace o;


class oString implements Exportable {

    /**
     * @var string $originalString
     */
    public $originalString;

    /**
     * @var string $currentString
     */
    public $currentString;


    public function __construct ($string='') {

        $this->originalString = $string;
        $this->currentString  = $string;

    }


	public function __get_state() {
		return var_export($this->currentString,true);
	}



    /**
     * (PHP 4, PHP 5)<br/>
     * Get string length
     * @link http://php.net/manual/en/function.strlen.php
     * @return int The length of the <i>string</i> on success,
     * and 0 if the <i>string</i> is empty.
     */
    public function len () {

        return strlen($this->currentString);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Return part of a string
     * @link http://php.net/manual/en/function.substr.php
     * @param int $start <p>
     * If start is non-negative, the returned string
     * will start at the start'th position in
     * string, counting from zero. For instance,
     * in the string 'abcdef', the character at
     * position 0 is 'a', the
     * character at position 2 is
     * 'c', and so forth.
     * </p>
     * <p>
     * If start is negative, the returned string
     * will start at the start'th character
     * from the end of string.
     * </p>
     * <p>
     * If string is less than or equal to
     * start characters long, false will be returned.
     * </p>
     * <p>
     * Using a negative start
     * ]]>
     * </p>
     * @param int $length [optional] <p>
     * If length is given and is positive, the string
     * returned will contain at most length characters
     * beginning from start (depending on the length of
     * string).
     * </p>
     * <p>
     * If length is given and is negative, then that many
     * characters will be omitted from the end of string
     * (after the start position has been calculated when a
     * start is negative). If
     * start denotes a position beyond this truncation,
     * an empty string will be returned.
     * </p>
     * <p>
     * If length is given and is 0,
     * false or &null; an empty string will be returned.
     * </p>
     * Using a negative length
     * ]]>
     * @return string the extracted part of string or false on failure.
     */
    public function sub ($start=0,$length=null) {

        $this->currentString = substr($this->currentString,$start,$length);

    return $this;
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Replace all occurrences of the search string with the replacement string
     * @link http://php.net/manual/en/function.str-replace.php
     * @param string $search <p>
     * The value being searched for, otherwise known as the needle.
     * </p>
     * @param string $replace <p>
     * The replacement value that replaces found search values.
     * </p>
     * @param int $count [optional] If passed, this will hold the number of matched and replaced needles.
     * @return oString oString with the replaced values.
     */
    public function replace ($search = '',$replace='') {
        $this->currentString = str_replace($search,$replace,$this->currentString);

    return $this;
    }


    /**
     * (PHP 4, PHP 5)<br/>
     * Strip whitespace (or other characters) from the beginning and end of a string
     * @link http://php.net/manual/en/function.trim.php
     * @param string $charlist [optional] <p>
     * Optionally, the stripped characters can also be specified using
     * the charlist parameter.
     * Simply list all characters that you want to be stripped. With
     * .. you can specify a range of characters.
     * </p>
     * @return string The trimmed string.
     */
    public function trim ($charlist=null) {

        if ($charlist) {
            $this->currentString = trim($this->currentString,$charlist);
        } else {
            $this->currentString = trim($this->currentString);
        }

    return $this;
    }


    /**
     * (PHP 4, PHP 5)<br/>
     * Uppercase the first character of each word in a string
     * @link http://php.net/manual/en/function.ucwords.php
     * @return string the modified string.
     */
    public function ucwords () {

        $this->currentString = ucwords($this->currentString);

    return $this;
    }



	/**
	 * (PHP 4, PHP 5)<br/>
	 * Split a string by string
	 * @link http://php.net/manual/en/function.explode.php
	 * @param string $delimiter <p>
	 * The boundary string.
	 * </p>
	 * @param int $limit [optional] <p>
	 * If limit is set and positive, the returned array will contain
	 * a maximum of limit elements with the last
	 * element containing the rest of string.
	 * </p>
	 * @return oArray
	 * If delimiter contains a value that is not
	 * contained in string and a negative
	 * limit is used, then an empty oArray will be
	 * returned. For any other limit, oArray containing
	 * string will be returned.
	 */
	public function explode ( $delimiter, $limit=null ) {

		if ( is_null($limit) ) {
			return new oArray( explode( $delimiter, $this->currentString) );
		} else {
			return new oArray( explode( $delimiter, $this->currentString, $limit ) );
		}

	}







    /**
     * EXTRA SWEETIES
     */






	/**
	 * Validate string
	 * @link http://todo
	 * @return string
	 */
	public function validate ($pattern) {

		if ($pattern == 'email') {
			return filter_var($this->currentString, FILTER_VALIDATE_EMAIL);
		}

	return false;
	}


    /**
     * Humanize And Capitalize dehumanized_String
     * @link http://todo
     * @return string
     */
    public function glorify () {

        $this->humanize();
        $this->ucwords();

    return $this;
    }


    /**
     * CamelIze just_anyString
     * @link http://todo
     * @return string
     */
    public function camelize () {

        $this->glorify();
        $this->replace(' ','');

    return $this;
    }


    /**
     * this is humanized string
     * this_is_not_humanized_string
     * @link http://todo
     * @return string
     */
    public function humanize() {

        $this->replace('_',' ');

    return $this;
    }


    /**
     * de_camel_ize SomeCamelizedStriong
     * @link http://todo
     * @return string
     */
    function deCamelize($string) {

        if (strtolower($string) == $string) {
            return $string;
        }

        $string = lcfirst($string);
        $string = preg_replace_callback('([A-Z])',
            function ( $match ) {
                return '_'.strtolower($match[0]);
            },
            $string);

        $this->currentString = $string;

    return $this;
    }


    /**
     * @return string
     */
    public function __toString() {

    return $this->currentString;
    }

}

