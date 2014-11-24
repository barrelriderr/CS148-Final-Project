<?php

require_once("top.php");

if (Controller::is_admin())
	get_controller("Admin", "delete_user");
else
	get_controller("User", "account");

?>