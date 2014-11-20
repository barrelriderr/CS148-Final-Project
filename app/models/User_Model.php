<?php

class User_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	public function signin($email, $password) {

		$hashed_password = hash('sha256', $password);
		
		$query = "SELECT user_id FROM users WHERE email LIKE ? AND password LIKE ?";

		$results = $this->return_query($query, array($email, $hashed_password));

		$user_id = intval($results[0][0]);

		return $user_id;
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