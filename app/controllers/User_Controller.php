<?php

class User_Controller extends Controller {

	public static $input = array();
	public static $computer_list = [];
	public static $unfinished_computer_list = [];
	public static $email;


	public function sign_in() {

		if ($_SERVER["REQUEST_METHOD"] == 'POST') {

			$email = trim(htmlentities($_POST['email']));
			$password = trim(htmlentities($_POST['password']));

			static::$input['email'] = $email;

			if (strtoupper($email) == "GUEST") {
				// Guest account
				$email = "guest";
			}else {
				if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match_all('$\S*(?=\S{8,100})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $password)) {
					static::$error_messages['signin'] = "Invalid credentials";
				}
			}
			
			if (count(static::$error_messages) == 0) {
				require("../app/models/User_Model.php");
				$this->model = new User_Model();
				if ($email == "guest") {
					$user_id = 1;
				}else {
					$user_id = $this->model->signin($email, $password);
				}

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

		if (Controller::get_alias()) {
			//KILL ALIAS SUPER GLOBAL
			unset($_SESSION['alias']);
			View::redirect("account");
		} else {
			session_destroy();
			View::redirect("index");
		}
	}

	public function account() {
		if (Controller::is_signed_in()) {

			$user_id = Controller::get_user_id();

			require("../app/models/User_Model.php");
			$this->model = new User_Model();

			$email = $this->model->get_email($user_id);

			static::$email = htmlentities($email[0]['email']);

			static::$computer_list = $this->model->get_users_computers($user_id);

			static::$unfinished_computer_list = $this->model->get_users_unfinished_computers($user_id);

			View::make('user/account');
		}
	}

	public static function make_computer_list($list) {
		if($list == null)
			return;

		$html = "<ol>\n";

		foreach ($list as $value) {
			$html .= '<li><a href="addHardware.php?bid='.$value['computer_id'].'">'.$value['name']."</a></li>\n";
		}

		$html .= "</ol>\n";

		echo $html;
	}
}

