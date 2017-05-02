<?php defined('BASEPATH') OR exit('No direct script access allowed');

abstract class Zyght_model extends CI_Model {
	protected $table = NULL;
	protected $id = NULL;

	function __construct() {
		parent::__construct();
		
		$this->load->database();
	}

	function get_all() {
		$this->db->select('*');
		$this->db->from($this->table);
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	function get_by_id($id) {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where($this->id, $id);
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->row() : array();
	}
}

