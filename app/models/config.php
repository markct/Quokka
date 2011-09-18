<?php

class ConfigModel {
	
	private $data = array();

	function __construct() {
		if (is_file('app/config.php')) $this->data = require_once('app/config.php');
	}

	function get($key) {
		return array_key_exists($key, $this->data)? $this->data[$key] : null;
	}

}
