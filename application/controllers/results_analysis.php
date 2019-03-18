<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// instantiate and use the dompdf class
require_once APPPATH.'third_party/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

class Results_analysis extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('questionary_model');
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
	
    public function get_report($company_id = NULL, $questionary_id = 1){
		if (empty($questionary_id) || empty($company_id)) {
			$this->response_error(400);
		}
		
		$report_data = $this->questionary_model->get_questionary_report($company_id, $questionary_id);
		$report_data['printToPdf'] = false;
		
        $view['content'] = $this->load->view('exportable_report',$report_data, TRUE);
		
        $this->load->view('layout', $view);
    }
	
    public function get_pdf_report($company_id = NULL, $questionary_id = 1){
		if (empty($questionary_id) || empty($company_id)) {
			$this->response_error(400);
		}
		
		$report_data = $this->questionary_model->get_questionary_report($company_id, $questionary_id);
		$report_data['printToPdf'] = true;
		
        $view['content'] = $this->load->view('exportable_report',$report_data, TRUE);
		
		
		$dompdf = new Dompdf();
		$dompdf->loadHtml($view['content']);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'portrait');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
