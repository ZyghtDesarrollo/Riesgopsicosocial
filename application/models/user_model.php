<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class User_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'AppUser';
		$this->id = 'id';
	}

	public function create($username, $password, $admin, $company_id) {
		$this->db->insert($this->table, array(
			'username' => $username,
			'password' => $password,
			'admin' => $admin,
			'company_id' => $company_id,
			'active' => 1
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}

	public function update($id, $username = NULL, $password = NULL, $admin = NULL, $company_id = NULL) {
		$data = array();

		if (isset($username)) {
			$data['username'] = $username;
		}
		
		if (isset($password)) {
			$data['password'] = $password;
		}

		if (isset($admin)) {
			$data['admin'] = $admin;
		}

		if (isset($company_id)) {
			$data['company_id'] = $company_id;
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
	
	public function get_all_with_company($company_id = '') {
		$this->db->select('u.*, c.name as company');
		$this->db->from($this->table ." as u");
		$this->db->join("Company as c", "c.id = u.company_id");
		$this->db->where('u.username != ', 'superadmin');

		if ($company_id != '') {
			$this->db->where('u.company_id', $company_id);
		}

		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}

	public function login($username, $password, $company_code) {
		$this->db->select(
			$this->table .'.id, ' . 
			$this->table .'.username, ' . 
			$this->table .'.admin, ' . 
			$this->table .'.company_id, ' . 
			$this->table .'.active, ' . 
			$this->table .'.access_token'
		);
		$this->db->from($this->table);
		$this->db->join('company', 'company.id = '. $this->table .'.company_id', 'left');
		$this->db->where($this->table .'.active', 1);

		if (!empty($company_code)) {
			$this->db->where($this->table .'.username', $username);
			$this->db->where($this->table .'.password', $password);
			$this->db->where('company.code', $company_code);
			$this->db->where('company.active', 1);
		} else {
			// superadmin
			$this->db->where($this->table .'.username', SUPER_ADMIN_USER);
			$this->db->where($this->table .'.password', SUPER_ADMIN_PASS);			
		}

		$query = $this->db->get();

		if ($query->num_rows() == 0) {
			return FALSE;
		}

		$users = $query->result();
		$user = $users[0];
		$user->access_token = $this->_generate_token($user->id);

		return $user;
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
