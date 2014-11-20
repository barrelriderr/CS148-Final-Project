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

	public function insert_computer($builder_id, $name, $description, $color, $purpose) {

		$query = 'INSERT INTO computers (builder_id, name, description, color, purpose) VALUES (?, ?, ?, ?, ?)';

		$success = $this->binary_query($query, array($builder_id, $name, $description, $color, $purpose));

		// Get id of last insert
		$build_id = $this->last_insert();

		return $build_id[0][0];
	}
}