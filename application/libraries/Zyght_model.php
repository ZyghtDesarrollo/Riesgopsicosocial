<?php defined('BASEPATH') OR exit('No direct script access allowed');

abstract class Zyght_model extends CI_Model {
	private $CI;
	protected $table = NULL;
	protected $id = NULL;

	function __construct() {
		parent::__construct();
		$this->CI =& get_instance();
		
		$this->load->database();
	}

	function get_all() {
		$this->CI->db->select('*');
		$this->CI->db->from($this->table);
		$query = $this->CI->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	function get_by_id($id) {
		$this->CI->db->select('*');
		$this->CI->db->from($this->table);
		$this->CI->db->where($this->id, $id);
		$query = $this->CI->db->get();

		return ($query->num_rows() > 0) ? $query->row() : array();
	}
}

