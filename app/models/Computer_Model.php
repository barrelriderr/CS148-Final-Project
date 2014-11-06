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

	public function insert_computer($name, $description, $color, $purpose) {
		$builder_id = Controller::get_user_id();
		$query = 'INSERT INTO computers (name, builder_id, description, color, purpose) VALUES (?, ?, ?, ?, ?)';

		$success = $this->binary_query($query, array($name, $builder_id,$description, $color, $purpose));

		return $success;
	}
}