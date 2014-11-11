<?php

// Enable error logging
require "../src/logger/Logger.php";
// Enable controllers
require "../src/controlling/Controller.php";
// Enable database
require "../src/modelling/Model.php";
// Enable views
require "../src/viewing/View.php";

function get_controller($controller, $function, $parameters = null) {
	$controller .= "_Controller";


	require("../app/controllers/$controller.php");

	$controller = new $controller;

	if ($parameters == null) {
		call_user_func(array($controller, $function));
	}else {
		call_user_func_array(array($controller, $function), $parameters);
	}
}