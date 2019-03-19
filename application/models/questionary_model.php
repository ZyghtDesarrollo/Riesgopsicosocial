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
	
    public function get_risk_per_company($company_id = -1, $questionary_id = -1)
    {
		$report = array();
		$report["evaluation_total_workers"] = 0;
		$report["evaluation_total_answers"] = 0;
		$report["risk_score"] = 'N/A';
		$report["risk_label"] = 'N/A';
		// RESULTADO GLOBAL SIN FILTRO
        $global_no_filter_sql = "SELECT CompanyName, CompanyRut, QuestionCategory, Risk, sum(rowWeight) / max(amountOfWorkers) as percentageOfWorkersInDimension, max(amountOfWorkers) as amountOfWorkers
				FROM 
				(
					SELECT QuestionaryCompletionId, QuestionCategory, Ponderation,
					CASE 
					WHEN Ponderation BETWEEN low_min and low_max THEN -1
					WHEN Ponderation BETWEEN medium_min and medium_max THEN 0
					WHEN Ponderation BETWEEN high_min and high_max THEN 1
					ELSE 0
					END as Risk
					FROM
					(
						SELECT QuestionaryCompletionId, QuestionCategoryId, QuestionCategory, sum(QuestionOptionPonderation) as Ponderation
						FROM vQuestionaryResults
						WHERE QuestionaryId = $questionary_id
						GROUP BY QuestionaryCompletionId, QuestionCategoryId, QuestionCategory
					) AS AggregatedQuestionaryResults
					JOIN Ratings ON AggregatedQuestionaryResults.QuestionCategoryId = Ratings.question_category_id
				) AS RESULTS
				JOIN 
				(
					SELECT QuestionaryCompletionId, CompanyName, CompanyRut, JobPosition, AgeFilter, SexFilter,
					1.0 as rowWeight,
					COUNT(QuestionaryCompletionId) OVER (PARTITION BY CompanyName) AS amountOfWorkers
					FROM vQuestionaryFilters
					WHERE QuestionaryId = $questionary_id AND CompanyId = $company_id
				) as FILTERS ON RESULTS.QuestionaryCompletionId = FILTERS.QuestionaryCompletionId
				GROUP BY CompanyName, CompanyRut, QuestionCategory, Risk
				ORDER BY CompanyName, CompanyRut, QuestionCategory, Risk;";
		
		$query_global_no_filter = $this->db->query($global_no_filter_sql);
		if($query_global_no_filter->num_rows() > 0)
		{
			$total_answers = 0;
			$risk_score = 0;
			$dimension_score = array();
			$result = $query_global_no_filter->result();
			foreach ($result as $r){
				$total_answers = max($total_answers,$r->amountOfWorkers);
				if(!array_key_exists($r->QuestionCategory, $dimension_score))
				{
					$dimension_score[$r->QuestionCategory] = 0;
				}
				if(0.5 < $r->percentageOfWorkersInDimension)
				{
					$dimension_score[$r->QuestionCategory] = $r->Risk;
					$risk_score = $risk_score + $r->Risk;
				}
			}
			$report["evaluation_total_answers"] = $total_answers;
			$report["risk_score"] = $risk_score;
			$report["risk_label"] = $this->getRiskLabelFromScore($risk_score);
		}
		// RESULTADO GLOBAL SIN FILTRO
        $random_user_sql = "SELECT count(id) as amountOfUsers, min(date) as startDate, max(date) as endDate 
							FROM RandomUser 
							WHERE company_id = $company_id";
		
		$query_random_user = $this->db->query($random_user_sql);
		if($query_random_user->num_rows() > 0)
		{
			$result = $query_random_user->result();
			foreach ($result as $r){
				$report["evaluation_total_workers"] = $r->amountOfUsers;
				//$report["description"]["evaluation_start_date"] = $r->startDate;
				//$report["description"]["evaluation_end_date"] = $r->endDate;
			}
		}
		return $report;
	}
	
    public function get_questionary_report($company_id = -1, $questionary_id = -1)
    {
		$report = array();
		$report["description"] = array();
		$report["description"]["company_name"] = "";
		$report["description"]["company_rut"] = "";
		$report["description"]["evaluation_instrument"] = "ISTAS-21";
		$report["description"]["evaluation_methodology"] = "App Psicosocial";
		$report["description"]["evaluation_start_date"] = "";
		$report["description"]["evaluation_end_date"] = "";
		$report["description"]["evaluation_total_workers"] = 0;
		$report["description"]["evaluation_total_answers"] = 0;
		$report["description"]["evaluation_total_answers_by_sex"] = array();
		$report["description"]["evaluation_total_answers_by_age"] = array();
		$report["description"]["evaluation_total_answers_by_job_position"] = array();
		$report["global"] = array();
		$report["global"]["total"] = array();
		$report["global"]["total"]["risk_score"] = "";
		$report["global"]["total"]["risk_label"] = "";
		$report["global"]["sex"] = array();
		//$report["global"]["sex"][sex_value] = array();
		//$report["global"]["sex"][sex_value]["risk_score"] = "";
		//$report["global"]["sex"][sex_value]["risk_label"] = "";
		$report["global"]["age"] = array();
		//$report["global"]["age"][age_value] = array();
		//$report["global"]["age"][age_value]["risk_score"] = "";
		//$report["global"]["age"][age_value]["risk_label"] = "";
		$report["global"]["update_rule"] = "";
		$report["job_position"] = array();
		//$report["job_position"][job_position_value] = array();
		//$report["job_position"][job_position_value]["total"] = array();
		//$report["job_position"][job_position_value]["total"]["risk_score"] = array();
		//$report["job_position"][job_position_value]["total"]["risk_label"] = array();
		//$report["job_position"][job_position_value]["sex"] = array();
		//$report["job_position"][job_position_value]["sex"][sex_value] = array();
		//$report["job_position"][job_position_value]["sex"][sex_value]["risk_score"] = "";
		//$report["job_position"][job_position_value]["sex"][sex_value]["risk_label"] = "";
		//$report["job_position"][job_position_value]["age"] = array();
		//$report["job_position"][job_position_value]["age"][age_value] = array();
		//$report["job_position"][job_position_value]["age"][age_value]["risk_score"] = "";
		//$report["job_position"][job_position_value]["age"][age_value]["risk_label"] = "";
		$report["dimension"] = array();
		//$report["dimension"][dimension_value]["total"] = array();
		//$report["dimension"][dimension_value]["total"]["risk_score"] = "";
		//$report["dimension"][dimension_value]["total"]["risk_label"] = "";
		//$report["dimension"][dimension_value]["sex"] = array();
		//$report["dimension"][dimension_value]["sex"][sex_value] = array();
		//$report["dimension"][dimension_value]["sex"][sex_value]["risk_score"] = "";
		//$report["dimension"][dimension_value]["sex"][sex_value]["risk_label"] = "";
		//$report["dimension"][dimension_value]["age"] = array();
		//$report["dimension"][dimension_value]["age"][age_value] = array();
		//$report["dimension"][dimension_value]["age"][age_value]["risk_score"] = "";
		//$report["dimension"][dimension_value]["age"][age_value]["risk_label"] = "";
		
		// RESULTADO GLOBAL SIN FILTRO
        $random_user_sql = "SELECT count(id) as amountOfUsers, min(date) as startDate, max(date) as endDate 
							FROM RandomUser 
							WHERE company_id = $company_id";
		
		$query_random_user = $this->db->query($random_user_sql);
		if($query_random_user->num_rows() > 0)
		{
			$result = $query_random_user->result();
			foreach ($result as $r){
				$report["description"]["evaluation_start_date"] = $r->startDate;
				$report["description"]["evaluation_end_date"] = $r->endDate;
				$report["description"]["evaluation_total_workers"] = $r->amountOfUsers;
			}
		}
		
		// FILTRO DE SEXO
		$sex_filter_array = array();
        $sex_sql = "SELECT QuestionOptions.title as QuestionOptionTitle
					FROM Question
					JOIN QuestionCategory on Question.question_category_id = QuestionCategory.id
					LEFT JOIN QuestionOptions on Question.id = QuestionOptions.question_id
					WHERE Question.active = 1
					AND Question.question_category_id = 6
					AND Question.id = 21";
		
		$query_sex = $this->db->query($sex_sql);
		if($query_sex->num_rows() > 0)
		{
			$result = $query_sex->result();
			foreach ($result as $r){
				$sex_filter_array[] = $r->QuestionOptionTitle;
			}
		}
		
		// FILTRO DE EDAD
		$age_filter_array = array();
        $age_sql = "SELECT QuestionOptions.title as QuestionOptionTitle
					FROM Question
					JOIN QuestionCategory on Question.question_category_id = QuestionCategory.id
					LEFT JOIN QuestionOptions on Question.id = QuestionOptions.question_id
					WHERE Question.active = 1
					AND Question.question_category_id = 6
					AND Question.id = 22";
		
		$query_age = $this->db->query($age_sql);
		if($query_age->num_rows() > 0)
		{
			$result = $query_age->result();
			foreach ($result as $r){
				$age_filter_array[] = $r->QuestionOptionTitle;
			}
		}
		
		// RESULTADO GLOBAL SIN FILTRO
        $global_no_filter_sql = "SELECT CompanyName, CompanyRut, QuestionCategory, Risk, sum(rowWeight) / max(amountOfWorkers) as percentageOfWorkersInDimension, max(amountOfWorkers) as amountOfWorkers
				FROM 
				(
					SELECT QuestionaryCompletionId, QuestionCategory, Ponderation,
					CASE 
					WHEN Ponderation BETWEEN low_min and low_max THEN -1
					WHEN Ponderation BETWEEN medium_min and medium_max THEN 0
					WHEN Ponderation BETWEEN high_min and high_max THEN 1
					ELSE 0
					END as Risk
					FROM
					(
						SELECT QuestionaryCompletionId, QuestionCategoryId, QuestionCategory, sum(QuestionOptionPonderation) as Ponderation
						FROM vQuestionaryResults
						WHERE QuestionaryId = $questionary_id
						GROUP BY QuestionaryCompletionId, QuestionCategoryId, QuestionCategory
					) AS AggregatedQuestionaryResults
					JOIN Ratings ON AggregatedQuestionaryResults.QuestionCategoryId = Ratings.question_category_id
				) AS RESULTS
				JOIN 
				(
					SELECT QuestionaryCompletionId, CompanyName, CompanyRut, JobPosition, AgeFilter, SexFilter,
					1.0 as rowWeight,
					COUNT(QuestionaryCompletionId) OVER (PARTITION BY CompanyName) AS amountOfWorkers
					FROM vQuestionaryFilters
					WHERE QuestionaryId = $questionary_id AND CompanyId = $company_id
				) as FILTERS ON RESULTS.QuestionaryCompletionId = FILTERS.QuestionaryCompletionId
				GROUP BY CompanyName, CompanyRut, QuestionCategory, Risk
				ORDER BY CompanyName, CompanyRut, QuestionCategory, Risk;";
		
		$query_global_no_filter = $this->db->query($global_no_filter_sql);
		if($query_global_no_filter->num_rows() > 0)
		{
			$company_name = "";
			$company_rut = "";
			$total_answers = 0;
			$risk_score = 0;
			$dimension_score = array();
			$result = $query_global_no_filter->result();
			foreach ($result as $r){
				$company_name = $r->CompanyName;
				$company_rut = $r->CompanyRut;
				$total_answers = max($total_answers,$r->amountOfWorkers);
				if(!array_key_exists($r->QuestionCategory, $dimension_score))
				{
					$dimension_score[$r->QuestionCategory] = 0;
				}
				if(0.5 < $r->percentageOfWorkersInDimension)
				{
					$dimension_score[$r->QuestionCategory] = $r->Risk;
					$risk_score = $risk_score + $r->Risk;
				}
			}
			$report["description"]["company_name"] = $company_name;
			$report["description"]["company_rut"] = $company_rut;
			$report["description"]["evaluation_total_answers"] = $total_answers;
			$report["global"]["total"]["risk_score"] = $risk_score;
			$report["global"]["total"]["risk_label"] = $this->getRiskLabelFromScore($risk_score);
			$update_rule = $this->getRiskTimeGapFromScore($risk_score);
			$report["global"]["update_rule"] = "Por su riesgo global, le corresponde una nueva evaluación en $update_rule años.";
			foreach($dimension_score as $dimension_key => $dimension_value)
			{
				$report["dimension"][$dimension_key]["total"]["risk_score"] = $dimension_value;
				$report["dimension"][$dimension_key]["total"]["risk_label"] = $this->getRiskPointLabelFromScore($dimension_value);
			}
		}
		
		// RESULTADO GLOBAL CON FILTRO: SEXO
        $global_sex_filter_sql = "SELECT CompanyName, QuestionCategory, SexFilter, Risk, sum(rowWeight) / max(amountOfWorkers) as percentageOfWorkersInDimension, max(amountOfWorkers) as amountOfWorkers
				FROM 
				(
					SELECT QuestionaryCompletionId, QuestionCategory, Ponderation,
					CASE 
					WHEN Ponderation BETWEEN low_min and low_max THEN -1
					WHEN Ponderation BETWEEN medium_min and medium_max THEN 0
					WHEN Ponderation BETWEEN high_min and high_max THEN 1
					ELSE 0
					END as Risk
					FROM
					(
						SELECT QuestionaryCompletionId, QuestionCategoryId, QuestionCategory, sum(QuestionOptionPonderation) as Ponderation
						FROM vQuestionaryResults
						WHERE QuestionaryId = $questionary_id
						GROUP BY QuestionaryCompletionId, QuestionCategoryId, QuestionCategory
					) AS AggregatedQuestionaryResults
					JOIN Ratings ON AggregatedQuestionaryResults.QuestionCategoryId = Ratings.question_category_id
				) AS RESULTS
				JOIN 
				(
					SELECT QuestionaryCompletionId, CompanyName, JobPosition, AgeFilter, SexFilter,
					1.0 as rowWeight,
					COUNT(QuestionaryCompletionId) OVER (PARTITION BY CompanyName, SexFilter) AS amountOfWorkers
					FROM vQuestionaryFilters
					WHERE QuestionaryId = $questionary_id AND CompanyId = $company_id
				) as FILTERS ON RESULTS.QuestionaryCompletionId = FILTERS.QuestionaryCompletionId
				GROUP BY CompanyName, QuestionCategory, SexFilter, Risk
				ORDER BY CompanyName, QuestionCategory, SexFilter, Risk";
		$query_global_sex_filter = $this->db->query($global_sex_filter_sql);
		
		$sex_filter_answers = array();
		foreach($sex_filter_array as $sex_filter_key)
		{
			$sex_filter_answers[$sex_filter_key] = 'N/A';
		}
		if($query_global_sex_filter->num_rows() > 0)
		{		
			$sex_filter_risk_score = array();
			$sex_filter_dimension_score = array();
			$result = $query_global_sex_filter->result();
			foreach ($result as $r){
				$sex_filter_answers[$r->SexFilter] = array_key_exists($r->SexFilter, $sex_filter_answers) ? max($sex_filter_answers[$r->SexFilter], $r->amountOfWorkers) : $r->amountOfWorkers;
				if(!array_key_exists($r->QuestionCategory, $sex_filter_dimension_score))
				{
					$sex_filter_dimension_score[$r->QuestionCategory] = array();
				}
				if(!array_key_exists($r->SexFilter, $sex_filter_dimension_score[$r->QuestionCategory]))
				{
					$sex_filter_dimension_score[$r->QuestionCategory][$r->SexFilter] = 0;
				}
				if(!array_key_exists($r->SexFilter, $sex_filter_risk_score))
				{
					$sex_filter_risk_score[$r->SexFilter] = 0;
				}
				if(0.5 < $r->percentageOfWorkersInDimension)
				{
					$sex_filter_dimension_score[$r->QuestionCategory][$r->SexFilter] = $r->Risk;
					$sex_filter_risk_score[$r->SexFilter] = $sex_filter_risk_score[$r->SexFilter] + $r->Risk;
				}
			}
			$report["description"]["evaluation_total_answers_by_sex"] = $sex_filter_answers;
			foreach($sex_filter_array as $sex_filter_key)
			{
				$current_score = array_key_exists($sex_filter_key, $sex_filter_risk_score) ? $sex_filter_risk_score[$sex_filter_key] : 'N/A';
				$current_label = array_key_exists($sex_filter_key, $sex_filter_risk_score) ? $this->getRiskLabelFromScore($current_score) : 'N/A';
				$report["global"]["sex"][$sex_filter_key]["risk_score"] = $current_score;
				$report["global"]["sex"][$sex_filter_key]["risk_label"] = $current_label;
			}
			foreach($sex_filter_dimension_score as $sex_filter_dimension_key => $sex_filter_dimension_array)
			{
				foreach($sex_filter_array as $sex_filter_key)
				{
					$current_score = array_key_exists($sex_filter_key, $sex_filter_dimension_array) ? $sex_filter_dimension_array[$sex_filter_key] : 'N/A';
					$current_label = array_key_exists($sex_filter_key, $sex_filter_dimension_array) ? $this->getRiskPointLabelFromScore($current_score) : 'N/A';
					$report["dimension"][$sex_filter_dimension_key]["sex"][$sex_filter_key]["risk_score"] = $current_score;
					$report["dimension"][$sex_filter_dimension_key]["sex"][$sex_filter_key]["risk_label"] = $current_label;
				}
			}
		} 
		
		// RESULTADO GLOBAL CON FILTRO: EDAD
        $global_age_filter_sql = "SELECT CompanyName, QuestionCategory, AgeFilter, Risk, sum(rowWeight) / max(amountOfWorkers) as percentageOfWorkersInDimension, max(amountOfWorkers) as amountOfWorkers
								FROM 
								(
									SELECT QuestionaryCompletionId, QuestionCategory, Ponderation,
									CASE 
									WHEN Ponderation BETWEEN low_min and low_max THEN -1
									WHEN Ponderation BETWEEN medium_min and medium_max THEN 0
									WHEN Ponderation BETWEEN high_min and high_max THEN 1
									ELSE 0
									END as Risk
									FROM
									(
										SELECT QuestionaryCompletionId, QuestionCategoryId, QuestionCategory, sum(QuestionOptionPonderation) as Ponderation
										FROM vQuestionaryResults
										WHERE QuestionaryId = $questionary_id
										GROUP BY QuestionaryCompletionId, QuestionCategoryId, QuestionCategory
									) AS AggregatedQuestionaryResults
									JOIN Ratings ON AggregatedQuestionaryResults.QuestionCategoryId = Ratings.question_category_id
								) AS RESULTS
								JOIN 
								(
									SELECT QuestionaryCompletionId, CompanyName, JobPosition, AgeFilter, SexFilter,
									1.0 as rowWeight,
									COUNT(QuestionaryCompletionId) OVER (PARTITION BY CompanyName, AgeFilter) AS amountOfWorkers
									FROM vQuestionaryFilters
									WHERE QuestionaryId = $questionary_id AND CompanyId = $company_id
								) as FILTERS ON RESULTS.QuestionaryCompletionId = FILTERS.QuestionaryCompletionId
								GROUP BY CompanyName, QuestionCategory, AgeFilter, Risk
								ORDER BY CompanyName, QuestionCategory, AgeFilter, Risk";
		$query_global_age_filter = $this->db->query($global_age_filter_sql);
		
		$age_filter_answers = array();
		foreach($age_filter_array as $age_filter_key)
		{
			$age_filter_answers[$age_filter_key] = 'N/A';
		}
		if($query_global_age_filter->num_rows() > 0)
		{		
			$age_filter_risk_score = array();
			$age_filter_dimension_score = array();
			$result = $query_global_age_filter->result();
			foreach ($result as $r){
				$age_filter_answers[$r->AgeFilter] = array_key_exists($r->AgeFilter, $age_filter_answers) ? max($age_filter_answers[$r->AgeFilter], $r->amountOfWorkers) : $r->amountOfWorkers;
				if(!array_key_exists($r->QuestionCategory, $age_filter_dimension_score))
				{
					$age_filter_dimension_score[$r->QuestionCategory] = array();
				}
				if(!array_key_exists($r->AgeFilter, $age_filter_dimension_score[$r->QuestionCategory]))
				{
					$age_filter_dimension_score[$r->QuestionCategory][$r->AgeFilter] = 0;
				}
				if(!array_key_exists($r->AgeFilter, $age_filter_risk_score))
				{
					$age_filter_risk_score[$r->AgeFilter] = 0;
				}
				if(0.5 < $r->percentageOfWorkersInDimension)
				{
					$age_filter_dimension_score[$r->QuestionCategory][$r->AgeFilter] = $r->Risk;
					$age_filter_risk_score[$r->AgeFilter] = $age_filter_risk_score[$r->AgeFilter] + $r->Risk;
				}
			}
			$report["description"]["evaluation_total_answers_by_age"] = $age_filter_answers;
			foreach($age_filter_array as $age_filter_key)
			{
				$current_score = array_key_exists($age_filter_key, $age_filter_risk_score) ? $age_filter_risk_score[$age_filter_key] : 'N/A';
				$current_label = array_key_exists($age_filter_key, $age_filter_risk_score) ? $this->getRiskLabelFromScore($current_score) : 'N/A';
				$report["global"]["age"][$age_filter_key]["risk_score"] = $current_score;
				$report["global"]["age"][$age_filter_key]["risk_label"] = $current_label;
			}
			foreach($age_filter_dimension_score as $age_filter_dimension_key => $age_filter_dimension_array)
			{
				foreach($age_filter_array as $age_filter_key)
				{
					$current_score = array_key_exists($age_filter_key, $age_filter_dimension_array) ? $age_filter_dimension_array[$age_filter_key] : 'N/A';
					$current_label = array_key_exists($age_filter_key, $age_filter_dimension_array) ? $this->getRiskPointLabelFromScore($current_score) : 'N/A';
					$report["dimension"][$age_filter_dimension_key]["age"][$age_filter_key]["risk_score"] = $current_score;
					$report["dimension"][$age_filter_dimension_key]["age"][$age_filter_key]["risk_label"] = $current_label;
				}
			}
		}
		
		// RESULTADO POR CARGO SIN FILTRO
        $job_position_no_filter_sql = "SELECT JobPosition, QuestionCategory, Risk, sum(rowWeight) / max(amountOfWorkers) as percentageOfWorkersInDimension, max(amountOfWorkers) as amountOfWorkers
								FROM 
								(
									SELECT QuestionaryCompletionId, QuestionCategory, Ponderation,
									CASE 
									WHEN Ponderation BETWEEN low_min and low_max THEN -1
									WHEN Ponderation BETWEEN medium_min and medium_max THEN 0
									WHEN Ponderation BETWEEN high_min and high_max THEN 1
									ELSE 0
									END as Risk
									FROM
									(
										SELECT QuestionaryCompletionId, QuestionCategoryId, QuestionCategory, sum(QuestionOptionPonderation) as Ponderation
										FROM vQuestionaryResults
										WHERE QuestionaryId = $questionary_id
										GROUP BY QuestionaryCompletionId, QuestionCategoryId, QuestionCategory
									) AS AggregatedQuestionaryResults
									JOIN Ratings ON AggregatedQuestionaryResults.QuestionCategoryId = Ratings.question_category_id
								) AS RESULTS
								JOIN 
								(
									SELECT QuestionaryCompletionId, CompanyName, JobPosition, AgeFilter, SexFilter,
									1.0 as rowWeight,
									COUNT(QuestionaryCompletionId) OVER (PARTITION BY JobPosition) AS amountOfWorkers
									FROM vQuestionaryFilters
									WHERE QuestionaryId = $questionary_id AND CompanyId = $company_id
								) as FILTERS ON RESULTS.QuestionaryCompletionId = FILTERS.QuestionaryCompletionId
								GROUP BY JobPosition, QuestionCategory, Risk
								ORDER BY JobPosition, QuestionCategory, Risk";
		$query_job_position_no_filter = $this->db->query($job_position_no_filter_sql);
		
		if($query_job_position_no_filter->num_rows() > 0)
		{		
			$job_position_no_filter_answers = array();
			$job_position_no_filter_risk_score = array();
			$result = $query_job_position_no_filter->result();
			foreach ($result as $r){
				$job_position_no_filter_answers[$r->JobPosition] = array_key_exists($r->JobPosition, $job_position_no_filter_answers) ? max($job_position_no_filter_answers[$r->JobPosition], $r->amountOfWorkers) : $r->amountOfWorkers;
				if(!array_key_exists($r->JobPosition, $job_position_no_filter_risk_score))
				{
					$job_position_no_filter_risk_score[$r->JobPosition] = 0;
				}
				if(0.5 < $r->percentageOfWorkersInDimension)
				{
					$job_position_no_filter_risk_score[$r->JobPosition] = $job_position_no_filter_risk_score[$r->JobPosition] + $r->Risk;
				}
			}
			$report["description"]["evaluation_total_answers_by_job_position"] = $job_position_no_filter_answers;
			foreach($job_position_no_filter_risk_score as $job_position_no_filter_key => $job_position_no_filter_value)
			{
				$report["job_position"][$job_position_no_filter_key]["total"]["risk_score"] = $job_position_no_filter_value;
				$report["job_position"][$job_position_no_filter_key]["total"]["risk_label"] = $this->getRiskLabelFromScore($job_position_no_filter_value);
			}
		}
		
		// RESULTADO POR CARGO CON FILTRO: SEXO
        $job_position_sex_filter_sql = "SELECT JobPosition, QuestionCategory, SexFilter, Risk, sum(rowWeight) / max(amountOfWorkers) as percentageOfWorkersInDimension, max(amountOfWorkers) as amountOfWorkers
										FROM 
										(
											SELECT QuestionaryCompletionId, QuestionCategory, Ponderation,
											CASE 
											WHEN Ponderation BETWEEN low_min and low_max THEN -1
											WHEN Ponderation BETWEEN medium_min and medium_max THEN 0
											WHEN Ponderation BETWEEN high_min and high_max THEN 1
											ELSE 0
											END as Risk
											FROM
											(
												SELECT QuestionaryCompletionId, QuestionCategoryId, QuestionCategory, sum(QuestionOptionPonderation) as Ponderation
												FROM vQuestionaryResults
												WHERE QuestionaryId = $questionary_id
												GROUP BY QuestionaryCompletionId, QuestionCategoryId, QuestionCategory
											) AS AggregatedQuestionaryResults
											JOIN Ratings ON AggregatedQuestionaryResults.QuestionCategoryId = Ratings.question_category_id
										) AS RESULTS
										JOIN 
										(
											SELECT QuestionaryCompletionId, CompanyName, JobPosition, AgeFilter, SexFilter,
											1.0 as rowWeight,
											COUNT(QuestionaryCompletionId) OVER (PARTITION BY JobPosition, SexFilter) AS amountOfWorkers
											FROM vQuestionaryFilters
										WHERE QuestionaryId = $questionary_id AND CompanyId = $company_id
										) as FILTERS ON RESULTS.QuestionaryCompletionId = FILTERS.QuestionaryCompletionId
										GROUP BY JobPosition, QuestionCategory, SexFilter, Risk
										ORDER BY JobPosition, QuestionCategory, SexFilter, Risk;";
		$query_job_position_sex_filter = $this->db->query($job_position_sex_filter_sql);
		
		if($query_job_position_sex_filter->num_rows() > 0)
		{		
			$job_position_sex_filter_risk_score = array();
			$result = $query_job_position_sex_filter->result();
			foreach ($result as $r){
				if(!array_key_exists($r->JobPosition, $job_position_sex_filter_risk_score))
				{
					$job_position_sex_filter_risk_score[$r->JobPosition] = array();
				}
				if(!array_key_exists($r->SexFilter, $job_position_sex_filter_risk_score[$r->JobPosition]))
				{
					$job_position_sex_filter_risk_score[$r->JobPosition][$r->SexFilter] = 0;
				}
				if(0.5 < $r->percentageOfWorkersInDimension)
				{
					$job_position_sex_filter_risk_score[$r->JobPosition][$r->SexFilter] = $job_position_sex_filter_risk_score[$r->JobPosition][$r->SexFilter] + $r->Risk;
				}
			}
			foreach($job_position_sex_filter_risk_score as $job_position_key => $job_position_array)
			{
				foreach($sex_filter_array as $sex_filter_key)
				{
					$current_score = array_key_exists($sex_filter_key, $job_position_array) ? $job_position_array[$sex_filter_key] : 'N/A';
					$current_label = array_key_exists($sex_filter_key, $job_position_array) ? $this->getRiskLabelFromScore($current_score) : 'N/A';
					$report["job_position"][$job_position_key]["sex"][$sex_filter_key]["risk_score"] = $current_score;
					$report["job_position"][$job_position_key]["sex"][$sex_filter_key]["risk_label"] = $current_label;
				}
			}
		}
		
		// RESULTADO POR CARGO CON FILTRO: EDAD
        $job_position_age_filter_sql = "SELECT JobPosition, QuestionCategory, AgeFilter, Risk, sum(rowWeight) / max(amountOfWorkers) as percentageOfWorkersInDimension, max(amountOfWorkers) as amountOfWorkers
										FROM 
										(
											SELECT QuestionaryCompletionId, QuestionCategory, Ponderation,
											CASE 
											WHEN Ponderation BETWEEN low_min and low_max THEN -1
											WHEN Ponderation BETWEEN medium_min and medium_max THEN 0
											WHEN Ponderation BETWEEN high_min and high_max THEN 1
											ELSE 0
											END as Risk
											FROM
											(
												SELECT QuestionaryCompletionId, QuestionCategoryId, QuestionCategory, sum(QuestionOptionPonderation) as Ponderation
												FROM vQuestionaryResults
												WHERE QuestionaryId = $questionary_id
												GROUP BY QuestionaryCompletionId, QuestionCategoryId, QuestionCategory
											) AS AggregatedQuestionaryResults
											JOIN Ratings ON AggregatedQuestionaryResults.QuestionCategoryId = Ratings.question_category_id
										) AS RESULTS
										JOIN 
										(
											SELECT QuestionaryCompletionId, CompanyName, JobPosition, AgeFilter, SexFilter,
											1.0 as rowWeight,
											COUNT(QuestionaryCompletionId) OVER (PARTITION BY JobPosition, AgeFilter) AS amountOfWorkers
											FROM vQuestionaryFilters
											WHERE QuestionaryId = $questionary_id AND CompanyId = $company_id
										) as FILTERS ON RESULTS.QuestionaryCompletionId = FILTERS.QuestionaryCompletionId
										GROUP BY JobPosition, QuestionCategory, AgeFilter, Risk
										ORDER BY JobPosition, QuestionCategory, AgeFilter, Risk;";
		$query_job_position_age_filter = $this->db->query($job_position_age_filter_sql);
		
		if($query_job_position_age_filter->num_rows() > 0)
		{		
			$job_position_age_filter_risk_score = array();
			$result = $query_job_position_age_filter->result();
			foreach ($result as $r){
				if(!array_key_exists($r->JobPosition, $job_position_age_filter_risk_score))
				{
					$job_position_age_filter_risk_score[$r->JobPosition] = array();
				}
				if(!array_key_exists($r->AgeFilter, $job_position_age_filter_risk_score[$r->JobPosition]))
				{
					$job_position_age_filter_risk_score[$r->JobPosition][$r->AgeFilter] = 0;
				}
				if(0.5 < $r->percentageOfWorkersInDimension)
				{
					$job_position_age_filter_risk_score[$r->JobPosition][$r->AgeFilter] = $job_position_age_filter_risk_score[$r->JobPosition][$r->AgeFilter] + $r->Risk;
				}
			}
			
			foreach($job_position_age_filter_risk_score as $job_position_key => $job_position_array)
			{
				foreach($age_filter_array as $age_filter_key)
				{
					$current_score = array_key_exists($age_filter_key, $job_position_array) ? $job_position_array[$age_filter_key] : 'N/A';
					$current_label = array_key_exists($age_filter_key, $job_position_array) ? $this->getRiskLabelFromScore($current_score) : 'N/A';
					$report["job_position"][$job_position_key]["age"][$age_filter_key]["risk_score"] = $current_score;
					$report["job_position"][$job_position_key]["age"][$age_filter_key]["risk_label"] = $current_label;
				}
			}
		}
        return $report;
    }
	
	private function getRiskLabelFromScore($risk_score = 0)
	{
		if($risk_score >= HIGH_RISK_THRESHOLD) return HIGH_RISK_NAME;
		if($risk_score < MEDIUM_RISK_THRESHOLD) return LOW_RISK_NAME;
		return MEDIUM_RISK_NAME;
	}
	
	private function getRiskTimeGapFromScore($risk_score = 0)
	{
		if($risk_score >= HIGH_RISK_THRESHOLD) return HIGH_RISK_TIME_GAP;
		if($risk_score < MEDIUM_RISK_THRESHOLD) return LOW_RISK_TIME_GAP;
		return MEDIUM_RISK_TIME_GAP;
	}
	
	private function getRiskPointLabelFromScore($risk_point_score = 0)
	{
		if($risk_point_score == HIGH_RISK_POINT) return HIGH_RISK_POINT_NAME;
		if($risk_point_score == LOW_RISK_POINT) return LOW_RISK_POINT_NAME;
		return MEDIUM_RISK_POINT_NAME;
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
