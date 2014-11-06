<?php

class Register_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	public function is_unique_email($email) {
		$query = "SELECT email FROM users WHERE email LIKE ?";
		$results = $this->return_query($query, array($email));

		if ($results == null) {
			return true;
		}
		// Email already in use.
		return false;
	}

	public function insert_user($email, $password) {
		$query = "INSERT INTO users (email, password) VALUES (?, ?)";

		$success = $this->binary_query($query, array($email, $password));

		return $success;
	}

	public function get_last_user_key() {

		$user_id = $this->last_insert();

		$query = "SELECT date_joined FROM users WHERE user_id=? AND confirmed IS NULL";

		$results= $this->return_query($query, array($user_id));

		$date_joined = $results[0][0];

		$date_joined = sha1($date_joined);

		return $date_joined;
	}

//NOT TESTED
	public function check_user($user_id, $key) {
		$query = "SELECT date_joined FROM users WHERE user_id=? AND confirmed IS NULL";

		$results= $this->return_query($query, array($user_id));

		$date_joined = $results[0][0];

		$date_joined = sha1($date_joined);

		if ($date_joined == $key) {
			return true;
		}

		return false;
	}

// NOT TESTED
	public function confirm_user($user_id) {
		$query = "UPDATE users SET confirmed=1 WHERE user_id=?";

		$success = $this->binary_query($query, array($user_id));

		return $success;
	}

}