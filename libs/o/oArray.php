<?php

namespace o;


class oArray implements \Iterator {

    public $originalArray = Array();
    public $currentArray = Array();
    private $__position;

    /**
     * ITERATION
     */


    public function __construct() {
        $this->__position = 0;

        if ( func_num_args() > 0 ) {
            $array = func_get_args();
        } else if ( func_num_args() == 1 ) {
            $array =  func_get_arg(0);
            if (!is_array($array)) {
                if (is_string($array) || is_int($array)) {
                    $array = Array($array);
                }
            }
        } else {
            $array = Array();
        }


        $this->currentArray = $array;
        $this->originalArray = $array;

    }

    function rewind() {
        $this->__position = 0;
    }

    function current() {
        return $this->currentArray[$this->__position];
    }

    function key() {
        return $this->__position;
    }

    function next() {
        ++$this->__position;
    }

    function valid() {
        return isset($this->currentArray[$this->__position]);
    }


    /**
     * STANDARD
     *
     *
     *
     *
     *
     *
     *
     *
     */



    /**
     * (PHP 4 &gt;= 4.0.7, PHP 5)<br/>
     * Checks if the given key or index exists in the array
     * @link http://php.net/manual/en/function.array-key-exists.php
     * @param mixed $key <p>
     * Value to check.
     * </p>
     * @return bool true on success or false on failure.
     */
    function keyExists( $key )
    {
        if (function_exists('array_key_exists')) {
            return array_key_exists( $key, $this->currentArray );
        } else if (function_exists('key_exists')) {
            return key_exists( $key, $this->currentArray );
        }
    }



    /**
     * (PHP 4, PHP 5)<br/>
     * Exchanges all keys with their associated values in an array
     * @link http://php.net/manual/en/function.array-flip.php
     * </p>
     * @return iArray
     */
    function flip( )
    {
            $this->currentArray = array_flip( $this->currentArray );
            return $this;
    }



	/**
	 * (PHP 4, PHP 5)<br/>
	 * Counts all elements in an array
	 * @link http://php.net/manual/en/function.count.php
	 * @param int $mode [optional] If the optional mode parameter is set to
	 * COUNT_RECURSIVE (or 1), count
	 * will recursively count the array. This is particularly useful for
	 * counting all the elements of a multidimensional array. count does not detect infinite recursion.
	 * @return int the number of elements in var, which is
	 * typically an array, since anything else will have one
	 * element.
	 * </p>
	 * <p>
	 * If var is not an array
	 * 1 will be returned.
	 * There is one exception, if var is &null;,
	 * 0 will be returned.
	 * </p>
	 * <p>
	 * Caution: count may return 0 for a variable that isn't set,
	 * but it may also return 0 for a variable that has been initialized with an
	 * empty array. Use isset to test if a variable is set.
	 */
	function count( $mode=COUNT_NORMAL )
	{
		return count( $this->currentArray, $mode );
	}


    /**
     * (PHP 4, PHP 5)<br/>
     * Join array elements with a string
     * @link http://php.net/manual/en/function.implode.php
     * @param string $glue [optional]<p>
     * Defaults to an empty string. This is not the preferred usage of
     * implode as glue would be
     * the second parameter and thus, the bad prototype would be used.
     * </p>
     * @return iString with a string containing a string representation of all the array
     * elements in the same order, with the glue string between each element.
     */
    function implode( $glue='' )
    {

    return new oString (implode( $glue, $this->currentArray ));
    }



    /**
     * (PHP 4, PHP 5)<br/>
     * Return an array with elements in reverse order
     * @link http://php.net/manual/en/function.array-reverse.php
     * @param bool $preserve_keys [optional] <p>
     * If set to true keys are preserved.
     * </p>
     * @return array the reversed array.
     */
    function reverse( $preserve_keys=null )
    {
        $this->currentArray = array_reverse($this->currentArray, $preserve_keys);
        return $this;
    }


	/**
	 * (PHP 4, PHP 5)<br/>
	 * Return all the keys of an array
	 * @link http://php.net/manual/en/function.array-keys.php
	 * </p>
	 * @param mixed $search_value [optional] <p>
	 * If specified, then only keys containing these values are returned.
	 * </p>
	 * @param bool $strict [optional] <p>
	 * Determines if strict comparison (===) should be used during the search.
	 * </p>
	 * @return array an array of all the keys in input.
	 */
	function keys( $search_value = null, $strict = null )
	{
		if (function_exists('array_keys')) {
			return array_keys($this->currentArray, $search_value, $strict );
		}
	}




	/**
	 * SWEETIES
	 * @todo make optional
	 *
	 * @param string $key
	 * A key to get
	 */

	function get($key=null) {

		if ($key===null) {
			return $this->currentArray;
		}
		if (isset($this->currentArray[$key])) {
			return $this->currentArray[$key];
		}

	return null;
	}




}