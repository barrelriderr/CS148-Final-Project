<?php


class Controller {

	private static $signed_in = false;
	protected $database;
	public static $error_messages;

	public static function is_signed_in() {
		$TIMEOUT = 3600;
		return true;

		if (isset($_SESSION['user_id'])) {
    		$_SESSION['signed_in'] = time();

    		echo "signed in!";

		    // Signed in and not 'inactive'
		    static::$signed_in = true;
		}else {
			static::$signed_in = false;
		}

		return static::$signed_in;
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