<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_positions extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){	
		$view['content'] = $this->load->view('job_positions',NULL, TRUE);
		$this->load->view('layout', $view);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
