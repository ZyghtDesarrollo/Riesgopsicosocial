<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Psychosocial_team extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){	
		$view['content'] = $this->load->view('psychosocial_team',NULL, TRUE);
		$this->load->view('layout', $view);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
