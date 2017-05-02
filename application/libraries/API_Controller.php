<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once('REST_Controller.php');

abstract class API_Controller extends REST_Controller {
	public function __construct() {
		parent::__construct();

		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  
			
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		}
	}

	public function response_ok($data) {
		$result = array(
			"response" => $data
		);

		$this->response($result, 200);
	}

	public function response_error($number, $errors = array()) {
		$result = array(
			"errors" => $errors
		);

		$this->response($result, $number);
	}
	
	public function json_decode($value) {
		$response = json_decode($value);

		if (!$response) {
			$response = json_decode(stripslashes($value));
		}

		return $response;
	}
	
	public function get_access_token() {
		return $_SERVER["HTTP_AUTHORIZATION"];
	}
}
