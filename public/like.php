<?php

require_once("top.php");

if (Controller::is_signed_in())
	get_controller("Computer", "like");
else
	get_controller("User", "sign_in");
?>