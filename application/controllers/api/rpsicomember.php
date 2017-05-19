<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rpsicomember extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rpsicomember';

		$this->load->model('psicomember_model');
	}

	public function add_post() {
		$result = $this->psicomember_model->create(
			$this->post('company_id'),
			$this->post('name'),
			$this->post('rut'),
			$this->post('phone'),
			$this->post('email')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function edit_post() {
		$result = $this->psicomember_model->update(
			$this->post('id'), 
			$this->post('company_id'),
			$this->post('name'),
			$this->post('rut'),
			$this->post('phone'),
			$this->post('email')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_get() {
		$result = $this->psicomember_model->get_all();

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_id_get() {
		if (!$this->get('id')) {
			$this->response_error(400);
		}

		$result = $this->psicomember_model->get_by_id($this->get('id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_company_id_get() {
		if (!$this->get('company_id')) {
			$this->response_error(400);
		}

		$result = $this->psicomember_model->get_by_company_id($this->get('company_id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}
	
	public function list_by_company_code_get() {
		if (!$this->get('code')) {
			$this->response_error(400);
		}
	
		$result = $this->psicomember_model->get_by_company_code($this->get('code'));
	
		if ($result === FALSE) {
			$this->response_error(404);
		}
	
		$this->response_ok($result);
	}

}

