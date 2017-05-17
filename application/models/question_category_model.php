<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Question_category_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'QuestionCategory';
		$this->id = 'id';
	}
	
	public function get_actives() {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('active', 1);
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}

}
