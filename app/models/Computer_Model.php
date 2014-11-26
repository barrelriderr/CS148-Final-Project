<?php

class Computer_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	public function is_unique_name($name) {
		$query = "	SELECT 
						name 
					FROM 
						computers 
					WHERE 
						name LIKE ?";
		$results = $this->return_query($query, array($name));

		if ($results == null) {
			return true;
		}
		// Name already in use.
		return false;
	}

	public function get_tags() {
		$query = "	SELECT 
						tag_id, 
						tag 
					FROM 
						tags";

		return $this->return_query($query);
	}

	public function get_colors() {
		$query = "SELECT 
					color_id, 
					color 
				FROM 
					colors";

		return $this->return_query($query);
	}

	public function delete_computer($computer_id){
		$query = "	DELETE FROM 
						computers 
					WHERE 
						computer_id=?
						AND builder_id=?";

		$user_id = Controller::get_user_id();

		return $this->binary_query($query, array($computer_id, $user_id));
	}

	public function insert_computer($builder_id, $name, $color, $tags) {

		$query = '	INSERT INTO 
						computers 
							(builder_id,
							 name, 
							 color) 
					VALUES 
						(?, ?, ?)';

		$success = $this->binary_query($query, array($builder_id, $name, $color));

		$last_insert = $this->last_insert();
		$computer_id = $last_insert[0][0];

		foreach ($tags as $value) {
			$id_and_tags[] = $computer_id;
			$id_and_tags[] = $value;
			$values_array[] = "(?, ?)";
		}

		$query = "	INSERT INTO 
						computer_tags 
							(computer_id, 
							 tag_id) 
					VALUES 
						".implode(", ", $values_array);

		$success_2 = $this->binary_query($query, $id_and_tags);

		return $computer_id;
	}

	public function like($computer_id) {
		$query = "	INSERT INTO
						computer_likes
							(computer_id,
							 liker_id)
					VALUES
						(?, ?)";

		$user_id = Controller::get_user_id();

		return $this->binary_query($query, array($computer_id, $user_id));
	}

	public function unlike($computer_id) {
		$query = "	DELETE FROM
						computer_likes
					WHERE
						computer_id=?
						AND liker_id=?";

		$user_id = Controller::get_user_id();

		return $this->binary_query($query, array($computer_id, $user_id));
	}

	public function get_likes() {
		$query = "	SELECT
						computer_id
					FROM
						computer_likes
					WHERE
						liker_id=?
					ORDER BY 
						computer_id";

		$user_id = Controller::get_user_id();

		return $this->return_query($query, array($user_id));
	}

	public function get_computers() {
			// Get all computers
			$query = "	SELECT
							comps.computer_id,
							comps.name,
							CONCAT(cpu_makers.name, ' ', cpus.model) AS cpu_model,
							CONCAT(gpu_makers.name, ' ', gpus.model, ' x', comps.gpu_count) AS gpu_model,
							COUNT(likes.computer_id) AS count,
							users.username
						FROM
							users,
							cpus,
							cpu_makers,
							computers AS comps
  							LEFT JOIN 
  								computer_likes AS likes 
  								ON comps.computer_id = likes.computer_id 
  							LEFT JOIN
  								gpus
  								ON comps.gpu_id = gpus.gpu_id
  							LEFT JOIN
  								gpu_makers
  								ON gpu_makers.maker_id = gpus.maker_id
						WHERE
							comps.cpu_id IS NOT NULL
							AND users.user_id = comps.builder_id
							AND comps.cpu_id = cpus.cpu_id
							AND cpus.maker_id = cpu_makers.maker_id
                        GROUP BY
                        	comps.computer_id
                        ORDER BY
                        	count DESC
						LIMIT
							500";

			return $this->return_query($query);
		}
}