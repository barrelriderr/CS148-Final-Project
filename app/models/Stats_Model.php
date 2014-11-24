<?php

class Stats_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	// Select CPUs in used for comparison
	public function get_cpu_core_distribution($make) {
		$query = "	SELECT 
						COUNT(builder_id) AS count, 
						cores 
					FROM 
						computers, 
						cpus, 
						cpu_makers 
					WHERE 
						cpus.cpu_id = computers.cpu_id 
						AND cpus.maker_id = cpu_makers.maker_id 
						AND cpu_makers.name LIKE ? 
					GROUP BY
						cores
					ORDER BY 
						cores";

		return $this->return_query($query, array($make));
	}

	public function get_cpu_core_range() {
		$query = "	SELECT
						DISTINCT(cores)
					FROM
						cpus
					ORDER BY
						cores";
						
		return $this->return_query($query);
	}

	public function get_cpu_speed_distribution($make) {
		$query = "	SELECT 
						COUNT(builder_id) AS count, 
						clock_speed 
					FROM 
						computers, 
						cpus, 
						cpu_makers 
					WHERE 
						cpus.cpu_id = computers.cpu_id 
						AND cpus.maker_id = cpu_makers.maker_id 
						AND cpu_makers.name LIKE ? 
					GROUP BY 
						clock_speed";

		return $this->return_query($query, array($make));
	}

	public function get_gpu_counts() {
		$query = "	SELECT 
						COUNT(builder_id) AS user_count, 
						gpu_count 
					FROM 
						computers 
					GROUP BY 
						gpu_count
					ORDER BY
						gpu_count";

		return $this->return_query($query);
	}

	// Bar graph bar set for each speed. size x count?
	public function get_ram_distribution() {
		$query = "	SELECT 
						COUNT(builder_id) AS count, 
						size, 
						speed 
					FROM 
						computers AS c, 
						ram_sizes, 
						ram_speeds 
					WHERE 
						c.ram_size = ram_sizes.ram_id 
						AND c.ram_speed = ram_speeds.ram_id 
					GROUP BY speed, size";

		return $this->return_query($query);
	}

	public function get_top_5_cpus() {
		$query = "	SELECT 
						COUNT(computer_id) AS count,
						comp.cpu_id, 
						CONCAT(cpu_makers.name, ' ', cpus.model) AS cpu
					FROM 
						computers AS comp, 
						cpus,
						cpu_makers
					WHERE 
                    	comp.cpu_id = cpus.cpu_id
                        AND cpus.maker_id = cpu_makers.maker_id
						AND comp.cpu_id IS NOT NULL 
					GROUP BY 
						comp.cpu_id 
					ORDER BY 
						count DESC 
					LIMIT 
						5";

		return $this->return_query($query);
	}

	public function get_computer_standing($build_id) {
		$query = "";

		return $this->return_query($query, array($build_id));
	}
}