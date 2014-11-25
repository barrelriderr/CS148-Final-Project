<?php

class User_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	// Allows username and email value for $account.
	public function signin($account, $password) {

		$hashed_password = hash('sha256', $password);
		
		$query = "	SELECT 
						user_id,
						is_admin
					FROM 
						users 
					WHERE 
						(username LIKE ?
						 OR email LIKE ?) 
						AND password LIKE ? 
						AND confirmed=1";

		return $this->return_query($query, array($account, $account, $hashed_password));
	}

	public function get_users_computers($user_id){
		$query = "	SELECT 
						computer_id, 
						name 
					FROM 
						computers 
					WHERE 
						builder_id=? 
						AND cpu_id IS NOT NULL 
					LIMIT 
						20";

		$results = $this->return_query($query, array($user_id));

		return $results;
	}

	public function get_email($user_id) {
		$query = "	SELECT
						email
					FROM
						users
					WHERE
						user_id=?";

		return $this->return_query($query, array($user_id));
	}

	public function get_username($user_id) {
		$query = "	SELECT
						username
					FROM
						users
					WHERE
						user_id=?";

		return $this->return_query($query, array($user_id));
	}


	public function get_users_unfinished_computers($user_id) {
		$query = "	SELECT 
						computer_id, 
						name 
					FROM 
						computers 
					WHERE 
						builder_id=? 
						AND cpu_id IS NULL 
					LIMIT 
						20";

		$results = $this->return_query($query, array($user_id));

		return $results;
	}
}