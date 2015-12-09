<?php include 'optimizely.php';?>
<?php include 'canvas-log.php';?>
<?php


Class Canvas {

	private $config_file = "canvas.config";

	protected $app_id;

	protected $app_version;

	protected $app_name;

	protected $private_key;

	public $debug;

	protected $optimizely;

	protected $canvas_log;

	private $token;

	private $token_type;

	private $project_id;

	public function __construct() {
		$this->readConfig();
		
		$params = $_GET;

		if (!array_key_exists('signed_request', $params)) {
			$this->error("Error: Canvas signed_request parameter is missing");
			exit();
		}

		$signed_request = $params['signed_request'];

		if ($signed_request == null) {
			$this->error("Error: Canvas signed_request parameter is missing");
			exit();
		}
		$signed_request = explode(".", $signed_request);

		if (count($signed_request) != 2) {
			$this->error("Error: Invalid Canvas signed_request parameter");
			exit();
		}

		
		// authorise the canvas app
		if (!$this->authorize($signed_request)) {
			$this->error("Error: The Canvas authentication failed. Please contact the App developer");
			exit();
		}

		// initialise Optimizely
		$this->optimizely = new Optimizely($this->token);
		// make sure we set the token type to bearer
		$this->optimizely->set_token_type($this->token_type);
		
	}

	private function authorize($signed_request) {

		$hash = sha256Encode($signed_request[1], $this->private_key);
		$hash = base64Encode($hash);

		if ($hash != $signed_request[0]) {
			return false;
		} 

		$context = json_decode(base64Decode($signed_request[1]));
		$context = $context->context;
		$this->token = $context->client->access_token;
		$this->token_type = $context->client->token_type;
		$this->project_id = $context->environment->current_project;
		return true;
	}

	private function readConfig() {
		$CONFIG = parse_ini_file($this->config_file);

		if (!array_key_exists('SHOW_ERROR', $CONFIG)) {
			$this->debug = false;
			$this->canvas_log = new Canvas_log(false);
		} else {
			$this->debug = $CONFIG['SHOW_ERROR'];
			error_reporting(E_ALL);
			ini_set('display_errors', 'on');
			$this->canvas_log = new Canvas_log(true);
		}
		
		if (!array_key_exists('APP_ID', $CONFIG)) {
			$this->error("Error: Invalid Canvas config. APP_ID is missing");
			exit();
		}
		$this->app_id = $CONFIG['APP_ID'];

		if (!array_key_exists('APP_VERSION', $CONFIG)) {
			$this->error("Error: Invalid Canvas config. APP_VERSION is missing");
			exit();
		}
		$this->app_version = $CONFIG['APP_VERSION'];

		if (!array_key_exists('APP_NAME', $CONFIG)) {
			$this->error("Error: Invalid Canvas config. APP_NAME is missing");
			exit();
		}
		$this->app_name = $CONFIG['APP_NAME'];

		if (!array_key_exists('PRIVATE_KEY', $CONFIG)) {
			$this->error("Error: Invalid Canvas config. PRIVATE_KEY is missing");
			exit();
		}
		$this->private_key = $CONFIG['PRIVATE_KEY'];
	}

	public function error($msg) {
		$this->canvas_log->error($msg);
	}

	public function debug($msg) {
		$this->canvas_log->debug($msg);
	}

	public function info($msg) {
		$this->canvas_log->info($msg);
	}

	public function get_project_id() {
		return $this->project_id;
	}

	public function get_app_id() {
		return $this->app_id;
	}

	public function get_app_name() {
		return $this->app_name;
	}

	public function get_app_version() {
		return $this->app_version;
	}

	public function get_private_key() {
		return $this->private_key;
	}

	public function get_optimizely() {
		return $this->optimizely;
	}
}


$canvas = new Canvas();

//-----------------------------------



function sha256Encode($str, $key) {
	return hash_hmac('sha256', $str, $key);
}

function base64Encode($str) {
	return base64_encode($str);
}

function base64Decode($str) {
	return base64_decode($str);
}

function debug($msg) {
	echo "<pre>";
	print_r($msg);
	echo "</pre>";
}


?>

