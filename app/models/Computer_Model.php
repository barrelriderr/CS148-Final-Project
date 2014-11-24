<?php

class Computer_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	public function is_unique_name($name) {
		$query = "SELECT name FROM computers WHERE name LIKE ?";
		$results = $this->return_query($query, array($name));

		if ($results == null) {
			return true;
		}
		// Name already in use.
		return false;
	}

	public function get_tags() {
		$query = "SELECT tag_id, tag FROM tags";

		return $this->return_query($query);
	}

	public function get_colors() {
		$query = "SELECT color_id, color FROM colors";

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

	public function insert_computer($builder_id, $name, $description, $color, $tags) {

		$query = 'INSERT INTO computers (builder_id, name, description, color) VALUES (?, ?, ?, ?)';

		$success = $this->binary_query($query, array($builder_id, $name, $description, $color));

		$last_insert = $this->last_insert();
		$computer_id = $last_insert[0][0];

		foreach ($tags as $value) {
			$id_and_tags[] = $computer_id;
			$id_and_tags[] = $value;
			$values_array[] = "(?, ?)";
		}

		$query = "INSERT INTO computer_tags (computer_id, tag_id) VALUES ".implode(", ", $values_array);

		$success_2 = $this->binary_query($query, $id_and_tags);

		return $computer_id;
	}
}