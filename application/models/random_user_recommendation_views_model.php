<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Random_user_recommendation_views_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'RandomUserRecommendationViews';
		$this->id = 'id';

        $this->load->model('randomuser_model');
        $this->load->model('recommendation_model');
	}

	public function create($random_user_id, $recommendation_id, $company_id) {
		$this->db->trans_start();

        $has_valid_random_user_id = $this->randomuser_model->random_user_exists_at_company($random_user_id, $company_id);
        if(FALSE === $has_valid_random_user_id)
        {
            return FALSE;
        }
        $has_valid_recommendation_id = $this->recommendation_model->recommendation_exists_at_company($recommendation_id, $company_id);
        if(FALSE === $has_valid_recommendation_id)
        {
            return FALSE;
        }

		$this->db->insert($this->table, array(
			'random_user_id' => $random_user_id,
			'recommendation_id' => $recommendation_id
		));

		$random_user_recommendation_id = $this->db->insert_id();
        $this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
			// generate an error... or use the log_message() function to log your error
			return FALSE;
		}

		return $random_user_recommendation_id;
	}

    public function get_recommendation_views_summary_by_company_id($company_id)
    {
        $this->db->select('Recommendation.title as recommendation_name, count(DISTINCT RandomUserRecommendationViews.random_user_id) as unique_random_users, count(RandomUserRecommendationViews.id) as amount_of_views');
        $this->db->from($this->table);
        $this->db->join('RandomUser',$this->table.'.random_user_id = RandomUser.id AND RandomUser.company_id = '.$company_id);
        $this->db->join('Recommendation',$this->table.'.recommendation_id = Recommendation.id AND Recommendation.company_id = '.$company_id);
        $this->db->group_by('Recommendation.title');
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : FALSE;
    }
}
