<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rquestionary extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rquestionary';

		$this->load->model('questionary_model');
		$this->load->model('randomuser_model');
		$this->load->model('answer_model');
		
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

	public function add_post() {
		$access_token = $this->get_access_token();

		$user = $this->randomuser_model->get_loggedin_user($access_token);
		if ($user === FALSE) {
			$this->response_error(404, array(
				"Error en token"
			));
		}

/*
print_r($user); echo "<hr>";
print_r($this->post('questionaryId')); echo "<hr>";
print_r($this->post('jobPositionId')); echo "<hr>";
print_r($this->post('answers')); echo "<hr>";
die;
*/

		$answers = $this->json_decode($this->post('answers'));

		$result = $this->questionary_model->create(
			$user,
			$this->post('questionaryId'),
			$this->post('jobPositionId'),
			$answers
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

}

