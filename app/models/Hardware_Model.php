<?php

class Hardware_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	public function get_build_name($computer_id) {
		$query = "SELECT name FROM computers WHERE computer_id=?";
		$results = $this->return_query($query, array($computer_id));

		return $results[0]['name'];
	}

	public function get_gpu_list($make) {
		$query = "SELECT model FROM (
						SELECT CONCAT(gpus.make, ' ', series, ' ', model, ' ', super_name) AS model
						 	FROM gpus, gpu_maker_details AS details 
						 	WHERE gpus.make LIKE details.make 
						 		AND super_version=1 
						 		AND gpus.make LIKE 'Nvidia' 
			 		UNION ALL 
			 			SELECT CONCAT(gpus.make, ' ', series, ' ', model) AS model 
			 				FROM gpus 
			 				WHERE gpus.make LIKE 'Nvidia'
			 		) AS cards 
					ORDER BY model";

		$results = $this->return_query($query, array($make));

		return $results;
	}



}