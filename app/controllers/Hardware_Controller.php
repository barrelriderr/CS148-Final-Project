<?php

class Hardware_Controller extends Controller{

	public static $input = [];
	public $build_name = null;

	public function __construct() {}

	public function add() {
		require("../app/models/Hardware_Model.php");
		$this->model = new Hardware_Model();
		$build_id = filter_input(INPUT_GET, "bid", FILTER_VALIDATE_INT);

		if (isset($build_id))
			$this->build_name = $this->model->get_build_name($build_id);
		else
			echo "Invalid build id!"

		if ($this->build_name == null){
			// Handle error

		}else if ($_SERVER["REQUEST_METHOD"] == 'POST') {
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

			if ($gpu != null) {

				$purposes_value = 0;

				foreach ($amd_gpu_list as $key => $value) {
					foreach ($purposes as $purpose) {
						if ($value == $purpose)
							static::$input['purposes.'.$value] = " checked";
							$purposes_value += $key;
					}
				}

			}else {
				static::$error_messages['gpu'] = "Select at least 1 purpose.";
			}

			static::$input['name'] = $name;
			static::$input['description'] = $description;

			if (count(static::$error_messages) == 0) {				

				echo "computer complete!";
				

			}
		}
		View::make('add/add_hardware');
	}
}