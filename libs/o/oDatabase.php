<?php

namespace o;

class oDatabase {



	/**
	 * @var \mysqli | \SQLiteDatabase
	 */
	static $link = null;
	static $config;

	function oDatabase() {
		return $this->init();
	}


	function init() {

		return self::setConfig();

	}


	static function setConfig( $config = null ) {

		if (!$config) {
			$config = oConfig::database();
		}

		self::$config = $config;

		prd(self::$config);

		if ( self::$config['type'] ) {
			if ( self::$config['type'] == 'mysql') {
				return new oDatabaseMysql();
			}
		}

	}


	/**
	 * Connect to MYSQL DB
	 */
	static function connect() {
		rec('oDatabase:connect()');



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
	 * @return array
	 * @throws oException
	 */
	static function query( $query ) {

		if (!self::$link) {
			self::connect();
		}

		$result = self::$link->query($query);

		if (!$result) {
			throw new oException( self::$link->error, 1);
		}

		$db_config = oConfig::database();

		$data = array();
		if ($db_config['type'] == 'mysql') {
			if ($result->num_rows) {
				while ($row = $result->fetch_assoc()) {
					$data[] = $row;
				}
			}
		}
		else if ($db_config['type'] == 'sqlite') {
			while($row = $result->fetchArray(1)){
				$data[] = $row;
			}
		}

		return $data;
	}




}