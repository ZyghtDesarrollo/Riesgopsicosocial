<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Answer_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Answer';
		$this->id = 'id';
	}

	public function create($travel_id, $question_id, $value) {
		$this->db->insert($this->table, array(
			'travel_id' => $travel_id,
			'question_id' => $question_id,
			'value' => $value
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}
}
