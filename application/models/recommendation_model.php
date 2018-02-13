<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Recommendation_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Recommendation';
		$this->id = 'id';
	}

	public function create($title, $link, $description, $question_category_id, $company_id) {
		$this->db->insert($this->table, array(
			'title' => $title,
			'link' => $link,
			'description' => $description,
			'question_category_id' => (int) $question_category_id,
			'company_id' => $company_id,
			'active' => 1
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}

    public function recommendation_exists_at_company($recommendation_id, $company_id){
        $this->db->select('1');
        $this->db->from($this->table);
        $this->db->where($this->id, $recommendation_id);
        $this->db->where('company_id', $company_id);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? TRUE : FALSE;
    }

	public function associate_recommendations($recommendations_id, $job_position_id){
		$this->db->trans_start();
		$this->disassociate_recomendations($job_position_id);
		if(is_array($recommendations_id)){
			foreach ($recommendations_id as $r_id){
				$this->db->insert('PositionRecommendations',
						array(
								'job_position_id' => (int) $job_position_id,
								'recommendation_id' => (int) $r_id
							)
						);
			}
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE) {
			// generate an error... or use the log_message() function to log your error
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function disassociate_recomendations($job_position_id){
		$this->db->where('job_position_id', (int) $job_position_id);
		$this->db->delete('PositionRecommendations');
		return TRUE;
	}

	public function update($id, $title = NULL, $link = NULL, $description = NULL,
			$question_category_id = NULL) {
		$data = array();

		if (!empty($title)) {
			$data['title'] = $title;
		}
		
		if (!empty($link)) {
			$data['link'] = $link;
		}
		
		if (!empty($description)) {
			$data['description'] = $description;
		}
		
		if(!empty($question_category_id)){
			$data['question_category_id'] = (int) $question_category_id;
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
		$this->db->select('r.*, qc.title AS question_category_title');
		$this->db->from($this->table.' AS r');
		$this->db->join('QuestionCategory AS qc', 'qc.id = r.question_category_id');
		$this->db->where('r.active', 1);
		$this->db->order_by('qc.title', 'ASC');
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	public function get_by_company_id($company_id){
		$this->db->select('r.*, qc.title AS question_category_title');
		$this->db->from($this->table.' AS r');
		$this->db->join('QuestionCategory AS qc', 'qc.id = r.question_category_id', 'right');
		$this->db->where('r.active', 1);
		$this->db->where('qc.active', 1);
		$this->db->where('r.company_id', (int) $company_id);
		$this->db->order_by('qc.title', 'ASC');
		$query = $this->db->get();
		
		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}
	
	public function get_by_job_position_id($job_position_id){
		$this->db->select('r.*, qc.title AS question_category_title');
		$this->db->from($this->table.' AS r');
		$this->db->join('QuestionCategory AS qc', 'qc.id = r.question_category_id');
		$this->db->join('PositionRecommendations AS pr', 'pr.recommendation_id = r.id');
		$this->db->where('pr.job_position_id', (int) $job_position_id);
		$query = $this->db->get();
	
		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}

	public function get_all(){
		$this->db->select('r.*, qc.title AS question_category_title');
		$this->db->from($this->table.' AS r');
		$this->db->join('QuestionCategory AS qc', 'qc.id = r.question_category_id');
		$this->db->join('PositionRecommendations AS pr', 'pr.recommendation_id = r.id');
		$query = $this->db->get();
		
		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}
	
	
	public function get_by_questionary_completion_id($qc_id){
		$this->db->select('r.*, qc.id AS qc_id, qc.title AS qc_title');
		$this->db->from($this->table.' AS r');
		$this->db->join('QuestionaryRecommendations AS qr','qr.recommendation_id = r.id');
		$this->db->join('QuestionCategory AS qc','qc.id = r.question_category_id');
		$this->db->where('qr.questionary_completion_id', (int) $qc_id);
		$this->db->order_by('qc.title', 'ASC');
		$query = $this->db->get();
		
		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}
	
	public function get_by_params($company_id, $job_position_id){
		$this->db->select('r.*, qc.title AS category');
		$this->db->from($this->table.' AS r');
		$this->db->join('QuestionCategory AS qc','qc.id = r.question_category_id');
		$this->db->join('PositionRecommendations AS pr ','pr.recommendation_id = r.id');
		$this->db->join('JobPosition AS jp','jp.id = pr.job_position_id');
		$this->db->where('jp.company_id', (int) $company_id);
		$this->db->where('pr.job_position_id', (int) $job_position_id);		
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}
}