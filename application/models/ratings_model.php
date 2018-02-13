<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Ratings_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Ratings';
		$this->id = 'id';
	}
	
	public function get_all() {
		$this->db->select('*');
		$this->db->from($this->table);
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$result = [];
			foreach ($query->result() as $row){
				$result[$row->question_category_id] = 
					array(
						'low' => array('min' => $row->low_min, 
									  'max' => $row->low_max),
						'medium' => array('min' => $row->medium_min,
										  'max' => $row->medium_max),
						'high' => array('min' => $row->high_min,
										'max' => $row->high_max)
					);
			}
			return $result;
		}
		return  FALSE;
	}
}
