<?php

class User_Controller extends Controller {

	public static $input = array();
	public static $computer_list = array();
	public static $unfinished_computer_list = array();
	public static $username;

	public function sign_in() {

		if ($_SERVER["REQUEST_METHOD"] == 'POST') {

			$account = trim(htmlentities($_POST['account']));
			$password = trim(htmlentities($_POST['password']));

			static::$input['account'] = $account;

			if (strtoupper($account) == "GUEST") {
				// Guest account
				$account = "guest";
			}else {
				if ($account == null || !preg_match_all('$\S*(?=\S{8,100})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $password, $matches)) {
					static::$error_messages['signin'] = "Invalid credentials";
					echo "didn't check";
				}
			}
			
			if (count(static::$error_messages) == 0) {
				require("../app/models/User_Model.php");
				$this->model = new User_Model();
				if ($account == "guest") {
					$user_id = 1;
				}else {
					$result = $this->model->signin($account, $password);
					$user_id = $result[0]['user_id'];
					$is_admin = $result[0]['is_admin'];
				}

				if ($user_id) {
					$_SESSION['user_id'] = intval($user_id);
					$_SESSION['is_admin'] = intval($is_admin);
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

			$username = $this->model->get_username($user_id);

			static::$username = htmlentities($username[0]['username']);

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

