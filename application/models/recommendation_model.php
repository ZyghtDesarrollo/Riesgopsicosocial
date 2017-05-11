<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Recommendation_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Recommendation';
		$this->id = 'id';
	}

	public function create($title, $link, $description) {
		$this->db->insert($this->table, array(
			'title' => $title,
			'link' => $link,
			'description' => $description,
			'active' => 1
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}

	public function update($id, $title = NULL, $link = NULL, $description = NULL) {
		$data = array();

		if (isset($title) && !empty($title)) {
			$data['title'] = $title;
		}
		
		if (isset($link) && !empty($link)) {
			$data['link'] = $link;
		}
		
		if (isset($description) && !empty($description)) {
			$data['description'] = $description;
		}
		
		if (!empty($data)) {
			$this->db->where($this->id, $id);
			return $this->db->update($this->table, $data);
		}
		
		return FALSE;
	}

	public function activate($id, $activate) {
		$data = array();

		if (isset($activate)) {
			$data['active'] = $activate;
		}
		
		if (!empty($data)) {
			$this->db->where($this->id, $id);
			return $this->db->update($this->table, $data);
		}
		
		return FALSE;
	}
	
	public function get_actives() {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('active', 1);
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	public function get_by_questionary_completion_id($qc_id){
		$this->db->select('r.*');
		$this->db->from($this->table.' AS r');
		$this->db->join('QuestionaryRecommendations AS qr','qr.recommendation_id = r.id');
		$this->db->where('qr.questionary_completion_id', (int) $qc_id);
		$this->db->order_by('r.id', 'ASC');
		$query = $this->db->get();
		
		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}
}