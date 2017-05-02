<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');
require_once(APPPATH . 'models/travellog_model.php');
require_once(APPPATH . 'models/answer_model.php');

class Travel_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Travel';
		$this->id = 'id';

		$this->load->model('travellog_model');
		$this->load->model('answer_model');
	}

	public function create($user, $answers, $travel_logs) {
		$this->db->trans_start();

		$this->db->insert($this->table, array(
			'user_id' => $user->id,
			'date' => date('Y-m-d H:i:s')
		));

		$travel_id = $this->db->insert_id();

		foreach ($answers as $answer) {
			$this->answer_model->create($travel_id, $answer->question_id, $answer->value);
		}

		foreach ($travel_logs as $log) {
			$this->travellog_model->create($travel_id, $log->latitude, $log->longitude, $log->date);
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			// generate an error... or use the log_message() function to log your error
			return FALSE;
		}

		return $travel_id;
	}

	public function get_all_with_user($company_id = '', $user_id = '') {
		$this->db->select('t.*, u.username as appuser');
		$this->db->from($this->table ." as t");
		$this->db->join("Appuser as u", "u.id = t.user_id");

		if ($company_id != '') {
			$this->db->where('u.company_id', $company_id);
		}

		if ($user_id != '') {
			$this->db->where('u.id', $user_id);
		}

		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}
}
