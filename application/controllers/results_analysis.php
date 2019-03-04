<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Results_analysis extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){	
		$view['content'] = $this->load->view('results_analysis',NULL, TRUE);
		$this->load->view('layout', $view);
	}
	
	public function questionary(){
		$view['content'] = $this->load->view('questionary',NULL, TRUE);
		$this->load->view('layout', $view);
	}
	
	public function answered_questionary(){
		$view['content'] = $this->load->view('answered_questionary',NULL, TRUE);
		$this->load->view('layout', $view);
	}
	
	public function get_results(){
		$view['content'] = $this->load->view('results_analysis',NULL, TRUE);
		$this->load->view('layout', $view);
	}

    public function get_global_result(){
        $view['content'] = $this->load->view('global_results_analysis',NULL, TRUE);
        $this->load->view('layout', $view);
    }
	
    public function get_report(){
		$company_id = 18;
		$questionary_id = 1;
		
		if (!$questionary_id) {
			$this->response_error(400);
		}
		
		$this->load->model('questionary_model');
		$report_data = $this->questionary_model->get_questionary_report($company_id, $questionary_id);
		
        $view['content'] = $this->load->view('exportable_report',$report_data, TRUE);
        $this->load->view('layout', $view);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
