<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Questionary_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Questionary';
		$this->id = 'id';
	}

	public function get_questionaries() {
		$response = [];
		$questions_aux = [];
		$categories_aux = [];
		$questionary_aux = [];

		$this->db->select($this->table .'.*');
		$this->db->from($this->table);
		$this->db->where($this->table .'.active', 1);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$questionaries = $query->result();

			foreach ($questionaries as $questionary) {

				$sql = "
					SELECT DISTINCT qc.*
					FROM Question as q
					INNER JOIN QuestionCategory as qc ON qc.id = q.question_category_id
					WHERE q.questionary_id = ". $questionary->id ." AND q.active = 1 AND qc.active = 1
				";

				$query2 = $this->db->query($sql);
				$categories = $query2->result();

				$categories_aux = [];
				foreach ($categories as $category) {
					$this->db->select('*');
					$this->db->from('Question');
					$this->db->where('questionary_id', $questionary->id);
					$this->db->where('question_category_id', $category->id);
					$this->db->where('active', 1);

					$query3 = $this->db->get();
					$questions = $query3->result();

					$questions_aux = [];
					foreach ($questions as $question) {
						if ($question->type == 1) {
							$this->db->select('*');
							$this->db->from('QuestionOptions');
							$this->db->where('question_id', $question->id);

							$query4 = $this->db->get();
							$options = $query4->result();

							$question->options = $options;
							$questions_aux[] = $question;
						}else{
							$questions_aux[] = $question;
						}
					}

					$category->questions = $questions_aux;	
					$categories_aux[] = $category;
				}

				$questionary->categories = $categories_aux;
				$questionary_aux[] = $questionary;
			}

			return $questionary_aux;
		}

		return FALSE;
	}
	
	public function create($user, $questionary_id, $job_position_id, $answers) {
		$this->db->trans_start();

		$this->db->insert("QuestionaryCompletion", array(
			'random_user_id' => $user->id,
			'questionary_id' => $questionary_id,
			'job_position_id' => $job_position_id,
			'created_at' => date('Y-m-d H:i:s')
		));

		$questionary_completion_id = $this->db->insert_id();

		foreach ($answers as $answer) {
			$this->answer_model->create($questionary_completion_id,$answer->questionId,  $answer->questionOptionId, $answer->value);
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			// generate an error... or use the log_message() function to log your error
			return FALSE;
		}

		return $questionary_completion_id;
	}
	
	public function get_job_position_questionary_by_company_id($company_id){
		$this->db->select('jp.id AS position_id, jp.position, count(jp.position) AS quantity');
		$this->db->select('CASE WHEN pr.job_position_id IS NULL THEN 0 ELSE 1 END AS has_recommendation');
		$this->db->from('QuestionaryCompletion AS qc');
		$this->db->join('JobPosition AS jp', 'jp.id = qc.job_position_id');
		$this->db->join('(SELECT DISTINCT(job_position_id) FROM PositionRecommendations) AS pr', 'pr.job_position_id = jp.id', 'left');
		$this->db->where('company_id', (int) $company_id);
		$this->db->group_by('position, jp.id, pr.job_position_id');
		$this->db->order_by('jp.position');
		
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}
	
	public function get_questionary_completions_by_company_id($company_id) {
		$this->db->select('qc.*, jp.company_id, jp.position, q.name');
		$this->db->select('CASE WHEN qr.questionary_completion_id IS NULL THEN 0 ELSE 1 END AS has_recommendation');
		$this->db->from('QuestionaryCompletion AS qc');
		$this->db->join('JobPosition AS jp', 'qc.job_position_id = jp.Id');
		$this->db->join('Questionary AS q', 'qc.questionary_id = q.id');
		$this->db->join('(SELECT DISTINCT(questionary_completion_id) FROM [dbo].[QuestionaryRecommendations]) AS qr', 
				'qr.questionary_completion_id = qc.id', 'left');
		$this->db->where('q.active', 1);
		if($company_id != 0){
			$this->db->where('jp.company_id', (int) $company_id);
		}
		$query = $this->db->get();
	
		return ($query->num_rows() > 0) ? $query->result() : FALSE;
	}
	
	public function get_category_results_by_job_position_id($job_position_id, $company_id){
		$this->load->model('Question_category_model', 'category');
		
		$categories = $this->category->get_by_questionary_id(1);
		
		$this->db->select('qc.id, qcat.id AS question_category_id, qcat.title AS category, 
				COUNT (qcat.id) AS question_qty, SUM (qo.ponderation) AS ponderation');
		$this->db->from('QuestionaryCompletion AS qc');
		$this->db->join('Question AS q', 'q.questionary_id = qc.questionary_id');
		$this->db->join('QuestionCategory AS qcat', 'qcat.id = q.question_category_id');
        $this->db->join('Answer AS a', 'a.question_id = q.id AND a.questionary_completion_id = qc.id');
        $this->db->join('RandomUser AS r', 'r.id = qc.random_user_id AND r.company_id = '.$company_id);
		$this->db->join('QuestionOptions AS qo', 'qo.id = a.question_option_id', 'left');
		if(-1 != $job_position_id)
        {
            $this->db->where('qc.job_position_id', (int) $job_position_id);
        }
		$this->db->group_by('qc.id, qcat.id, qcat.title');
		$this->db->order_by('qc.id','ASC');

		$query = $this->db->get();
		if($query->num_rows() > 0){
			$rows=[];
			$risk_level = [];
			$categories_map = [];
			$category_index = 1;
			
			if($categories !== FALSE){
				foreach ($categories as $cat_id => $cat) {
					$categories_map[$cat->question_category_id] = array('category' => $cat->title, 
							'risk_high' => 0, 'risk_medium' => 0, 'risk_low' => 0);
					$categories[$cat_id]->categoryData = $category_index;
					$categories[$cat_id]->riskData = $category_index + 1;
					$category_index+=2;
				}
			}
			
			foreach ($query->result() as $r){
				if(array_key_exists($r->id, $rows)){
					array_push($rows[$r->id], $r->ponderation);
				}else{
					$rows[$r->id] = array($r->ponderation);
				}
				
				$risk_level = $this->_check_risk_level($r->question_category_id, $r->ponderation);
				array_push($rows[$r->id], $risk_level);
				
				if(array_key_exists($r->question_category_id, $categories_map)){
					switch($risk_level){
						case 'A':
							$categories_map[$r->question_category_id]['risk_high']++;
							break;
						case 'M':
							$categories_map[$r->question_category_id]['risk_medium']++;
							break;
						default:
							$categories_map[$r->question_category_id]['risk_low']++;
							break;
					}
				}
			}
			
			$total_questionary = count($rows);
			
			foreach($categories_map as $key => $per_cat){
				$categories_map[$key]['risk_high'] = ($categories_map[$key]['risk_high'] >  0) ? ($categories_map[$key]['risk_high'] * 100) / $total_questionary : 0;
				$categories_map[$key]['risk_medium'] = ($categories_map[$key]['risk_medium'] >  0) ? ($categories_map[$key]['risk_medium'] * 100) / $total_questionary : 0;
				$categories_map[$key]['risk_low'] = ($categories_map[$key]['risk_low'] >  0) ? ($categories_map[$key]['risk_low'] * 100) / $total_questionary : 0;
			}
			
			$resp = array('head' => $categories, 'rows' => $rows, 'percent' => $categories_map);
			return $resp;
		}
		
		return FALSE;
	}

    public function get_questionary_completions_summary_by_company_id($company_id = -1)
    {
        $sql = "SELECT 
                CASE WHEN questionary_name IS NULL THEN 'No Respondidos' ELSE questionary_name END as questionary_name, 
                CASE WHEN job_position_name IS NULL THEN 'N/A' ELSE job_position_name END as job_position_name, 
                count(user_id) as amount_of_users
                FROM
                (
                SELECT id as user_id, date_of_answer, questionary_id, questionary_name, job_position_id, job_position_name
                FROM
                (
                SELECT id
                FROM RandomUser
                WHERE company_id = $company_id
                ) as FilteredRandomUser
                LEFT JOIN
                (
                SELECT random_user_id, created_at as date_of_answer, Questionary.id as questionary_id, Questionary.name as questionary_name, JobPosition.id as job_position_id, JobPosition.position as job_position_name 
                FROM QuestionaryCompletion
                INNER JOIN Questionary ON QuestionaryCompletion.questionary_id = Questionary.id AND Questionary.active = 1
                INNER JOIN JobPosition ON QuestionaryCompletion.job_position_id = JobPosition.id AND JobPosition.active = 1 AND JobPosition.company_id = $company_id
                ) as QuestionaryData
                ON FilteredRandomUser.id = QuestionaryData.random_user_id
                ) as CompleteData
                GROUP BY questionary_name, job_position_name
                ORDER BY questionary_name, job_position_name;";

        $query = $this->db->query($sql);

        return ($query->num_rows() > 0) ? $query->result() : FALSE;
    }

    public function get_activity_log_by_company_id($company_id = -1)
    {
        $sql = "SELECT log_date, activity_name, activity_info FROM
                (
                (
                SELECT QuestionaryCompletion.created_at as log_date, Questionary.name as activity_name, JobPosition.position as activity_info
                FROM QuestionaryCompletion
                INNER JOIN Questionary ON QuestionaryCompletion.questionary_id = Questionary.id AND Questionary.active = 1
                INNER JOIN JobPosition ON QuestionaryCompletion.job_position_id = JobPosition.id AND JobPosition.active = 1 AND JobPosition.company_id = $company_id
                )
                UNION
                (
                SELECT RandomUserRecommendationViews.visited_at as log_date, 'Visualizaci&oacute;n de Videos' as activity_name, Recommendation.title as activity_info
                FROM RandomUserRecommendationViews
                INNER JOIN RandomUser ON RandomUserRecommendationViews.random_user_id = RandomUser.id AND RandomUser.company_id = $company_id
                INNER JOIN Recommendation ON RandomUserRecommendationViews.recommendation_id = Recommendation.id AND Recommendation.company_id = $company_id
                )
                ) as activity_log
                ORDER BY log_date DESC;";

        $query = $this->db->query($sql);

        return ($query->num_rows() > 0) ? $query->result() : FALSE;
    }

	private function _check_risk_level($question_category_id, $ponderarion){
		$this->load->model('Ratings_model', 'ratings');
		$ratings = $this->ratings->get_all();
		$risk_level = '-';
		if($ratings !== FALSE){
			if(array_key_exists($question_category_id, $ratings)){
				switch (TRUE) {
					case ($ponderarion >= $ratings[$question_category_id]['high']['min']) &&
					($ponderarion <= $ratings[$question_category_id]['high']['max']):
					$risk_level = 'A';
					break;
		
					case ($ponderarion >= $ratings[$question_category_id]['medium']['min']) &&
					($ponderarion <= $ratings[$question_category_id]['medium']['max']):
					$risk_level = 'M';
					break;
					
					default:
						$risk_level = 'B';
					break;
				}
			}
		}
		return $risk_level;
	}

	public function set_recommendations($questionary_completion_id, $recommendation_ids){
		$this->db->trans_start();
		$this->delete_all_recommendations($questionary_completion_id);
		if(is_array($recommendation_ids)){
			foreach ($recommendation_ids as $r_id){
				$this->db->insert('QuestionaryRecommendations',
						array(
								'questionary_completion_id' => (int) $questionary_completion_id,
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
	
	public function delete_all_recommendations($questionary_completion_id){
		$this->db->where('questionary_completion_id', $questionary_completion_id);
		$this->db->delete('QuestionaryRecommendations');
		return TRUE;
	}
	
	public function has_random_user_a_questionary($random_user_id){
		$this->db->select('TOP 1 id');
		$this->db->from('QuestionaryCompletion');
		$this->db->where('random_user_id', $random_user_id);
		$query = $this->db->get();
		
		return ($query->num_rows() > 0) ? TRUE : FALSE;
	}
}
