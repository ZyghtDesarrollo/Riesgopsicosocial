<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rjobposition extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rjobposition';

		$this->load->model('jobposition_model');
	}

	public function add_post() {
		$result = $this->jobposition_model->create(
			$this->post('company_id'),
			$this->post('position')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function edit_post() {
		$result = $this->jobposition_model->update(
			$this->post('id'), 
			$this->post('company_id'),
			$this->post('position')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_company_id_get() {
		$result = $this->jobposition_model->get_by_company_id($this->get('company_id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_id_get() {
		if (!$this->get('id')) {
			$this->response_error(400);
		}

		$result = $this->jobposition_model->get_by_id($this->get('id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}


}
