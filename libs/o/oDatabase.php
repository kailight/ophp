<?php

namespace o;

class oDatabase implements StaticInit {



	/**
	 * @var \mysqli | \SQLiteDatabase
	 */
	static $link = null;
	static $config;
	public static $database;

	function oDatabase() {
		$this->init();
	}


	static function init() {

		self::setConfig();

	}


	static function setConfig( $config = null ) {

		if (!$config) {
			$config = oConfig::database();
		}

		self::$config = $config;

	}


	/**
	 * Connect to MYSQL DB
	 */
	static function connect() {
		rec('oDatabase:connect() - this method should be extended');


		/*
		if (self::$config['type'] == "sqlite") {
			if (phpversion() > '5.4' && phpversion() < '5.5') {
				self::$link = new \SQLite3(ROOT.self::$config['database']);
			} else {
				self::$link = new \SQLiteDatabase(ROOT.self::$config['database']);
			}
		}
		*/

	}

	/**
	 * @param string | oString $query
	 * @return oDatabaseResult|null
	 * @throws oException
	 */
	static function query( $query ) {
		warning(__CLASS__.':query() - this method should be extended');

	}




}