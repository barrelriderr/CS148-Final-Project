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
						username,
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
						username,
						email,
						date_joined,
						confirmed
					FROM 
						users
					WHERE
						user_id=?";

		return $this->return_query($query, array($user_id));
	}

	public function delete_user($user_id) {
		$query = "	DELETE FROM 
						users 
					WHERE 
						user_id=?";

		return $this->binary_query($query, array($user_id));
	}
}