<?php

class Admin_Model extends Model {

	public function __construct() {
		if (!Controller::is_admin()) {
			die("INVALID CREDENTIALS");
		}
		parent::__construct();
	}

	public function get_users() {
		$query = "	SELECT 
						user_id,
						email,
						date_joined,
						confirmed
					FROM 
						users
					LIMIT
						100";

		return $this->return_query($query);
	}

	public function get_user_information($user_id) {
		$query = "	SELECT 
						user_id,
						email,
						date_joined,
						confirmed
					FROM 
						users
					WHERE
						user_id=?";

		return $this->return_query($query, array($user_id));
	}

	// NOT FINISHED
	public function get_computers($user_id = null) {
		if ($user_id == null) {
			// Get all computers
			$query = "	SELECT
							computer_id,
							cpu_id,
							gpu_id,
						FROM
							computers
						LIMIT
							100";
			$args = array($user_id);
		}else {
			$query = "	SELECT
							computer_id,
							cpu_id,
							gpu_id
						FROM
							computers
						WHERE
							user_id=?
						LIMIT 
							100";

			$args = null;
		}

		return $this->return_query($query, $args);
	}

	public function delete_user($user_id) {
		$query = "	DELETE FROM 
						users 
					WHERE 
						user_id=?";

		return $this->binary_query($query, array($user_id));
	}
}