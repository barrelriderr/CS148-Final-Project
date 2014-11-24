<?php

require_once("top.php");

if (Controller::is_signed_in())
	get_controller("User", "account");
else
	get_controller("Register", "index");