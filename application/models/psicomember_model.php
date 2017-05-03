<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Psicomember_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'PsicoMember';
		$this->id = 'id';
	}

	public function create($company_id, $name, $rut, $phone, $email) {
		$this->db->insert($this->table, array(
			'company_id' => $company_id,
			'name' => $name,
			'rut' => $rut,
			'phone' => $phone,
			'email' => $email
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}

	public function update($id, $company_id = NULL, $name = NULL, $rut = NULL, $phone = NULL, $email = NULL) {
		$data = array();

		if (isset($company_id) && !empty($company_id)) {
			$data['company_id'] = $company_id;
		}
		
		if (isset($name) && !empty($name)) {
			$data['name'] = $name;
		}
		
		if (isset($rut) && !empty($rut)) {
			$data['rut'] = $rut;
		}
		
		if (isset($phone) && !empty($phone)) {
			$data['phone'] = $phone;
		}
		
		if (isset($email) && !empty($email)) {
			$data['email'] = $email;
		}
		
		if (!empty($data)) {
			$this->db->where($this->id, $id);
			return $this->db->update($this->table, $data);
		}
		
		return FALSE;
	}

	public function get_by_company_id($company_id) {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where($this->table . '.company_id', $company_id);
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}

}

