<?php

require_once("top.php");

if (Controller::is_signed_in())
	get_controller("Home", "index");
else
	get_controller("Register", "confirmation");

?>