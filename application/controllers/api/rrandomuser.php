<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rrandomuser extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rrandomuser';

		$this->load->model('randomuser_model');
	}

	public function generate_post() {
		$result = $this->randomuser_model->generate(
			$this->post('company_id'),
			$this->post('amount')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_company_id_get() {
		$result = $this->randomuser_model->get_by_company_id($this->get('company_id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_id_get() {
		if (!$this->get('id')) {
			$this->response_error(400);
		}

		$result = $this->randomuser_model->get_by_id($this->get('id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	// USER -------------------------------------------------------------------------------

	public function login_post() {
		$user = $this->randomuser_model->login(
			$this->post('password'),
			$this->post('code'),
			$this->post('deviceToken')
		);

		if ($user === FALSE) {
			$this->response_error(404);
		}

		$this->response(array(
			'access_token' => $user->access_token,
			'user' => $user
		), 200);
	}
}

