<?php

class Stats_Controller extends Controller{

	public static $cpu_core_distribution = [];
	public static $cpu_speed_distribution = [];
	public static $gpu_count = [];
	public static $top_cpus;
	public static $top_colors;
	public static $top_computers;

	public function index() {
		require("../app/models/Stats_Model.php");
		$this->model = new Stats_Model();

		$this->get_core_distribution_data();
		$this->get_speed_distribution_data();
		$this->get_gpu_count_data();
		$this->get_top_cpus();
		$this->get_top_colors();
		$this->get_top_computers();

		View::make('stats');
	}

	private function get_gpu_count_data() {
		$gpu_count_data = $this->model->get_gpu_counts();

		$gpu_count = [];
		$j = 0;

		for ($i=0; $i < 5; $i++) { 
			if (intval($gpu_count_data[$j]['gpu_count']) == $i) {
				$gpu_count[] = $gpu_count_data[$j]['user_count'];
				$j++;
			}else {
				$gpu_count[] = 0;
			}
		}

		static::$gpu_count = $gpu_count;
	}

	private function get_top_cpus() {
		$top_cpus = $this->model->get_top_cpus(5);

		$html = "<tr><th>Model</th><th>Cores</th><th>Speed</th><th>Users</th></tr>";

		foreach ($top_cpus as $cpu) {
			$model = $cpu['model'];
			$cores = $cpu['cores'];
			$speed = $cpu['speed'];
			if (floatval($speed) == intval($speed)) {
				$speed = strval($speed).".0";
			}

			$users = $cpu['count'];

			$html .= "<tr><td>$model</td><td>$cores</td><td>$speed</td><td>$users</td></tr>\n";
		}

		static::$top_cpus = $html;
	}

	private function get_top_colors() {
		$top_colors = $this->model->get_top_colors(5);

		$html = "<tr><th>Color</th><th>Users</th></tr>";

		foreach ($top_colors as $value) {
			$color = $value['color'];
			$users = $value['count'];

			$html .= "<tr><td>$color</td><td>$users</td></tr>\n";
		}

		static::$top_colors = $html;
	}

	private function get_top_computers() {
		$top_computers = $this->model->get_top_computers(5);

		$html = "<tr><th>Computer</th><th>Creator</th><th>Likes</th></tr>";

		foreach ($top_computers as $computer) {
			$name = $computer['name'];
			$email = $computer['email'];
			$likes = $computer['count'];

			$creator = explode("@", $email);

			$html .= "<tr><td>$name</td><td>$creator[0]</td><td>$likes</td></tr>\n";
		}

		static::$top_computers = $html;
	}

	private function get_core_distribution_data() {

		$core_range_data = $this->model->get_cpu_core_range();

		$amd_core_data = $this->model->get_cpu_core_distribution('AMD');
		$intel_core_data = $this->model->get_cpu_core_distribution('Intel');

		foreach ($core_range_data as $value) {
			$core_range[] = $value['cores'];
		}


		$amd_cores = [];
		$amd_counter = 0;

		$intel_cores = [];
		$intel_counter = 0;

		foreach ($core_range as $core_count) {
			$amd_current = $amd_core_data[$amd_counter];
			$intel_current = $intel_core_data[$intel_counter];

			if ($core_count == $amd_current['cores']) {
				$amd_cores[] = $amd_current['count'];
				$amd_counter++;
			}else {
				$amd_cores[] = 0;
			}

			if ($core_count == $intel_current['cores']) {
				$intel_cores[] = $intel_current['count'];
				$intel_counter++;
			}else {
				$intel_cores[] = 0;
			}
		}

		foreach ($core_range as $key => $cores) {
			$core_range[$key] = strval($cores) . " cores";
		}

		$labels = '"' . implode('", "', $core_range) . '"';

		$amd_cores_string = implode(', ', $amd_cores);
		$intel_cores_string = implode(', ', $intel_cores);

		static::$cpu_core_distribution['labels'] = $labels;
		static::$cpu_core_distribution['amd_data'] = $amd_cores_string;
		static::$cpu_core_distribution['intel_data'] = $intel_cores_string;
	}

	private function get_speed_distribution_data(){

		$speed_range_data = $this->model->get_cpu_speed_range();

		$amd_speed_data = $this->model->get_cpu_speed_distribution("AMD");
		$intel_speed_data = $this->model->get_cpu_speed_distribution("Intel");

		foreach ($speed_range_data as $value) {
			$speed_range[] = $value['speed'];
		}


		$amd_speeds = [];
		$amd_counter = 0;

		$intel_speeds = [];
		$intel_counter = 0;

		foreach ($speed_range as $speed) {
			$amd_current = $amd_speed_data[$amd_counter];
			$intel_current = $intel_speed_data[$intel_counter];

			if ($speed == $amd_current['speed']) {
				$amd_speeds[] = $amd_current['count'];
				$amd_counter++;
			}else {
				$amd_speeds[] = 0;
			}

			if ($speed == $intel_current['speed']) {
				$intel_speeds[] = $intel_current['count'];
				$intel_counter++;
			}else {
				$intel_speeds[] = 0;
			}
		}

		foreach ($speed_range as $key => $speed) {
			if(floatval($speed) == intval($speed))
				$speed_range[$key] = strval($speed).".0 GHz";
			else
				$speed_range[$key] = strval($speed)." GHz";
		}

		$labels = '"' . implode('", "', $speed_range) . '"';

		$amd_speeds_string = implode(', ', $amd_speeds);
		$intel_speeds_string = implode(', ', $intel_speeds);

		static::$cpu_speed_distribution['labels'] = $labels;
		static::$cpu_speed_distribution['amd_data'] = $amd_speeds_string;
		static::$cpu_speed_distribution['intel_data'] = $intel_speeds_string;

	}

}

?>