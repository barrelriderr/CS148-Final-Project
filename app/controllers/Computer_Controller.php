<?php

class Computer_Controller extends Controller{

	public static $input = [];
	private $PURPOSES_LIST;
	private $COLORS_LIST;

	public function __construct() {
		$this->PURPOSES_LIST = array(8 => 'video', 4 => 'gaming', 2 => 'school', 1 => 'work');
		$this->COLORS_LIST = array('Build Color...', 'Black', 'White', 'Red', 'Blue', 'Yellow', 'Green', 'Pink', 'Grey');
	}


	public function add() {

		if ($_SERVER["REQUEST_METHOD"] == 'POST') {
			$name = trim(htmlentities($_POST['name']));
			$description = trim(htmlentities($_POST['description']));
			$color = trim(htmlentities($_POST['color']));
			$purposes = $_POST['purposes'];

			require("../app/models/Computer_Model.php");
			$this->model = new Computer_Model();

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
				static::$error_messages['description'] = "Description is longer than 60 000 characters.";
			}
			
			static::$input['description'] = $description;

			if ($color == $this->COLORS_LIST[0]) {
				static::$error_messages['color'] = "Please select a build color.";
			}else {
				static::$error_messages['color'] = "Please select a valid color.";

				foreach ($this->COLORS_LIST as $key => $valid_color) {
					if ($valid_color == $color) {
						static::$input['color.'.$color] = ' selected';
						unset(static::$error_messages['color']);
						break;
					}
				}
			}

			if ($purposes != null) {

				$purposes_value = 0;

				foreach ($this->PURPOSES_LIST as $key => $value) {
					foreach ($purposes as $purpose) {
						if ($value == $purpose)
							static::$input['purposes.'.$value] = " checked";
							$purposes_value += $key;
					}
				}
			}else {
				static::$error_messages['purposes'] = "Select at least 1 purpose.";
			}

			static::$input['name'] = $name;
			static::$input['description'] = $description;

			if (count(static::$error_messages) == 0) {				

				$build_id = $this->model->insert_computer(Controller::get_user_id(),
															static::$input['name'],
															static::$input['description'],
															$color,
															$purposes_value
															);
				if ($build_id) {
					ob_end_clean(); // Destroy buffer
					header("Location: add_hardware.php?bid=$build_id");
					exit();
				}
			}
		}
		View::make('add/add_build');
	}
}