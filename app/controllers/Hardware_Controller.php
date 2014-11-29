<?php

class Hardware_Controller extends Controller{

	public static $input = array();
	public static $computer_name = null;
	public static $finished_computer;
	public static $computer_id = null;
	private static $cpu_list = array();
	private static $gpu_list = array();
	private static $ram_list = array();

	public function __construct() {
		require("../app/models/Hardware_Model.php");
		$this->model = new Hardware_Model();

		// Load select CPU & GPU options
		static::$cpu_list['AMD'] = $this->model->get_cpu_list("AMD");
		static::$cpu_list['Intel'] = $this->model->get_cpu_list("Intel");

		static::$gpu_list['AMD'] = $this->model->get_gpu_list("AMD");
		static::$gpu_list['Nvidia'] = $this->model->get_gpu_list("Nvidia");

		static::$ram_list['speed'] = $this->model->get_ram_speed_list();
		static::$ram_list['size'] = $this->model->get_ram_size_list();

	}

	public function add() {
		$computer_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

		// Get Computer Name
		if (isset($computer_id)){
			static::$computer_id = $computer_id;
			$results = $this->model->get_computer($computer_id);
			static::$computer_name = $results[0]['name'];

			if ($results[0]['cpu_id'] == null) {
				static::$finished_computer = false;
			}else {
				static::$finished_computer = true;
			}
		}

		if (static::$computer_name == null){
			// Handle error
			View::make('add/computer_not_found');

		}else {
			if ($_SERVER["REQUEST_METHOD"] == 'POST') {

				// CPU VALIDATION
				$cpu_make = intval($_POST['cpu_make']);

				static::$input["cpu_make.".$cpu_make] = "selected";
				$cpu_id = floatval($_POST[$cpu_make."_cpu"]);
				static::$input["cpu_model"] = $cpu_id;

				if ($cpu_make != 1 && $cpu_make != 2) {
					static::$error_messages['cpu_make'] = "Please a CPU brand";
				}

				if (static::$input["cpu_model"] < 1) {
					static::$error_messages['cpu_model'] = "Please select a CPU";
				}


				// GPU VALIDATION
				$gpu_make = intval($_POST['gpu_make']);

				if ($gpu_make == 1 || $gpu_make == 2) {
					static::$input["gpu_make.".$gpu_make] = "selected";
					$gpu_id = floatval(trim(htmlentities($_POST[$gpu_make."_gpu"])));
					static::$input["gpu_model"] = $gpu_id;

					$gpu_count = intval(trim(htmlentities($_POST["gpu_count"])));
					if ($gpu_count < 1 || $gpu_count > 4)
						$gpu_count = 1;
					static::$input["gpu_count.$gpu_count"] = "checked";
				}

				// RAM VALIDATION
				$ram_speed = intval($_POST['ram_speed']);
				$ram_size = intval($_POST['ram_size']);


				$ram_list = static::$ram_list;

				// Check speed
				$valid_speed = false;

				foreach ($ram_list['speed'] as $value) {
					if ($value['ram_id'] == $ram_speed){
						$valid_speed = true;
						break;
					}
				}

				if (!$valid_speed)
					static::$error_messages['ram_speed'] = "Please select a RAM speed";
				else
					static::$input['ram_speed'] = $ram_speed;

				// Check size
				$valid_size = false;

				foreach ($ram_list['size'] as $value) {
					if ($value['ram_id'] == $ram_size){
						$valid_size = true;
						break;
					}
				}

				if (!$valid_size)
					static::$error_messages['ram_size'] = "Please select a RAM size";
				else
					static::$input['ram_size'] = $ram_size;

				if (count(static::$error_messages) == 0) {				
					$computer_specs = array(
										$cpu_id,
										$gpu_id,
										$gpu_count,
										$ram_speed,
										$ram_size,
										$computer_id,
										Controller::get_user_id()
						);
					if($this->model->add_hardware($computer_specs)) {
						View::redirect("account", "success=1");
					}
				}
			}else if (static::$finished_computer == true) {
				$old_hardware = $this->model->get_hardware(static::$computer_id);

				$cpu_make = $old_hardware[0]['cpu_maker'];
				$gpu_make = $old_hardware[0]['gpu_maker'];
				$gpu_count = $old_hardware[0]['gpu_count'];

				static::$input["cpu_make.$cpu_make"] = "selected";
				static::$input["cpu_model"] = $old_hardware[0]['cpu_id'];

				static::$input["gpu_make.$gpu_make"] = "selected";
				static::$input["gpu_model"] = $old_hardware[0]['gpu_id'];
				static::$input["gpu_count.$gpu_count"] = "checked";

				static::$input['ram_speed'] = $old_hardware[0]['ram_speed'];
				static::$input['ram_size'] = $old_hardware[0]['ram_size'];
			}

			View::make('add/add_hardware');
		}
	}

	public function new_hardware() {

		View::make('admin/edit_hardware');
	}

	public static function make_cpu_list($make) {
		$html = "";
		$input = static::$input["cpu_model"];

		foreach (static::$cpu_list[$make] as $value) {
			$html .= '<option value="'.$value['cpu_id'].'"';
			if ($value['cpu_id'] == $input) {
				$html .= " selected";
			}
			$html .= ">".$value['model']."</option>\n"; 
		}

		echo $html;
	}

	public static function make_gpu_list($make) {
		$html = "";
		$input = static::$input["gpu_model"];

		foreach (static::$gpu_list[$make] as $value) {
			$html .= '<option value="'.$value['gpu_id'].'"';
			if ($value['gpu_id'] == $input) {
				$html .= " selected";
			}
			$html .= ">".$value['model']."</option>\n"; 
		}

		echo $html;
	}

	public static function make_ram_speed_list() {
		$html = "";
		$input = static::$input["ram_speed"];

		foreach (static::$ram_list['speed'] as $value) {
			$html .= '<option value="'.$value['ram_id'].'"';
			if ($value['ram_id'] == $input) {
				$html .= " selected";
			}
			$html .= ">".$value['speed']."</option>\n"; 
		}

		echo $html;
	}

	public static function make_ram_size_list() {
		$html = "";
		$input = static::$input["ram_size"];

		foreach (static::$ram_list['size'] as $value) {
			$html .= '<option value="'.$value['ram_id'].'"';
			if ($value['ram_id'] == $input) {
				$html .= " selected";
			}
			$html .= ">".$value['size']."</option>\n"; 
		}

		echo $html;
	}
}