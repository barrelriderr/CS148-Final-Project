<?php



class Computer_Controller extends Controller{

	public static $input;
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

			if (strlen($description) > 60000) {
				static::$error_messages['description'] = "Description is longer than 60 000 characters.";
			}

			var_dump($color);

			
			
			if ($color == $this->COLORS_LIST[0]) {
				static::$error_messages['color'] = "Please select a build color.";
			}else {
				static::$error_messages['color'] = "Please select a valid color.";

				foreach ($this->COLORS_LIST as $key => $valid_color) {
					if ($valid_color == $color) {
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
							$purposes_value += $key;
					}
				}
			}else {
				static::$error_messages['purposes'] = "Select at least 1 purpose.";
			}

			static::$input['name'] = $name;
			static::$input['description'] = $description;
			static::$input['color'] = $color;
			static::$input['purposes'] = $purposes;



			if (count(static::$error_messages) == 0) {				

				$user_id = $this->database->signin($email, $password);

				if ($user_id) {
					$_SESSION['user_id'] = intval($user_id);
					$_SESSION['signed_in'] = time();
					
					View::redirect('account');
				}else {
					static::$error_messages['signin'] = "Invalid credentials";
				}
			}
		}
		View::make('add');
	}

	//Display HTML for check boxes with previous selections.
	public static function display_purposes_input() {
		$input = $purposes = $_POST['purposes'];
		$counter = 0;
		if !is_array($input)
			$input = array($input);

		foreach ($this->PURPOSES_LIST as $key => $purpose) {
			echo '<input type="checkbox" name="purpose" value="'.$purpose.'"';
			if $purpose == $input[$counter];
				echo " checked>";
			else
				echo ">$purpose<br>\n";
		}
	}

	public static function display_color_input() {
		$input = static::$input['color'];
		echo "<select name=\"color\">\n";
		foreach ($this->COLORS_LIST as $color) {
			echo "<option" . ($color == $input) ? " selected>" : ">";
			echo $color."</option>\n";
		}
		echo "</select>\n";
	}
}