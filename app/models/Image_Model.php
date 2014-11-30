<?php

class Image_Model extends Model {

	public function __construct() {
		parent::__construct();
	}


	public function get_images($computer_id) {
		$query = " 	SELECT
						image_id,
						CONCAT(computer_images.image_id,'.',computer_images.extension) AS image
					FROM 
						computer_images
					WHERE
						computer_id=?";

		return $this->return_query($query, array($computer_id));
	}

	public function delete_image($image_id) {
		$query = "	DELETE computer_images.*
					FROM 
						computer_images
					LEFT JOIN
						computers AS comps
					ON 	
						comps.computer_id = computer_images.computer_id
					WHERE
						image_id=?
						AND comps.builder_id=?";

		$user_id = Controller::get_user_id();

		return $this->binary_query($query, array($image_id, $user_id));
	}

	public function delete_image_override($image_id) {
		$query = "	DELETE FROM
						computer_images
					WHERE
						image_id=?";

		return $this->binary_query($query, array($image_id));
	}

	public function get_image_id($computer_id) {
		$query = "	SELECT
						image_id
					FROM
						computer_images
					WHERE
						computer_id=?";

		return $this->return_query($query, array($computer_id));
	}

	public function get_image($image_id) {
		$query = " 	SELECT
						CONCAT(computer_images.image_id,'.',computer_images.extension) AS image
					FROM 
						computer_images
					WHERE
						image_id=?";

		return $this->return_query($query, array($image_id));
	}


	public function new_image($computer_id, $extension) {
		$query = "	INSERT INTO
						computer_images
							(computer_id,
							 extension)
					VALUES
						(?, ?)";

		$this->binary_query($query, array($computer_id, $extension));

		$last_insert = $this->last_insert();

		// Returns image id (used for filename)
		return $last_insert[0][0];
	}

}