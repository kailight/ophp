<?php

namespace o;

/**
 * Class o
 * @package o
 * Serves as Factory
 * <code>
 * $database = o::init('Database');
 * echo classname($database);
 * // o\oDatabase
 * // or, if config is set to type = mysql
 * // o\oDatabaseMysql
 * </code>
 * @example
 */
class o implements StaticInit {

	/**
	 * @var \mysqli | \SQLiteDatabase
	 */
	static $link = null;
	static $config;

	static function o($class) {
		self::init($class);
	}

	/**
	 * @param string $class
	 * @param mixed $args
	 * @return object | false on failure
	 */
	static function init() {

		if (!func_num_args()) {
			return;
			// throw new oException("o is a Factory and should be passed at least one parameter",0);
		}
		$args = func_get_args();
		if ($args[0]) {
			$classname = $args[0];
		}



		if (strpos($classname,'o') !== 0) {
			$classname = 'o'.$classname;
		}
		if (__NAMESPACE__) {
			$classname = __NAMESPACE__.'\\'.$classname;
		}

		if ( $classname && class_exists($classname) ) {
			if ($args[1]) {
				$args = $args[1];
			}
			$class = new $classname($args);
		} else {
			return false;
		}

		$classname = get_class($class);


		if ( str_replace('o\o','',$classname) == 'Database' ) {
			if ($class::$config && $class::$config['type']) {
				if (__NAMESPACE__) {
					$classname2 = $classname.ucfirst($class::$config['type']);
					$class2 = new $classname2($args);
					return $class2;
				}
			}
		}

	return $class;
	}






}