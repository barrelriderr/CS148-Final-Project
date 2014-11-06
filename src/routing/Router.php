<?php
class Router {
	// Routes added by routes.php
	private $_controller = [];
	// Requested route
	private $uri_GET_param;
	private $parent_route;

	public function __construct() {
		$this->parent_route = null;
	}

	/*
	 *	Builds a collection of internal URI's to look for
	 *
	 */
	public function add($route, $controller) {
		$this->_controller[$route] = array('controller' => $controller);
	}

	/*
	 *	Process URI request.
	 */
	public function submit() {
		// Get URI from server.
		$this->get_uri();
		
		$route_index = $this->match_uri();
		echo "Matched URI $route_index";
		if($route_index === false) {
			View::make("404");
			return;
		}

		$use_controller = $this->_controller[$route_index]['controller'];

		if(is_array($use_controller)) {
			$this->call_controller($use_controller);
		}
		else {
			// Argument was a function.
			call_user_func($use_controller);
		}
	}

	// Compare requested route to indexes of _controller added by routes.php
	private function match_uri() {

		// Search _controller for requested route.
		foreach($this->_controller as $key => $value) {
			// Route
			$test_route = $key;
			echo "Test route $test_route __";
			// Test key against request
			if($this->is_uri_match($test_route)) {
				// Route found!
				return $key;
			}
		}
		// Route not found!
		return false;
	}

	private function get_uri() {
		// GETs URI request. Enabled by .htaccess.
		$this->uri_GET_param = isset($_GET['uri']) ? '/' . $_GET['uri'] : '/';
	}

	// Compares a valid route with requested route.
	private function is_uri_match($route) {
		$match = preg_match("#^$route$#", $this->uri_GET_param);
		return $match;
	}

	// Calls requested controller method.
	private function call_controller($use_controller) {
		$uses = $use_controller['uses'];
		$calls = $use_controller['calls'];

		if (isset($use_controller['with']))
			$with = $use_controller['with'];
		else
			$with = null;

		require('../app/controllers/'.$uses.".php");

		$controller = new $uses;

		if ($with == null) {
			call_user_func(array($controller, $calls));
		}
		else {
			call_user_func_array(array($controller, $calls), $with);
		}
	}
}