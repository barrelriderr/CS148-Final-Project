<?php


class Controller {

	private static $signed_in = false;
	protected $model;
	public static $error_messages;

	public static function is_signed_in() {
		$TIMEOUT = 3600;

		if (isset($_SESSION['user_id'])) {
    		$_SESSION['signed_in'] = time();

		    // Signed in and not 'inactive'
		    static::$signed_in = true;
		}else {
			static::$signed_in = false;
		}

		return static::$signed_in;
	}

	public static function get_alias() {
		if (isset($_SESSION['alias'])) {
			return $_SESSION['alias'];
		}
		return false;
	}

	public static function get_user_id() {
		$alias = Controller::get_alias();
		if ($alias) {
			return $alias;
		}
		return intval($_SESSION['user_id']);
	}

	public static function is_admin() {
		if (!Controller::get_alias() && $_SESSION['is_admin'] == 1){
			return true;
		}

		return false;
	}

	public function display_errors() {
        $messages = static::$error_messages;

        if (count($messages) != 0) {
            echo "<ol>\n";
            foreach ($messages as $field => $msg) {
                echo "<li><span class=\"error\">ERROR: $msg</span></li>";
            }
            echo "</ol>\n";
        }
	}
}

?>