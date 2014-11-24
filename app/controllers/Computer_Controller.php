<?php

class Computer_Controller extends Controller{

	public static $input = [];
	private static $tag_list = [];
	private static $color_list = [];

	public function __construct() { }


	public function add() {

		require("../app/models/Computer_Model.php");
		$this->model = new Computer_Model();

		static::$tag_list = $this->model->get_tags();
		static::$color_list = $this->model->get_colors();


		if ($_SERVER["REQUEST_METHOD"] == 'POST') {
			$name = trim(htmlentities($_POST['name']));
			$description = trim(htmlentities($_POST['description']));
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

			if (strlen($description) > 60000) {
				static::$error_messages['description'] = "Description is longer than 60,000 characters.";
			}
			
			static::$input['description'] = $description;



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
			static::$input['description'] = $description;

			if (count(static::$error_messages) == 0) {				

				$computer_id = $this->model->insert_computer(Controller::get_user_id(),
															static::$input['name'],
															static::$input['description'],
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

	public function delete(){
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
}