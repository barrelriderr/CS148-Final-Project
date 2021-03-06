<?php



class Hardware_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	// Note that last 2 elements of the computer specs array must be computer id and user id.
	public function add_hardware($computer_specs) {
		$query = "	UPDATE 
						computers 
					SET 
						cpu_id=?, 
						gpu_id=?, 
						gpu_count=?, 
						ram_speed=?, 
						ram_size=? 
					WHERE 
						computer_id=? 
						AND builder_id=?";

		return $this->binary_query($query, $computer_specs);
	}

	public function update_computer($computer_specs) {
		$query = "	UPDATE 
						computers 
					SET 
						description=?,
						cpu_id=?, 
						gpu_id=?, 
						gpu_count=?, 
						ram_speed=?, 
						ram_size=? 
					WHERE 
						computer_id=? 
						AND builder_id=?";

		$computer_specs[] = Controller::get_user_id();

		return $this->return_query($query, $computer_specs);
	}

	public function get_hardware($computer_id) {

		$query = "	SELECT
						computers.description,
						computers.cpu_id,
						computers.gpu_id,
						gpu_count,
						ram_size,
						ram_speed,
						cpus.maker_id AS cpu_maker,
						gpus.maker_id AS gpu_maker
					FROM
						computers,
						cpus,
						gpus
					WHERE
						cpus.cpu_id = computers.cpu_id
						AND gpus.gpu_id = computers.gpu_id
						AND computer_id=?
						AND builder_id=?";

		$user_id = Controller::get_user_id();

		return $this->return_query($query, array($computer_id, $user_id));
	}

	public function get_computer($computer_id) {

		$query = "SELECT 
						name,
						cpu_id
					FROM 
						computers 
					WHERE 
						computer_id=?
						AND builder_id=?";

		$user_id = Controller::get_user_id();

		return $this->return_query($query, array($computer_id, $user_id));
	}

	public function get_cpu_list($make) {
		$query ="	SELECT 
						cpu_id, 
						model
					FROM 
						cpu_makers AS makers, 
						cpus 
					WHERE 
						cpus.maker_id=makers.maker_id 
						AND name LIKE ? 
						ORDER BY model";

		$results = $this->return_query($query, array($make));

		return $results;
	}

	public function get_gpu_list($make) {
		$query = "SELECT 
			 				gpus.gpu_id,
			 				CONCAT(details.name, ' ', series, ' ', model, ' ', suffix) AS model 
			 			FROM 
			 				gpus, gpu_makers AS details 
						 WHERE 
						 	gpus.maker_id LIKE details.maker_id 
						 	AND details.name LIKE ?";

		$results = $this->return_query($query, array($make));

		return $results;
	}

	public function get_ram_speed_list() {
		$query ="	SELECT 
						ram_id, 
						speed
					FROM 
						ram_speeds
					ORDER BY
						speed";

		$results = $this->return_query($query);

		return $results;
	}

	public function get_ram_size_list() {
		$query ="	SELECT 
						ram_id, 
						size_name AS size
					FROM 
						ram_sizes";

		$results = $this->return_query($query);

		return $results;
	}
}