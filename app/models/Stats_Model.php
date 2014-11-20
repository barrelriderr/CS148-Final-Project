<?php

class Hardware_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	// Select CPUs in used for comparison
	public function get_cpu_core_distribution($make) {
		$query = "";

		return $this->binary_query($query, $computer_specs);
	}

	public function get_cpu_speed_distribution($make) {
		$query = "";

		return $this->binary_query($query, $computer_specs);
	}
}