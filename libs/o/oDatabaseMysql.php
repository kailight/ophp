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


	function init() {

		self::setConfig();

	}


	static function setConfig($config=null) {

		parent::setConfig($config);


	}



	/**
	 * Connect to MYSQL DB
	 */
	static function connect() {
		rec('oDatabase:connect()');



		self::$link = new \mysqli($config['host'], $config['user'], $config['pass']);
		if (self::$link->connect_errno) {
			msg ("Failed to connect to MySQL: (" . self::$link->connect_errno . ") " . self::$link->connect_error);
		}
		self::$link->select_db($config['database']);
		if (self::$link->connect_error) {
			throw new oException(self::$link->connect_error,0);
		}


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




}