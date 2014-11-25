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
			$username = trim(htmlentities($_POST['username']));
			$email = trim(htmlentities($_POST['email']));
			$password = trim(htmlentities($_POST['password']));
			$confirm_password = trim(htmlentities($_POST['confirm_password']));

			// Validate
			if ($username =="") {
				static::$error_messages['username'] = "Provide an username.";

			}else if(!preg_match ("/^([[:alnum:]]|-|\.|_|')+$/", $username)){
				static::$error_messages['username'] = "Invalid username.";

			}else {
				$unique_username = $this->model->is_unique_username($username);
				if (!$unique_username) {
					static::$error_messages['username'] = "Username already in use.";
				}
			}

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
			}else if (!preg_match_all('$\S*(?=\S{8,100})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $password, $match)) {
				static::$error_messages['password'] = "Password must be between 8 and 100 characters with at least 1 upper and lower case letter.";
			}

			if ($confirm_password == "") {
				static::$error_messages['confirm_password'] = "Confirm your password.";

			}else if ($password != $confirm_password) {
				static::$error_messages['confirm_password'] = "Passwords do not match.";
			}

			static::$input = array('username' => $username, 'email' => $email, 'password' => $password, 'confirm_password' => $confirm_password);
			
			// Validation check
			if (count(static::$error_messages) == 0) {

				// Insert data
				$email = static::$input['username'];
				$email = static::$input['email'];
				$password = static::$input['password'];

				$password = hash('sha256', $password);

				if($this->model->insert_user($username, $email, $password)) {
					$results = $this->model->get_last_user_key();
					
					$user_id = $results['user_id'];
					$key = $results['key'];

					$this->send_confirmation_email($email, $username, $user_id, $key);
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

	private function send_confirmation_email($to, $username, $user_id, $key) {
        
		$from = "btnewton@uvm.edu";

		$subject = "Buildr Confirmation";

	    $message = "Please confirm your address by following the link below:\n\n";

	    $message .= htmlspecialchars("https://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'])."?id=$user_id&key=$key";

	    $headers = "From: " . $from . "\r\n";

	    /* this line actually sends the email */
	    return mail($to, $subject, $message, $headers);
	}

	public function confirmation() {
		
		$user_id = intval(trim(htmlentities($_GET['id'])));
		$key =  trim(htmlentities($_GET['key']));

		require("../app/models/Register_Model.php");
		$this->model = new Register_Model();
		
		if ($this->model->check_user($user_id, $key)) {
			$this->model->confirm_user($user_id, $key);
			View::make('confirmation/confirmation_success');
		}else {
			View::make('confirmation/confirmation_failure');
		}
		
	}
}