<?php

class Computer_Controller extends Controller{

	public static $input = array();
	private static $tag_list = array();
	private static $color_list = array();
	public static $computer_list;

	public function __construct() { }


	public function add() {

		require("../app/models/Computer_Model.php");
		$this->model = new Computer_Model();

		static::$tag_list = $this->model->get_tags();
		static::$color_list = $this->model->get_colors();


		if ($_SERVER["REQUEST_METHOD"] == 'POST') {
			$name = trim(htmlentities($_POST['name']));
			$color_input = intval(trim(htmlentities($_POST['color'])));
			$tags_input = $_POST['tags'];

			if ($name == "") {
				static::$error_messages['name'] = "Provide a name.";
			}else {
				$unique_name = $this->model->is_unique_name($name);
				if (!$unique_name) {
					static::$error_messages['name'] = "Name already in use.";
				}
			}

			static::$input['name'] = $name;

			if ($color_input != null) {
				foreach (static::$color_list as $valid_color) {
					if ($valid_color['color_id'] == $color_input) {
						static::$input['color'] = $color_input;
						break;
					}
				}
			}

			if (static::$input['color'] == null) {
				static::$error_messages['color'] = "Please select a valid color.";
			}

			if ($tags_input != null) {
				foreach ($tags_input as $tag) {
					foreach (static::$tag_list as $valid_tag)
						if($valid_tag['tag_id'] == $tag) {
							static::$input['tags'][] = intval($tag);
							break;
						}
				}
			}
			if (static::$input['tags'] == null) {
				static::$error_messages['tags'] = "Select at least 1 tag.";
			}

			static::$input['name'] = $name;

			if (count(static::$error_messages) == 0) {				

				$computer_id = $this->model->insert_computer(Controller::get_user_id(),
															static::$input['name'],
															static::$input['color'],
															static::$input['tags']
															);
				if ($computer_id) {
					ob_end_clean(); // Destroy buffer
					header("Location: addHardware.php?bid=$computer_id");
					exit();
				}
			}
		}
		View::make('add/add_computer');
	}

	public static function make_color_list() {
		$html = "";
		$input = static::$input["color"];

		foreach (static::$color_list as $color) {
			$html .= '<option value="'.$color['color_id'].'"';
			if ($color['color_id'] == $input) {
				$html .= " selected";
			}
			$html .= ">".$color['color']."</option>\n"; 
		}

		echo $html;
	}

	public static function make_tag_list() {
		
		$html = "";
		$input = static::$input["tags"];

		if($input == null) {
			$input = array();
		}

		foreach (static::$tag_list as $tag) {
			$html .= '<input type="checkbox" name="tags[]" value="'.$tag['tag_id'].'"';
			foreach ($input as $value) {
				if ($tag['tag_id'] == $value) {
					$html .= " checked";
					break;
				}
			}
			$html .= ">".$tag['tag']."<br>\n"; 
		}
		echo $html;
	}

	public function delete() {
		$computer_id = intval($_GET['id']);

		if ($_SERVER["REQUEST_METHOD"] == 'POST') {
			if($computer_id) {
				require("../app/models/Computer_Model.php");
				$this->model = new Computer_Model();

				$this->model->delete_computer($computer_id);

				View::redirect('account');
			}

		}else {
			View::make('add/delete_computer');
		}
	}

	public function like() {
		$computer_id = intval($_GET['id']);

		if ($computer_id > 0) {
			require("../app/models/Computer_Model.php");
			$this->model = new Computer_Model();

			$this->model->like($computer_id);
		}
		
		View::redirect('browse');
	}

	public function unlike() {
		$computer_id = intval($_GET['id']);

		if ($computer_id > 0) {
			require("../app/models/Computer_Model.php");
			$this->model = new Computer_Model();

			$this->model->unlike($computer_id);
		}
		
		View::redirect('browse');
	}

	public function browse() {
		require("../app/models/Computer_Model.php");
		$this->model = new Computer_Model();

		$computers = $this->model->get_computers();

		if(Controller::is_signed_in()){
			$user_likes = $this->model->get_likes();
		}else {
			$user_likes = array();
		}

		$html = "<tr><th>Name</th><th>Creator</th><th>CPU</th><th>GPU</th><th>Likes</th><th></th></tr>\n";

		foreach ($computers as $key => $computer) {
			$computer_id = $computer['computer_id'];
			$name = $computer['name'];
			$username = $computer['username'];
			$cpu = $computer['cpu_model'];
			$gpu = $computer['gpu_model'];

			if ($gpu == null) {
				$gpu = "N/A";
			}

			$likes = $computer['count'];

			$html .= "<tr><td>$name</td><td>$username</td><td>$cpu</td><td>$gpu</td><td>$likes</td><td>";

			$like_html = "<a href='like.php?id=$computer_id'>like</a>";
			
			// Check if liked
			$like_offset = 0;
			for ($i=$like_offset; $i < count($user_likes); $i++) { 
				$liked_computer = $user_likes[$i]['computer_id'];
				if ($liked_computer == $computer_id){
					$like_html = "<a href='unlike.php?id=$computer_id'>unlike</a>";
					$like_offset++;
					break;
				}
			}

			$html .= $like_html."</td></tr>\n";
		}

		static::$computer_list = $html;

		View::make('browse');
	}
}