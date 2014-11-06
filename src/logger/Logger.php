<?php

class Logger {
	public static function error($error_message) {
		$error_log_file = '../logs/error_log.txt';

		if ($error_message == null) {
			$error_message = "Blank error message";
		}
		
		$error_message .= " -@".date('l jS \of F Y h:i:s A', time())."\n";

		$error_log = fopen($error_log_file, "a");

		fwrite($error_log, $error_message);
		fclose($error_log);
	}


}