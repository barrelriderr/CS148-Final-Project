<?php

class User_Controller extends Controller {

	public static $input = array();

	public function sign_in() {

		if ($_SERVER["REQUEST_METHOD"] == 'POST') {

			$email = trim(htmlentities($_POST['email']));
			$password = trim(htmlentities($_POST['password']));

			static::$input['email'] = $email;

			if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match_all('$\S*(?=\S{8,100})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $password)) {
				static::$error_messages['signin'] = "Invalid credentials";
			}
			
			if (count(static::$error_messages) == 0) {
				require("../app/models/User_Model.php");
				$this->database = new User_Model();

				$user_id = $this->database->signin($email, $password);

				if ($user_id) {
					$_SESSION['user_id'] = intval($user_id);
					$_SESSION['signed_in'] = time();
					
					View::redirect('account');
				}else {
					static::$error_messages['signin'] = "Invalid credentials";
				}
			}
		}
		View::make('user/signin');	
	}

	public function sign_out() {

		//session_destroy();

		View::redirect("index");
	}

	public function account() {
		if (Controller::is_signed_in()) {
			View::make('user/account');
		}
	}

}

