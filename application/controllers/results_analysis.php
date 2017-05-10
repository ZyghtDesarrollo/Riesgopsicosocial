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
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
