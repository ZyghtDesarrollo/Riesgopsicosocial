<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rquestioncategory extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rquestioncategory';

		$this->load->model('question_category_model');
	}

	public function list_actives_get() {
		$result = $this->question_category_model->get_actives();

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}
	
}
