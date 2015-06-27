<?php

namespace o;

class oDatabaseMysql extends oDatabase {



	/**
	 * @var \mysqli
	 */
	static $link = null;
	static $config = null;

	function oDatabase() {
		$this->init();
	}


	static function init() {

		self::setConfig();

	}


	static function setConfig($config=null) {

		parent::setConfig($config);
		self::$config = parent::$config;

	}



	/**
	 * Connect to MYSQL DB
	 */
	static function connect() {
		rec('oDatabaseMysql::connect()');

		if (!self::$config) {
			throw new oException('No database configuration provided',0);
		}

		self::$link = new \mysqli(self::$config['host'], self::$config['user'], self::$config['pass']);
		if (self::$link->connect_errno) {
			msg ("Failed to connect to MySQL: (" . self::$link->connect_errno . ") " . self::$link->connect_error);
		}
		self::$link->select_db(self::$config['database']);
		if (self::$link->connect_error) {
			throw new oException(self::$link->connect_error,0);
		}


	}

	/**
	 * @param string | oString $query
	 * @return oDatabaseResult
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

		$data = array();
		if ($result->num_rows) {
			while ($row = $result->fetch_assoc()) {
				$data[] = $row;
			}
		}

		$data = new oDatabaseResult($data);

	return $data;
	}




}