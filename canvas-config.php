<?php

Class CanvasConfig {
	
	private $config_file = "canvas.config";

	private $config;

	public function __construct() {
		$this->config = parse_ini_file($this->config_file);
	}

	public function get($name = null) {
		if ($name == null) {
			return $this->config;
		} else if (isset($this->config[$name])) {
			return $this->config[$name];
		} 
		return null;
	}


}


?>