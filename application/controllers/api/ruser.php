<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Ruser extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Ruser';

		$this->load->model('user_model');
	}

	//TODO: Validar usuario unico
	public function add_post() {
		$result = $this->user_model->create(
			$this->post('username'), 
			$this->post('password'),
			$this->post('admin'),
			$this->post('company_id'),
			1
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	//TODO: Validar usuario unico
	public function edit_post() {
		$result = $this->user_model->update(
			$this->post('id'), 
			$this->post('username'), 
			$this->post('password'),
			$this->post('admin'),
			$this->post('company_id')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function activate_post() {
		$result = $this->user_model->activate($this->post('id'), 1);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function deactivate_post() {
		$result = $this->user_model->activate($this->post('id'), 0);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_actives_get() {
		$result = $this->user_model->get_actives();

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_get() {
		$result = $this->user_model->get_all_with_company($this->get('company_id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_id_get() {
		if (!$this->get('id')) {
			$this->response_error(400);
		}

		$result = $this->user_model->get_by_id($this->get('id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function login_post() {
		$user = $this->user_model->login(
			$this->post('username'), 
			$this->post('password'),
			$this->post('code')
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
