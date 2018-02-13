<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rrecommendation extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rrecommendation';

        $this->load->model('recommendation_model');
        $this->load->model('random_user_recommendation_views_model');
	}

	public function add_post() {
		$result = $this->recommendation_model->create(
			$this->post('title'),
			$this->post('link'),
			$this->post('description'),
			$this->post('questionCategoryId'),
			$this->post('company_id')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function edit_post() {
		$result = $this->recommendation_model->update(
			$this->post('id'), 
			$this->post('title'),
			$this->post('link'),
			$this->post('description'),
			$this->post('questionCategoryId')
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}
	
	public function associate_by_job_position_post(){
		$result = $this->recommendation_model->associate_recommendations(
					$this->post('recommendations_id'),
					$this->post('job_position_id')
				);
		
				if ($result === FALSE) {
					$this->response_error(404);
				}
				
		$this->response_ok($result);
	}

	public function activate_post() {
		$result = $this->recommendation_model->activate($this->post('id'), 1);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function deactivate_post() {
		$result = $this->recommendation_model->activate($this->post('id'), 0);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_actives_get() {
		$result = $this->recommendation_model->get_actives();

		$this->response_ok($result);
	}
	
	public function list_actives_by_company_id_get() {
		if (!$this->get('company_id')) {
			$this->response_error(400);
		}
	
		$result = $this->recommendation_model->get_by_company_id($this->get('company_id'));
	
		$this->response_ok($result);
	}
	
	public function list_by_job_position_id_get() {
		if (!$this->get('job_position_id')) {
			$this->response_error(400);
		}

		$result = $this->recommendation_model->get_by_job_position_id($this->get('job_position_id'));
		
		$this->response_ok($result);
	}

	public function list_get() {
		$result = $this->recommendation_model->get_all();

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function list_by_id_get() {
		if (!$this->get('id')) {
			$this->response_error(400);
		}

		$result = $this->recommendation_model->get_by_id($this->get('id'));

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}
	
	public function list_by_params_get() {
		$company_id = $this->get('company_id');
		$job_position_id = $this->get('job_position_id');

		if ( empty($company_id) || empty($job_position_id)) {
			$this->response_error(400); 
		}
	
		$result = $this->recommendation_model->get_by_params($company_id, $job_position_id);
	
		if ($result === FALSE) {
			$this->response_error(404);
		}
	
		$this->response_ok($result);
	}
	
	public function list_by_questionary_completion_id_get() {
		if (!$this->get('qc_id')) {
			$this->response_error(400);
		}
	
		$result = $this->recommendation_model->get_by_questionary_completion_id($this->get('qc_id'));
	
		if ($result === FALSE) {
			$this->response_error(404);
		}
	
		$this->response_ok($result);
	}

    public function register_recommendation_view_post() {
        $random_user_id = $this->post('random_user_id');
        if (is_null($random_user_id) || !is_numeric($random_user_id))
        {
            $this->response_error(400);
        }
        $recommendation_id = $this->post('recommendation_id');
        if (is_null($recommendation_id) || !is_numeric($recommendation_id))
        {
            $this->response_error(400);
        }
        $company_id = $this->post('company_id');
        if (is_null($company_id) || !is_numeric($company_id))
        {
            $this->response_error(400);
        }
        $result = $this->random_user_recommendation_views_model->create(
            $random_user_id,
            $recommendation_id,
            $company_id
        );

        if ($result === FALSE) {
            $this->response_error(404);
        }

        $this->response_ok($result);
    }

    public function register_recommendation_view_get() {
        $random_user_id = $this->get('random_user_id');
        if (is_null($random_user_id) || !is_numeric($random_user_id))
        {
            $this->response_error(400);
        }
        $recommendation_id = $this->get('recommendation_id');
        if (is_null($recommendation_id) || !is_numeric($recommendation_id))
        {
            $this->response_error(400);
        }
        $company_id = $this->get('company_id');
        if (is_null($company_id) || !is_numeric($company_id))
        {
            $this->response_error(400);
        }
        $result = $this->random_user_recommendation_views_model->create(
            $random_user_id,
            $recommendation_id,
            $company_id
        );

        if ($result === FALSE) {
            $this->response_error(404);
        }

        $this->response_ok($result);
    }
}

