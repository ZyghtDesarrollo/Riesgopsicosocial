<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Travellog_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Log';
		$this->id = 'id';
	}

	public function create($travel_id, $latitude, $longitude, $date) {
		$this->db->insert($this->table, array(
			'travel_id' => $travel_id,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'date' => $date
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}
	
	public function get_by_travel_id($id) {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('travel_id', $id);
		$this->db->order_by('id');
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}

	public function get_travel_info($id) {
		$logs = $this->get_by_travel_id($id);

		$count = count($logs);

		if ($count <= 1) {
			return FALSE;
		}

		$distance = 0;

		for ($pos = 1; $pos < $count; $pos++) {
			$prev = $pos - 1;

			$aux = $this->_distance($logs[$prev]->latitude, $logs[$prev]->longitude, $logs[$pos]->latitude, $logs[$pos]->longitude, "K");

			$distance += $aux;
		}

		$time1 = new DateTime($logs[0]->date);
		$time2 = new DateTime($logs[$count - 1]->date);

		$diff = $time2->getTimestamp() - $time1->getTimestamp();

		$hours = $diff / 3600;
		$velocity = round($distance / $hours, 2);

		return array(
			"distance" => $distance,
			"hours" => $hours,
			"time" => sprintf("%02d%s%02d%s%02d", floor($diff / 3600), ":", ($diff / 60) % 60, ":", $diff % 60),
			"velocity" => $velocity
		);
	}

	private function _distance($lat1, $lon1, $lat2, $lon2, $unit) {
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}
}
