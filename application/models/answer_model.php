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

echo $this->db->last_query();
echo "<hr>";
echo $id . "<hr>";

		return ($id > 0) ? $id : FALSE;
	}

}
