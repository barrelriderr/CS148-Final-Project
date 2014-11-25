<?php

class Stats_Model extends Model {

	public function __construct() {
		parent::__construct();
	}

	// Selects cpu cores by make.
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

	// Gets the entire range of CPU cores in the database.
	public function get_cpu_core_range() {
		$query = "	SELECT
						DISTINCT(cores)
					FROM
						cpus
					ORDER BY
						cores";
						
		return $this->return_query($query);
	}

	// Gets speed of all CPUs
	public function get_cpu_speed_distribution($make) {
		$query = "	SELECT 
						COUNT(builder_id) AS count, 
						clock_speed AS speed
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

	// Gets the range of all CPU speeds
	public function get_cpu_speed_range() {
		$query = "	SELECT
						DISTINCT(clock_speed) AS speed
					FROM
						cpus
					ORDER BY
						speed";

		return $this->return_query($query);
	}

	// Gets the number of GPUS per system
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

	// Bar graph set for each speed. size x count?
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

	// List of most popular CPUs
	public function get_top_cpus($count = 5) {

		$query = "	SELECT 
						COUNT(computer_id) AS count,
						comp.cpu_id, 
						CONCAT(cpu_makers.name, ' ', cpus.model) AS model,
						cpus.cores,
						cpus.clock_speed AS speed
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
						$count";


		return $this->return_query($query, array($count));
	}

	// List of most popular CPUs
	public function get_top_colors($count = 5) {
		
		$query = "	SELECT 
						COUNT(computer_id) AS count,
						colors.color
					FROM 
						computers AS comp, 
						colors
					WHERE 
                    	comp.color = colors.color_id
					GROUP BY 
						comp.color 
					ORDER BY 
						count DESC 
					LIMIT 
						$count";

		return $this->return_query($query, array($count));
	}

	public function get_top_computers($count = 5) {
		
		$query = "	SELECT
						COUNT(likes.computer_id) AS count,
						comps.name AS name,
						email
					FROM
						users,
						computers AS comps,
						computer_likes AS likes
					WHERE
						comps.computer_id = likes.computer_id
						AND comps.builder_id = users.user_id
					GROUP BY
						likes.computer_id
					ORDER BY
						count DESC
					LIMIT
						$count";

		return $this->return_query($query, array($count));
	}

	// idk...
	public function get_computer_standing($computer_id) {
		$query = "";

		return $this->return_query($query, array($computer_id));
	}
}