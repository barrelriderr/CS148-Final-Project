<?php

class Stats_Controller extends Controller{

	public static $cpu_core_distribution = [];
	public static $gpu_count = [];

	public function index() {
		require("../app/models/Stats_Model.php");
		$this->model = new Stats_Model();

		$this->get_core_distribution_data();
		$this->get_gpu_count_data();

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

		$labels = '"' . implode('", "', $core_range) . '"';

		$amd_cores_string = implode(', ', $amd_cores);
		$intel_cores_string = implode(', ', $intel_cores);

		static::$cpu_core_distribution['labels'] = $labels;
		static::$cpu_core_distribution['amd_data'] = $amd_cores_string;
		static::$cpu_core_distribution['intel_data'] = $intel_cores_string;
	}

}

?>