<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Answer_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Answer';
		$this->id = 'id';
	}

	public function create($questionary_completion_id, $question_option_id, $open_answer = null) {
		$this->db->insert($this->table, array(
			'questionary_completion_id' => $questionary_completion_id,
			'question_option_id' => $question_option_id,
			'open_answer' => $open_answer
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}
	
	
	public function get_by_questionary_completion_id($id) {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('questionary_completion_id', $id);
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}

}
