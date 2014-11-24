<?php

require_once("top.php");

if (Controller::is_admin())
	get_controller("Admin", "editComputers");
else
	get_controller("User", "Account");