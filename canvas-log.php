<?php

Class Canvas_log {

	private $debug = false;

	public $EVENT_TYPE_INFO = "EVENT_TYPE_INFO";
	public $EVENT_TYPE_ERROR = "EVENT_TYPE_ERROR";
	public $EVENT_TYPE_DEBUG = "EVENT_TYPE_DEBUG";

	public function __construct($debug) {
		$this->debug = $debug;
	}

	public function log_event($event_type, $msg) {

	}

	public function error($msg) {
		$this->log_event($this->EVENT_TYPE_ERROR, $msg);
		if ($this->debug) {
			echo "<pre class='canvas-error'>";
			print_r($msg);
			echo "</pre>";
		}
	}

	public function info($msg) {
		$this->log_event($this->EVENT_TYPE_INFO, $msg);
		if ($this->debug) {
			echo "<pre class='canvas-info'>";
			print_r($msg);
			echo "</pre>";
		}
	}

	public function debug($msg) {
		$this->log_event($this->EVENT_TYPE_DEBUG, $msg);
		if ($this->debug) {
			echo "<pre class='canvas-debug'>";
			print_r($msg);
			echo "</pre>";
		}
	}

}