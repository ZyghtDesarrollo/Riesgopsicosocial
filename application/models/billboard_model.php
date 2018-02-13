<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'libraries/Zyght_Model.php');

class Billboard_model extends Zyght_model {

	public function __construct(){
		parent::__construct();

		$this->table = 'Billboard';
		$this->id = 'id';
	}

	public function create($company_id, $content) {
	    $prev_record_id = $this->_is_exists($company_id);

	    if($prev_record_id){
	       return $this->update($prev_record_id, $company_id, $content);
        }else{
            $this->db->insert($this->table, array(
                'company_id' => $company_id,
                'content' => $content
            ));

            $id = $this->db->insert_id();

            return ($id > 0) ? $id : FALSE;
        }
	}

	private function _is_exists($company_id){
	    $this->db->select('id');
	    $this->db->from($this->table);
	    $this->db->where('company_id', (int) $company_id);
	    $query = $this->db->get();

	    return ($query->num_rows > 0 ) ? $query->row()->id : FALSE;
    }

	public function update($id, $company_id = NULL, $content = NULL) {
		if (!empty($id) && !empty($company_id) && !empty($content)) {
			$this->db->where($this->id, (int) $id);
			$this->db->where('company_id', (int) $company_id);
			return $this->db->update($this->table, ['content' => $content]);
		}
		
		return FALSE;
	}

    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($this->id, $id);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row() : FALSE;
    }

	public function get_by_company_id($company_id) {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('company_id', (int) $company_id);

		$query = $this->db->get();

 		return ($query->num_rows() > 0) ? $query->row() : FALSE;
	}

    public function get_by_company_code($code, $published = NULL) {
        $this->db->select('b.*, c.name');
        $this->db->from($this->table.' AS b');
        $this->db->join('Company AS c', 'c.id = b.company_id');
        $this->db->where('c.code', (int) $code);

        if(!empty($published)){
            $this->db->where('published', 1);
        }

        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row() : FALSE;
    }

    public function publish($billboard_id, $flag = 0){
	    $this->db->where('id', (int) $billboard_id);
	    if($this->db->update($this->table, ['published' => $flag])){
            return $this->get_by_id($billboard_id);
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

}
