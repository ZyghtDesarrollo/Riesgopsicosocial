<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rcompany extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rcompany';

        $this->load->model('company_model');
        $this->load->model('questionary_model');
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

        foreach ($result as $result_row)
        {
            $company_risk = $this->questionary_model->get_category_results_by_job_position_id(-1, $result_row->id);
            $calculated_risk = 0;
            if(!empty($company_risk) && array_key_exists("percent",$company_risk))
            {
                foreach($company_risk["percent"] as $category_risk)
                {
                    if(array_key_exists("risk_high",$category_risk) && 50 <= $category_risk["risk_high"])
                    {
                        $calculated_risk++;
                    }
                    if(array_key_exists("risk_low",$category_risk) && 50 <= $category_risk["risk_low"])
                    {
                        $calculated_risk--;
                    }
                }
            }
            $result_row->company_risk = $calculated_risk;
            $result_row->company_risk_name = MEDIUM_RISK_NAME;
            if(MEDIUM_RISK_THRESHOLD > $calculated_risk )
            {
                $result_row->company_risk_name = LOW_RISK_NAME;
            }
            else if(HIGH_RISK_THRESHOLD <= $calculated_risk )
            {
                $result_row->company_risk_name = HIGH_RISK_NAME;
            }
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
