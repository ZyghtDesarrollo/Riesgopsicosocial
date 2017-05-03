<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rquestionary extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rquestionary';

		$this->load->model('questionary_model');
		$this->load->helper('file');
	}

	public function initialdata_get() {
		$file = FCPATH . 'application/uploads/questionaries.json';
		$content = file_get_contents($file);
		$response = json_decode($content);

		$this->response($response, 200);
	}

	public function create_initialdata_get() {
		$questionaries = $this->questionary_model->get_questionaries();

		$file = FCPATH . 'application/uploads/questionaries.json';
		$data = json_encode($questionaries, JSON_UNESCAPED_UNICODE);
		write_file($file, $data);

		$this->response($questionaries, 200);
	}

}

