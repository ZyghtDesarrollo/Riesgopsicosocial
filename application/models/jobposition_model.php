<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Jobposition_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'JobPosition';
		$this->id = 'id';
	}

	public function create($company_id, $position) {
		$this->db->insert($this->table, array(
			'company_id' => $company_id,
			'position' => $position
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}

	public function update($id, $company_id = NULL, $position = NULL) {
		$data = array();

		if (isset($company_id) && !empty($company_id)) {
			$data['company_id'] = $company_id;
		}
		
		if (isset($position) && !empty($position)) {
			$data['position'] = $position;
		}

		if (!empty($data)) {
			$this->db->where($this->id, $id);
			return $this->db->update($this->table, $data);
		}
		
		return FALSE;
	}

	public function get_by_company_id($company_id, $list_inactives = 0) {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('company_id', $company_id);

		if (!$list_inactives) {
			$this->db->where('active', 1);
		}

		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
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

}
