<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rcompany extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rcompany';

		$this->load->model('company_model');
	}

	public function add_post() {
		$result = $this->company_model->create(
			$this->post('name'), 
			$this->post('code'),
			$this->post('password'),
			$this->post('email'),
			$this->post('rut')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function edit_post() {
		$result = $this->company_model->update(
			$this->post('id'), 
			$this->post('name'),
			$this->post('code'),
			$this->post('password'),
			$this->post('email'),
			$this->post('rut')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function activate_post() {
		$result = $this->company_model->activate($this->post('id'), 1);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function deactivate_post() {
		$result = $this->company_model->activate($this->post('id'), 0);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_actives_get() {
		$result = $this->company_model->get_actives();
		
		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_get() {
		$result = $this->company_model->get_all();

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_id_get() {
		if (!$this->get('id')) {
			$this->response_error(400);
		}

		$result = $this->company_model->get_by_id($this->get('id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	// USER -------------------------------------------------------------------------------

	public function login_post() {
		if (($this->post('password') == SUPER_ADMIN_PASS) 
			&& ($this->post('code') == SUPER_ADMIN_CODE)) {
			$token = md5(date("mdY_His"));

			$this->response(array(
				'access_token' => $token,
				'user' => [
					'id' => 0,
					'name' => 'superadmin'
				]
			), 200);

			die;
		}


		$company = $this->company_model->login(
			$this->post('password'),
			$this->post('code')
		);

		if ($company === FALSE) {
			$this->response_error(404);
		}

		$this->response(array(
			'access_token' => $company->access_token,
			'user' => $company
		), 200);
	}
	
}
