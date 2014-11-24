<?php

class Admin_Controller extends Controller{
	public static $input = array();
	public static $user_html;

	public function __construct() {
		if(!Controller::is_admin()){
			die("INVALID CREDENTIALS.");
		}
	}

	public function editUsers() {
		require("../app/models/Admin_Model.php");
		$this->model = new Admin_Model();

		$user_id = intval($_GET['uid']);

		if($user_id != null) {

			$alias = $_GET['alias'];

			if ($alias) {
				$this->set_alias($user_id);
				View::Redirect('account');
			}else {
				$this->user_options($user_id);
			}

		}else {
			$this->user_list();
		}
	}

	private function user_options($user_id){
		$user_information = $this->model->get_user_information($user_id);

		$email = htmlentities($user_information[0]['email']);
		$date = htmlentities($user_information[0]['date_joined']);
		if(htmlentities($user_information[0]['confirmed']) == 1) {
			$confirmed = "Yes";
		}else {
			$confirmed = "No";
		}

		static::$user_html = "<li>User id: $user_id</li>\n";
		static::$user_html .= "<li>Email: $email</li>\n";
		static::$user_html .= "<li>Joined: $date</li>\n";
		static::$user_html .= "<li>Confirmed: $confirmed</li>\n";

		View::make("admin/user");
	}

	private function user_list() {
		$html = "";

		$user_list = $this->model->get_users();

		foreach ($user_list as $key => $value) {
			$user_id = $value['user_id'];
			$id = '<a href="editUsers.php?uid='.$user_id.'">'.$user_id.'</a>';
			$email = $value['email'];
			$joined = $value['date_joined'];

			if(htmlentities($value['confirmed']) == 1) {
				$confirmed = "Yes";
			}else {
				$confirmed = "No";
			}

			$html .= "<tr><td>$id</td><td>$email</td><td>$joined</td><td>$confirmed</td></tr>\n";
		}

		static::$user_html = $html;

		// Invalid input or form not submitted
		View::make('admin/edit_users');
	}

	public function delete_user(){
		$user_id = intval($_GET['uid']);

		if ($_SERVER["REQUEST_METHOD"] == 'POST') {
			if($user_id) {
				require("../app/models/Admin_Model.php");
				$this->model = new Admin_Model();

				$this->model->delete_user($user_id);

				View::redirect('account');
			}

			View::make('admin/delete_user');
		}else {
			View::make('admin/delete_user');
		}
	}

	public function editHardware() {
		
		View::make('admin/edit_hardware');
	}

	public function set_alias($user_id) {
		if ($user_id != Controller::get_user_id()) {
			$_SESSION['alias'] = $user_id;
		}
	}

	public function editComputers() {
		
		
		View::make('admin/edit_computers');
	}
}