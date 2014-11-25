<?php

require_once("top.php");

if (Controller::is_signed_in())
	get_controller("User", "account");
else if(isset($_GET['id']) && isset($_GET['key']))
	get_controller("Register", "confirmation");
else
	get_controller("Register", "index");