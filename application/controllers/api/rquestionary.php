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

	public function list_completions_by_company_id_get() {
		if (!$this->get('company_id')) {
			$this->response_error(400);
		}
		
 		$result = $this->questionary_model->get_questionary_completions_by_company_id($this->get('company_id'));
	
		if ($result === FALSE) {
			$this->response_error(404);
		}
	
		$this->response_ok($result);
	}
	
	public function add_recommendations_post(){
		$questionaryc_id = $this->post('id');
		$recommendations = $this->post('recommendations');
		
		if (!$questionaryc_id) {
			$this->response_error(400);
		}
		
		$result = $this->questionary_model->set_recommendations($questionaryc_id, $recommendations);
		
		if ($result === FALSE) {
			$this->response_error(404);
		}
		
		$this->response_ok($result);
	}
}

