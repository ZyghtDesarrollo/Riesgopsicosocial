<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Company_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Company';
		$this->id = 'id';
	}

	public function create($name, $code, $password, $email, $rut) {
		$this->db->insert($this->table, array(
			'name' => $name,
			'code' => $code,
			'active' => 1,
			'password' => $password,
			'email' => $email,
			'rut' => $rut
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}

	public function update($id, $name = NULL, $code = NULL, $password = NULL, $email = NULL, $rut = NULL) {
		$data = array();

		if (isset($name) && !empty($name)) {
			$data['name'] = $name;
		}
		
		if (isset($code) && !empty($code)) {
			$data['code'] = $code;
		}
		
		if (isset($password) && !empty($password)) {
			$data['password'] = $password;
		}
		
		if (isset($email) && !empty($email)) {
			$data['email'] = $email;
		}
		
		if (isset($rut) && !empty($rut)) {
			$data['rut'] = $rut;
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

	// USER -------------------------------------------------------------------------------

	public function login($password, $company_code) {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where($this->table .'.active', 1);

		if (!empty($company_code)) {
			$this->db->where($this->table .'.password', $password);
			$this->db->where($this->table .'.code', $company_code);
		} else {
			$this->db->where($this->table .'.password', SUPER_ADMIN_PASS);
			$this->db->where($this->table .'.code', SUPER_ADMIN_CODE);			
		}

		$query = $this->db->get();

		if ($query->num_rows() == 0) {
			return FALSE;
		}

		$company = $query->row();
		$company->access_token = $this->_generate_token($company->id);

		return $company;
	}

	private function _generate_token($id) {
		$timestamp = date("mdY_His");
		$token = md5($timestamp);

		$this->db->where($this->id, $id);
		$this->db->update($this->table, array(
			'access_token' => $token
		));

		return $token;
	}

	public function get_loggedin_user($access_token) {
		$this->db->select($this->table .'.*');
		$this->db->from($this->table);
		$this->db->where($this->table .'.access_token', $access_token);
		$this->db->where($this->table .'.active', 1);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$list = $query->result();
			return $list[0];
		}

		return FALSE;
	}

}
