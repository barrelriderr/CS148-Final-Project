<?php

class Register_Controller extends Controller{
	public static $input = array();

	public function __construct() {
	}

	public function index() {

		if ($_SERVER["REQUEST_METHOD"] == 'POST') {

			require("../app/models/Register_Model.php");
			$this->model = new Register_Model();

			// Sanitize
			$email = trim(htmlentities($_POST['email']));
			$password = trim(htmlentities($_POST['password']));
			$confirm_password = trim(htmlentities($_POST['confirm_password']));

			// Validate
			if ($email == "") {
				static::$error_messages['email'] = "Provide an email.";

			}else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				static::$error_messages['email'] = "Invalid email.";

			}else {

				$unique_email = $this->model->is_unique_email($email);
				if (!$unique_email) {
					static::$error_messages['email'] = "Email already in use.";
				}

			}

			if ($password == "") {
				static::$error_messages['password'] = "Provide a password.";
			}else if (!preg_match_all('$\S*(?=\S{8,100})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $password)) {
				static::$error_messages['password'] = "Password must be between 8 and 100 characters with at least 1 upper and lower case letter.";
			}

			if ($confirm_password == "") {
				static::$error_messages['confirm_password'] = "Confirm your password.";

			}else if ($password != $confirm_password) {
				static::$error_messages['confirm_password'] = "Passwords do not match.";
			}

			static::$input = array('email' => $email, 'password' => $password, 'confirm_password' => $confirm_password);
			
			// Validation check
			if (count(static::$error_messages) == 0) {

				// Insert data
				$email = static::$input['email'];
				$password = static::$input['password'];

				$password = hash('sha256', $password);

				if($this->model->insert_user($email, $password)) {
					View::make('registration/registration_success');
					return;
				}else {
					static::$error_messages['processing'] = "There was an error on our end.";
				}
			}
		}

		// Invalid input or form not submitted
		View::make('registration/register');
	}

	public function confirmation() {
		
		$user_id = intval(trim(htmlentities($_GET[''])));
		$key =  trim(htmlentities($_GET['']));

		if ($user_id != null && $key != null) {
			$this->model = new Register_Model();
			
			if ($this->model->check_user($user_id, $key)) {
				$this->model->confirm_user($user_id, $key);
				return;
			}
		}
		View::make('confirmation');
	}
}